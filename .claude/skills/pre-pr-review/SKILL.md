---
name: pre-pr-review
description: Use when the developer says 'pre-pr review', 'review my branch', 'audit my work', or 'self review' — self-reviews the current branch's diff against a plain-PHP / mysqli / session / security checklist before opening a PR, then saves a report to .claude/workspace/reports/pr/.
model: opus
---

# Pre-PR Review (Self-Audit)

Self-review your feature-branch diff **before** opening a PR. This is a plain-PHP
(no framework) healthcare-clinic site: page-per-file `*.php` at the repo root, raw
`mysqli_*` calls against a MySQL/MariaDB `mypenawar` database, `$_SESSION` for login
state, Bootstrap/jQuery from CDNs. The goal is to catch SQL, session, and security
problems in NEW code early — not to retrofit a framework onto 2022-era pages.

## Trigger

- `"pre-pr review"` / `"self review"`
- `"review my branch"` / `"review my work"` / `"review my code"`
- `"audit my work"` / `"audit my branch"`

## Do NOT flag (owned elsewhere)

- **Syntax** — `php -l` owns it (`just lint`). Run it; don't hand-review it.
- **Pre-existing patterns** the developer copied from the codebase — the legacy pages
  interpolate `$_POST` into SQL and store plaintext passwords; that debt is not this
  branch's problem unless the branch touches those exact lines. Hold NEW code to the
  checklist; report untouched legacy debt at most once as a note, not per-line.
- **`Mail/phpmailer/`** — vendored third-party library; never review or "fix" it.

## Step 1 — Branch & base

```bash
git branch --show-current
```

If on `main`: **STOP** — "You're on `main`; switch to your feature branch first."

```bash
git fetch origin main
git diff origin/main...HEAD --name-only
```

If no files changed: **STOP** — "No changes vs `main`."

Scope the review to reviewable source: `*.php` and `*.html` at the repo root,
`style.css`, `mypenawar.sql`. **Exclude** `Mail/phpmailer/`, `image/`, and
`.claude/`. If only excluded files changed: **STOP** — "No reviewable source
changed."

Report: "Branch `{name}` changed {N} source files ({php} .php, {html} .html). Running review."

## Step 2 — Fetch the diff

```bash
git diff origin/main...HEAD -- '*.php' '*.html' 'style.css' 'mypenawar.sql'
```

For context-dependent checks (session state, redirect flow, which page includes
which), read the **full file**, not just the hunk — these pages mix HTML, CSS, JS,
and PHP in one file, and the PHP block is often at the bottom. If the diff exceeds
~4000 lines, prioritise the highest-change files and note "focused review on largest
files".

## Step 3 — Run the checklist

Verify each finding against the actual code before reporting it (grep how existing
code does the same thing; the bar for NEW code is the checklist, not the legacy page).

