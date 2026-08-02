# Development workflow

> **TL;DR** Edit a page file, refresh the browser (`php -S` has no cache), `just lint`
> and `just test` before every commit. Branch for features, Conventional Commits, no attribution
> footers. Don't retrofit the legacy pages; don't copy their SQL-interpolation or
> plaintext-password patterns into NEW code.

## The day-2 loop

1. `just start` (or `just serve` in a second terminal to watch request logs).
2. Edit the page script — each screen is one root-level `.php`/`.html` file; there is no
   build step, so a browser refresh shows the change immediately.
3. `just lint` — `php -l` over every PHP file — then `just test`, the HTTP smoke suite
   (`tests/smoke.ps1`): the same lint sweep plus the no-DB pages served from a private
   server on port 8614. No composer, no PHPUnit, no CI — these two local gates are the
   automation; treat a failure in either as a blocker. DB-backed behavior still needs a
   manual check against a real MySQL on 3307.
4. Commit with a Conventional Commits message (`feat:`, `fix:`, `docs:` ...) — the
   `/commit` skill automates this. PRs via `/create-pr`, self-review via
   `/pre-pr-review`.

## House rules

| Rule | Why |
| --- | --- |
| Quote space-bearing filenames (`"patient booking.php"`, `"employee profile.php"`, `"monthly report.php"`, `"patient profile.php"`) | Unquoted they split into two shell arguments |
| Never edit `Mail/phpmailer/` | Vendored third-party library (PHPMailer 5.x) |
| Don't suppress errors globally to hide deprecations | The code targets PHP ~8.1; 8.4 deprecation notices on a rendering page are cosmetic |
| Don't "fix" a DB failure by catching/muting `mysqli_sql_exception` | The fix is a running MariaDB on `localhost:3307`; muting hides real errors |
| NEW code: use prepared statements + `password_hash()` | The legacy interpolated-SQL / plaintext-password patterns are documented debt, not a style guide |
| Keep `db_config.php` pointing at `localhost:3307` | Committed contract matching `mypenawar.sql`'s origin (XAMPP MariaDB) |
| Serve only on port 8112 | The assigned port for this repo; `just start` enforces it |

## Sharp edges

- **`/` is not the home page.** `php -S` serves `index.php` (a scratch page) for `/`;
  the real home page is `/index.html`.
- **Mid-page `session_start()`** in several pages prints a "headers already sent" notice
  once error display is on. Pre-existing; only fix it in pages you are already changing.
- **Duplicated DB credentials.** Many pages re-declare `mysqli_connect(...)` inline
  instead of requiring `db_config.php` — a config change must touch all of them.
- **`index2.php` queries a `staff` table** that `mypenawar.sql` never creates — that
  page can never work against the committed schema; it is a dead variant of `login.php`.

## Claude Code

- Skills catalog: [`.claude/skills/README.md`](../../.claude/skills/README.md) —
  `/commit`, `/create-pr`, `/pre-pr-review`, `/lint-check`, `/define-goal`,
  `/claude-transfer`, `/llm-transfer`, `/setup-mcp`, `/test-all-mcp`, `/audit-skills`.
- MCP servers wire via `.mcp.json.stub` → git-ignored `.mcp.json` (seeded by
  `setup.ps1`); health-check with `/test-all-mcp`.
- Project memory lives in `.claude/memory/MEMORY.md`.

## Related docs

| Doc | Why |
| --- | --- |
| [../05-reference/commands.md](../05-reference/commands.md) | Every `just` recipe |
| [../01-overview/architecture.md](../01-overview/architecture.md) | Sessions, tables, write paths |
| [../06-troubleshooting/common-issues.md](../06-troubleshooting/common-issues.md) | Failure modes you'll hit while developing |
