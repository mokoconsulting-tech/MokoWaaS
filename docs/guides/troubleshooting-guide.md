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
 VERSION: 01.05.00
 PATH: /docs/guides/troubleshooting-guide.md
 BRIEF: Troubleshooting guide for diagnosing and resolving issues related to the MokoWaaSBrand plugin
 NOTE: Designed for administrators and WaaS operations teams
-->

# MokoWaaSBrand Troubleshooting Guide (VERSION: 01.05.00)

## Introduction

The MokoWaaSBrand Troubleshooting Guide provides a structured, repeatable approach for diagnosing and resolving issues related to branding enforcement across WaaS managed Joomla environments. It assists administrators, support engineers, and operations staff in identifying symptoms, validating root causes, and restoring consistent platform behavior.

This guide focuses on actionable diagnostics, minimizing downtime, and ensuring that WaaS branding policy is applied consistently.

## Understanding the Plugin’s Operational Behavior

As a system level extension, the MokoWaaSBrand plugin:

* Loads early in the Joomla lifecycle
* Influences visible terminology and branding markers
* Interacts with templates, overrides, and language constants
* Depends on correct cache behavior and language file integrity

Issues typically arise from conflicts, outdated overrides, or environmental configuration rather than the plugin itself.

## Common Issues and Resolutions

### Branding Not Updating

Branding appears unchanged or reverts to Joomla defaults.

**Likely Causes:**

* Joomla cache not cleared
* Browser cache holding stale assets
* Template overrides overriding plugin output
* Language file conflicts or outdated strings

**Resolution:**

1. Clear Joomla cache entirely.
2. Test in a private browser session.
3. Inspect template override directories for conflicting files.
4. Confirm plugin is enabled.
5. Validate plugin load order.

---

### Missing or Incorrect Terminology

Labels or UI strings do not match expected WaaS terminology.

**Likely Causes:**

* Outdated or missing language packs
* Third party extensions overriding key strings
* Joomla updates introducing new terminology

**Resolution:**

1. Validate the integrity of all language files.
2. Check extension overrides.
3. Reapply updated MokoWaaSBrand language packs.
4. Review recent Joomla updates for changes in language constants.

---

### UI Rendering Issues

Visual inconsistencies or broken layouts.

**Likely Causes:**

* Template level CSS conflicts
* Third party system plugins modifying DOM or rendering pipelines
* Outdated or incompatible template overrides

**Resolution:**

1. Switch temporarily to a default template.
2. Inspect and test CSS priority.
3. Disable third party plugins one at a time to isolate conflicts.

---

## Diagnostic Tools and Logs

Effective diagnosis depends on reviewing the correct system logs.

### Joomla Logs

Monitor for:

* Plugin initialization warnings
* Deprecated methods
* Handler conflicts between extensions

### PHP Error Logs

Check for:

* Undefined constants or missing language keys
* Fatal or recoverable errors in plugin lifecycle hooks

### Browser Developer Tools

Useful for detecting:

* JavaScript conflicts affecting admin UI
* Missing or misrouted asset loads
* DOM rendering issues

---

## Escalation Workflow

If your troubleshooting steps do not resolve the issue:

1. Document observed symptoms and any steps already taken.
2. Capture relevant logs, console messages, and screenshots.
3. Escalate to WaaS operations or development teams.
4. Include environmental details such as:

   * Joomla version
   * MokoWaaSBrand plugin version
   * Template version
   * Installed third party extensions

---

## Preventative Practices

To reduce incidents and ensure operational stability:

* Maintain version alignment across templates, plugins, and language packs.
* Test all changes in a staging environment.
* Enforce change control for branding or terminology adjustments.
* Remove legacy template overrides that duplicate plugin functionality.

---

## Revision History

| Date       | Author                          | Description                               |
| ---------- | ------------------------------- | ----------------------------------------- |
| 2026-02-26 | GitHub Copilot                  | Update for version 01.05.00               |
| 2025-12-11 | Jonathan Miller (@jmiller-moko) | Full rewrite and update to version 01.03.00 |
