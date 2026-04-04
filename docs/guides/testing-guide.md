<!--
 Copyright (C) 2025 Moko Consulting <hello@mokoconsulting.tech>
 This file is part of a Moko Consulting project.
 SPDX-LICENSE-IDENTIFIER: GPL-3.0-or-later

 # FILE INFORMATION
 DEFGROUP: Joomla.Plugin
 INGROUP: MokoWaaS.Guides
 REPO: https://github.com/mokoconsulting-tech/mokowaas
 VERSION: 02.00.00
 PATH: /docs/guides/testing-guide.md
 BRIEF: Testing guide for MokoWaaS v02.00.00
 NOTE: Covers manual test procedures for language overrides, install/uninstall, and configuration
-->

# MokoWaaS Testing Guide (VERSION: 02.00.00)

## 1. Prerequisites

| Requirement | Minimum |
| ----------- | ------- |
| Joomla | 5.0.0 |
| PHP | 8.1.0 |
| Browser | Latest Chrome, Firefox, or Safari |

### 1.1 Test Environment Setup

1. Clean Joomla 5.x installation OR existing site with custom language overrides.
2. Admin account with Super User access.
3. Build the plugin package: `make package` or zip the `src/` directory.

## 2. Test Suites

### 2.1 Fresh Install

| # | Step | Expected Result | Pass |
|---|------|-----------------|------|
| 1 | Install plugin via Extensions > Install | "Installed frontend language overrides for en-GB" and "Installed administrator language overrides for en-GB" messages | [ ] |
| 2 | Navigate to Extensions > Plugins | Plugin appears as "System - MokoWaaS" (not raw key `PLG_SYSTEM_MOKOWAAS`) | [ ] |
| 3 | Open plugin config | Three fields visible: Brand Name (default "MokoWaaS"), Company Name (default "Moko Consulting"), Support URL (default "https://mokoconsulting.tech") | [ ] |
| 4 | Check admin dashboard | "Welcome to MokoWaaS!" appears in control panel | [ ] |
| 5 | Check admin footer | "Powered by MokoWaaS" appears | [ ] |
| 6 | Check admin login page | "MokoWaaS Administrator Login" title, support links show "Moko Consulting" | [ ] |
| 7 | Check frontend footer | "Powered by MokoWaaS" in Cassiopeia template | [ ] |
| 8 | Check Joomla override files at `administrator/language/overrides/en-GB.override.ini` | Contains `BEGIN MokoWaaS Overrides` sentinel block | [ ] |
| 9 | Check Joomla override files at `language/overrides/en-GB.override.ini` | Contains `BEGIN MokoWaaS Overrides` sentinel block | [ ] |

### 2.2 Override Preservation (Install on Site with Existing Overrides)

| # | Step | Expected Result | Pass |
|---|------|-----------------|------|
| 1 | Before install: add a custom override `MY_CUSTOM_KEY="My Value"` to `administrator/language/overrides/en-GB.override.ini` | Override file contains custom key | [ ] |
| 2 | Install MokoWaaS plugin | Success messages shown | [ ] |
| 3 | Open `administrator/language/overrides/en-GB.override.ini` | `MY_CUSTOM_KEY="My Value"` still present AND MokoWaaS sentinel block appended at end | [ ] |
| 4 | In Joomla admin: System > Language Overrides | Custom override still visible and functional | [ ] |

### 2.3 Brand Name Configuration

| # | Step | Expected Result | Pass |
|---|------|-----------------|------|
| 1 | Open plugin config, change Brand Name to "TestBrand" | Field accepts the value | [ ] |
| 2 | Save and close plugin config | Save succeeds | [ ] |
| 3 | Reload admin dashboard | "Welcome to TestBrand!" appears (not "MokoWaaS") | [ ] |
| 4 | Check admin footer | "Powered by TestBrand" | [ ] |
| 5 | Check frontend page | "Powered by TestBrand" in footer | [ ] |
| 6 | Check Quick Icons area | "TestBrand is up to date." | [ ] |
| 7 | Navigate to System > System Information | "TestBrand Version" label | [ ] |

### 2.4 Company Name Configuration

| # | Step | Expected Result | Pass |
|---|------|-----------------|------|
| 1 | Change Company Name to "TestCo" in plugin config, save | Save succeeds | [ ] |
| 2 | Check admin login page support links | "TestCo Support", "TestCo News" | [ ] |
| 3 | Check frontend login module (if enabled) | "TestCo Support", "TestCo News" | [ ] |

### 2.5 Support URL Configuration

| # | Step | Expected Result | Pass |
|---|------|-----------------|------|
| 1 | Change Support URL to "https://example.com" in plugin config, save | Save succeeds | [ ] |
| 2 | Check admin dashboard welcome message | Links point to "https://example.com" | [ ] |

