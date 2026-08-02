# Project layout

> **TL;DR** Everything lives at the repo root — one file per screen. Static marketing
> pages are `.html`, dynamic screens are `.php`, four filenames contain spaces, the DB
> schema is `mypenawar.sql`, and the only subfolders are `image/`, `Mail/phpmailer/`
> (vendored), `.docs/`, and `.claude/`.

## Annotated tree

```
my-penawar/
  index.html              # real home page (Tailwind hero + typed.js, services, footer)
  index.php               # PHP output-test scratch page (served for "/" by php -S)
  partials/               # shared head/nav/footer includes for the PHP pages (2026 facelift)
  aboutus.html            # clinic history + address
  contact.html            # contact info
  member.html             # staff gallery
  demo.html, notification.html, receipt.html   # static mockups
  login.php               # patient/staff login + patient registration
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
  recover_psw.php         # email a reset link (PHPMailer)
  reset_psw.php           # set new password from emailed token
  message.php             # session flash-message partial
  db_config.php           # mysqli connection (localhost:3307 / mypenawar / root)
  mypenawar.sql           # phpMyAdmin dump: schema + seed data for all 5 tables
  Mail/phpmailer/         # vendored PHPMailer 5.x (third-party, do not edit)
  image/                  # logos, staff photos, background
  style.css               # legacy 2022 stylesheet — used only by the dead variants now
  justfile                # dev recipes (start/serve/stop/lint/test + claude launchers)
  tests/                  # smoke.ps1 — HTTP smoke-test suite, no-DB scope (`just test`)
  setup.ps1               # one-time machine setup (idempotent)
  README.md               # entry point + quick start
  CLAUDE.md               # AI-assistant briefing (stack, rules, gotchas)
  .docs/                  # this documentation set
  .claude/                # skills, hooks, settings, memory
  .mcp.json.stub          # committed MCP config template (.mcp.json itself is git-ignored)
```

## Where to make which change

| You want to... | Touch |
| --- | --- |
| Change clinic copy/branding | `index.html`, `aboutus.html`, `contact.html`, `member.html` (+ mirror shared header/footer edits in `partials/`) |
| Change login or registration | `login.php` (ignore `login2.php`/`index2.php` — dead variants) |
| Change booking rules (slots, times) | `patient booking.php` (form + slot check) |
| Change booking admin/editing | `patient.php` → `patientedit.php` → `code.php` |
| Change receipts/payments | `receipt.php`, `code.php` (payment INSERT) |
| Change reports | `monthly report.php` |
| Change password recovery | `recover_psw.php`, `reset_psw.php` (never `Mail/phpmailer/`) |
| Change DB connection | `db_config.php` — but note many pages re-declare it inline |
| Change schema/seed data | `mypenawar.sql` (+ re-import locally) |
| Add/adjust dev tooling | `justfile`, `setup.ps1` |

## Related docs

| Doc | Why |
| --- | --- |
| [../01-overview/architecture.md](../01-overview/architecture.md) | How these files interact at runtime |
| [commands.md](commands.md) | The recipes that serve/lint this tree |
