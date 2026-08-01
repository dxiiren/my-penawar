# Project overview

> **TL;DR** MyPenawar is a 2022 student-project website for "Poliklinik Penawar", a
> polyclinic in Semenyih, Selangor: static HTML marketing pages plus page-per-file PHP
> scripts for login, appointment booking, booking admin, receipts, monthly reports, and
> password recovery — all raw `mysqli` against a MySQL/MariaDB `mypenawar` database.
> No framework. Runs locally on http://127.0.0.1:8112.

## What it is

MyPenawar presents a healthcare service provider ("Kumpulan Perubatan Penawar Sdn. Bhd",
Semenyih Sentral, Selangor) and lets its patients and staff manage appointments. It was
built in 2022 for the XAMPP stack (PHP ~8.1 + MariaDB 10.4 on port 3307) and is served
here unchanged by the PHP 8.4 built-in dev server.

## Features

| Area | Pages | What you can do |
| --- | --- | --- |
| Public site | `index.html`, `aboutus.html`, `contact.html`, `member.html` | Read about the clinic, its staff, and contact details |
| Auth | `login.php` | Log in as patient or staff; register a new patient account |
| Patient | `patient profile.php`, `patient booking.php`, `appList2.php`, `receipt.php` | View/update profile, book an appointment slot, see booking history, view a payment receipt |
| Staff | `employee profile.php`, `patient.php`, `patientedit.php`, `code.php`, `monthly report.php` | View/update staff profile, list all bookings, edit/delete a booking, record payments, monthly bookings/revenue report |
| Password recovery | `recover_psw.php`, `reset_psw.php` | Email a reset token (vendored PHPMailer), set a new password |
| AJAX | `get_data.php` | Patient lookup by IC number (used by admin pages) |

## Key design points

- **Page-per-file PHP** — every screen is one script at the repo root containing its own
  HTML, CSS, form handling, and SQL. There is no router, no controller layer, no template
  engine.
- **Raw `mysqli`** — pages call `mysqli_connect`/`mysqli_query` directly, either via
  `require 'db_config.php'` or by re-declaring the same credentials inline. Queries
  interpolate `$_POST` values (2022-era code; see the security note below).
- **Native sessions** — `login.php` stores the logged-in identity in
  `$_SESSION["user1"]`; downstream pages read it to scope queries.
- **Seed data committed** — `mypenawar.sql` is a phpMyAdmin dump with schema plus rows
  for all five tables: `booking`, `employee`, `patient`, `payment`, `service`.

## What it is not

- Not deployed anywhere; no CI/CD ([04-deployment](../04-deployment/deployment.md)).
- Not secure by modern standards: SQL is built by string interpolation and passwords are
  stored in plaintext. That is documented legacy debt — the repo standard is to not
  introduce NEW code with those patterns, not to retrofit the old pages.
- Not framework-migratable within scope — treat it as a legacy artifact.

## Dead/scratch files kept for history

- `index.php` — a "PHP output test" scratch page (it is what `php -S` serves for `/`).
- `index2.php`, `login2.php` — older login variants; `index2.php` queries a `staff`
  table that does not exist in `mypenawar.sql`.
- `demo.html`, `notification.html`, `receipt.html` — static mockups of dynamic pages.

## Related docs

| Doc | Why |
| --- | --- |
| [architecture.md](architecture.md) | How the pages, sessions, and tables fit together |
| [../02-setup/getting-started.md](../02-setup/getting-started.md) | Get it running on a fresh PC |
| [../03-development/workflow.md](../03-development/workflow.md) | House rules for changing this code |
