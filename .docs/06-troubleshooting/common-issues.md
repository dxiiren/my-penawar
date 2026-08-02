# Common issues

> **TL;DR** Almost everything you'll hit locally is one of: MariaDB not started
> (`just db-start` — DB pages print `mysqli_sql_exception` without it), a stale/missing
> pinned PHP or MariaDB (`just` guard tells you to run `setup.ps1`), PHP 8.1+ exception
> behavior making the legacy `OR DIE` guards dead code, or cosmetic warnings from
> 2022-era code on PHP 8.4.

## Page shows `Fatal error: Uncaught mysqli_sql_exception: No connection could be made ...`

**Symptom (observed on every DB page without a database running):**

```
Fatal error: Uncaught mysqli_sql_exception: No connection could be made because the
target machine actively refused it in ...\db_config.php:7
```

- Pages that `require 'db_config.php'` at the top — `patient.php`, `patientedit.php`,
  `receipt.php`, `get_data.php`, `code.php`, `reset_psw.php` — return HTTP **200**
  whose entire body is this error (with `display_errors` on, PHP prints the error
  instead of sending 500).
- Pages that connect mid-page — `login.php`, `login2.php`, `index2.php`,
  `patient booking.php`, `monthly report.php`, `recover_psw.php` (on submit) — render
  their full HTML first, then print the same exception at the bottom. (The
  auth-guarded portal pages — `patient profile.php`, `employee profile.php`,
  `appList2.php` — only reach their query when you're logged in, so anonymous hits
  now 302 to `login.php` instead of erroring.)

**Cause:** MariaDB isn't running, or the `mypenawar` database is missing. **Fix:**

```powershell
just db-start   # portable MariaDB from setup.ps1, on 127.0.0.1:3307
just db-seed    # only if the mypenawar database itself is missing
```

Do **not** "fix" this by wrapping the connect in try/catch or lowering
`error_reporting` — since PHP 8.1 `mysqli` throws on failure (the legacy
`OR DIE("Connection Failed")` guards never run), and hiding the exception just makes
the pages fail silently.

## `/` shows "PHP Output Test", not the clinic site

Expected. `php -S` serves `index.php` for `/`, and this repo's `index.php` is a legacy
PHP scratch page. The real home page is **`/index.html`**. (The scratch page also
prints one `Warning: Undefined variable $variableName` — a 2022 line that intended to
show the literal text; harmless.)

## `error: PHP 8.4 not found at C:\Users\...\Programs\php-8.4\php.exe`

Any `just` recipe fails with this if the pinned PHP is missing. Run
`pwsh ./setup.ps1`, then close and reopen PowerShell. The recipes deliberately use the
absolute pinned path so they work even in shells opened before setup updated PATH.

## `Call to undefined function mysqli_connect()`

The pinned PHP's `php.ini` lost the `mysqli` extension line (e.g. the PHP folder was
reinstalled by hand). Re-run `pwsh ./setup.ps1` — it idempotently appends
`extension=mysqli` to an existing `php.ini` when absent (and creates the ini from the
development template when the folder is fresh).

## Warnings / deprecation notices above otherwise-fine pages

The code targets PHP ~8.1 (XAMPP 2022) and runs here on PHP 8.4 with the development
php.ini (`display_errors=On`). Deprecation notices on a page that still renders are
cosmetic legacy behavior — fix them only in pages you are already changing, never by
muting errors globally.

**FIXED — unauthenticated portal hits.** The worst instance of this class is gone:
`patient profile.php`, `employee profile.php` and `appList2.php` used to call
`session_start()` mid-page ("headers already sent") and then query with an undefined
`$id` ("Undefined variable") when hit anonymously. Each now has a top-of-file auth
guard — `session_start()` before any output, then `header("Location: login.php");
exit;` for anonymous visitors — so those hits are a clean, warning-free 302 (the suite
asserts it for all three pages). If a portal page ever warns like that again, its guard
was removed or a new page skipped the pattern.

**FIXED — a rejected login used to hand out a session.** `login.php` wrote
`$_SESSION["user1"] = $_POST["id"]` *before* running the credential query, so any POST
with a bogus username satisfied the portal guards above — the guards existed but proved
nothing. The assignment now happens only inside the verified-match branch of each of the
patient and staff paths, and the suite pins it (bogus login → the guard still 302s).
`login2.php` (a dead 2022 variant, not linked from the app) still carries the original
pattern; do not copy it.

## Page renders unstyled / hero animation missing

Tailwind (Play CDN), Google Fonts, Font Awesome, typed.js, and jQuery/jQuery UI load
from CDNs. No internet → bare HTML. Expected.

## `just stop` says `Stopped 0 project php.exe process(es)`

Nothing was running for THIS repo — that's the normal answer, not an error. The recipe
only kills `php.exe` whose command line contains this repo's path, so other projects'
servers never count.

## Port 8112 already in use

A previous server from this repo is still up — `just start` runs `stop` first, so
plain `just start` normally clears it. If something else owns 8112:
`Get-NetTCPConnection -LocalPort 8112 | Select OwningProcess` and stop that process, or
serve once on another port with `$env:PORT=8113; just start` (assigned port stays 8112).

## Related docs

| Doc | Why |
| --- | --- |
| [../02-setup/getting-started.md](../02-setup/getting-started.md) | The DB import these fixes point at |
| [../07-faq/faq.md](../07-faq/faq.md) | Quick answers that aren't failures |
| [../01-overview/architecture.md](../01-overview/architecture.md) | Why the two DB failure shapes differ |
