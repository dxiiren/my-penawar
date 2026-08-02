# Architecture

> **TL;DR** No framework: the PHP built-in server maps URLs straight to root-level
> `.php`/`.html` files. Each PHP page owns its whole flow — HTML, form POST handling,
> `mysqli` SQL, session reads/writes. Identity lives in `$_SESSION["user1"]`; data lives
> in five MariaDB tables (`booking`, `employee`, `patient`, `payment`, `service`)
> connected via `db_config.php` (`localhost:3307`).

## Request flow

```
browser → php -S 127.0.0.1:8112 -t .
        → /index.html, /aboutus.html ...          static, no PHP
        → /login.php, /patient.php ...            one script = one screen
             ├─ session_start() (often mid-page)
             ├─ require 'db_config.php'  OR  inline mysqli_connect(...)
             ├─ if ($_POST[...]) { ... mysqli_query ... }
             └─ echoes its own HTML (Tailwind CDN via partials/head.php)
```

`php -S` serves `index.php` for `/` (not `index.html` — the real home page must be
requested as `/index.html`).

## Identity and sessions

| Session key | Set by | Read by |
| --- | --- | --- |
| `$_SESSION["user1"]` | `login.php` on successful login (patient IC or staff username) | `appList2.php`, `receipt.php`, profile pages — to scope queries to "me" |
| `$_SESSION["bookID"]` | `appList2.php` (selected booking) | `receipt.php` — which payment to print |
| `$_SESSION["token"]`, `$_SESSION["email"]` | `recover_psw.php` | `reset_psw.php` — validates the emailed reset token |
| flash messages | various handlers | `message.php` partial (included by admin pages) |

`login.php` distinguishes patient vs staff by a radio button (`user`/`user1` POST field)
and queries `patient` or `employee` accordingly. The session-dependent portal pages
(`patient profile.php`, `employee profile.php`, `appList2.php`) start the session at the
**top of the file** and guard it: anonymous direct hits get
`header("Location: login.php"); exit;` before any output — no mid-page
`session_start()` warnings, no undefined-`$id` queries. (They historically started the
session mid-page and warned; that class is fixed — keep new session pages on the
top-guard pattern.)

## Data model (`mypenawar.sql`)

| Table | Holds | Key columns |
| --- | --- | --- |
| `patient` | patient accounts | `patientIC` (natural key), `patientUser`, `patientPassword` (plaintext), contact fields |
| `employee` | staff accounts | `empID`, `empUser`, `empPassword`, role/photo fields |
| `service` | clinic services | `serviceID`, `serviceName`, `empID` (the doctor giving it) |
| `booking` | appointments | `bookingID`, `bookingDate`/`Time`/`Desc`, `bookingStatus`, `patientIC`, `empID` |
| `payment` | payments for bookings | `paymentAmount`, `paymentMethod`, `paymentDate` |

Dump metadata: phpMyAdmin 5.2.0, MariaDB 10.4.24, host `localhost:3307` — i.e. the 2022
XAMPP defaults, which is why `db_config.php` targets port 3307.

## Write paths

| Action | Page → SQL |
| --- | --- |
| Register patient | `login.php` → `INSERT INTO patient(...)` (checks IC not taken first) |
| Book appointment | `patient booking.php` → slot check (3 fixed times/day) → `INSERT INTO booking(...)` |
| Edit/delete booking, record payment | `patientedit.php` posts to `code.php` → `UPDATE booking` / `DELETE FROM booking` / `INSERT payment(...)` |
| Update profile | `patient profile.php` / `employee profile.php` → `UPDATE` on own row |
| Reset password | `recover_psw.php` emails token (PHPMailer 5.x from `Mail/phpmailer/`) → `reset_psw.php` → `UPDATE patient SET patientPassword=...` |

All SQL interpolates request values directly into the query string. Since PHP 8.1,
`mysqli` throws `mysqli_sql_exception` on connect/query failure, so the legacy
`OR DIE(...)` guards are dead code — without a DB the pages either 500 (top-of-file
`require 'db_config.php'`) or render then print the uncaught exception (mid-page
connects).

## Frontend

Modernized in 2026 to a single Tailwind-CDN design system (teal/slate palette, Plus
Jakarta Sans). PHP pages include the shared partials `partials/head.php`,
`partials/nav.php`, and `partials/footer.php`; the static `.html` pages carry an
identical inline copy of those blocks (kept in sync by hand — `php -S` cannot include
into `.html`). Tailwind, Font Awesome, Google Fonts, typed.js (home hero), and
jQuery/jQuery UI (datepicker pages) all come from CDNs — internet is required for full
styling. `style.css` is legacy-only: it is still linked by the dead variants
(`login2.php`, `index2.php`, `demo.html`) and untouched otherwise. The original 2022
design is preserved in git history (pre-facelift commit `921630e`). `get_data.php` is
the one AJAX endpoint (jQuery POST from admin pages, returns an HTML table fragment for
a patient IC).

## Related docs

| Doc | Why |
| --- | --- |
| [project-overview.md](project-overview.md) | What the app does, feature-by-feature |
| [../05-reference/project-layout.md](../05-reference/project-layout.md) | Where each file lives and where to make which change |
| [../06-troubleshooting/common-issues.md](../06-troubleshooting/common-issues.md) | What the no-DB failure modes look like |
