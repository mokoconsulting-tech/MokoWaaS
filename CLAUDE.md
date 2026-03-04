<!--
 Copyright (C) 2025 Moko Consulting <hello@mokoconsulting.tech>

 This file is part of a Moko Consulting project.

 SPDX-LICENSE-IDENTIFIER: GPL-3.0-or-later

 This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 3 of the License, or (at your option) any later version.

 This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the IMPLIED WARRANTY of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.

 You should have received a copy of the GNU General Public License (./LICENSE.md).

 # FILE INFORMATION
 DEFGROUP: Joomla.Plugin
 INGROUP: MokoWaaS-Brand
 REPO: https://github.com/mokoconsulting-tech/MokoWaaSBrand
 VERSION: 01.06.00
 PATH: /CLAUDE.md
 BRIEF: Claude Code context file for the MokoWaaS-Brand repository
-->

# What This Repo Is

**MokoWaaS-Brand** (https://github.com/mokoconsulting-tech/MokoWaaSBrand) is a Joomla 5.x system plugin that provides a comprehensive identity override layer for the MokoWaaS platform. It replaces Joomla branding with MokoWaaS terminology across both the administrator backend and frontend interfaces by injecting language override strings at runtime. It is deployed exclusively within the MokoWaaS WaaS delivery stack by Moko Consulting. It is not a standalone template, not a general-purpose library, and not a monorepo root — it is a single installable Joomla system plugin distributed as a ZIP archive built from the `src/` directory.

# Repo Structure

```
MokoWaaSBrand/
├── src/                              # Installable plugin source — everything here is zipped for release
│   └── plugins/system/mokowaasbrand/
│       ├── mokowaasbrand.xml         # Joomla plugin manifest: version, files list, config params, update server
│       ├── script.php                # InstallerScriptInterface implementation: handles install/update/uninstall
│       ├── services/
│       │   └── provider.php          # Joomla 5.x DI container service provider — registers the plugin class
│       ├── src/Extension/
│       │   └── MokoWaaSBrand.php     # Main plugin class (namespace: Moko\Plugin\System\MokoWaaSBrand\Extension)
│       ├── language/                 # Frontend language files
│       │   ├── en-GB/                # plg_system_mokowaasbrand.ini + en-GB.override.ini
│       │   ├── en-US/                # plg_system_mokowaasbrand.ini + en-US.override.ini
│       │   └── overrides/            # Runtime-loaded frontend override files
│       └── administrator/
│           └── language/             # Administrator-side language files
│               ├── en-GB/            # plg_system_mokowaasbrand.sys.ini + en-GB.override.ini
│               ├── en-US/            # plg_system_mokowaasbrand.sys.ini + en-US.override.ini
│               └── overrides/        # Runtime-loaded admin override files
├── docs/                             # All project documentation
│   ├── guides/                       # Operational guides: build, install, config, ops, upgrade, rollback, troubleshooting
│   ├── reference/                    # Reference material: plugin-overview.md
│   ├── index.md                      # Documentation index
│   └── plugin-basic.md               # Plugin basics overview
├── scripts/                          # Build and validation shell scripts
│   ├── validate_manifest.sh          # Validates mokowaasbrand.xml structure and required fields
│   ├── verify_changelog.sh           # Checks CHANGELOG.md format
│   └── update_changelog.sh           # Inserts a new version header into CHANGELOG.md
├── .github/
│   ├── workflows/
│   │   ├── ci.yml                    # PR validation pipeline: PHP lint, manifest, changelog
│   │   ├── build.yml                 # ZIP packaging triggered on release events
│   │   ├── release_from_version.yml  # Automated release from version/* branches
│   │   └── ...                       # Additional branch management and update-server workflows
│   ├── ISSUE_TEMPLATE/               # Structured GitHub issue templates
│   └── pull_request_template.md      # Standard PR checklist template
├── phpcs.xml                         # PHP_CodeSniffer config (PSR-12 base + MokoStandards rules)
├── phpstan.neon                      # PHPStan static analysis config (level 5)
├── psalm.xml                         # Psalm static analysis config (error level 4)
├── .editorconfig                     # Per-language indentation and encoding rules
├── .gitmessage                       # Conventional Commits commit message template
├── CHANGELOG.md                      # Version history (Keep a Changelog format)
├── updates.xml                       # Joomla update server manifest (auto-maintained by CI)
└── LICENSE.md                        # GPL-3.0-or-later
```

# File Header Requirements

Every PHP, XML, shell, and Markdown file must include a full SPDX license header followed by a FILE INFORMATION metadata block. JSON files, binary files, generated files, and `index.html` security stubs are exempt.

## Full header format

**PHP files:**

```php
<?php
/**
 * Copyright (C) 2025 Moko Consulting <hello@mokoconsulting.tech>
 *
 * This file is part of a Moko Consulting project.
 *
 * SPDX-LICENSE-IDENTIFIER: GPL-3.0-or-later
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 3 of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the IMPLIED WARRANTY of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License (./LICENSE.md).
 *
 * FILE INFORMATION
 * DEFGROUP: Joomla.Plugin
 * INGROUP: MokoWaaS-Brand
 * REPO: https://github.com/mokoconsulting-tech/MokoWaaSBrand
 * VERSION: 01.06.00
 * PATH: /src/plugins/system/mokowaasbrand/src/Extension/MokoWaaSBrand.php
 * BRIEF: Main plugin class for MokoWaaS-Brand system plugin
 */
```

**XML files:** Same content inside `<!-- ... -->`. **Markdown files:** Same content inside `<!-- ... -->`. **Shell scripts:** Same content as `#` line comments.

## FILE INFORMATION block fields

| Field | Required | Description |
|-------|----------|-------------|
| `DEFGROUP` | Yes | Always `Joomla.Plugin` for all files in this repo |
| `INGROUP` | Yes | `MokoWaaS-Brand` or `MokoWaaS-Brand.<subgroup>` (e.g. `MokoWaaS-Brand.Build`) |
| `REPO` | Yes | `https://github.com/mokoconsulting-tech/MokoWaaSBrand` |
| `VERSION` | Yes | Must match `<version>` in `mokowaasbrand.xml` (e.g. `01.06.00`) |
| `PATH` | Yes | Absolute path from repo root (e.g. `/src/plugins/system/mokowaasbrand/script.php`) |
| `BRIEF` | Yes | Single-line description of the file's purpose |
| `NOTE` | No | Optional additional context line |

## Exempt file types

- JSON files — no header; formatted with 2-space indentation
- Binary files and generated files
- `index.html` security stub files throughout the plugin directory tree
- `.ini` language files — plain `KEY="value"` format with no header

# Coding Standards

This repo follows [MokoStandards](https://github.com/mokoconsulting-tech/MokoStandards) with PSR-12 as the PHP base standard (enforced by `phpcs.xml`).

## Indentation

| File type | Style | Visual width |
|-----------|-------|--------------|
| PHP, XML, shell, Markdown | Tab | 2 spaces |
| YAML (`.yml`, `.yaml`) | Space | 2 |
| JSON | Space | 2 |

All files: UTF-8 encoding, LF line endings. Exception: `.ps1`, `.bat`, `.cmd` files use CRLF.

## Line length (PHP)

Warning threshold: 120 characters. Hard limit: 150 characters. Enforced by `Generic.Files.LineLength` in `phpcs.xml`.

## Naming conventions

| Context | Convention | Example |
|---------|------------|---------|
| PHP classes | PascalCase | `MokoWaaSBrand` |
| PHP methods and functions | camelCase | `loadLanguageOverrides()` |
| PHP properties | camelCase | `$autoloadLanguage` |
| PHP constants | UPPER_SNAKE_CASE | `JPATH_PLUGINS` |
| PHP class files | PascalCase | `MokoWaaSBrand.php` |
| PHP config/script files | lowercase | `script.php`, `provider.php` |
| Shell scripts | lowercase with underscores | `validate_manifest.sh` |

## Forbidden PHP patterns

From `phpcs.xml` `Generic.PHP.ForbiddenFunctions`: `eval`, `create_function`, `var_dump`, `print_r`. Also enforced: no long array syntax (`array()` → `[]`), no commented-out code blocks, no empty statements.

# Language-Specific Requirements

## PHP

**Type hints:** All method parameters and return types require explicit type declarations. Methods implementing `InstallerScriptInterface` (`preflight`, `postflight`, `install`, `update`, `uninstall`) must declare an explicit `: bool` return type.

**Docblocks:** Every class, property, and method requires a docblock. Required tags by context:

```php
/**
 * Short one-line description.
 *
 * Optional longer description paragraph.
 *
 * @param   string  $filePath  The path to the language file
 *
 * @return  array  Array of language strings
 *
 * @since   01.06.00
 */
```

Use `@var` for properties, `@param` and `@return` for methods, `@since` on all declarations.

**Namespace placement:** The namespace declaration must appear before `defined('_JEXEC') or die;`:

```php
namespace Moko\Plugin\System\MokoWaaSBrand\Extension;

defined('_JEXEC') or die;
```

**Joomla API:** Use `Joomla\CMS\*` namespace imports only. Do not use deprecated aliases (`JFactory`, `JText`, `JHTML`, etc.).

**Error handling:** Do not throw unhandled exceptions from event handler methods. Return early on failure or log via `Joomla\CMS\Log\Log`.

**Script class naming:** The installer script class in `script.php` is named `plgSystemMokoWaaSBrandInstallerScript` (not the plugin class name).

## Shell scripts

Use `#!/usr/bin/env bash` or `#!/bin/bash` as shebang. Enable strict mode with `set -e` or `set -euo pipefail`. Accept version or path arguments explicitly via positional parameters — do not rely on global state.

# Commit Message Format

From `.gitmessage`:

```
<type>(<scope>): <subject>

<body: what and why>

BREAKING CHANGE: <description>
Closes: #<issue>
Signed-off-by: <Name> <email>
```

**Valid types:** `build` | `chore` | `ci` | `docs` | `feat` | `fix` | `perf` | `refactor` | `revert` | `style` | `test`

**Subject rules:** imperative mood, lower-case first word, no trailing period, ≤72 characters.

**Body:** Separated from subject by a blank line. Explain what changed and why, not how.

**Footer:** `BREAKING CHANGE:`, `Closes:`, and `Signed-off-by:` each on their own line after a blank line following the body.

Example:

```
feat(language): add override strings for search and contact forms

Expands the frontend override file with 12 new strings covering
search results pages, contact form field labels, and error page copy.

Closes: #42
```

# Running Validation

Run these commands from the repo root before every commit:

```bash
# 1. PHP syntax check
find src -type f -name "*.php" -print0 | xargs -0 -n 1 -P 4 php -l

# 2. Validate plugin manifest
./scripts/validate_manifest.sh

# 3. Verify changelog format
./scripts/verify_changelog.sh

# 4. Changelog CI check — fails if the script would modify CHANGELOG.md
./scripts/update_changelog.sh --ci
```

If static analysis tools are installed:

```bash
vendor/bin/phpcs src
vendor/bin/phpstan analyse
vendor/bin/psalm
```

There is currently no `composer.json` in this repo. If one is added, also run `composer install --no-interaction` and `composer test` (if a `test` script is defined).

# Contribution Workflow

1. **Fork** the repository or create a branch from `main` (internal contributors create branches directly).
2. **Name the branch:**
	- New feature: `feature/<short-description>`
	- Bug fix: `bugfix/<short-description>`
	- Release preparation: `version/<version-number>` (triggers CI and the release pipeline)
3. **Set up:** Ensure PHP 8.1+, a Joomla 5.x test environment, and the `zip` CLI utility are available.
4. **Implement changes** following the standards in this file.
5. **Validate locally** using all commands in [Running Validation](#running-validation).
6. **Commit** using the Conventional Commits format (see [Commit Message Format](#commit-message-format)).
7. **Open a PR** against `main`. Include: purpose, change summary, screenshots for UI changes, and a reference to the related issue.
8. **Merge strategy:** Squash merge into `main`.

`version/*` branches trigger the full CI pipeline. The `release_from_version.yml` workflow handles tagging, ZIP packaging via `build.yml`, and automatic `updates.xml` updates for stable (non-prerelease) versions.

# PR Checklist

- [ ] PHP files pass `php -l` syntax check
- [ ] Plugin manifest validates via `./scripts/validate_manifest.sh`
- [ ] CHANGELOG.md is up to date and passes `./scripts/verify_changelog.sh`
- [ ] All new and modified files include the full SPDX license header and FILE INFORMATION block
- [ ] `VERSION` in every FILE INFORMATION block matches `<version>` in `mokowaasbrand.xml`
- [ ] Namespace declaration appears before `defined('_JEXEC') or die;` in all PHP class files
- [ ] All `InstallerScriptInterface` methods declare `: bool` return type explicitly
- [ ] Both `en-GB` and `en-US` language variants updated when override strings change
- [ ] Language override files (`.override.ini`) are NOT declared in the XML `<languages>` sections
- [ ] No deprecated Joomla APIs (`JFactory`, `JText`, etc.) used
- [ ] No forbidden PHP functions (`eval`, `create_function`, `var_dump`, `print_r`)
- [ ] No commented-out code blocks left in PHP files
- [ ] PR description includes purpose, change summary, and issue reference
- [ ] Screenshots included for any change visible in Joomla frontend or administrator UI

# What NOT to Do

- **Do not commit** `dist/`, `vendor/`, `node_modules/`, or IDE artifacts (`.idea/`, `.vscode/`).
- **Do not declare** `.override.ini` language files in the XML manifest `<languages>` sections — they are loaded programmatically by `MokoWaaSBrand.php` at runtime.
- **Do not add** `.sys.ini` files under the frontend `language/` directory — `.sys.ini` files belong only under `administrator/language/`.
- **Do not edit** `updates.xml` manually — it is maintained automatically by the `release_from_version.yml` workflow after each stable release.
- **Do not use** deprecated Joomla APIs — use `Joomla\CMS\Factory`, `Joomla\CMS\Language\Text`, etc.
- **Do not name** the plugin class `PlgSystemMokoWaaSBrand` — the class is `MokoWaaSBrand` in namespace `Moko\Plugin\System\MokoWaaSBrand\Extension`.
- **Do not place** the namespace declaration after `defined('_JEXEC') or die;` — the namespace must come first in the file.
- **Do not use** `eval`, `create_function`, `var_dump`, or `print_r` anywhere in PHP source.
- **Do not update** only one locale when changing language overrides — always update both `en-GB` and `en-US` variants.

# Key Policy Documents

- [CONTRIBUTING.md](CONTRIBUTING.md) — Contribution workflow, branching model, and PR requirements
- [docs/guides/build-guide.md](docs/guides/build-guide.md) — Build, packaging, and release workflow
- [docs/guides/installation-guide.md](docs/guides/installation-guide.md) — Plugin installation and setup
- [docs/guides/upgrade-and-versioning-guide.md](docs/guides/upgrade-and-versioning-guide.md) — Version numbering (`01.06.00` format) and upgrade process
- [docs/reference/plugin-overview.md](docs/reference/plugin-overview.md) — Architectural overview of the plugin in the WaaS stack
- [MokoStandards](https://github.com/mokoconsulting-tech/MokoStandards) — Coding and documentation standards governing this repo
