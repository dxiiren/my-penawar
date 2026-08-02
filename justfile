# MyPenawar justfile — development recipes

set shell := ["powershell.exe", "-NoProfile", "-Command"]

# PHP extracted by setup.ps1 into the user's LOCALAPPDATA\Programs\php-8.4.
# Absolute path keeps recipes working even in PowerShell sessions opened before
# setup.ps1 updated the User PATH environment variable.
php  := env_var('LOCALAPPDATA') + '\Programs\php-8.4\php.exe'
port := env_var_or_default('PORT', '8112')

# Portable MariaDB installed by setup.ps1 (no service, no admin). db_config.php pins
# 127.0.0.1:3307 / root / empty password, so the port is NOT overridable here.
mariadb := env_var('LOCALAPPDATA') + '\Programs\mariadb'
db_port := '3307'

# List available recipes
default:
    @just --list

# ─── Guards ───────────────────────────────────────────────

# PHP 8.4 — installed by setup.ps1 to a pinned path; needed by every recipe here.
[private]
_require-php:
    @if (-not (Test-Path '{{php}}')) { Write-Error "PHP 8.4 not found at {{php}}`n  -> Run setup.ps1 first:  pwsh ./setup.ps1"; exit 1 }

# ─── App lifecycle ───────────────────────────────────────

# Runs `stop` first so a previous run's server doesn't linger. The docroot path in the
# command line is what lets `stop` scope the kill to THIS project.
# Start the built-in PHP dev server on http://127.0.0.1:{{port}} (background window).
start: _require-php stop
    Start-Process powershell -ArgumentList "-NoProfile", "-Command", "& '{{php}}' -S 127.0.0.1:{{port}} -t '{{justfile_directory()}}\.'"
    Start-Sleep -Seconds 2
    Write-Host "Started: http://127.0.0.1:{{port}}  (stop with: just stop)"

# Serve in the FOREGROUND (Ctrl+C to stop) — handy for watching request logs.
serve: _require-php
    & '{{php}}' -S 127.0.0.1:{{port}} -t '{{justfile_directory()}}\.'

# Matches php whose command line contains this repo's path (trailing '\').
# Stop only THIS project's php.exe, not every php on the box.
stop:
    $procs = @(Get-CimInstance Win32_Process -Filter "Name = 'php.exe'" | Where-Object { $_.CommandLine -like '*{{justfile_directory()}}\*' }); $procs | ForEach-Object { Stop-Process -Id $_.ProcessId -Force -ErrorAction SilentlyContinue }; Write-Host "Stopped $($procs.Count) project php.exe process(es)"

# ─── Database (portable MariaDB) ─────────────────────────

# MariaDB from setup.ps1 — needed by every db-* recipe below.
[private]
_require-mariadb:
    @if (-not (Test-Path '{{mariadb}}\bin\mariadbd.exe')) { Write-Error "MariaDB not found at {{mariadb}}`n  -> Run setup.ps1 first:  pwsh ./setup.ps1"; exit 1 }

# Start MariaDB on 127.0.0.1:{{db_port}} (background). `just start` does NOT run this
# for you — the pairing for DB-backed pages is:  just db-start && just start
db-start: _require-mariadb
    if ((Test-NetConnection 127.0.0.1 -Port {{db_port}} -WarningAction SilentlyContinue).TcpTestSucceeded) { Write-Host 'MariaDB already answering on 127.0.0.1:{{db_port}}' } else { Start-Process -FilePath '{{mariadb}}\bin\mariadbd.exe' -ArgumentList '--datadir="{{mariadb}}\data"', '--port={{db_port}}', '--bind-address=127.0.0.1' -WindowStyle Hidden; $ok = $false; for ($i = 0; $i -lt 40; $i++) { Start-Sleep -Milliseconds 500; if ((Test-NetConnection 127.0.0.1 -Port {{db_port}} -WarningAction SilentlyContinue).TcpTestSucceeded) { $ok = $true; break } }; if ($ok) { Write-Host 'Started: MariaDB on 127.0.0.1:{{db_port}}  (stop with: just db-stop)' } else { Write-Error 'MariaDB did not come up on port {{db_port}} — check %LOCALAPPDATA%\Programs\mariadb\data\*.err'; exit 1 } }

