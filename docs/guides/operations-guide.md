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
 PATH: /docs/guides/operations-guide.md
 BRIEF: Operational guide for administering and managing the MokoWaaSBrand system plugin
 NOTE: Defines lifecycle, responsibilities, and operational behaviors
-->

# MokoWaaSBrand Operations Guide (VERSION: 01.04.00)

## Introduction

The MokoWaaSBrand Operations Guide defines how the plugin is managed across WaaS governed Joomla environments. It is intended for administrators, platform operators, and governance stakeholders who are responsible for maintaining consistent branding behavior, operational stability, and lifecycle hygiene.

This document focuses on day to day responsibilities, monitoring expectations, and coordination points with other parts of the WaaS platform.

## Operational Scope

The MokoWaaSBrand plugin operates as a system level extension that enforces WaaS branding, terminology, and identity across administrative user interfaces. Because it runs early in the request lifecycle, it requires explicit operational oversight to ensure:

* Consistent behavior after template or core updates
* Stable interaction with other system plugins
* Alignment with WaaS branding policy and governance

## Roles and Responsibilities

### WaaS Platform Administrators

* Maintain the plugin at the approved version for each environment
* Validate branding consistency following platform or template changes
* Coordinate with development teams on rollout of new features or terminology

### Governance and Brand Owners

* Approve changes to WaaS terminology or visible branding
* Review that the plugin’s behavior aligns with documented brand guidelines
* Provide input for configuration changes that affect end user perception

### Development and Release Teams

* Implement and test changes to the plugin
* Prepare release artifacts and update documentation
* Coordinate rollout sequencing with templates, modules, and language packs

## Routine Operational Tasks

### Branding Consistency Reviews

Performed when:

* Joomla core is updated
* Templates or overrides change
* Language packs are updated or replaced

Administrators should verify that:

* Branding remains consistent across key administrator views
* No reverted or legacy Joomla labels reappear

### Cache Management

To avoid stale UI output:

* Clear Joomla cache after plugin updates
* Clear CDN or reverse proxy cache where applicable
* Encourage administrators to hard refresh their browsers after major changes

### Change Logging

For each operational change involving the plugin:

* Record the change type (configuration, version, dependency)
* Record the timestamp and operator
* Capture any observed side effects during or after the change

## Monitoring and Alerts

Operational logging and monitoring should focus on:

* Plugin initialization or load order errors
* Warnings related to language strings or missing constants
* UI rendering anomalies reported by administrators

Recommended monitoring sources:

* Joomla Administrator logs
* Web server and PHP error logs
* Centralized WaaS logging and observability tools where available

## Maintenance Lifecycle

### Scheduled Maintenance

During planned maintenance windows:

* Validate that branding and terminology still match WaaS standards
* Confirm that newly deployed templates or components do not conflict with plugin output
* Review configuration settings to ensure they align with current policy

### Emergency Maintenance

If the plugin negatively affects platform stability or administrator usability:

1. Temporarily disable the plugin in the Joomla Plugin Manager.
2. Clear Joomla and application caches.
3. Validate that core navigation and access remain functional.
4. Engage development or governance teams to determine remediation steps.

## Dependency and Compatibility Considerations

* Template overrides may change how or where branding appears.
* Joomla updates may introduce new strings that require plugin or language updates.
* Third party system plugins may alter the same UI surfaces and require coordination.

Administrators should factor these dependencies into maintenance and upgrade planning.

## Revision History

| Date       | Author                          | Description                  |
| ---------- | ------------------------------- | ---------------------------- |
| 2025-12-11 | Jonathan Miller (@jmiller-moko) | Rewrite for version 01.03.00 |
