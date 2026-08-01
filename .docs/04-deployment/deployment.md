# Deployment

> **TL;DR** There is no deployment. No CI/CD, no hosting target, no build pipeline —
> the app runs locally via the PHP built-in dev server (`just start`). This page exists
> to say so honestly and to sketch what a real deploy would need.

## Current state

| Aspect | State |
| --- | --- |
| Hosting | None — local only, `php -S 127.0.0.1:8112 -t .` |
| CI/CD | None — no workflow files; `just lint` runs locally |
| Build step | None — no composer, no npm, files are served as-is |
| Database | Developer-local MySQL/MariaDB on `localhost:3307`, imported by hand from `mypenawar.sql` |
| Secrets | `db_config.php` is committed with root/empty-password credentials — fine for a local XAMPP-era project, a blocker for any real deploy |

## If this ever had to be deployed (checklist, not a plan)

1. Real web server (Apache/nginx + PHP-FPM) — `php -S` is single-threaded and
   development-only.
2. Parameterise `db_config.php` from environment variables and remove the inline
   credential copies in the login/profile/booking/report pages.
3. Prepared statements everywhere and `password_hash()`/`password_verify()` for stored
   passwords — the current interpolated SQL and plaintext passwords are not shippable.
4. Move `session_start()` calls before any output; disable `display_errors`.
5. TLS, and SMTP credentials for the PHPMailer password-recovery flow.

## Related docs

| Doc | Why |
| --- | --- |
| [../02-setup/getting-started.md](../02-setup/getting-started.md) | The only "deployment" that exists: your machine |
| [../01-overview/architecture.md](../01-overview/architecture.md) | Why the checklist above looks like that |
