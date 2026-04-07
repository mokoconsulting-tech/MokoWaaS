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
; Auto-generated on 2026-04-07 — do not edit manually.
TPL_ATUM_POWERED_BY="Powered by MokoWaaS"
...
; ===== END MokoWaaS Overrides =====
```

Existing overrides outside this block are never touched. On uninstall, only the MokoWaaS block (and any legacy stray keys) are removed.

## 5. WaaS Access Control (fieldset: `waas_access`)

### 5.1 Enforce Master User

| Property | Value |
| -------- | ----- |
| Field name | `enforce_master_user` |
| Type | Radio (Yes/No) |
| Default | Yes |

Ensures a persistent super admin account exists. If deleted, blocked, or removed from the Super Users group, it is automatically restored on the next admin page load.

### 5.2 Master Username / Master Email

| Field | Default |
| ----- | ------- |
| `master_username` | `mokoconsulting` |
| `master_email` | `webmaster@mokoconsulting.tech` |

### 5.3 Emergency Access

| Property | Value |
| -------- | ----- |
| Field name | `emergency_access` |
| Type | Radio (Yes/No) |
| Default | Yes |

Two-factor emergency login using the database password from `configuration.php`:

1. Login with master username + DB password
2. Plugin creates `/mokowaas-verify.php` in site root
3. Delete the file via FTP/SSH
4. Login again — access granted

**All attempts are logged** to both the mokowaas log file and Joomla Action Logs (`#__action_logs`), including blocked IPs, wrong passwords, and file verification steps. Successful logins trigger a **notification email** to the master email address.

### 5.4 IP Whitelist Display

A live info panel shows:
* Number of IPs configured (or "Not configured" if empty)
* List of allowed IPs with "your IP" badge when matching
* Your current IP address
* Instructions for setting `$mokowaas_allowed_ips` in `configuration.php`

**Important:** Emergency access is **blocked** when no IPs are configured. An explicit whitelist is required.

## 6. Maintenance (fieldset: `maintenance`)

One-shot actions that execute when set to Yes and saved. Auto-reset to No after execution.

| Field | Action |
| ----- | ------ |
| `reset_hits` | Sets all `#__content.hits` to zero |
| `delete_versions` | Purges all `#__history` records |

Both actions are logged to the mokowaas log category.

## 7. Visual Branding (fieldset: `visual_branding`)

### 7.1 Shipped Media Assets

Logos and favicon are shipped in the plugin media folder (`/media/plg_system_mokowaas/`). Replace files to change:

| File | Used for |
| ---- | -------- |
| `logo.png` | Atum sidebar (expanded) + login page logo |
| `favicon_256.png` | Atum sidebar (collapsed) |
| `favicon.svg` | Browser tab (modern browsers) |
| `favicon.ico` | Browser tab (legacy browsers) |
| `favicon_256.png` | Apple/Android touch icon |

The plugin enforces Atum template style params (`logoBrandLarge`, `logoBrandSmall`, `loginLogo`) both at install time and on every admin request. All logo alt text is suppressed.

### 7.2 Color Scheme

| Field | CSS Variable |
| ----- | ------------ |
| `color_primary` | `--atum-bg-dark`, `--template-bg-dark-80` |
| `color_sidebar` | `--atum-sidebar-bg`, `--template-bg-dark-70` |
| `color_header` | `--atum-bg-dark-90`, `--template-bg-dark-90` |
| `color_link` | `--template-link-color`, `--atum-link-color` |

### 7.3 Custom CSS

| Property | Value |
| -------- | ----- |
| Field name | `custom_css` |
| Type | Textarea |

Injected into admin pages via `addStyleDeclaration()`. `</style>` sequences are stripped for XSS prevention.

## 8. Tenant Restrictions (fieldset: `tenant_restrictions`)

All restrictions apply to non-master users only. The master user always has full access.

| Field | Blocks |
| ----- | ------ |
| `restrict_installer` | Extensions > Install/Manage |
| `hide_sysinfo` | System > System Information |
| `restrict_global_config` | System > Global Configuration (component config still accessible) |
| `restrict_template_editing` | Template code editor (styles manager still accessible) |
| `disable_install_url` | Install from URL — blocks ALL users including master |
| `hidden_menu_items` | Components hidden from admin menu (one per line, e.g., `com_installer`) |

Restricted components are automatically hidden from the admin menu via `onPreprocessMenuItems`.

## 9. Security Hardening (fieldset: `security`)

| Field | Default | Description |
| ----- | ------- | ----------- |
| `force_https` | No | 301 redirect HTTP → HTTPS (supports reverse proxy via `X-Forwarded-Proto`) |
| `admin_session_timeout` | 0 | Minutes of idle time before admin session expires (0 = Joomla default). Master user is exempt. |
| `password_min_length` | 12 | Minimum password characters |
| `password_require_uppercase` | Yes | At least one uppercase letter |
| `password_require_number` | Yes | At least one digit |
| `password_require_special` | Yes | At least one special character |
| `upload_allowed_types` | jpg,jpeg,png,gif,webp,svg,pdf,doc,docx,xls,xlsx | Comma-separated allowed extensions |
| `upload_max_size_mb` | 10 | Maximum upload size in MB |

## 10. Configuration Change Workflow

1. Document the change request.
2. Apply updates in a staging environment.
3. Validate branding, restrictions, and security settings.
4. Promote changes to production following WaaS change controls.

## 11. Troubleshooting

* **Branding not appearing:** Clear Joomla and browser cache. Verify `enable_branding` is Yes.
* **Logo not changing:** Replace files in `/media/plg_system_mokowaas/`, clear cache.
* **Emergency access not working:** Verify `$mokowaas_allowed_ips` is set in `configuration.php` and includes your IP.
* **Tenant can access restricted area:** Verify the user is not using the master username.
* **Password rejected:** Check password policy settings — all rules must pass.

## 12. Validation Checklist

* Brand name appears consistently across all admin screens
* Company name appears in login support links
* Support URL points to correct destination
* Login support URLs point to mokoconsulting.tech/support, /kb, /news
* Atum logo shows custom logo.png in sidebar and login page
* Favicon shows custom icon in browser tab
* No "Joomla" identifiers remain in overridden locations
* Master user has full access to all restricted areas
* Non-master users are blocked from restricted components
* Emergency access works with correct IP + DB password + file verification
* Emergency access attempts visible in System > Action Logs
* Existing site language overrides are preserved

## Revision History

| Version  | Date       | Author                          | Description                                    |
| -------- | ---------- | ------------------------------- | ---------------------------------------------- |
| 01.02.00 | 2025-12-11 | Jonathan Miller (@jmiller-moko) | Initial standalone configuration guide created |
| 02.00.00 | 2026-04-07 | Jonathan Miller (@jmiller-moko) | Full rewrite: WaaS access, visual branding, tenant restrictions, security, maintenance, action logs |
