<!--
 Copyright (C) 2025 Moko Consulting <hello@mokoconsulting.tech>

 This file is part of a Moko Consulting project.

 SPDX-LICENSE-IDENTIFIER: GPL-3.0-or-later

 This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the IMPLIED WARRANTY of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 
 You should have received a copy of the GNU General Public License (./LICENSE.md).
 
 # FILE INFORMATION 
 DEFGROUP: 
 INGROUP: MokoWaaS.Documentation
 REPO: https://github.com/mokoconsulting-tech/mokowaas
 PATH: ./CHANGELOG.md
 VERSION: 02.00.01
 BRIEF: Version history using `Keep a Changelog`
-->

# Changelog

All notable changes to the MokoWaaS plugin will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

### Planned
- Heartbeat telemetry to WaaS dashboard (#54)
- License/subscription check
- System email template branding (DB approach)

## [02.00.01] - 2026-04-07

### Added
- Template-based language overrides with `{{BRAND_NAME}}`, `{{COMPANY_NAME}}`, `{{SUPPORT_URL}}` placeholders
- Configurable brand name, company name, and support URL via plugin params
- Sentinel-block merge pattern that preserves existing site overrides
- Install respects user-defined overrides (non-overwrite)
- ~50 override keys across admin and frontend
- Powered by links with anchor tag to support URL
- Login support URL enforcement (mokoconsulting.tech/support, /kb, /news)
- Atum template branding via params (logoBrandLarge, logoBrandSmall, loginLogo)
- Shipped media assets: logo.png, favicon.ico, favicon.svg, favicon_256.png
- Favicon injection (SVG + ICO + Apple touch icon)
- Admin color scheme via Atum template style params (hue, link-color, special-color)
- Custom CSS textarea injection
- Master user enforcement (persistent super admin — "Webmaster")
- Emergency access (DB password + file verification two-factor)
- IP whitelist via configuration.php (empty blocks access)
- IP whitelist display in plugin config (shows current IPs + your IP)
- All emergency access attempts logged to Joomla Action Logs
- Email notification on successful emergency login
- Tenant restrictions: Extension Installer, System Info, Global Configuration, Template code editor
- Dynamic admin menu hiding via onPreprocessMenuItems
- Disable install-from-URL for all users
- Force HTTPS redirect (supports reverse proxy)
- Admin session idle timeout (default 60 min, master user exempt)
- Password policy (min length, uppercase, number, special character)
- Upload type and size restrictions (default 100MB)
- Maintenance actions: reset all hits, delete all versions
- Auto-enable plugin on first install
- Action log extension registration in #__action_logs_extensions and #__action_log_config
- Custom AllowedIpsField form field for IP whitelist display
- Joomla 5.x and 6.x compatibility

### Fixed
- Column heading overrides removed (broke module/plugin list views)
- RegularLabs Position column workaround
- Nested `<a>` tags in login support overrides
- Emergency access moved from onUserAuthenticate to onAfterInitialise (Joomla uses isolated auth dispatcher)
- Session created directly for emergency login (bypasses auth dispatcher)
- Auto-complete emergency login after verify file deletion (no re-entering credentials)

### Changed
- Version bumped to 02.00.01 across all files
- Configuration guide fully rewritten with all fieldsets documented
- Testing guide with 17 test suites
- README updated with Usage section, new features, Joomla 5/6 badges

## [01.04.00] - 2026-02-22

### Added
- Complete Joomla 5.x system plugin implementation with modern architecture
- Main plugin class (`src/mokowaas.php`) with event handlers:
  - `onAfterInitialise` event hook for framework initialization
  - `onAfterRoute` event hook for routing integration
- Plugin manifest (`src/mokowaas.xml`) with Joomla 5.x namespace support
  - Namespace: `Moko\Plugin\System\MokoWaaS`
  - Configuration parameter for enabling/disabling branding
- Dependency injection service provider (`src/services/provider.php`)
  - DI container registration for Joomla 5.x compatibility
- Plugin language files in `src/language/en-GB/`:
  - `plg_system_mokowaas.ini` - Plugin UI strings
  - `plg_system_mokowaas.sys.ini` - System/installation strings
- Enhanced language overrides (57+ strings):
  - Installation sample data branding
  - Site name labels
  - Admin-specific UI elements
  - Version and About sections
- Security `index.html` files throughout directory structure
- Comprehensive README.md with:
  - Badges for version, license, Joomla, and PHP compatibility
  - Table of contents with 12+ major sections
  - Detailed installation instructions (2 methods)
  - Technical implementation documentation
  - Repository structure overview
  - Development and build instructions

### Changed
- Updated all documentation to version 01.04.00
- Enhanced language overrides with more comprehensive coverage
- Improved plugin configuration options

### Fixed
- Typo in language override: "ERROR OCCURED" → "ERROR OCCURRED"
- Repository references updated from placeholders to actual GitHub URLs

### Technical
- Integrates with Joomla's native language override system
- No programmatic string loading (performance optimization)
- Event-driven architecture for minimal overhead
- PSR-4 autoloading through service provider

## [01.03.00] - 2025-12-11

### Changed
- General cleanup and code organization
- Documentation structure improvements

## [01.02.01] - 2025-12-11

### Changed
- Version bump for release alignment

## [01.02.00] - 2025-12-11

### Added
- Documentation directory (`/docs/`) with comprehensive guides:
  - Installation guide
  - Configuration guide
  - Build guide
  - Operations guide
  - Troubleshooting guide
  - Upgrade and versioning guide
  - Rollback and recovery guide
- GitHub workflow for automated builds (`.github/workflows/build.yml`)
- Image and favicon replacement feature for complete branding

### Changed
- Improved documentation structure and organization

## [01.01.05] - 2025-12-11

### Changed
- Version bump for release coordination

## [01.01.04] - 2025-12-11

### Fixed
- Plugin manifest corrections and validation fixes

## [01.01.03] - 2025-12-11

### Fixed
- Administrator language file location corrected
- Language override path alignment with Joomla standards

## [01.01.02] - 2025-12-11

### Changed
- Moved plugin code to `/src/` directory for better organization
- Aligned repository structure with release deployment pipeline
- Improved packaging workflow

### Added
- Release deployment pipeline integration
- Automated build and validation scripts

## [1.0.0] - 2025-12-11

### Added
- Initial release of MokoWaaS plugin
- Basic language override system for Joomla rebranding
- Frontend language overrides (en-GB, en-US)
- Administrator language overrides (en-GB, en-US)
- Core branding replacements:
  - Footer "Powered by" text
  - Control panel welcome messages
  - Help and documentation links
  - Generic Joomla→MokoWaaS replacements
- Basic plugin structure and manifest
- License (GPL-3.0-or-later)
- Contributing guidelines
- Code of conduct

### Technical Details
- Joomla 5.x compatible
- PHP 8.1+ requirement
- Language override mechanism using Joomla's native system

---

## Version History Summary

| Version    | Date       | Type      | Summary                                    |
|------------|------------|-----------|-------------------------------------------|
| 01.04.00   | 2026-02-22 | Major     | Complete plugin implementation & enhanced docs |
| 01.03.00   | 2025-12-11 | Minor     | Cleanup and organization                  |
| 01.02.01   | 2025-12-11 | Patch     | Version alignment                         |
| 01.02.00   | 2025-12-11 | Minor     | Documentation and build system            |
| 01.01.05   | 2025-12-11 | Patch     | Version coordination                      |
| 01.01.04   | 2025-12-11 | Patch     | Manifest fixes                            |
| 01.01.03   | 2025-12-11 | Patch     | Language location fix                     |
| 01.01.02   | 2025-12-11 | Patch     | Repository restructuring                  |
| 1.0.0      | 2025-12-11 | Major     | Initial release                           |

---

## Upgrade Notes

### Upgrading to 01.04.00

**Breaking Changes:** None

**New Features:**
- Complete Joomla 5.x plugin implementation
- Dependency injection support
- Enhanced language overrides (14+ new strings)

**Installation:**
1. Backup your current installation
2. Download the latest release package
3. Install via Joomla Extension Manager
4. Clear Joomla cache
5. Verify branding appears correctly

### Upgrading to 01.02.00

**New Features:**
- Comprehensive documentation in `/docs/`
- Automated build workflows

**Notes:**
- Review new documentation for operational guidance
- Check GitHub workflows for automated builds

---

## Contributing

Please read [CONTRIBUTING.md](CONTRIBUTING.md) for details on our code of conduct and the process for submitting pull requests.

When adding entries to this changelog:
1. Add new changes under `[Unreleased]` section
2. Use categories: Added, Changed, Deprecated, Removed, Fixed, Security
3. Include clear, concise descriptions
4. Reference issue numbers where applicable
5. Move items from Unreleased to versioned section upon release

## Links

- [MokoStandards](https://github.com/mokoconsulting-tech/MokoStandards) - Coding and documentation standards
- [Keep a Changelog](https://keepachangelog.com/en/1.0.0/) - Changelog format specification
- [Semantic Versioning](https://semver.org/spec/v2.0.0.html) - Version numbering specification
- [Repository](https://github.com/mokoconsulting-tech/mokowaas) - Project repository

---

**Note:** For detailed technical documentation, see the `/docs/` directory and [README.md](README.md).
