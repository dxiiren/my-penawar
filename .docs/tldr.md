# TL;DR — every doc in 30 seconds

One paragraph per document. Read this page, then jump to what you need.

## [01-overview/project-overview.md](01-overview/project-overview.md)

MyPenawar is a 2022 student-project website for "Poliklinik Penawar" (Semenyih,
Selangor): static HTML marketing pages plus page-per-file PHP for login/registration,
appointment booking, booking admin, receipts, monthly reports, and password recovery —
all raw `mysqli` against a MariaDB `mypenawar` database seeded from `mypenawar.sql`.
No framework, no composer, no npm. Ships some dead variants (`index.php` scratch page,
`login2.php`, `index2.php`) kept for history. Runs locally on http://127.0.0.1:8112.

## [01-overview/architecture.md](01-overview/architecture.md)

`php -S` maps URLs straight to root-level files — each PHP page owns its HTML, form
handling, SQL, and session use. Login stores identity in `$_SESSION["user1"]`
(patient IC or staff username); five tables (`booking`, `employee`, `patient`,
`payment`, `service`) hold the data; `db_config.php` targets `localhost:3307` (2022
XAMPP default) and many pages re-declare the same connection inline. Since PHP 8.1
`mysqli` throws, so the legacy `OR DIE` guards are dead code. Frontend is Bootstrap 4 /
jQuery from CDNs.

## [02-setup/getting-started.md](02-setup/getting-started.md)

Three steps on a stock Windows machine: `pwsh ./setup.ps1` (Git, PHP 8.4 with `mysqli`
enabled, uv/Python, just, Claude Code — idempotent), reopen PowerShell, `just start` →
http://127.0.0.1:8112 (home page at `/index.html`). Optionally run MariaDB/MySQL on
`localhost:3307` and import `mypenawar.sql` to light up the dynamic pages — without it
every SQL-running page prints a `mysqli_sql_exception`.

## [03-development/workflow.md](03-development/workflow.md)

Edit the page file, refresh the browser (no build step), `just lint` before every
commit — it's the repo's whole quality gate. Quote the four space-bearing filenames,
never touch vendored `Mail/phpmailer/`, don't mute errors to hide legacy warnings, and
never copy the interpolated-SQL / plaintext-password patterns into new code. Commits
follow Conventional Commits via the `/commit` skill; PRs via `/create-pr`.

## [04-deployment/deployment.md](04-deployment/deployment.md)

Honest state: there is no deployment. No CI/CD, no hosting, no build — local-only via
the PHP dev server. The doc lists what a real deploy would demand (real web server,
env-based config, prepared statements, hashed passwords, TLS/SMTP) precisely because
none of it exists today.

## [05-reference/commands.md](05-reference/commands.md)

The `just` recipe table: `start` (background server on 8112, self-stopping first),
`serve` (foreground), `stop` (kills only this repo's `php.exe`), `lint` (`php -l`
everything), plus `claudex`/`claudeo`/`claudeh` Claude Code launchers. PHP is pinned to
`%LOCALAPPDATA%\Programs\php-8.4\php.exe`; `PORT` env var can override 8112 for a
one-off.

## [05-reference/project-layout.md](05-reference/project-layout.md)

Annotated tree of the flat repo root — which `.php` file is which screen, the four
filenames with spaces, dead variants, `mypenawar.sql`, vendored `Mail/phpmailer/` —
plus a "where to make which change" table mapping tasks to files.

## [06-troubleshooting/common-issues.md](06-troubleshooting/common-issues.md)

Real failure modes with observed symptoms: the two shapes of the no-database
`mysqli_sql_exception` (error-only 200 body vs rendered-page-then-error), `/` showing
the scratch page instead of the site, the `_require-php` guard message, a missing
`mysqli` extension, cosmetic PHP 8.4 warnings, `just stop` reporting 0 processes, and
port-8112 conflicts.

## [07-faq/faq.md](07-faq/faq.md)

Quick answers: why `/` isn't the home page, when you actually need the database, why
port 3307, where test accounts live (seeded in the dump, plaintext), why duplicate
login pages exist, why filenames have spaces, why PHPMailer stays untouched, and that
`just lint` is the only automated gate.
