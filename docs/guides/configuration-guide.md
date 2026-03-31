<!--
 Copyright (C) 2025 Moko Consulting <hello@mokoconsulting.tech>
 This file is part of a Moko Consulting project.
 SPDX-LICENSE-IDENTIFIER: GPL-3.0-or-later
 This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 3 of the License, or (at your option) any later version.
 This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the IMPLIED WARRANTY of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 You should have received a copy of the GNU General Public License (./LICENSE.md).

 # FILE INFORMATION
 DEFGROUP: Joomla.Plugin
 INGROUP: MokoWaaS.Guides
 REPO: https://github.com/mokoconsulting-tech/mokowaas
 VERSION: 02.00.00
 PATH: /docs/guides/configuration-guide.md
 BRIEF: Configuration guide for the MokoWaaS system plugin
 NOTE: Defines plugin parameters, expected behaviors, and recommended defaults
-->

# MokoWaaS Configuration Guide (VERSION: 02.00.00)

## 1. Objective

This guide outlines the configuration parameters available within the MokoWaaS system plugin and establishes recommended defaults for WaaS governed environments. Proper configuration ensures consistent branding behavior across templates, modules, and administrative surfaces.

## 2. Accessing Plugin Configuration

1. Log in to Joomla Administrator.
2. Navigate to **System > Plugins**.
3. Search for **MokoWaaS**.
4. Select the plugin name to open the configuration panel.

## 3. Plugin Parameters

### 3.1 Enable Branding

| Property | Value |
| -------- | ----- |
| Field name | `enable_branding` |
| Type | Radio (Yes/No) |
| Default | Yes |

Master switch for all branding overrides. When disabled, no language overrides are applied and the Joomla interface reverts to its default strings.

### 3.2 Brand Name

| Property | Value |
| -------- | ----- |
| Field name | `brand_name` |
| Type | Text |
| Default | `MokoWaaS` |

The brand name that replaces "Joomla" throughout the interface. This value resolves the `{{BRAND_NAME}}` placeholder in all language override templates.

**Affected areas:**
* Admin and site footer ("Powered by …")
* Control panel greetings
* Quick icon status messages
* System info and version labels
* Installer and update component text
* Error pages and system messages
* Privacy component headings

### 3.3 Company Name

| Property | Value |
| -------- | ----- |
| Field name | `company_name` |
| Type | Text |
| Default | `Moko Consulting` |

Your company name, used in support links and attribution. Resolves the `{{COMPANY_NAME}}` placeholder.

**Affected areas:**
* Admin login support links (forum, documentation, news)
* Frontend login support links

### 3.4 Support URL

| Property | Value |
| -------- | ----- |
| Field name | `support_url` |
| Type | URL |
| Default | `https://mokoconsulting.tech` |

URL for support and documentation links. Resolves the `{{SUPPORT_URL}}` placeholder.

**Affected areas:**
* Dashboard welcome message links
* Documentation and support links

## 4. How Overrides Work

MokoWaaS uses a two-layer override system:

### 4.1 Runtime Resolution (Primary)

On every page load, the plugin reads override template files shipped with the plugin, resolves `{{BRAND_NAME}}`, `{{COMPANY_NAME}}`, and `{{SUPPORT_URL}}` from plugin params, and injects the resolved strings into Joomla's Language object.

**Effect:** Changing the brand name in plugin config takes effect on the next page load — no reinstall needed.

### 4.2 Install-time Resolution (Fallback)

During install/update, the install script resolves placeholders and writes the result into Joomla's global language override files inside a sentinel block:

```ini
; ===== BEGIN MokoWaaS Overrides (do not edit this block) =====
; Auto-generated on 2026-03-31 12:00:00 — do not edit manually.
TPL_ATUM_POWERED_BY="Powered by MokoWaaS"
...
; ===== END MokoWaaS Overrides =====
```

Existing overrides outside this block are never touched. On uninstall, only the MokoWaaS block (and any legacy stray keys) are removed.

## 5. Override Coverage

### 5.1 Admin Overrides (administrator/language/overrides/)

| Section | Keys | Description |
| ------- | ---- | ----------- |
| Footer & template | 2 | Powered by text in Atum template |
| Control panel | 5 | Welcome messages, beginners guide, stats |
| Help/Docs | 2 | Help site labels |
| Generic | 3 | Defaults, package type, library name |
| System messages | 2 | Error and field labels |
| Admin login | 4 | Login support links and page title |
| Error messages | 1 | Generic error layout |
| Admin branding | 4 | Control panel title, module/plugin headings |
| Extensions | 2 | Installer type and success message |
| Quick Icons | 3 | Update check status messages |
| System Info | 3 | Version, help, compat plugin |
| Installer | 5 | Upload, warnings, update notices |
| Global Config | 1 | Meta version label |
| Update component | 11 | Titles, descriptions, status messages |
| Privacy | 1 | Core capabilities heading |
| Library errors | 2 | Minimum version, XML setup file |
| Version/About | 3 | Powered by, documentation, support |

### 5.2 Site/Frontend Overrides (language/overrides/)

| Section | Keys | Description |
| ------- | ---- | ----------- |
| Footer & template | 2 | Powered by text in Cassiopeia template |
| Generic | 2 | Defaults, library name |
| System messages | 2 | Error and field labels |
| Error messages/pages | 4 | Error layout, 404, generic errors |
| Installer/Sample data | 6 | Site name, sample data sets |
| Login support | 3 | Forum, documentation, news links |
| Site offline | 1 | Maintenance message |
| Version/About | 1 | Powered by text |

## 6. Configuration Change Workflow

To ensure continuity across managed environments:

1. Document the change request.
2. Apply updates in a staging environment.
3. Validate branding presentation across admin and frontend.
4. Promote changes to production following WaaS change controls.

## 7. Troubleshooting Configuration Issues

* If changes do not appear, clear Joomla and browser cache.
* Confirm that no template override is superseding plugin outputs.
* Review logs for load order conflicts.
* Confirm that extension priority does not conflict with other system plugins.
* If brand name change doesn't take effect, verify the plugin is enabled and the `enable_branding` param is set to Yes.

## 8. Validation Checklist

* Brand name appears consistently across all administrator screens.
* Company name appears in login support links.
* Support URL points to correct destination.
* No "Joomla" identifiers remain in overridden locations.
* Frontend and backend output align with configured brand values.
* Existing site language overrides (outside the MokoWaaS block) are preserved.

## Revision History

| Version  | Date       | Author                          | Description                                    |
| -------- | ---------- | ------------------------------- | ---------------------------------------------- |
| 01.02.00 | 2025-12-11 | Jonathan Miller (@jmiller-moko) | Initial standalone configuration guide created |
| 02.00.00 | 2026-03-31 | Jonathan Miller (@jmiller-moko) | Template-based overrides, configurable brand name/company/URL, expanded override coverage |
