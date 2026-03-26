# Copilot Instructions for MokoWaaS

This is a **Joomla 5.x system plugin** written in PHP 8.1+ that provides a comprehensive identity override layer for the MokoWaaS platform. It replaces Joomla branding with MokoWaaS terminology across both the administrator backend and frontend interfaces.

## Repository Structure

```
MokoWaaS/
├── src/                                      # Installable plugin source (zipped for release)
│   └── plugins/system/mokowaas/
│       ├── mokowaas.xml                 # Joomla plugin manifest
│       ├── script.php                        # Installation/upgrade script
│       ├── services/provider.php             # DI service provider (Joomla 5.x)
│       ├── src/Extension/MokoWaaS.php  # Main plugin class
│       ├── language/                         # Frontend language files and overrides
│       └── administrator/language/          # Administrator language files and overrides
├── docs/                                     # Documentation
│   ├── guides/                               # Operational guides
│   └── reference/                            # Reference materials
├── scripts/                                  # Build and validation scripts
│   ├── validate_manifest.sh
│   ├── verify_changelog.sh
│   └── update_changelog.sh
├── .github/workflows/                        # GitHub Actions CI/CD
│   ├── ci.yml                                # PR validation pipeline
│   ├── build.yml                             # ZIP packaging on release
│   └── release_from_version.yml             # Automated release workflow
├── phpcs.xml                                 # PHP_CodeSniffer config (PSR-12 base)
├── phpstan.neon                              # PHPStan static analysis config
├── psalm.xml                                 # Psalm static analysis config
├── CHANGELOG.md                              # Version history
└── updates.xml                               # Joomla update server manifest
```

## Build and Validation

### PHP Syntax Check
```bash
find src -type f -name "*.php" -print0 | xargs -0 -n 1 -P 4 php -l
```

### Validate Plugin Manifest
```bash
./scripts/validate_manifest.sh
```

### Verify Changelog Format
```bash
./scripts/verify_changelog.sh
```

### Update Changelog (CI mode — checks for uncommitted changes)
```bash
./scripts/update_changelog.sh --ci
```

### Build Installable ZIP
```bash
cd src/plugins/system/mokowaas
zip -r ../../../../dist/MokoWaaS-<version>.zip .
```

### CI Pipeline (runs on every PR and push to `main`/`version/*`)
1. PHP lint — `find src -name "*.php" | xargs php -l`
2. Composer install and test (if `composer.json` exists)
3. Manifest validation — `scripts/validate_manifest.sh`
4. Changelog verification — `scripts/update_changelog.sh --ci`

## Coding Standards

- Follow **[MokoStandards](https://github.com/mokoconsulting-tech/MokoStandards)** coding standards.
- PHP files must comply with **PSR-12** (enforced by `phpcs.xml`).
- Maximum line length: **120 characters** (hard limit 150).
- Forbidden PHP functions: `eval`, `create_function`, `var_dump`, `print_r`.
- Use **tab characters** for indentation in PHP, XML, shell scripts, and Markdown; editors should display tabs as 2 spaces wide (configured via `.editorconfig`).
- Use **spaces** for YAML (2 spaces) and JSON (2 spaces).
- All files must use **UTF-8 encoding** and **LF line endings** (CRLF only for `.ps1`, `.bat`, `.cmd`).

### Required File Header

Every PHP, XML, shell, and Markdown file must include:

1. **SPDX license header** with GPL-3.0-or-later
2. **FILE INFORMATION metadata block**:
   ```
   # FILE INFORMATION
   DEFGROUP: Joomla.Plugin
   INGROUP: MokoWaaS[.<subgroup>]
   REPO: https://github.com/mokoconsulting-tech/mokowaas
   VERSION: <current version>
   PATH: /<path-from-repo-root>
   BRIEF: <one-line description>
   ```

### Joomla 5.x Plugin Architecture

- Plugin namespace: `Moko\Plugin\System\MokoWaaS` (declared **before** `defined('_JEXEC')`)
- Main class: `MokoWaaS` in `src/Extension/MokoWaaS.php`
- Service provider: `services/provider.php` registers the plugin via DI container
- All methods implementing `InstallerScriptInterface` must have explicit `: bool` return types
- Avoid deprecated Joomla APIs; use Joomla 5.x event-driven patterns

### Language Files

- Frontend `.ini` files → `language/en-GB/` and `language/en-US/`
- Admin `.sys.ini` files → `administrator/language/en-GB/` and `administrator/language/en-US/`
- Frontend `.override.ini` files → `language/en-GB/` and `language/en-US/`
- Admin `.override.ini` files → `administrator/language/en-GB/` and `administrator/language/en-US/`
- Language override files must **not** be declared in the XML manifest `<languages>` sections

## Version Management

- Versioning: `MAJOR.MINOR.PATCH` with zero-padded two-digit components (e.g., `01.06.00`) — this is intentional per MokoStandards for consistent sorting and display
- Version must be updated consistently across:
  - `src/plugins/system/mokowaas/mokowaas.xml`
  - All PHP file headers
  - `CHANGELOG.md`
  - `updates.xml`
  - Documentation metadata
- MAJOR: structural or architectural changes
- MINOR: feature updates or terminology expansion
- PATCH: bug fixes or language corrections

## Branching Model

- `main` — production stable
- `develop` — next minor release aggregation
- `feature/*` — new enhancements
- `bugfix/*` — hotfixes and corrections
- `version/*` — release preparation branches (trigger CI)

## Key Guidelines

1. Preserve **load order compatibility** with other Joomla system plugins.
2. Do not introduce deprecated Joomla APIs.
3. When modifying language overrides, update **both** `en-GB` and `en-US` variants.
4. When changing the plugin version, update it in **all** locations listed above.
5. Pull requests must include a description, version bump (when applicable), and reference to related issues.
6. Documentation in `docs/` must include metadata and maintain revision history following MokoStandards.
7. The `updates.xml` file in the repository root is automatically updated by the `release_from_version.yml` workflow after stable releases — do not edit it manually unless necessary.
