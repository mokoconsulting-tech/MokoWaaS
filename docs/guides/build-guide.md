<!--
 Copyright (C) 2025 Moko Consulting <hello@mokoconsulting.tech>
 This file is part of a Moko Consulting project.
 SPDX-LICENSE-IDENTIFIER: GPL-3.0-or-later
 This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 3 of the License, or (at your option) any later version.
 This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the IMPLIED WARRANTY of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 You should have received a copy of the GNU General Public License (./LICENSE.md).

 # FILE INFORMATION
 DEFGROUP: Joomla.Plugin
 INGROUP: MokoWaaS.Build
 REPO: https://github.com/mokoconsulting-tech/mokowaas
 FILE: build-guide.md
 VERSION: 02.01.08
 PATH: /docs/guides/
 BRIEF: Build and packaging guide for the MokoWaaS system plugin
 NOTE: Defines environment setup, repository layout, packaging rules, and release preparation
-->

# MokoWaaS Build Guide (VERSION: 02.01.08)

## 1. Purpose

This document defines the complete build and packaging workflow for the MokoWaaS system plugin. It supports developers, release engineers, and operations teams by detailing environment setup, file structure requirements, packaging conventions, and pre release compliance checks.

## 2. Build Requirements

To build the plugin correctly, ensure the following environment prerequisites:

