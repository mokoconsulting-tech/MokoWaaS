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
 REPO: https://github.com/mokoconsulting-tech/mokowaasbrand
 PATH: /docs/plugin-basic.md
 VERSION: 01.03.00
 BRIEF: Baseline documentation for the MokoWaaS-Brand system plugin
 NOTE: Foundational reference for internal and external stakeholders
-->

# MokoWaaS-Brand Plugin Overview (VERSION: 01.03.00)

## Introduction

The MokoWaaS-Brand plugin is a foundational system component used across WaaS-managed Joomla environments. It ensures consistent application of platform identity, terminology, and user experience standards. By centralizing key branding functions, the plugin supports multi‑tenant WaaS operations and reduces administrative fragmentation.

## Role in the WaaS Platform

The plugin establishes a unified naming and branding layer across administrator and user interfaces. As the primary enforcement point for WaaS branding policy, it integrates with templates, modules, and language packs to maintain consistent terminology and presentation.

Key functions include:

* Replacing Joomla-native labels with WaaS-approved terminology.
* Ensuring consistent visual identifiers in administrative interfaces.
* Providing a stable branding baseline consumed by other system extensions.

## System Requirements

To ensure correct operation, the plugin requires:

* Joomla 5.x or higher
* PHP 8.1 or higher
* A compatible WaaS template aligned with Moko platform standards
* System-level plugin execution priority before template rendering

## Installation Overview

The plugin is deployed through the Joomla Extension Manager.

High‑level installation flow:

1. Upload the distributed plugin package.
2. Validate manifest metadata for accuracy and compliance.
3. Enable the plugin under System Plugins.
4. Clear system and browser caches to ensure fresh language loads.

## Configuration Overview

The plugin provides configurable controls under the Joomla Plugin Manager.

Primary configuration categories include:

* **Terminology Controls:** Apply standardized WaaS vocabulary.
* **UI Adjustments:** Modify display elements such as headers or default labels.
* **Visibility Controls:** Suppress or replace Joomla identifiers as needed.
* **Branding Elements:** Manage powered‑by references and footer behavior.

Configuration ensures a consistent and predictable WaaS identity across all managed sites.

## Operational Expectations

Platform operators should maintain the plugin in an enabled state at all times. Updates may affect downstream systems such as templates or modules, so operational workflows must include:

* Version alignment across branding components
* Review of template overrides for conflict prevention
* Coordination with WaaS governance for terminology changes

## Constraints and Considerations

While the plugin provides broad branding coverage, certain constraints apply:

* Third‑party extensions may require additional overrides
* Joomla core updates may introduce changes requiring terminology refresh
* Legacy templates may require refactoring to fully adopt standardized naming

## Revision History

| Date       | Author                          | Description                  |
| ---------- | ------------------------------- | ---------------------------- |
| 2025-12-11 | Jonathan Miller (@jmiller-moko) | Rewrite for version 01.03.00 |
