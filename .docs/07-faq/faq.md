# FAQ

> **TL;DR** Quick answers: it's a 2022 XAMPP-era student project served on PHP 8.4;
> `/index.html` is the real home page; the DB is `just db-start` (portable MariaDB from
> setup.ps1) and only needed for the dynamic pages; test credentials are in
> `mypenawar.sql`; four filenames contain spaces; don't edit `Mail/phpmailer/`.

## Why does `/` not show the clinic site?

`php -S` prefers `index.php` over `index.html`, and this repo's `index.php` is a legacy
"PHP Output Test" scratch page. Use `/index.html`.

## Do I need the database to work on this repo?

Only for the dynamic pages. Static pages, layout/CSS work, and the login/registration
*forms* render without it. Anything that runs SQL — logging in, booking, admin lists,
reports, receipts, password reset — needs the DB: `just db-start` (portable MariaDB
installed and seeded by `setup.ps1`, listening on `127.0.0.1:3307` —
[setup](../02-setup/getting-started.md)).

## Why port 3307 and not 3306?

The project was built on a XAMPP install whose MariaDB listened on 3307 (see the
`mypenawar.sql` dump header). `db_config.php` — and the inline copies of it in several
pages — encode that, which is why `just db-start` serves 3307 rather than editing
them all.

## Are there test accounts?

Yes — `mypenawar.sql` seeds `patient` and `employee` rows (passwords are stored in
plaintext, so read them straight from the dump). Or register a fresh patient via the
login page once the DB is up.

## Why do some pages exist twice (`login.php` vs `login2.php`/`index2.php`)?

They're older iterations kept in the repo. `login.php` is the live one; `index2.php`
even queries a `staff` table that the schema never creates. Don't extend the dead
variants.

## Why are there filenames with spaces?

2022 authoring choice: `patient booking.php`, `patient profile.php`,
`employee profile.php`, `monthly report.php`. Renaming them would break the hardcoded
links between pages, so they stay — always quote them in shell commands.

## Can I upgrade PHPMailer / fix warnings inside `Mail/phpmailer/`?

No. It's a vendored third-party library — treat it as read-only. The password-recovery
flow (`recover_psw.php`) is the only consumer.

## Is there a test suite or CI?

No CI, but there is a local suite: `just test` runs `tests/smoke.ps1` — 20 checks: the
`php -l` sweep, a secret sweep, per-page heading assertions for the static pages (home,
about, contact), the login form and `index.php`, the three portal auth-guard redirects,
and the seeded DB flows (patient **and** staff login, `appList2.php`'s single row,
`patient.php`'s five rows, `monthly report.php`'s three aggregate rows — all read-only).
The DB checks are **mandatory**: the suite starts MariaDB itself when 3307 is silent, and
a database that is installed but broken fails the run instead of skipping. All against a
private server on port 8614.
`just lint` remains available as the fast syntax-only gate; `06-troubleshooting`
documents the runtime failure modes.

## Where do I ask an AI assistant for help?

`just claudex` launches Claude Code with this repo's `CLAUDE.md` briefing, skills
(`.claude/skills/README.md`), and MCP wiring already in place.

## Related docs

| Doc | Why |
| --- | --- |
| [../06-troubleshooting/common-issues.md](../06-troubleshooting/common-issues.md) | When something actually fails |
| [../01-overview/project-overview.md](../01-overview/project-overview.md) | The longer story behind these answers |