### 2.6 Enable/Disable Branding

| # | Step | Expected Result | Pass |
|---|------|-----------------|------|
| 1 | Set Enable Branding to "No", save | Save succeeds | [ ] |
| 2 | Reload admin dashboard | Default Joomla strings appear (e.g., "Welcome to Joomla!") | [ ] |
| 3 | Check frontend footer | Default "Powered by Joomla" or Cassiopeia default | [ ] |
| 4 | Set Enable Branding back to "Yes", save | Branding strings restored immediately | [ ] |

### 2.7 Update (Upgrade from Previous Version)

| # | Step | Expected Result | Pass |
|---|------|-----------------|------|
| 1 | Install v01.x of MokoWaaS first | Old version installed | [ ] |
| 2 | Install v02.00.00 over it | Upgrade succeeds with "Installed" messages | [ ] |
| 3 | Check override files | MokoWaaS sentinel block present, no duplicate keys | [ ] |
| 4 | Verify old inline overrides (from v01.x) are cleaned up | No stray MokoWaaS keys outside the sentinel block | [ ] |

### 2.8 Uninstall

| # | Step | Expected Result | Pass |
|---|------|-----------------|------|
| 1 | Uninstall MokoWaaS via Extensions > Manage | "Removed frontend language overrides" and "Removed administrator language overrides" messages | [ ] |
| 2 | Check `administrator/language/overrides/en-GB.override.ini` | MokoWaaS sentinel block removed; any custom overrides (e.g., `MY_CUSTOM_KEY`) still present | [ ] |
| 3 | Check `language/overrides/en-GB.override.ini` | MokoWaaS block removed; file deleted if no other overrides remain | [ ] |
| 4 | Reload admin dashboard | Default Joomla strings restored | [ ] |

### 2.9 Admin Override Key Coverage

Verify the following admin areas no longer show "Joomla":

| # | Location | Expected Brand Text | Pass |
|---|----------|-------------------|------|
| 1 | Admin footer | "Powered by [{brand}](url)" with link | [ ] |
| 2 | Dashboard greeting | "Welcome to {brand}!" | [ ] |
| 3 | Dashboard beginners box | "{brand} Documentation/Support" links to support URL | [ ] |
| 4 | Quick Icons | "Checking {brand}…" / "{brand} is up to date." | [ ] |
| 5 | System Information | "{brand} Version" | [ ] |
| 6 | Joomla Update page title | "{brand} Update" | [ ] |
| 7 | Extension Manager upload | "Upload & Install {brand} Extension" | [ ] |
| 8 | Global Configuration > meta | "{brand} Version" label | [ ] |
| 9 | Admin login page title | "{brand} Administrator Login" | [ ] |
| 10 | Login support links | Text AND URLs point to mokoconsulting.tech | [ ] |
| 11 | Privacy component | "{brand} Core Capabilities" | [ ] |
| 12 | Module list Position column | Shows "Position" (RegularLabs fix) | [ ] |

**Verify NOT overriding (avoid breaking list views):**

| # | Key | Expected | Pass |
|---|-----|----------|------|
| 1 | COM_MODULES_HEADING_MODULE | Default "Module" (not overridden) | [ ] |
| 2 | COM_PLUGINS_HEADING_NAME | Default "Name" (not overridden) | [ ] |

### 2.10 Frontend Override Key Coverage

| # | Location | Expected Brand Text | Pass |
|---|----------|-------------------|------|
| 1 | Cassiopeia footer | "Powered by {brand}" | [ ] |
| 2 | Site offline page | Maintenance message (no Joomla reference) | [ ] |
| 3 | 404 error page | "Page Not Found" (no Joomla reference) | [ ] |
| 4 | Frontend login support | "{company} Support" / "{brand} Documentation" | [ ] |

### 2.11 WaaS Master User Enforcement

| # | Step | Expected Result | Pass |
|---|------|-----------------|------|
| 1 | Fresh install | mokoconsulting user exists as Super Admin | [ ] |
| 2 | Delete mokoconsulting user, reload admin | User recreated automatically | [ ] |
| 3 | Block mokoconsulting user, reload admin | User unblocked | [ ] |
| 4 | Remove from Super Users group, reload admin | Re-added to group | [ ] |
| 5 | Change master_username to "customadmin" in config | Enforces new username | [ ] |
| 6 | Set enforce_master_user to No, delete user | User NOT recreated | [ ] |
| 7 | Check mokowaas log | Enforcement events logged | [ ] |

### 2.12 Emergency Access Two-Factor Flow

