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
 PATH: /docs/guides/configuration-guide.md
 BRIEF: Configuration guide for the MokoWaaSBrand system plugin
 NOTE: Defines plugin parameters, expected behaviors, and recommended defaults
-->

# MokoWaaSBrand Configuration Guide (VERSION: 01.04.00)

## 1. Objective

This guide outlines the configuration parameters available within the MokoWaaSBrand system plugin and establishes recommended defaults for WaaS governed environments. Proper configuration ensures consistent branding behavior across templates, modules, and administrative surfaces.

## 2. Accessing Plugin Configuration

1. Log in to Joomla Administrator.
2. Navigate to **System > Plugins**.
3. Search for **MokoWaaSBrand**.
4. Select the plugin name to open the configuration panel.

## 3. Configuration Sections

The plugin provides several configuration areas designed to control branding behavior.

### 3.1 Brand Terminology Controls

These toggles replace Joomla native labels with WaaS aligned naming conventions.

Use cases include:

* Standardizing administrator UI language
* Reinforcing WaaS platform identity across components

Recommended Default: **Enabled**

### 3.2 Footer and Attribution Controls

Controls platform level branding elements including:

* Footer identification
* Powered By statements
* Attribution placements

Recommended Default: **Enabled**

### 3.3 Visibility Controls

Options for concealing or modifying native Joomla identifiers when appropriate.

Examples include:

* Hiding Joomla version labels
* Suppressing default metadata references

Recommended Default: **Enabled** for WaaS managed sites.

### 3.4 Rendering Enhancements

Controls adjustments to UI elements to ensure consistent presentation.

Examples:

* Header text alignment
* Replacement of naming strings

Recommended Default: **Enabled**

## 4. Configuration Change Workflow

To ensure continuity across managed environments:

1. Document the change request.
2. Apply updates in a staging environment.
3. Validate branding presentation.
4. Promote changes to production following WaaS change controls.

## 5. Troubleshooting Configuration Issues

* If changes do not appear, clear Joomla and browser cache.
* Confirm that no template override is superseding plugin outputs.
* Review logs for load order conflicts.
* Confirm that extension priority does not conflict with other system plugins.

## 6. Validation Checklist

* Branding reads consistently across administrator screens.
* No Joomla specific identifiers remain where replacement is expected.
* Frontend and backend output align with WaaS design expectations.

## Revision History

| Version  | Date       | Author                          | Description                                    |
| -------- | ---------- | ------------------------------- | ---------------------------------------------- |
| 01.02.00 | 2025-12-11 | Jonathan Miller (@jmiller-moko) | Initial standalone configuration guide created |
