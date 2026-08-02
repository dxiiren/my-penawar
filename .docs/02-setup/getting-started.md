# Getting started

> **TL;DR** `pwsh ./setup.ps1` (installs Git, PHP 8.4 with `mysqli`, portable MariaDB
> 11.4 with `mypenawar.sql` imported, uv/Python, just, Claude Code — idempotent),
> reopen PowerShell, `just db-start` + `just start` → http://127.0.0.1:8112 with every
> page working. Skip `db-start` and you still get the static pages + login forms.

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
| 7 | MariaDB 11.4 (portable) | pinned zip from archive.mariadb.org into `%LOCALAPPDATA%\Programs\mariadb` (no service, no admin); initialises the data dir (root, **empty password**) and **imports `mypenawar.sql`** (skipped when the 5 tables already exist) via a temporary server on 3307 |
| 8 | just | via `uv tool install rust-just` |
| 9 | GitHub CLI | for the `/commit` and `/create-pr` skills; run `gh auth login` once |
| 10 | `.mcp.json` | copied from `.mcp.json.stub` (git-ignored; fill the GitHub PAT by hand) |

Then **close and reopen PowerShell** so the User PATH changes land.

## 2. Start the database, then the server

```powershell
just db-start  # portable MariaDB on 127.0.0.1:3307 (background)
just start     # PHP dev server, background window, http://127.0.0.1:8112
just serve     # same server, foreground — watch request logs, Ctrl+C to stop
just stop      # kill only THIS repo's php.exe processes
just db-stop   # stop MariaDB (graceful shutdown)
```

`just start` deliberately does **not** auto-start the database — the static site works
without it, and the DB lifecycle stays explicit. Verify: open
http://127.0.0.1:8112/index.html — the clinic home page with navbar and hero text.
(`/` serves `index.php`, which is a legacy PHP output-test scratch page, not the real
home page.) With the DB up, log in with a seeded account (see the `patient`/`employee`
INSERT rows in `mypenawar.sql`) and browse the portal pages.

## 3. The database, if you need to touch it directly

`db_config.php` pins the contract: host `localhost`, port **3307** (the 2022 XAMPP
default this project was built against), database `mypenawar`, user `root`, **empty
password**. setup.ps1's portable MariaDB matches all of it, and `just db-seed`
re-imports `mypenawar.sql` if the database ever goes missing. The CLI client is at
`%LOCALAPPDATA%\Programs\mariadb\bin\mariadb.exe`:

```powershell
& "$env:LOCALAPPDATA\Programs\mariadb\bin\mariadb.exe" -h 127.0.0.1 -P 3307 -u root mypenawar
```

If you'd rather point the app at your own MySQL on the standard 3306, change
`$hostname` in `db_config.php` locally — but **do not commit** that change; port 3307
is the committed contract. Note that `login.php`, `login2.php`, `index2.php`,
`appList2.php`, the profile pages, `patient booking.php`, and `monthly report.php`
re-declare the connection inline with the same credentials, so a committed port change
would have to touch all of them — another reason to keep the DB on 3307.

## 4. Sanity checks

```powershell
just lint      # php -l over every PHP file — must pass
just test      # 20-check suite; starts MariaDB itself, and fails if the DB is broken
just           # list all recipes
```

## Related docs

| Doc | Why |
| --- | --- |
| [../01-overview/project-overview.md](../01-overview/project-overview.md) | What you just installed |
| [../03-development/workflow.md](../03-development/workflow.md) | The day-2 loop |
| [../06-troubleshooting/common-issues.md](../06-troubleshooting/common-issues.md) | When a step above fails |