| #   | Check                        | Label      | What to look for                                                                                                                                                                                              |
| --- | ---------------------------- | ---------- | -------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------------- |
| 1   | **SQL injection**            | issue      | NEW queries interpolating `$_POST`/`$_GET`/`$_SESSION` directly into SQL strings. New code must at minimum use `mysqli_real_escape_string` (the `code.php` pattern) — prepared statements preferred.            |
| 2   | **XSS / output escaping**    | issue      | NEW `echo` of user-supplied values into HTML/JS without `htmlspecialchars` — especially the `echo "<script>alert('$var')"` pattern (an attacker-controlled `$var` breaks out of the string).                    |
| 3   | **Auth / session gating**    | issue      | A NEW staff/patient page that doesn't check `$_SESSION` before showing data (house state: `$_SESSION["user1"]` set by `login.php`); `session_start()` missing or called after output on a page that reads it.   |
| 4   | **Plaintext credentials**    | issue      | NEW code storing or comparing passwords in plaintext (legacy does — new auth code must use `password_hash`/`password_verify`); any hardcoded SMTP/DB/API credential added to a committed file.                  |
| 5   | **DB connection handling**   | issue      | A NEW page opening its own inline `mysqli_connect` instead of `require 'db_config.php'`; connecting before deciding the page needs the DB at all (static content should not 500 when MySQL is down).            |
| 6   | **Schema drift**             | issue      | SQL referencing tables/columns not in `mypenawar.sql` (the only tables are `booking`, `employee`, `patient`, `payment`, `service` — e.g. legacy `index2.php` queries a nonexistent `staff` table; don't add more). |
| 7   | **Redirect flow**            | issue      | NEW navigation via `echo '<meta http-equiv="refresh" ...>'` after partial output where a `header('Location: ...')` before output would work; a redirect target file that doesn't exist (filenames have spaces — check exactly). |
| 8   | **PHP 8.4 compatibility**    | issue      | NEW code using removed/deprecated constructs (`$str{0}` offsets, implicit-nullable params, undefined constants like bare `f1`); relying on `mysqli_connect` returning `false` (PHP >= 8.1 throws `mysqli_sql_exception` instead). |
| 9   | **Secrets / config**         | issue      | Committing `.mcp.json`, tokens, or a real SMTP password; copying the legacy hardcoded Elastic Email credential into new code (it must not spread).                                                              |
| 10  | **No debug leftovers**       | issue      | `var_dump` / `print_r` / `console.log` / commented-out dead blocks / `TODO` without a follow-up.                                                                                                                |
| 11  | **File placement / naming**  | suggestion | New page filenames with spaces (legacy has `employee profile.php` — new files should use hyphens/underscores; spaces break naive links and tooling); duplicate near-copies of an existing page (the `login2.php`/`index2.php` anti-pattern). |
| 12  | **Shared markup drift**      | suggestion | Copy-pasting the navbar/footer block into a new page with edits that desync it from the other pages; new inline `<style>` duplicating rules already in `style.css`.                                             |
| 13  | **Naming / conventions**     | nitpick    | Inconsistent variable naming vs the touched file; mixed tabs/spaces beyond the file's existing style.                                                                                                          |

## Step 4 — Run the quality suite

```powershell
just lint
just test
```

Both must be green: the `php -l` sweep and the HTTP smoke suite (`tests/smoke.ps1` —
no-DB pages against a private server on port 8614). A failure in either is an **issue**
(blocking) — paste the failing output line. The smoke suite does not cover DB-backed
pages; if the branch changed page behavior, also load the page on
`http://127.0.0.1:8112` (`just start`) and note what you saw. DB-backed pages need
MySQL on `localhost:3307` — without it, verify the page still renders its static shell
(see `.docs/06-troubleshooting/common-issues.md`).

## Step 5 — Finding labels & caps

- **issue** (blocking) — fix before opening the PR.
- **suggestion** (non-blocking) — recommended.
- **nitpick** (non-blocking) — minor/optional.

Every finding must carry: the label, the `file:line`, and **WHY** it matters (not just what).
Issues: uncapped. Suggestions + nitpicks: cap at 15 total; note "{X} more non-blocking findings
omitted" if over.

## Step 6 — Present

```
## Pre-PR Review: {branch}
Branch: {branch} -> main   |   Files: {N} ({php} .php, {html} .html)
Quality suite: {php -l pass/fail} · {manual page check, if run}

### Issues (fix before PR)
1. [path:line] Finding — why it matters

### Suggestions
2. [path:line] Finding

### Nitpicks
3. [path:line] Finding

---
{Total} findings: {issues} issues, {suggestions} suggestions, {nitpicks} nitpicks
```

Zero findings → "No issues found — branch looks clean. Ready to open the PR."

## Step 7 — Save the report

Path: `.claude/workspace/reports/pr/{branch}-{YYYY-MM-DD}.md` (replace `/` in the branch name
with `-`; overwrite on a same-day re-run; create the folder if missing). Frontmatter then the
same body as the terminal output:

```yaml
---
branch: { branch }
base: main
date: { YYYY-MM-DD }
files_changed: { N }
issues: { count }
suggestions: { count }
nitpicks: { count }
---
```

Confirm: "Report saved to `{path}`".

## Tone

Self-improvement, not a verdict from a lead. "Consider extracting…", not "You must fix…". Never
directive, never judgmental.

## Evolution Log

- Ported from book-review's pre-pr-review (Laravel/Eloquent/Blade checklist) for this
  plain-PHP repo: checklist rebuilt around raw mysqli + `$_SESSION` + page-per-file
  realities (SQL injection, XSS-in-alert, session gating, plaintext passwords, schema
  drift vs `mypenawar.sql`, PHP 8.4 breakage); quality suite is `just lint` (php -l)
  since there is no Pint/PHPUnit.