# Stop MariaDB: graceful shutdown first, then kill any mariadbd from OUR install dir
# (never a system-wide MySQL someone else runs on another port).
db-stop: _require-mariadb
    $graceful = $false; if ((Test-NetConnection 127.0.0.1 -Port {{db_port}} -WarningAction SilentlyContinue).TcpTestSucceeded) { & '{{mariadb}}\bin\mariadb-admin.exe' --host=127.0.0.1 --port={{db_port}} --user=root shutdown 2>$null; $graceful = $true; Start-Sleep -Seconds 1 }; $procs = @(Get-CimInstance Win32_Process -Filter "Name = 'mariadbd.exe'" | Where-Object { $_.CommandLine -like '*{{mariadb}}*' }); $procs | ForEach-Object { Stop-Process -Id $_.ProcessId -Force -ErrorAction SilentlyContinue }; if ($graceful) { Write-Host 'Stopped MariaDB (graceful shutdown)' } else { Write-Host "Stopped $($procs.Count) mariadbd process(es)" }

# (Re-)import mypenawar.sql into the running MariaDB (idempotent: skips when the
# five app tables already exist). setup.ps1 already does this once for you.
db-seed: _require-mariadb
    if (-not (Test-NetConnection 127.0.0.1 -Port {{db_port}} -WarningAction SilentlyContinue).TcpTestSucceeded) { Write-Error 'MariaDB is not running — just db-start first'; exit 1 }; $cli = '{{mariadb}}\bin\mariadb.exe'; $tables = & $cli --host=127.0.0.1 --port={{db_port}} --user=root --batch --skip-column-names -e "SELECT COUNT(*) FROM information_schema.tables WHERE table_schema='mypenawar'"; if ([int]"$tables" -ge 5) { Write-Host "mypenawar already seeded ($tables tables) — nothing to do" } else { & $cli --host=127.0.0.1 --port={{db_port}} --user=root -e 'CREATE DATABASE IF NOT EXISTS mypenawar'; & $cli --host=127.0.0.1 --port={{db_port}} --user=root --default-character-set=utf8mb4 mypenawar -e "source {{justfile_directory()}}/mypenawar.sql"; $tables = & $cli --host=127.0.0.1 --port={{db_port}} --user=root --batch --skip-column-names -e "SELECT COUNT(*) FROM information_schema.tables WHERE table_schema='mypenawar'"; if ([int]"$tables" -ge 5) { Write-Host "Imported mypenawar.sql — $tables tables" } else { Write-Error "Import failed — mypenawar has $tables tables"; exit 1 } }

# ─── Quality ─────────────────────────────────────────────

# Fast syntax gate (no composer): a parse error in any file breaks the page that
# includes it, so gate on all of them. `test` below runs this sweep plus HTTP checks.
# Syntax-lint every PHP file with `php -l` (recurses; skips .git).
lint: _require-php
    $fail = 0; Get-ChildItem -Path '{{justfile_directory()}}' -Filter *.php -Recurse -File | Where-Object { $_.FullName -notlike '*\.git\*' } | ForEach-Object { $out = & '{{php}}' -l $_.FullName 2>&1; if ($LASTEXITCODE -ne 0) { $fail++; Write-Host ($out -join "`n") -ForegroundColor Red } }; if ($fail -gt 0) { Write-Error "$fail PHP file(s) failed php -l"; exit 1 } else { Write-Host "All PHP files pass php -l" -ForegroundColor Green }

# Test suite (tests/smoke.ps1): lint + secret gates, the static/portal pages against a
# private server on port 8614 (so it never fights `just start`), and the seeded DB flows.
# The suite starts MariaDB itself when it is down (same mechanism as db-start) and stops it
# again if it started it. -RequireDb makes a broken/absent database a FAILURE, never a SKIP.
test: _require-php
    powershell -NoProfile -ExecutionPolicy Bypass -File '{{justfile_directory()}}\tests\smoke.ps1' -RequireDb; exit $LASTEXITCODE

# ─── Tools ───────────────────────────────────────────────

# Launch Claude Code with all permissions — Sonnet (latest)
claudex:
    claude --dangerously-skip-permissions --model sonnet

# Launch Claude Code with all permissions — Opus (latest)
claudeo:
    claude --dangerously-skip-permissions --model opus

# Launch Claude Code with all permissions — Haiku (latest)
claudeh:
    claude --dangerously-skip-permissions --model haiku
