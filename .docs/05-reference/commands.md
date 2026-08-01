# Commands

> **TL;DR** Everything routine is a `just` recipe — run `just` to list them. `start` /
> `serve` / `stop` manage the dev server on port 8112, `lint` is the quality gate,
> `claudex`/`claudeo`/`claudeh` launch Claude Code. PHP is pinned to
> `%LOCALAPPDATA%\Programs\php-8.4\php.exe`.

## Daily

| Command | What it does |
| --- | --- |
| `just` | List every recipe |
| `just start` | Start the PHP dev server on http://127.0.0.1:8112 in a background window (runs `stop` first so a stale server never lingers) |
| `just serve` | Same server in the foreground — request log visible, Ctrl+C to stop |
| `just stop` | Kill only `php.exe` processes whose command line contains this repo's path — other projects' servers are untouched |
| `just lint` | `php -l` every `.php` file in the repo (recursive, skips `.git`); fails with a count if any file has a parse error |

## Claude Code launchers

| Command | Model |
| --- | --- |
| `just claudex` | Sonnet (latest), all permissions |
| `just claudeo` | Opus (latest), all permissions |
| `just claudeh` | Haiku (latest), all permissions |

## Notes

- `port` honours a `PORT` environment variable (`$env:PORT=8200; just start`) but
  defaults to the assigned **8112** — don't serve this repo on any other port in normal
  work.
- Every recipe that needs PHP is guarded by `_require-php`: if
  `%LOCALAPPDATA%\Programs\php-8.4\php.exe` is missing you get one clear error telling
  you to run `pwsh ./setup.ps1`.
- One-time machine setup is not a recipe: `pwsh ./setup.ps1` (idempotent — see
  [../02-setup/getting-started.md](../02-setup/getting-started.md)).
- There are no test/build/format recipes because the repo has no test suite, build step,
  or formatter — `lint` is the whole gate.

## Related docs

| Doc | Why |
| --- | --- |
| [../02-setup/getting-started.md](../02-setup/getting-started.md) | First-time setup before any of these work |
| [project-layout.md](project-layout.md) | What the files these commands act on are |
| [../06-troubleshooting/common-issues.md](../06-troubleshooting/common-issues.md) | When a command fails |
