<#
    HTTP smoke-test suite for my-penawar.

    - Boots ITS OWN php dev server on a spare port (default 8614) using the same
      command shape as `just serve` (pinned PHP 8.4, docroot = repo root), so it
      never fights a `just start` server on 8112.
    - Checks only what works WITHOUT MySQL (per README "Demo / local access"):
      static pages + the login form render.
    - Lints every *.php file with `php -l` as a gate.
    - Prints one PASS/FAIL line per check; exits 1 if any check failed.
    - ALWAYS kills the server it started (finally block, scoped to this repo's
      path + this port — other projects' php.exe processes are untouched).

    NOT tested — DB-dependent pages. This repo has no MySQL in the test loop, and
    without a MySQL/MariaDB server on localhost:3307 (+ mypenawar.sql imported)
    these pages either return a body that is just an uncaught mysqli_sql_exception
    (pages that `require 'db_config.php'` at the top: patient.php, patientedit.php,
    receipt.php, get_data.php, code.php, reset_psw.php) or render HTML then print
    the same exception at the bottom (pages that connect mid-page: login.php POST
    submission, login2.php, index2.php, "patient profile.php",
    "patient booking.php", "employee profile.php", "monthly report.php",
    appList2.php). recover_psw.php additionally needs SMTP. Asserting on those
    bodies would test the absence of a database, not the app — so they're out.

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

$script:Passed = 0
$script:Failed = 0

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

# One HTTP GET via curl.exe. Returns @{ Status; Body }.
function Invoke-Http([string]$Url) {
    $bodyFile = [System.IO.Path]::GetTempFileName()
    try {
        $status = & $Curl -s -o $bodyFile -w '%{http_code}' $Url
        $body = ''
        if (Test-Path $bodyFile) { $body = [System.IO.File]::ReadAllText($bodyFile) }
        return @{ Status = [int]$status; Body = $body }
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
if ($script:Failed -gt 0) {
    Write-Host "$($script:Passed) passed, $($script:Failed) failed." -ForegroundColor Red
    exit 1
}
Write-Host "All $($script:Passed) checks passed." -ForegroundColor Green
exit 0
