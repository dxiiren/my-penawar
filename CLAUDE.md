# CLAUDE.md — my-penawar

> Human-facing developer docs live in [`.docs/`](./.docs/README.md) — start at
> [`.docs/tldr.md`](./.docs/tldr.md). Keep them in sync when changing behavior they document.

## Project: MyPenawar

A 2022-era plain-PHP + HTML website for "Poliklinik Penawar", a fictional polyclinic in
Semenyih, Selangor. Public pages (home, about us, contact, members) are static HTML; the
dynamic pages (patient/staff login and registration, appointment booking, appointment
admin, monthly report, receipts, password recovery) are page-per-file PHP scripts using
raw `mysqli` against a MySQL/MariaDB `mypenawar` database (schema + seed data committed
in `mypenawar.sql`). No framework, no composer, no npm.

- **Repo:** GitHub — `github.com/dxiiren/my-penawar`
- **Runs locally only** — no CI/CD, no deployment target. `just start` serves on
  `http://127.0.0.1:8112`.

### Tech Stack Quick Reference

| Layer | Technology | Key details |
| --- | --- | --- |
| Language | **PHP 8.4** (plain, no framework) | Page-per-file scripts at the repo root; code written for PHP ~8.1/XAMPP in 2022, so expect deprecation warnings on 8.4 |
| Database | MySQL/MariaDB via `mysqli` | `db_config.php` connects to `localhost:3307`, db `mypenawar`, user `root`, empty password; schema + seed rows in `mypenawar.sql` (tables: `booking`, `employee`, `patient`, `payment`, `service`) |
| Sessions | native `$_SESSION` | `login.php` stores the logged-in username as `$_SESSION["user1"]`; several pages call `session_start()` mid-page (warns after output) |
| Frontend | HTML + `style.css` + inline `<style>` | Bootstrap 4, jQuery 3, Font Awesome, typed.js all loaded from CDNs — internet required for full styling |
| Mail | Vendored PHPMailer 5.x in `Mail/phpmailer/` + SMTP.js | Used by the password-recovery flow; never edit the vendored library |
| Serving | PHP built-in dev server | `just start` → `php -S 127.0.0.1:8112 -t .` |
| Quality | `php -l` + HTTP smoke suite | `just lint` syntax-lints every PHP file; `just test` runs `tests/smoke.ps1` (no-DB pages on a private port-8614 server); no CI |
| Task runner | `just` | `justfile` wraps start/serve/stop/lint/test; PHP pinned to `%LOCALAPPDATA%\Programs\php-8.4` |

### Project Structure

```
my-penawar/
  index.html              # real home page (navbar, typed.js hero, footer)
  index.php               # PHP output-test scratch page (served for "/" by php -S)
  aboutus.html, contact.html, member.html, demo.html, notification.html, receipt.html
  login.php               # patient/staff login + patient registration (DB at bottom of file)
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
  message.php             # session flash-message partial (included by patient.php etc.)
  db_config.php           # mysqli connection (localhost:3307 / mypenawar / root)
  mypenawar.sql           # phpMyAdmin dump: schema + seed data for all 5 tables
  Mail/phpmailer/         # vendored PHPMailer 5.x (third-party, do not edit)
  image/                  # logos, staff photos, background
  style.css               # shared stylesheet
  justfile, setup.ps1     # dev recipes + one-time machine setup
  tests/                  # smoke.ps1 — HTTP smoke-test suite, no-DB scope (`just test`)
  .docs/                  # numbered documentation set
  .claude/                # skills, hooks, settings, memory
```

## Git Commits

- **Conventional Commits** (`feat:`, `fix:`, `chore:`, `docs:` ...).
- **NEVER** add `Co-Authored-By` lines or "Generated with Claude Code" / session-link footers to
  **any** outward artifact — commit messages, PR descriptions, or issue comments.
- Commit author email for this repo is `mohdakmal875@gmail.com` (set repo-locally).
- Only stage and commit files relevant to the change. **Never auto-commit** after a fix — the
  developer says "commit" first.

## Local Development

- One-time machine setup: `pwsh ./setup.ps1` (idempotent — installs Git, PHP 8.4 (with
  mysqli enabled), uv/Python, just, the Claude Code CLI). Then `just start`.
- All day-2 commands are `just` recipes — run `just` to list them. Never invent an alternative
  command for something a recipe already covers.
- `just stop` kills only THIS repo's server processes (matched by repo path on the command
  line) — safe to run while other projects are serving.
- **The DB pages need MySQL/MariaDB on `localhost:3307`** with the `mypenawar` database
  imported from `mypenawar.sql`. Without it: static pages and the login/recover forms still
  render; pages that `require 'db_config.php'` at the top (`patient.php`, `patientedit.php`,
  `receipt.php`, `get_data.php`, `code.php`, `reset_psw.php`) return HTTP **200** whose
  entire body is the uncaught `mysqli_sql_exception` fatal error (the dev php.ini has
  `display_errors` on, so PHP prints the error instead of sending 500); pages that connect
  mid-page (`login.php`, profile/booking/report pages) render their HTML then print the
  same exception at the bottom.
- Since PHP 8.1 `mysqli` **throws** on connection/query failure — the legacy
  `OR DIE("Connection Failed")` guards never run. Don't "fix" a DB page by muting the
  exception; the fix is a running MySQL server (or a real try/catch if behavior must change).
- Several page filenames contain spaces (`employee profile.php`, `patient booking.php`,
  `monthly report.php`, `patient profile.php`) — always quote them in shell commands.
- `Mail/phpmailer/` is a vendored third-party library — never edit or lint-fix it.
- CDN assets (Bootstrap/jQuery/Font Awesome/typed.js) need internet; offline, pages render
  unstyled — expected, not a bug.

## Project Skills

Development skills live in `.claude/skills/` — check `.claude/skills/README.md` for the catalog
and **follow the relevant skill before writing code**. Notables: `/commit`, `/create-pr`,
`/pre-pr-review`, `/lint-check`, `/claude-transfer`, `/llm-transfer`, `/define-goal`,
`/setup-mcp`, `/test-all-mcp`, `/audit-skills`.

## MCP Servers

Wired via the committed-stub + git-ignored-secret pattern: `.mcp.json.stub` (committed,
placeholders) → `.mcp.json` (git-ignored, real — seeded by `setup.ps1`). Turnkey: `context7`
(library docs — call `resolve-library-id` then `query-docs` instead of recalling APIs),
`playwright` (drive a real browser). Per-dev: `github` (fill the PAT in `.mcp.json`).
Health check: `/test-all-mcp`. Fall back to native tools silently if a server is unavailable.

## Memory

Lightweight, single-developer, file-based project memory at `.claude/memory/`:

- **`MEMORY.md`** is the index (one line per memory: `- [Title](file.md) — hook`), loaded each
  session.
- Each memory is **one fact in its own `*.md` file** with frontmatter (`name`, `description`,
  `metadata.type` = `reference` | `feedback` | `project`). Read the fact file on demand when its
  index hook is relevant.
- After writing a fact file, add its one-line pointer to `MEMORY.md`. Update rather than
  duplicate; delete a memory that turns out wrong. Don't store what the repo already records.
