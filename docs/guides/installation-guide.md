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
 VERSION: 01.04.00
 PATH: /docs/guides/installation-guide.md
 BRIEF: Installation guide for the MokoWaaS system plugin
 NOTE: First document in the guide set
-->

# MokoWaaS Installation Guide (VERSION: 01.04.00)

## Introduction

The MokoWaaS Installation Guide provides the authoritative process for deploying the system plugin within WaaS-managed Joomla environments. The installation ensures consistent application of MokoWaaS branding policy, identity governance, and terminology alignment across all administrative interfaces.

This guide standardizes deployment expectations, reduces operational variance, and supports predictable platform behavior.

## Requirements

Before installation, ensure the following conditions are met:

* Joomla 5.x operational environment
* PHP 8.1 or higher
* Administrative access credentials
* Validated MokoWaaS plugin package from an approved release channel
* Recommended: environment snapshot or backup prior to installation

## Obtaining the Package

To maintain integrity and compliance:

1. Acquire the plugin package from the official MokoConsulting repository or release channel.
2. Validate package checksum or digital signature if provided.
3. Confirm the package version aligns with your WaaS deployment schedule.

## Installation Steps

Follow these steps to install the plugin:

1. Log in to the Joomla Administrator dashboard.
2. Navigate to **System > Extensions > Install**.
3. Choose **Upload Package File**.
4. Upload the MokoWaaS plugin package.
5. Confirm successful installation in the extension status message.

## Activation

After installation, the plugin must be activated:

1. Navigate to **System > Plugins**.
2. Search for **MokoWaaS**.
3. Confirm the plugin type is **System**.
4. Set status to **Enabled**.
5. Save and close.

## Post Installation Validation

To ensure proper activation and system compatibility, verify the following:

* MokoWaaS branding appears in the administrator footer.
* Terminology updates apply consistently across admin UI.
* No conflicts with templates, overrides, or extensions.
* Joomla and PHP logs show no errors related to the plugin.

## Rollback Procedure

If the plugin introduces issues or conflicts:

1. Disable the plugin via **System > Plugins**.
2. Clear Joomla and browser cache.
3. Test affected interfaces and confirm restoration of stability.
4. Restore environment snapshot if the issue persists.

## Revision History

| Date       | Author                          | Description                  |
| ---------- | ------------------------------- | ---------------------------- |
| 2025-12-11 | Jonathan Miller (@jmiller-moko) | Rewrite for version 01.03.00 |
