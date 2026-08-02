<#
    Test suite for my-penawar.

    - Lints every *.php file with `php -l` and sweeps the tree for live-looking
      credentials as a gate.
    - Boots ITS OWN php dev server on a spare port (default 8614) using the same
      command shape as `just serve` (pinned PHP 8.4, docroot = repo root), so it
      never fights a `just start` server on 8112.
    - STARTS THE DATABASE ITSELF when it is not already running, using the same
      mechanism as `just db-start` (portable MariaDB on 127.0.0.1:3307), and stops
      it again only if this run started it.
    - The DB-backed checks are MANDATORY whenever MariaDB is installed, or whenever
      -RequireDb is passed (which `just test` does). They only degrade to SKIP when
      the MariaDB binary is genuinely absent from the machine. A database that is
      installed but broken FAILS the suite — it must never be silently green.
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
    [int]$Port = 8614,
    # Hard-fail the DB checks even when MariaDB is not installed at all.
    # `just test` passes this so the gate can never be skipped in the normal loop.
    [switch]$RequireDb
)

$ErrorActionPreference = 'Stop'

$RepoRoot  = Split-Path -Parent $PSScriptRoot
$Php       = Join-Path $env:LOCALAPPDATA 'Programs\php-8.4\php.exe'
$Base      = "http://127.0.0.1:$Port"
$Curl      = (Get-Command curl.exe).Source

# Portable MariaDB from setup.ps1 — same paths/port as the justfile's db-* recipes.
$MariaDir  = Join-Path $env:LOCALAPPDATA 'Programs\mariadb'
$MariaExe  = Join-Path $MariaDir 'bin\mariadbd.exe'
$MariaAdm  = Join-Path $MariaDir 'bin\mariadb-admin.exe'
$DbPort    = 3307

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

# A DB-dependent check that could not run. FAILs whenever the database is meant to
# be there (installed, or -RequireDb); only SKIPs on a machine without MariaDB.
function DbGate([string]$Name, [string]$Reason) {
    if ($script:DbMandatory) { Check $false $Name $Reason } else { Skip $Name $Reason }
}

