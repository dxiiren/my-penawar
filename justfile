# MyPenawar justfile — development recipes

set shell := ["powershell.exe", "-NoProfile", "-Command"]

# PHP extracted by setup.ps1 into the user's LOCALAPPDATA\Programs\php-8.4.
# Absolute path keeps recipes working even in PowerShell sessions opened before
# setup.ps1 updated the User PATH environment variable.
php  := env_var('LOCALAPPDATA') + '\Programs\php-8.4\php.exe'
port := env_var_or_default('PORT', '8112')

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

# ─── Quality ─────────────────────────────────────────────

# The only automated quality gate this repo has (no composer, no test suite):
# a parse error in any file breaks the page that includes it, so gate on all of them.
# Syntax-lint every PHP file with `php -l` (recurses; skips .git).
lint: _require-php
    $fail = 0; Get-ChildItem -Path '{{justfile_directory()}}' -Filter *.php -Recurse -File | Where-Object { $_.FullName -notlike '*\.git\*' } | ForEach-Object { $out = & '{{php}}' -l $_.FullName 2>&1; if ($LASTEXITCODE -ne 0) { $fail++; Write-Host ($out -join "`n") -ForegroundColor Red } }; if ($fail -gt 0) { Write-Error "$fail PHP file(s) failed php -l"; exit 1 } else { Write-Host "All PHP files pass php -l" -ForegroundColor Green }

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