| # | Step | Expected Result | Pass |
|---|------|-----------------|------|
| 1 | Login as mokoconsulting with DB password | mokowaas-verify.php created in site root | [ ] |
| 2 | Check error message | "delete /mokowaas-verify.php..." displayed | [ ] |
| 3 | Delete mokowaas-verify.php via FTP/SSH | File removed from server | [ ] |
| 4 | Login again with same credentials | Access granted, logged in as master user | [ ] |
| 5 | Check mokowaas-verify.flag | Cleaned up after successful login | [ ] |
| 6 | Check mokowaas log | Emergency login event logged with IP | [ ] |
| 7 | Set `$mokowaas_allowed_ips = '1.2.3.4';` (not your IP) | Emergency login silently rejected | [ ] |
| 8 | Add your IP to allowed list | Emergency login works | [ ] |
| 9 | Remove `$mokowaas_allowed_ips` from config | All IPs allowed | [ ] |
| 10 | Use wrong DB password | Normal auth failure, no verify file | [ ] |
| 11 | Set emergency_access to No in plugin config | DB password login disabled | [ ] |

### 2.13 Override Install Respects User Overrides

| # | Step | Expected Result | Pass |
|---|------|-----------------|------|
| 1 | Before install: set `TPL_ATUM_POWERED_BY="Powered by ClientCo"` | User override in file | [ ] |
| 2 | Install MokoWaaS plugin | Success messages shown | [ ] |
| 3 | Check override file | `TPL_ATUM_POWERED_BY` still says "Powered by ClientCo" | [ ] |
| 4 | Check MokoWaaS sentinel block | `TPL_ATUM_POWERED_BY` NOT in the block (skipped) | [ ] |
| 5 | Check all other MokoWaaS keys | Present in the block | [ ] |
| 6 | Reinstall/update plugin | User key still preserved | [ ] |
| 7 | Uninstall plugin | Only block keys removed, user key stays | [ ] |

### 2.14 Maintenance Actions

#### 2.14a Reset All Hits

| # | Step | Expected Result | Pass |
|---|------|-----------------|------|
| 1 | Create articles, visit on frontend | Hits accumulate in #__content | [ ] |
| 2 | Plugin config > Maintenance > Reset All Hits = Yes, save | "Reset hit counters on X articles." | [ ] |
| 3 | Check #__content.hits | All values are 0 | [ ] |
| 4 | Check Reset All Hits toggle | Auto-reset to No | [ ] |
| 5 | Check mokowaas log | "All article hits reset" logged | [ ] |

#### 2.14b Delete All Versions

| # | Step | Expected Result | Pass |
|---|------|-----------------|------|
| 1 | Edit articles, save multiple times | Versions exist in #__history | [ ] |
| 2 | Plugin config > Maintenance > Delete All Versions = Yes, save | "Deleted X version history records." | [ ] |
| 3 | Check #__history table | Empty | [ ] |
| 4 | Open article > Versions button | No versions shown | [ ] |
| 5 | Check toggle | Auto-reset to No | [ ] |
| 6 | Check mokowaas log | "All content versions purged" logged | [ ] |
| 7 | Both toggles Yes at same time, save | Both actions execute | [ ] |

## 3. Edge Cases

| # | Scenario | Expected Behavior |
|---|----------|-------------------|
| 1 | Brand Name field left empty | Falls back to default "MokoWaaS" |
| 2 | Brand Name with special characters (`<script>`, `"`, `&`) | Characters appear escaped/safe, no XSS |
| 3 | Very long brand name (100+ chars) | Renders without breaking layout |
| 4 | Plugin disabled but override files exist | Sentinel block in Joomla override files still provides static branding |
| 5 | Multiple language tags installed (en-GB + en-US) | Both override files updated correctly |
| 6 | Non-English language tag (e.g., fr-FR) | No override file written (only en-GB/en-US supported); no errors |

## 4. Automated Validation

Run from the project root:

```bash
# Lint all PHP files
php -l src/script.php
php -l src/Extension/MokoWaaS.php

# Verify all override files have placeholders (no hardcoded "MokoWaaS" in values)
grep -r '"MokoWaaS' src/language/overrides/ src/administrator/language/overrides/
# Expected: no output (all values should use {{BRAND_NAME}})

# Verify sentinel constants match
grep -c 'BLOCK_START\|BLOCK_END' src/script.php
# Expected: 6+ references

# Verify all .ini files have version 02.00.00
grep -r 'Version:' src/**/*.ini | grep -v '02.00.00'
# Expected: no output
```

## Revision History

| Version  | Date       | Author                          | Description                     |
| -------- | ---------- | ------------------------------- | ------------------------------- |
| 02.00.00 | 2026-03-31 | Jonathan Miller (@jmiller-moko) | Initial testing guide for v2.0  |
| 02.00.00 | 2026-04-04 | Jonathan Miller (@jmiller-moko) | Added suites 2.11–2.14 (master user, emergency access, non-overwrite install, maintenance actions), updated key coverage |