# Fast TCP probe (Test-NetConnection costs seconds per call).
function Test-Port([int]$TcpPort) {
    $client = [System.Net.Sockets.TcpClient]::new()
    try {
        return $client.ConnectAsync('127.0.0.1', $TcpPort).Wait(700)
    } catch {
        return $false
    } finally {
        $client.Dispose()
    }
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

function Count-Of([string]$Haystack, [string]$Needle) {
    return [regex]::Matches($Haystack, [regex]::Escape($Needle)).Count
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

# ── 2. Secret sweep ─────────────────────────────────────────────────────────
# A live ElasticEmail API key was committed once (fixed in c8fa7de by swapping it
# for a placeholder). Nothing may reintroduce a real-looking credential. Vendored
# PHPMailer, images and the git-ignored .mcp.json (which legitimately holds a real
# PAT on a configured machine) are out of scope.
$secretRules = @(
    @{ Name = 'long hex literal (ElasticEmail-style API key)'; Pattern = '["''][A-Fa-f0-9]{28,}["'']' },
    @{ Name = 'credential assigned a long literal';            Pattern = '(?i)(password|passwd|api[_-]?key|secret|token)["'']?\s*[:=]\s*["''][^"'']{12,}["'']' },
    @{ Name = 'known token prefix';                            Pattern = '(ghp_[A-Za-z0-9]{20,}|github_pat_[A-Za-z0-9_]{20,}|sk-[A-Za-z0-9]{20,}|AKIA[0-9A-Z]{16})' }
)
$scanExts  = @('.php', '.html', '.htm', '.js', '.css', '.json', '.sql', '.md', '.stub', '.ps1', '.yml', '.yaml')
$scanFiles = Get-ChildItem -Path $RepoRoot -Recurse -File |
    Where-Object {
        $scanExts -contains $_.Extension.ToLower() -and
        $_.FullName -notlike '*\.git\*' -and
        $_.FullName -notlike '*\Mail\*' -and
        $_.FullName -notlike '*\image\*' -and
        $_.Name -ne '.mcp.json'
    }
$secretHits = @()
foreach ($f in $scanFiles) {
    $text = [System.IO.File]::ReadAllText($f.FullName)
    foreach ($rule in $secretRules) {
        foreach ($m in [regex]::Matches($text, $rule.Pattern)) {
            # Committed placeholders are the intended state, not a leak.
            if ($m.Value -match 'REPLACE_WITH_') { continue }
            $rel  = $f.FullName.Substring($RepoRoot.Length + 1)
            $line = ($text.Substring(0, $m.Index) -split "`n").Count
            $secretHits += "${rel}:$line $($rule.Name)"
        }
    }
}
Check ($secretHits.Count -eq 0) "secret sweep ($($scanFiles.Count) files, no live-looking credentials)" ($secretHits -join '; ')

# ── 3. Bring the database up ourselves (same mechanism as `just db-start`) ──
$script:DbMandatory = $RequireDb.IsPresent -or (Test-Path $MariaExe)
$dbStartedByUs = $false
$dbUp = Test-Port $DbPort
if (-not $dbUp -and (Test-Path $MariaExe)) {
    Start-Process -FilePath $MariaExe `
        -ArgumentList "--datadir=`"$MariaDir\data`"", "--port=$DbPort", '--bind-address=127.0.0.1' `
        -WindowStyle Hidden
    for ($i = 0; $i -lt 40; $i++) {
        Start-Sleep -Milliseconds 500
        if (Test-Port $DbPort) { $dbUp = $true; break }
    }
    $dbStartedByUs = $dbUp
}

# ── 4. HTTP checks against a private server instance ────────────────────────
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

    # ── Static pages: each must render ITS OWN headings, not merely 200 ──
    $r = Invoke-Http "$Base/index.html"
    Check ($r.Status -eq 200 `
            -and $r.Body -match 'Compassionate care for' `
            -and $r.Body -match 'every stage of life' `
            -and $r.Body -match '>Care under one roof<') `
        'GET /index.html renders the home hero and the "Care under one roof" section' "status=$($r.Status) bytes=$($r.Body.Length)"

    $r = Invoke-Http "$Base/aboutus.html"
    Check ($r.Status -eq 200 `
            -and $r.Body -match '>About us<' `
            -and $r.Body -match '>A clinic built around its community<' `
            -and $r.Body -match '>Background<') `
        'GET /aboutus.html renders the "A clinic built around its community" heading' "status=$($r.Status)"

    $r = Invoke-Http "$Base/contact.html"
    Check ($r.Status -eq 200 `
            -and $r.Body -match '>Contact us<' `
            -and $r.Body -match '>Get in touch<' `
            -and $r.Body -match '>Opening hours<') `
        'GET /contact.html renders the "Get in touch" heading and the opening-hours block' "status=$($r.Status)"

    $r = Invoke-Http "$Base/login.php"
    Check ($r.Status -eq 200 -and $r.Body -match 'id="login"' -and $r.Body -match '<form') `
        'GET /login.php is 200 and renders the login form' "status=$($r.Status)"

    $r = Invoke-Http "$Base/index.php"
    Check ($r.Status -eq 200 -and $r.Body -match 'PHP Output Test') `
        'GET /index.php is 200 (output-test scratch page)' "status=$($r.Status)"

    # ── Auth guards (work with or without the DB: the guard exits before any
    #    output or DB connect). Commit 2bbff57 guarded all three of these pages. ──
    foreach ($guarded in @(
        @{ Path = 'patient%20profile.php';  Label = 'patient profile.php' },
        @{ Path = 'appList2.php';           Label = 'appList2.php' },
        @{ Path = 'employee%20profile.php'; Label = 'employee profile.php' }
    )) {
        $r = Invoke-Http "$Base/$($guarded.Path)"
        Check ($r.Status -eq 302 -and $r.Redirect -match 'login\.php' -and $r.Body -notmatch 'Warning:|Deprecated:|Fatal error:') `
            "unauthenticated GET /$($guarded.Label) 302s to login.php, warning-free" `
            "status=$($r.Status) redirect=$($r.Redirect)"
    }

    # ── The DB gate itself ──
    if ($dbUp) {
        Check $true ("MariaDB answering on 127.0.0.1:$DbPort" + $(if ($dbStartedByUs) { ' (started by this run)' } else { '' }))
    } else {
        $why = if (Test-Path $MariaExe) {
            "MariaDB is installed at $MariaDir but did not come up on $DbPort -- check $MariaDir\data\*.err"
        } else {
            "MariaDB not installed at $MariaDir (run setup.ps1)"
        }
        DbGate "MariaDB answering on 127.0.0.1:$DbPort" $why
    }

    if ($dbUp) {
        $jar = Join-Path $env:TEMP ("penawar-smoke-" + [guid]::NewGuid().ToString('N') + '.jar')
        $bad = Join-Path $env:TEMP ("penawar-smoke-" + [guid]::NewGuid().ToString('N') + '.jar')
        try {
            # ── Patient flow: seeded account from mypenawar.sql ──
            $r = Invoke-Http @('-c', $jar, '-b', $jar, '-d', 'id=angela&pass=marieAngela2707&user=patient&login=1', "$Base/login.php")
            Check ($r.Status -eq 200 -and $r.Body -match 'Welcome angela' -and $r.Body -match 'patient profile\.php') `
                'POST seeded patient login succeeds and forwards to the profile' "status=$($r.Status)"

            $r = Invoke-Http @('-c', $jar, '-b', $jar, "$Base/patient%20profile.php")
            Check ($r.Status -eq 200 `
                    -and $r.Body -match 'Angela Marie Sulim' `
                    -and $r.Body -match '730727031099' `
                    -and $r.Body -match 'angelamarie\.sulim@gmail\.com') `
                'GET /patient profile.php (logged in) renders the seeded patient row' "status=$($r.Status)"

            # appList2.php joins patient x booking for the session user. Against the
            # seed that is EXACTLY one row, with these exact cells (empID is NULL).
            $r = Invoke-Http @('-c', $jar, '-b', $jar, "$Base/appList2.php")
            $row = '<td>1</td><td>730727031099</td><td>2022-01-11</td><td>10:00 A.M.</td><td></td><td>Completed</td>'
            Check ($r.Status -eq 200 -and $r.Body.Contains($row) -and (Count-Of $r.Body '<td>') -eq 6) `
                'GET /appList2.php as angela renders exactly 1 appointment row (booking 1, 2022-01-11, Completed)' `
                "status=$($r.Status) cells=$(Count-Of $r.Body '<td>')"

            # ── Staff flow: seeded employee login (employee table, empID as username) ──
            $staffJar = Join-Path $env:TEMP ("penawar-smoke-" + [guid]::NewGuid().ToString('N') + '.jar')
            try {
                $r = Invoke-Http @('-c', $staffJar, '-b', $staffJar, '-d', 'id=D1946&pass=DE@1946&user=staff&login=1', "$Base/login.php")
                Check ($r.Status -eq 200 -and $r.Body -match 'Welcome D1946' -and $r.Body -match 'URL=employee profile\.php') `
                    'POST seeded staff login (D1946) succeeds and forwards to the employee profile' "status=$($r.Status)"

                $r = Invoke-Http @('-c', $staffJar, '-b', $staffJar, "$Base/employee%20profile.php")
                Check ($r.Status -eq 200 `
                        -and $r.Body -match 'Staff Profile' `
                        -and $r.Body -match 'Desmond Soo' `
                        -and $r.Body -match '<td>D1946</td>' `
                        -and $r.Body -match 'desmond@gmail\.com') `
                    'GET /employee profile.php as D1946 renders that employee''s seeded row' "status=$($r.Status)"
            } finally {
                Remove-Item $staffJar -Force -ErrorAction SilentlyContinue
            }

            # ── Staff appointment list (no auth guard on this page by design) ──
            $r = Invoke-Http "$Base/patient.php"
            $listBody = $r.Body
            Check ($r.Status -eq 200 -and $listBody -notmatch 'mysqli_sql_exception' `
                    -and (Count-Of $listBody 'patientedit.php?id=') -eq 5) `
                'GET /patient.php renders exactly 5 seeded appointment rows' `
                "status=$($r.Status) rows=$(Count-Of $listBody 'patientedit.php?id=')"

            Check ($listBody -match '<td>730727031099</td>' `
                    -and $listBody -match 'Had facial pain and runny nose' `
                    -and $listBody -match '>Completed<' `
                    -and $listBody -match '<td>D1337</td>') `
                'patient.php row content matches the seed (Angela''s booking, doctor D1337, Completed)' 'row cells'

            # ── Monthly report: a GROUP BY over completed bookings joined to payments.
            #    Against the seed that is EXACTLY 3 rows. Booking 1 drops out (its
            #    serviceID 'selectS' is dangling) and booking 2 is 'Approved'. ──
            $r = Invoke-Http "$Base/monthly%20report.php"
            $rep = $r.Body
            $reportRows = @(
                '<td>SV007</td><td>Care for minor symptoms</td><td>1</td><td>30.00</td>',
                '<td>SV008</td><td>Treatment for common illness</td><td>1</td><td>20.00</td>',
                '<td>SV009</td><td>Treatment for minor injuries</td><td>1</td><td>48.00</td>'
            )
            $missing = @($reportRows | Where-Object { -not $rep.Contains($_) })
            Check ($r.Status -eq 200 -and $missing.Count -eq 0 -and (Count-Of $rep '<td>') -eq 12) `
                'GET /monthly report.php aggregates exactly 3 service rows (SV007 30.00, SV008 20.00, SV009 48.00)' `
                "status=$($r.Status) cells=$(Count-Of $rep '<td>') missing=$($missing -join ' | ')"

            # ── A REJECTED login must not hand out a session ──
            # login.php used to write $_SESSION["user1"] from the POST before checking
            # the credentials at all, so any bogus POST satisfied the guards that
            # commit 2bbff57 added. The session is now set only on a verified match.
            $r = Invoke-Http @('-c', $bad, '-b', $bad, '-d', 'id=nobody&pass=wrong&user=patient&login=1', "$Base/login.php")
            $rejected = $r.Body -match 'Unsuceessfully Login'
            $r = Invoke-Http @('-c', $bad, '-b', $bad, "$Base/patient%20profile.php")
            Check ($rejected -and $r.Status -eq 302 -and $r.Redirect -match 'login\.php') `
                'a rejected login grants no session -- the portal guard still 302s afterwards' `
                "rejected=$rejected status=$($r.Status) redirect=$($r.Redirect)"
        } finally {
            Remove-Item $jar -Force -ErrorAction SilentlyContinue
            Remove-Item $bad -Force -ErrorAction SilentlyContinue
        }
    } else {
        $why = 'MariaDB not answering on 127.0.0.1:3307 (just db-start)'
        foreach ($name in @(
            'POST seeded patient login succeeds and forwards to the profile',
            'GET /patient profile.php (logged in) renders the seeded patient row',
            'GET /appList2.php as angela renders exactly 1 appointment row (booking 1, 2022-01-11, Completed)',
            'POST seeded staff login (D1946) succeeds and forwards to the employee profile',
            'GET /employee profile.php as D1946 renders that employee''s seeded row',
            'GET /patient.php renders exactly 5 seeded appointment rows',
            'patient.php row content matches the seed (Angela''s booking, doctor D1337, Completed)',
            'GET /monthly report.php aggregates exactly 3 service rows (SV007 30.00, SV008 20.00, SV009 48.00)',
            'a rejected login grants no session -- the portal guard still 302s afterwards'
        )) { DbGate $name $why }
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

    # Only stop MariaDB if THIS run started it — a developer's `just db-start`
    # session must survive `just test`.
    if ($dbStartedByUs) {
        if (Test-Path $MariaAdm) {
            # mariadb-admin writes a passwordless-login warning to stderr, which
            # $ErrorActionPreference='Stop' would otherwise turn into a terminating
            # NativeCommandError and abort the run after every check had passed.
            $prevEap = $ErrorActionPreference
            $ErrorActionPreference = 'Continue'
            try { & $MariaAdm --host=127.0.0.1 --port=$DbPort --user=root shutdown 2>&1 | Out-Null } catch { }
            $ErrorActionPreference = $prevEap
            Start-Sleep -Seconds 1
        }
        Get-CimInstance Win32_Process -Filter "Name = 'mariadbd.exe'" |
            Where-Object { $_.CommandLine -like "*$MariaDir*" -and $_.CommandLine -like "*$DbPort*" } |
            ForEach-Object { Stop-Process -Id $_.ProcessId -Force -ErrorAction SilentlyContinue }
    }
}

# ── Summary ──────────────────────────────────────────────────────────────────
Write-Host ''
$skipNote = if ($script:Skipped -gt 0) { " ($($script:Skipped) skipped -- MariaDB not installed)" } else { '' }
if ($script:Failed -gt 0) {
    Write-Host "$($script:Passed) passed, $($script:Failed) failed.$skipNote" -ForegroundColor Red
    exit 1
}
Write-Host "All $($script:Passed) checks passed.$skipNote" -ForegroundColor Green
exit 0
