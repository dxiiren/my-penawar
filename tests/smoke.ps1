<#
    HTTP smoke-test suite for my-penawar.

    - Boots ITS OWN php dev server on a spare port (default 8614) using the same
      command shape as `just serve` (pinned PHP 8.4, docroot = repo root), so it
      never fights a `just start` server on 8112.
    - Always checks the no-DB pages (static pages + login form render) and the
      auth guard (unauthenticated portal hit 302s to login.php, warning-free).
    - When MariaDB answers on 127.0.0.1:3307 (just db-start; seeded by setup.ps1
      / just db-seed), ALSO drives the real DB flows: a seeded patient login and
      two data pages rendering seeded rows. When the DB is down those checks are
      SKIPped (yellow lines) instead of failing — the suite stays green either way.
    - Lints every *.php file with `php -l` as a gate.
    - Prints one PASS/FAIL/SKIP line per check; exits 1 if any check failed.
    - ALWAYS kills the server it started (finally block, scoped to this repo's
      path + this port — other projects' php.exe processes are untouched).

    Still NOT tested even with the DB up: recover_psw.php (needs SMTP), the dead
    variants (login2.php, index2.php — index2 queries a nonexistent `staff`
    table), and the write flows (booking insert, code.php update/delete) — smoke
    checks must not mutate the seeded database.

    Run via `just test`, or directly:
        powershell -NoProfile -ExecutionPolicy Bypass -File tests/smoke.ps1
#>
param(
    [int]$Port = 8614
)

$ErrorActionPreference = 'Stop'

$RepoRoot = Split-Path -Parent $PSScriptRoot
$Php      = Join-Path $env:LOCALAPPDATA 'Programs\php-8.4\php.exe'
$Base     = "http://127.0.0.1:$Port"
$Curl     = (Get-Command curl.exe).Source

if (-not (Test-Path $Php)) {
    Write-Host "FAIL  PHP 8.4 not found at $Php -- run setup.ps1 first." -ForegroundColor Red
    exit 1
}

$script:Passed  = 0
$script:Failed  = 0
$script:Skipped = 0

function Check([bool]$Ok, [string]$Name, [string]$Detail = '') {
    if ($Ok) {
        Write-Host ("PASS  {0}" -f $Name) -ForegroundColor Green
        $script:Passed++
    } else {
        if ($Detail) { $Detail = " -- $Detail" }
        Write-Host ("FAIL  {0}{1}" -f $Name, $Detail) -ForegroundColor Red
        $script:Failed++
    }
}

function Skip([string]$Name, [string]$Reason) {
    Write-Host ("SKIP  {0} -- {1}" -f $Name, $Reason) -ForegroundColor Yellow
    $script:Skipped++
}

# One HTTP call via curl.exe. Returns @{ Status; Redirect; Body }.
# Pass the URL plus any extra curl args (-c/-b cookie jars, -d form data, ...).
function Invoke-Http([string[]]$CurlArgs) {
    $bodyFile = [System.IO.Path]::GetTempFileName()
    try {
        $meta  = & $Curl -s -o $bodyFile -w '%{http_code}|%{redirect_url}' @CurlArgs
        $parts = "$meta".Split('|', 2)
        $body  = ''
        if (Test-Path $bodyFile) { $body = [System.IO.File]::ReadAllText($bodyFile) }
        return @{ Status = [int]$parts[0]; Redirect = "$($parts[1])"; Body = $body }
    } finally {
        Remove-Item $bodyFile -Force -ErrorAction SilentlyContinue
    }
}

# ── 1. Lint gate: php -l over every *.php in the repo (matches `just lint`) ──
$phpFiles = Get-ChildItem -Path $RepoRoot -Recurse -Filter *.php -File |
    Where-Object { $_.FullName -notlike '*\.git\*' }
$lintErrors = @()
foreach ($f in $phpFiles) {
    $out = & $Php -l $f.FullName 2>&1
    if ($LASTEXITCODE -ne 0) { $lintErrors += "$out" }
}
Check ($lintErrors.Count -eq 0) "php -l lint gate ($($phpFiles.Count) files)" ($lintErrors -join '; ')

# ── 2. HTTP smoke checks (no-DB scope) against a private server instance ────
$server = $null
try {
    # Kill any stale server left by a previous crashed run (this repo + this port only).
    Get-CimInstance Win32_Process -Filter "Name = 'php.exe'" |
        Where-Object { $_.CommandLine -like "*$RepoRoot*" -and $_.CommandLine -like "*:$Port*" } |
        ForEach-Object { Stop-Process -Id $_.ProcessId -Force -ErrorAction SilentlyContinue }

    # Boot: same shape as `just serve`, different port so `just start` is unaffected.
    $server = Start-Process -FilePath $Php `
        -ArgumentList '-S', "127.0.0.1:$Port", '-t', "$RepoRoot\." `
        -WindowStyle Hidden -PassThru

    # Wait until the server answers (max ~10 s).
    $ready = $false
    for ($i = 0; $i -lt 40; $i++) {
        $r = Invoke-Http "$Base/index.html"
        if ($r.Status -eq 200) { $ready = $true; break }
        Start-Sleep -Milliseconds 250
    }
    if (-not $ready) { throw "Server on $Base did not become ready." }

    $r = Invoke-Http "$Base/index.html"
    Check ($r.Status -eq 200 -and $r.Body.Trim().Length -gt 0) `
        'GET /index.html is 200 and non-empty' "status=$($r.Status) bytes=$($r.Body.Length)"

    $r = Invoke-Http "$Base/aboutus.html"
    Check ($r.Status -eq 200) 'GET /aboutus.html is 200' "status=$($r.Status)"

    $r = Invoke-Http "$Base/contact.html"
    Check ($r.Status -eq 200) 'GET /contact.html is 200' "status=$($r.Status)"

    $r = Invoke-Http "$Base/login.php"
    Check ($r.Status -eq 200 -and $r.Body -match 'id="login"' -and $r.Body -match '<form') `
        'GET /login.php is 200 and renders the login form' "status=$($r.Status)"

    $r = Invoke-Http "$Base/index.php"
    Check ($r.Status -eq 200 -and $r.Body -match 'PHP Output Test') `
        'GET /index.php is 200 (output-test scratch page)' "status=$($r.Status)"

    # ── Auth guard (works with or without the DB: the guard exits before any
    #    output or DB connect) ──
    $r = Invoke-Http "$Base/patient%20profile.php"
    Check ($r.Status -eq 302 -and $r.Redirect -match 'login\.php' -and $r.Body -notmatch 'Warning:|Deprecated:|Fatal error:') `
        'unauthenticated GET /patient profile.php 302s to login.php, warning-free' "status=$($r.Status) redirect=$($r.Redirect)"

    # ── DB-backed flows (only when MariaDB answers on 127.0.0.1:3307) ──
    $dbUp = (Test-NetConnection 127.0.0.1 -Port 3307 -WarningAction SilentlyContinue).TcpTestSucceeded
    if ($dbUp) {
        $jar = Join-Path $env:TEMP ("penawar-smoke-" + [guid]::NewGuid().ToString('N') + '.jar')
        try {
            # Seeded patient account from mypenawar.sql (patient table, plaintext demo data).
            $r = Invoke-Http @('-c', $jar, '-b', $jar, '-d', 'id=angela&pass=marieAngela2707&user=patient&login=1', "$Base/login.php")
            Check ($r.Status -eq 200 -and $r.Body -match 'Welcome angela' -and $r.Body -match 'patient profile\.php') `
                'POST seeded patient login succeeds and forwards to the profile' "status=$($r.Status)"

            $r = Invoke-Http @('-c', $jar, '-b', $jar, "$Base/patient%20profile.php")
            Check ($r.Status -eq 200 -and $r.Body -match 'Angela Marie Sulim') `
                'GET /patient profile.php (logged in) renders the seeded patient row' "status=$($r.Status)"

            $r = Invoke-Http "$Base/patient.php"
            Check ($r.Status -eq 200 -and $r.Body -match '730727031099|Completed' -and $r.Body -notmatch 'mysqli_sql_exception') `
                'GET /patient.php renders seeded appointment rows' "status=$($r.Status)"
        } finally {
            Remove-Item $jar -Force -ErrorAction SilentlyContinue
        }
    } else {
        $why = 'MariaDB not answering on 127.0.0.1:3307 (just db-start)'
        Skip 'POST seeded patient login succeeds and forwards to the profile' $why
        Skip 'GET /patient profile.php (logged in) renders the seeded patient row' $why
        Skip 'GET /patient.php renders seeded appointment rows' $why
    }
}
finally {
    # ALWAYS tear down the server this run started — never leave it lingering, and
    # never touch other projects' php.exe (match on repo path AND this port).
    if ($server -and -not $server.HasExited) {
        Stop-Process -Id $server.Id -Force -ErrorAction SilentlyContinue
    }
    Get-CimInstance Win32_Process -Filter "Name = 'php.exe'" |
        Where-Object { $_.CommandLine -like "*$RepoRoot*" -and $_.CommandLine -like "*:$Port*" } |
        ForEach-Object { Stop-Process -Id $_.ProcessId -Force -ErrorAction SilentlyContinue }
}

# ── Summary ──────────────────────────────────────────────────────────────────
Write-Host ''
$skipNote = if ($script:Skipped -gt 0) { " ($($script:Skipped) skipped -- DB down)" } else { '' }
if ($script:Failed -gt 0) {
    Write-Host "$($script:Passed) passed, $($script:Failed) failed.$skipNote" -ForegroundColor Red
    exit 1
}
Write-Host "All $($script:Passed) checks passed.$skipNote" -ForegroundColor Green
exit 0
