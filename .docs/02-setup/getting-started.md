# Getting started

> **TL;DR** `pwsh ./setup.ps1` (installs Git, PHP 8.4 with `mysqli`, uv/Python, just,
> Claude Code — idempotent), reopen PowerShell, `just start` →
> http://127.0.0.1:8112. Static pages and the login forms work immediately; DB-backed
> pages additionally need MySQL/MariaDB on `localhost:3307` with `mypenawar.sql`
> imported.

## 1. One-time machine setup

From the repo root, in PowerShell (not cmd.exe):

```powershell
pwsh ./setup.ps1
```

What it does, in order — every step is idempotent (`[OK]`-skips when already present):

| Step | Tool | Notes |
| --- | --- | --- |
| 1 | Git | via winget |
| 2 | Claude Code CLI | via npm if npm exists; warns (not fails) otherwise — the app itself never needs Node |
| 3–4 | uv + Python | used by `.claude` tooling (statusline, skill scripts) |
| 5 | VC++ 2015–2022 Redistributable | registry-checked first so re-runs never trigger a UAC prompt |
| 6 | PHP 8.4 | zip from php.net into `%LOCALAPPDATA%\Programs\php-8.4`; writes/patches `php.ini` so **`mysqli` is enabled** |
| 7 | just | via `uv tool install rust-just` |
| 8 | GitHub CLI | for the `/commit` and `/create-pr` skills; run `gh auth login` once |
| 9 | `.mcp.json` | copied from `.mcp.json.stub` (git-ignored; fill the GitHub PAT by hand) |

Then **close and reopen PowerShell** so the User PATH changes land.

## 2. Start the server

```powershell
just start     # background window, http://127.0.0.1:8112
just serve     # same, foreground — watch request logs, Ctrl+C to stop
just stop      # kill only THIS repo's php.exe processes
```

Verify: open http://127.0.0.1:8112/index.html — the clinic home page with navbar and
hero text. (`/` serves `index.php`, which is a legacy PHP output-test scratch page, not
the real home page.)

## 3. Optional: the database (needed for login and every dynamic page)

The app expects MySQL/MariaDB on **`localhost:3307`** (the 2022 XAMPP default this
project was built against), database **`mypenawar`**, user **`root`**, **empty
password** — see `db_config.php`. Without it the static pages and the login/registration
forms still render, but any page that runs SQL fails (see
[../06-troubleshooting/common-issues.md](../06-troubleshooting/common-issues.md)).

With XAMPP: configure MariaDB to listen on 3307 (or it already does if you kept the
project's original setup), then import the dump:

```powershell
mysql -h 127.0.0.1 -P 3307 -u root -e "CREATE DATABASE IF NOT EXISTS mypenawar"
mysql -h 127.0.0.1 -P 3307 -u root mypenawar < mypenawar.sql
```

If your MySQL runs on the standard 3306 instead, change `$hostname` in `db_config.php`
locally — but **do not commit** that change; port 3307 is the committed contract.
Note that `login.php`, `login2.php`, `index2.php`, `appList2.php`, the profile pages,
`patient booking.php`, and `monthly report.php` re-declare the connection inline with
the same credentials, so a committed port change would have to touch all of them —
another reason to run the DB on 3307 instead.

## 4. Sanity checks

```powershell
just lint      # php -l over every PHP file — must pass
just           # list all recipes
```

## Related docs

| Doc | Why |
| --- | --- |
| [../01-overview/project-overview.md](../01-overview/project-overview.md) | What you just installed |
| [../03-development/workflow.md](../03-development/workflow.md) | The day-2 loop |
| [../06-troubleshooting/common-issues.md](../06-troubleshooting/common-issues.md) | When a step above fails |
