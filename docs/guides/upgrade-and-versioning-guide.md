<!--
 Copyright (C) 2025 Moko Consulting <hello@mokoconsulting.tech>
 This file is part of a Moko Consulting project.
 SPDX-LICENSE-IDENTIFIER: GPL-3.0-or-later
 This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 3 of the License, or (at your option) any later version.
 This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the IMPLIED WARRANTY of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 You should have received a copy of the GNU General Public License (./LICENSE.md).

 # FILE INFORMATION
 DEFGROUP: Joomla.Plugin
 INGROUP: MokoWaaSBrand.Guides
 REPO: https://github.com/mokoconsulting-tech/mokowaasbrand
 VERSION: 01.04.00
 PATH: /docs/guides/upgrade-and-versioning-guide.md
 BRIEF: Guide for updating, versioning, and maintaining the MokoWaaSBrand plugin
 NOTE: Defines release flow, version rules, and upgrade validation
-->

# MokoWaaSBrand Upgrade and Versioning Guide (VERSION: 01.04.00)

## Introduction

The MokoWaaSBrand Upgrade and Versioning Guide establishes a consistent lifecycle management process for the plugin across WaaS governed environments. By defining clear versioning rules, upgrade requirements, and governance commitments, this guide ensures stability and predictable branding behavior throughout the platform.

## Versioning Standards

The plugin uses a semantic versioning model aligned with WaaS operational governance. Each segment communicates functional impact and expected deployment considerations.

### Version Structure

`01.03.00`

* **MAJOR**: Introduces changes that affect platform wide branding structure, architecture, or identity rules.
* **MINOR**: Adds new features, terminology mappings, UX updates, or configuration options.
* **PATCH**: Corrects bugs, updates language strings, or performs low impact adjustments.

This structure ensures operators understand the significance of each release.

## Upgrade Workflow

A well defined upgrade process reduces operational risk and ensures consistent branding enforcement.

### Pre Upgrade Validation

Before applying a new release:

1. Validate compatibility with:

	 * Joomla core version
	 * WaaS template version
	 * Language pack versions
2. Review release notes and change logs.
3. Capture an environment snapshot or backup.
4. Confirm that dependent extensions are ready for upgrade sequencing.

### Installing an Update

1. Navigate to **System > Extensions > Install**.
2. Upload the updated plugin package.
3. Confirm successful installation.
4. Validate the displayed plugin version.

### Post Upgrade Validation

After update:

* Confirm branding and terminology consistency across administrative surfaces.
* Validate template behavior.
* Monitor logs for warnings or conflicts.
* Ensure no override files reintroduce outdated terminology.

## Release Governance

Versioning and rollout require alignment across multiple teams.

### Development Teams

* Implement changes and fixes.
* Tag releases using semantic rules.
* Provide documentation, changelogs, and upgrade notes.

### WaaS Platform Operations

* Validate releases in staging.
* Approve and coordinate production rollout.
* Maintain version inventories and update paths.

### Governance and Brand Teams

* Approve major branding modifications.
* Ensure updates comply with identity management rules.
* Review terminology changes prior to deployment.

## Rollback Protocol

If unexpected issues or instability occur:

1. Disable the plugin via the Joomla Plugin Manager.
2. Revert to the last validated version.
3. Clear Joomla and browser caches.
4. Restore from environment backup if needed.
5. Document incident and remediation for future governance.

## Preventative Practices

To minimize disruptions:

* Maintain version parity across templates, plugins, and language packs.
* Avoid modifying plugin files outside formal release cycles.
* Confirm updates in staging environments before production deployment.
* Periodically audit template overrides for outdated terminology.

## Revision History

| Date       | Author                          | Description                                 |
| ---------- | ------------------------------- | ------------------------------------------- |
| 2025-12-11 | Jonathan Miller (@jmiller-moko) | Full rewrite and update to version 01.03.00 |
