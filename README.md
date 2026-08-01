# MyPenawar

A 2022-era plain-PHP + HTML website for "Poliklinik Penawar", a polyclinic in Semenyih,
Selangor. Public pages (home, about us, contact, members) are static HTML; the dynamic
pages — patient/staff login and registration, appointment booking, appointment admin,
monthly reports, receipts, password recovery — are page-per-file PHP scripts using raw
`mysqli` against a MySQL/MariaDB `mypenawar` database (schema + seed data committed in
`mypenawar.sql`). No framework, no composer, no npm.

> **New developer? Start with [`.docs/tldr.md`](.docs/tldr.md)** — every doc summarised on one
> page. The full guide lives in [`.docs/`](.docs/README.md).

## Prerequisites

| Tool | Version | Installed by |
| --- | --- | --- |
| PowerShell + winget | Windows 10/11 stock | — (the only true prerequisites) |
| Git | any recent | `setup.ps1` |
| PHP | 8.4 (with `mysqli` enabled) | `setup.ps1` |
| uv + Python | latest | `setup.ps1` (used by `.claude` tooling) |
| MySQL/MariaDB on `localhost:3307` | 10.4+ | **you, manually** — only needed for DB-backed pages ([see below](#pages-that-touch-the-database-fail)) |
| just | any recent | `setup.ps1` |
| Claude Code CLI | latest | `setup.ps1` (optional, for AI-assisted dev) |

## Quick start

```powershell
# 1. One-time machine setup (idempotent — safe to re-run)
pwsh ./setup.ps1

# 2. Close and reopen PowerShell so PATH updates land

# 3. Start the dev server
just start
```

The app is now at **http://127.0.0.1:8112**. Stop it with `just stop`.

Static pages and the login/registration forms render without a database. To make the
DB-backed pages work, run MySQL/MariaDB on `localhost:3307` (XAMPP default in this
project's era) and import the schema:

```powershell
mysql -h 127.0.0.1 -P 3307 -u root -e "CREATE DATABASE IF NOT EXISTS mypenawar"
mysql -h 127.0.0.1 -P 3307 -u root mypenawar < mypenawar.sql
```

Connection settings live in `db_config.php` (`localhost:3307`, db `mypenawar`, user
`root`, empty password).

## Commands

Run `just` with no arguments to list every recipe. The ones you'll use daily:

| Command | What it does |
| --- | --- |
| `just start` | Start the PHP dev server on http://127.0.0.1:8112 (background window) |
| `just serve` | Same server in the foreground — watch request logs, Ctrl+C to stop |
| `just stop` | Kill only THIS repo's `php.exe` processes |
| `just lint` | `php -l` syntax-check every PHP file (the repo's only quality gate) |
| `just claudex` | Launch Claude Code (Sonnet, all permissions) |

## Troubleshooting

### Pages that touch the database fail

No MySQL/MariaDB is listening on `localhost:3307`, or the `mypenawar` database is not
imported. Pages that `require 'db_config.php'` at the top (`patient.php`,
`patientedit.php`, `receipt.php`, `get_data.php`, `code.php`, `reset_psw.php`) return
HTTP 200 whose entire body is an uncaught `mysqli_sql_exception` fatal error
(`display_errors` is on, so PHP prints the error instead of sending 500); pages that
connect mid-page (`login.php`, the profile/booking/report pages) render their HTML and
then print the same exception at the bottom. Fix: start
MySQL/MariaDB on port 3307 and import `mypenawar.sql` (see Quick start). Since PHP 8.1
`mysqli` throws on failure, so the legacy `OR DIE("Connection Failed")` guards never run.

### `PHP 8.4 not found at ...\php-8.4\php.exe`

`just` recipes use a pinned PHP at `%LOCALAPPDATA%\Programs\php-8.4\php.exe`. Run
`pwsh ./setup.ps1` to install it, then reopen PowerShell.

### Deprecation warnings printed above pages

Expected. The code was written for PHP ~8.1 (XAMPP, 2022) and runs here on PHP 8.4 with
`display_errors` on (development php.ini). Warnings/deprecations on an otherwise-rendering
page are cosmetic — don't "fix" them by suppressing errors globally.

### Page renders without styling

Bootstrap, jQuery, Font Awesome, and typed.js load from CDNs — the site needs internet
for full styling. Offline, pages render unstyled HTML. Expected, not a bug.

More in [`.docs/06-troubleshooting/common-issues.md`](.docs/06-troubleshooting/common-issues.md).

## Project layout

```
my-penawar/
  index.html              # real home page (navbar, typed.js hero, footer)
  index.php               # PHP output-test scratch page (served for "/" by php -S)
  aboutus.html, contact.html, member.html, demo.html, notification.html, receipt.html
  login.php               # patient/staff login + patient registration
  login2.php, index2.php  # older login variants (index2 queries a nonexistent `staff` table)
  patient profile.php     # patient self-profile view/update    (filename has a space)
  patient booking.php     # appointment booking form + slot check (space)
  patient.php             # staff: appointment list admin
  patientedit.php         # staff: edit one appointment (posts to code.php)
  code.php                # booking delete/update + payment insert handler
  get_data.php            # AJAX patient lookup by IC
  appList2.php            # patient appointment history
  employee profile.php    # staff self-profile view/update (space)
  monthly report.php      # staff monthly bookings/payments report (space)
  receipt.php             # payment receipt
  recover_psw.php         # email a reset link (PHPMailer)  ·  reset_psw.php — set new password
  message.php             # session flash-message partial
  db_config.php           # mysqli connection (localhost:3307 / mypenawar / root)
  mypenawar.sql           # phpMyAdmin dump: schema + seed data (booking, employee, patient, payment, service)
  Mail/phpmailer/         # vendored PHPMailer 5.x (third-party, do not edit)
  image/                  # logos, staff photos, background
  style.css               # shared stylesheet
  justfile, setup.ps1     # dev recipes + one-time machine setup
  .docs/                  # numbered documentation set
  .claude/                # skills, hooks, settings, memory
```
