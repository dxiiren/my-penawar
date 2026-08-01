---
name: lint-check
description: Use when the developer says 'lint check', 'run lint', 'check lint', 'run the quality suite', or 'lint everything' — runs the quality checks available to this plain-PHP repo (php -l syntax lint via `just lint`, leftover-placeholder grep, debug-leftover grep) and reports pass/fail per layer. No Pint/PHPUnit/ESLint here — there is no composer or npm toolchain.
model: sonnet
---

# lint-check — Plain-PHP quality checks (php -l · placeholders · leftovers)

This repo has **no composer or npm toolchain** — no Pint, no PHPUnit, no ESLint. The
quality suite is therefore three lightweight layers that need only what `setup.ps1`
installs (PHP 8.4, grep). There is no CI — this suite is the whole quality gate; run
it before every commit/PR. Run each layer independently so one failure doesn't hide
the others.

## Trigger

When the developer says any of: "lint check", "run lint", "check lint",
"run the quality suite", "lint everything".

---

## What to Do

### 1 — PHP syntax lint (`php -l`)

A parse error in any file 500s every request that includes it, so gate on all of them:

```powershell
just lint          # php -l over every *.php in the repo (recurses; skips .git)
```

Pass = exit 0, `All PHP files pass php -l`. On FAIL the recipe prints each
`PHP Parse error: ... in <file> on line N` — fix the file at the reported line by
hand, then re-run. There is no auto-fix for syntax errors.

Note `php -l` is a **parse** check only — it does not catch runtime errors like an
undefined function (e.g. `mysqli_connect()` with the extension disabled) or a failed
DB connection. Those surface only when the page is requested (see layer notes below).

### 2 — Leftover template placeholders

The onboarding kit stamps files from templates whose fill-in tokens are delimited by
doubled at-signs. The `@[@]` character class below matches that delimiter in files
without this skill file matching its own check:

```bash
grep -rn "@[@]" --include="*.php" --include="*.html" --include="*.md" --include="*.ps1" --include="justfile" .
```

Pass = **zero hits** (grep exits 1). Any hit is an unfilled kit placeholder token —
fix it at the source.

### 3 — Debug / draft leftovers

```bash
grep -rn "var_dump\|print_r(\|console\.log\|debugger\|TODO\|FIXME\|lorem ipsum" --include="*.php" --include="*.html" . | grep -v "Mail/phpmailer"
```

Pass = zero hits (grep exits 1). `Mail/phpmailer/` is excluded — it is a vendored
third-party library (legacy PHPMailer 5.x), not this repo's code. A hit elsewhere is
not automatically fatal — judge it: a deliberate TODO with a follow-up is fine; a
stray `var_dump` in a page is not.

---

## Reporting back

Report a per-layer table, then an overall verdict:

```
LAYER         TOOL                STATUS
syntax        just lint (php -l)  PASS | FAIL (N files)
placeholders  grep "@[@]"         PASS | FAIL (N hits)
leftovers     grep debug/TODO     PASS | FAIL (N hits, judged)
OVERALL: PASS | FAIL
```

- **syntax** failures are fixed by hand at the reported `file:line` — never by
  deleting the file or excluding it from the recipe.
- **placeholders** failures mean an unfilled template token from the onboarding kit —
  fill it with the real value.
- **leftovers** hits require judgment — report each with its line and a verdict.

---

## Notes

- Run from the **repo root** — `just lint` resolves PHP by absolute path
  (`%LOCALAPPDATA%\Programs\php-8.4\php.exe`), so it works even in stale shells.
- Do NOT introduce composer/Pint/PHPUnit or an npm toolchain just to lint this
  2022-era plain-PHP site — `php -l` + grep is deliberate minimalism for this repo.
- Passing lint does NOT mean the DB pages work: pages that hit MySQL through
  `db_config.php` need a MariaDB/MySQL server on `localhost:3307` (see
  `.docs/06-troubleshooting/common-issues.md`). Verify behavior changes by reloading
  `http://127.0.0.1:8112` (`just start`).
- PHP 8.4 emits deprecation/warning notices on parts of this 2022 codebase — a
  rendered page with warnings is degraded-but-working, not a lint failure.

## Evolution Log

- Ported from book-review's lint-check (Pint + PHPUnit) for this plain-PHP repo:
  no composer toolchain exists, so the suite was rebuilt on career-estimation's
  build-gate+grep pattern with `php -l` (via `just lint`) as the lint layer, plus
  the kit-placeholder grep and a PHP-flavored debug-leftover grep.