* PHP 8.1 or higher
* Joomla 5.x compatible environment available for testing
* Git installed and configured
* Zip CLI utility or equivalent archiving tool
* Access to [MokoStandards](https://github.com/mokoconsulting-tech/MokoStandards) for header and structure compliance

Optional but recommended:

* Node.js and NPM for asset linting (if applicable)
* IDE or editor with Joomla syntax highlighting

## 3. Repository Structure Overview

The repository should maintain a clean, predictable, and modular structure suitable for Joomla system plugins, WaaS platform governance, and automated build tooling. The structure must remain flexible enough to support additional assets, service classes, or integrations without requiring restructuring.

```text
mokowaas/
  ├── src/
  │     ├── mokowaas.php        (main plugin file)
  │     ├── mokowaas.xml        (plugin manifest)
  │     ├── services/                (service providers for DI)
  │     │   └── provider.php
  │     ├── language/                (plugin language files)
  │     │   ├── en-GB/*.ini
  │     │   └── overrides/           (Joomla language overrides)
  │     └── administrator/
  │         └── language/
  │             └── overrides/       (admin language overrides)
  │
  ├── LICENSE.md                     (standard GPL license)
  ├── README.md                      (repository overview)
  ├── CHANGELOG.md                   (version history)
  ├── CONTRIBUTING.md                (contribution rules)
  ├── CODE_OF_CONDUCT.md             (community guidelines)
  │
  ├── docs/                          (documentation suite)
  │     ├── index.md
  │     ├── plugin-basic.md
  │     ├── guides/*.md
  │     └── reference/*.md
  │
  ├── scripts/                       (build and validation utilities)
  │     ├── validate_manifest.sh
  │     ├── verify_changelog.sh
  │     └── update_changelog.sh
  │
  └── .github/                       (CI/CD workflows)
        └── workflows/*.yml
```

All files must contain the standardized Moko Consulting copyright header.

## 4. Build Workflow

### 4.1 Step 1: Validate File Headers

Ensure:

* SPDX identifier is present
* FILE INFORMATION block is correct
* Version matches plugin manifest

### 4.2 Step 2: Validate Manifest

Open `templateDetails.xml` and confirm:

* Version number updated
* File references are correct
* Plugin name and metadata align with release

### 4.3 Step 3: Clean Workspace

Remove any unneeded files:

* IDE artifacts
* Temporary logs
* System caches

### 4.4 Step 4: Create Package

Using CLI:

```bash
zip -r mokowaas_v01.04.00.zip ./ -x "*.git*" "scripts/*" "docs/*"
```

Ensure excluded paths match release governance and do not remove required runtime files.

### 4.5 Step 5: Install and Validate

Before release:

1. Install the built plugin in a clean Joomla environment.
2. Validate load order and branding output.
3. Check logs for warnings or notices.
4. Validate language strings and UI terminology.

## 5. Release Requirements

For a version to qualify as release ready:

* Documentation updated with correct version
* CHANGELOG updated if applicable
* All guides reflect new behaviors where relevant
* Branch has passed peer review and any required governance checks
* Tag created following semantic versioning conventions

## 6. Automated Build Options

Automated build and validation can be handled via configured CI workflows (for example, `.github/workflows/build.yml`) or other non-interactive tooling maintained in the repository. This keeps build behavior consistent across environments and reduces manual intervention.

Possible automations:

* Header enforcement
* Manifest version synchronization
* Folder and file presence validation
* ZIP creation using approved naming conventions

## 7. Post Release Steps

After release:

* Update download links and release notes
* Notify WaaS internal release channels
* Update dependent templates or modules if required
* Record the release in any internal environment or asset registry

## 8. CI/CD Workflow Integration

A continuous integration and delivery pipeline is implemented using GitHub Actions. The workflows below operationalize the build, validation, and release actions referenced throughout this guide.

### 8.1 Build and Validate Workflow (`.github/workflows/build.yml`)

```yaml
name: Build and Validate MokoWaaS

on:
  push:
    branches: [ main, develop ]
  pull_request:
    branches: [ main, develop ]

jobs:
  build-validate:
    runs-on: ubuntu-latest

    steps:
      - name: Checkout repository
        uses: actions/checkout@v4

      - name: Set up PHP
        uses: shivammathur/setup-php@v2
        with:
          php-version: '8.1'

      - name: Validate headers and metadata
        run: |
          echo "[INFO] Run header and FILE INFORMATION validation here (linters, custom scripts, etc.)."

      - name: Validate manifest
        run: |
          echo "[INFO] Validate templateDetails.xml version, file list, and metadata."

      - name: Lint PHP and syntax check
        run: |
          echo "[INFO] Run php -l over src/ and any additional linting as needed."

      - name: Create build artifact
        run: |
          zip -r mokowaas_ci_build.zip ./ -x "*.git*" "docs/*" "scripts/*"

      - name: Upload build artifact
        uses: actions/upload-artifact@v4
        with:
          name: mokowaas-build
          path: mokowaas_ci_build.zip
```

### 8.2 Release Workflow (`.github/workflows/release.yml`)

```yaml
name: Release MokoWaaS

on:
  push:
    tags:
      - 'v*'

jobs:
  publish-release:
    runs-on: ubuntu-latest

    steps:
      - name: Checkout repository
        uses: actions/checkout@v4

      - name: Download build artifact
        uses: actions/download-artifact@v4
        with:
          name: mokowaas-build
          path: ./dist

      - name: Create GitHub Release
        uses: softprops/action-gh-release@v2
        with:
          files: |
            dist/mokowaas_ci_build.zip
        env:
          GITHUB_TOKEN: ${{ secrets.GITHUB_TOKEN }}
```

### 8.3 Governance Expectations

* All changes to workflow files must follow the same review process as application code.
* Workflows must implement the validation steps described in this guide (headers, manifest, structure, syntax).
* Any new automated action should be documented and traceable via commit history and release notes.

---

## 9. Extended Release Checklist

This checklist is narrowed to **workflow linked actions only**, ensuring alignment with CI/CD enforcement and automation.

### Before Build (Workflow Linked)

* [ ] GitHub Actions workflow passes all checks
* [ ] Manifest version synchronized automatically
* [ ] Metadata header validation workflow completes
* [ ] Folder structure and required file presence validated by CI

### During Build (Workflow Linked)

* [ ] Packaging job completes in Actions
* [ ] CI artifact successfully generated
* [ ] Automated syntax checks (PHP, XML, INI) pass

### After Build (Workflow Linked)

* [ ] Release pipeline triggers
* [ ] Git tag created by workflow
* [ ] Artifact uploaded to release via Actions
* [ ] Workflow posts release summary

---

## 11. Dependency Validation Rules

To prevent runtime failures, validate the following prior to packaging:

### 11.1 Joomla Compatibility

* Confirm plugin uses only supported Joomla API calls
* Validate absence of deprecated function references

### 11.2 PHP Compatibility

* All code must comply with PHP 8.1+ syntax
* No short tags, deprecated globals, or removed extensions

### 11.3 File Presence Validation

Required files:

* `mokowaas.xml`
* `mokowaas.php`
* `services/provider.php`
* Language files under `language/en-GB/`
* LICENSE.md
* README.md
* CONTRIBUTING.md

### 11.4 Optional Runtime Dependencies

If services, traits, or helpers are referenced, verify all matching files exist.

---

## 12. Template Version Synchronization Process

Templates and plugins must remain synchronized to avoid inconsistencies.

### 12.1 Synchronization Workflow

1. Update plugin terminology keys
2. Sync updated keys with template language files
3. Verify template overrides do not shadow plugin output
4. Validate layout consistency in UI

### 12.2 Alignment Rules

* Template updates must follow plugin updates, not precede them
* Template releases must specify minimum supported plugin version
* Plugin release notes must reference any required template updates

---

## Revision History

| Date       | Author                          | Description                     |
| ---------- | ------------------------------- | ------------------------------- |
| 2025-12-11 | Jonathan Miller (@jmiller-moko) | Initial creation of build guide |
