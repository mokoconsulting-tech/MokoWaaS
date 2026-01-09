<!--
 Copyright (C) 2025 Moko Consulting <hello@mokoconsulting.tech>
 This file is part of a Moko Consulting project.
 SPDX-LICENSE-IDENTIFIER: GPL-3.0-or-later
 This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 3 of the License, or (at your option) any later version.
 This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the IMPLIED WARRANTY of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 You should have received a copy of the GNU General Public License (./LICENSE.md).

 # FILE INFORMATION
 DEFGROUP: Joomla.Plugin
 INGROUP: MokoWaaS-Brand.Guides
 REPO: https://github.com/mokoconsulting-tech/mokowaasbrand
 VERSION: 01.03.00
 PATH: /docs/guides/rollback-and-recovery-guide.md
 BRIEF: Rollback and recovery guide for restoring stable operation after plugin related incidents
 NOTE: Completes the core guide set for WaaS plugin governance
-->

# MokoWaaS-Brand Rollback and Recovery Guide (VERSION: 01.03.00)

## Introduction

The Rollback and Recovery Guide defines the procedures required to restore a stable operational state when the MokoWaaS-Brand plugin introduces issues or when an environment must revert to a previously validated condition. It ensures WaaS administrators, incident responders, and platform operators have a consistent and predictable process during incidents.

Rollback and recovery are essential components of WaaS governance, reducing downtime and ensuring branding and UI consistency across environments.

## When to Initiate Rollback

Rollback should be initiated when any of the following conditions occur:

* Branding inconsistencies or terminology reversions
* UI rendering problems that impair administrator functionality
* Plugin updates introduce instability or regression
* Language conflicts or missing terminology keys
* Critical plugin or PHP errors appear in logs

These symptoms indicate that immediate containment and structured recovery are necessary.

## Immediate Containment Actions

To prevent further disruption:

1. Disable the MokoWaaS-Brand plugin via **System > Plugins**.
2. Clear Joomla cache.
3. Retest impacted areas to confirm whether disabling stabilizes behavior.
4. Review Joomla and PHP logs for indicators of root cause.

Containment limits the blast radius while enabling structured diagnosis.

## Full Rollback Procedure

### Restoring a Prior Plugin Version

1. Locate the last validated plugin release package.
2. Navigate to **System > Extensions > Install**.
3. Upload and reinstall the previous version.
4. Confirm proper installation via the extension manager.
5. Validate that branding and UI behavior return to expected baselines.

### Environment Snapshot Restoration

If plugin version rollback does not resolve the issue:

1. Restore the environment snapshot taken before the change.
2. Validate compatibility across templates, plugins, and language packs.
3. Reenable the plugin.
4. Retest critical paths and administrator workflows.

Snapshots provide a guaranteed restoration point for complex failures.

## Recovery Validation

Once recovery steps are complete:

* Ensure branding matches WaaS identity guidelines.
* Confirm no plugin initialization or load order errors.
* Validate terminology strings across admin surfaces.
* Verify stable rendering of the administrator dashboard.

Recovery is not complete unless all validation points succeed.

## Post Incident Documentation

All rollback or recovery events must be documented, including:

* Plugin version(s) involved
* Summary of symptoms and operational impact
* Detailed rollback actions taken
* Root cause indicators or contributing factors
* Recommendations for preventing recurrence

Documentation improves platform resilience and informs future release governance.

## Preventative Strategies

To reduce the likelihood of rollback events:

* Test all plugin and template updates in staging before production rollout
* Maintain version synchronization across branding related assets
* Acquire plugin builds only from approved WaaS release channels
* Enforce strict change control and governance for branding updates
* Audit template overrides regularly to avoid conflicts

These strategies improve long term WaaS platform stability.

## Revision History

| Date       | Author                          | Description                                 |
| ---------- | ------------------------------- | ------------------------------------------- |
| 2025-12-11 | Jonathan Miller (@jmiller-moko) | Full rewrite and update to version 01.03.00 |
