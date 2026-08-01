# MyPenawar — documentation

Developer documentation for the MyPenawar clinic website (plain PHP 8.4 + MySQL/MariaDB,
local-only on port 8112).

> **New here? Start with [`tldr.md`](tldr.md)** — every document below summarised in 30
> seconds each. Then follow the reading order for your role.

## Who is this for?

| Reader | Start here |
| --- | --- |
| Brand-new developer setting up a machine | [`02-setup/getting-started.md`](02-setup/getting-started.md) |
| Developer about to change code | [`03-development/workflow.md`](03-development/workflow.md) + [`01-overview/architecture.md`](01-overview/architecture.md) |
| Someone who just wants to know what this is | [`01-overview/project-overview.md`](01-overview/project-overview.md) |
| Someone whose page just printed a `mysqli_sql_exception` | [`06-troubleshooting/common-issues.md`](06-troubleshooting/common-issues.md) |
| Anyone with a quick question | [`07-faq/faq.md`](07-faq/faq.md) |

## Recommended reading order

1. [`tldr.md`](tldr.md) — the whole set at a glance
2. [`01-overview/project-overview.md`](01-overview/project-overview.md) — what and why
3. [`02-setup/getting-started.md`](02-setup/getting-started.md) — get it running
4. [`01-overview/architecture.md`](01-overview/architecture.md) — how it works
5. [`03-development/workflow.md`](03-development/workflow.md) — how to work on it
6. [`05-reference/commands.md`](05-reference/commands.md) — keep open while working
7. Everything else on demand.

## 01-overview

| Document | What it covers |
| --- | --- |
| [`project-overview.md`](01-overview/project-overview.md) | What the app is, features by role, key design points, dead/scratch files, what it is not |
| [`architecture.md`](01-overview/architecture.md) | Request flow, sessions, the five-table data model, write paths, the PHP 8.1+ mysqli-throws behavior |

## 02-setup

| Document | What it covers |
| --- | --- |
| [`getting-started.md`](02-setup/getting-started.md) | Fresh-PC setup: `setup.ps1` → reopen shell → `just start`, plus the optional MySQL/MariaDB (`localhost:3307`) import |

## 03-development

| Document | What it covers |
| --- | --- |
| [`workflow.md`](03-development/workflow.md) | The day-2 loop, house rules, sharp edges, Claude Code usage |

## 04-deployment

| Document | What it covers |
| --- | --- |
| [`deployment.md`](04-deployment/deployment.md) | Honest state: no CI/CD, runs locally — plus what a real deploy would require |

## 05-reference

| Document | What it covers |
| --- | --- |
| [`commands.md`](05-reference/commands.md) | Every `just` recipe, the pinned-PHP guard, the `PORT` override |
| [`project-layout.md`](05-reference/project-layout.md) | Annotated tree + a "where to make which change" table |

## 06-troubleshooting

| Document | What it covers |
| --- | --- |
| [`common-issues.md`](06-troubleshooting/common-issues.md) | Real observed symptoms: no-DB exception shapes, `/` scratch page, missing pinned PHP, missing mysqli, legacy warnings, port conflicts |

## 07-faq

| Document | What it covers |
| --- | --- |
| [`faq.md`](07-faq/faq.md) | Short answers on the home page, the database, port 3307, test accounts, duplicate pages, spaced filenames, PHPMailer, testing |
