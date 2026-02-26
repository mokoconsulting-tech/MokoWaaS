<!--
 Copyright (C) 2025 Moko Consulting <hello@mokoconsulting.tech>

 This file is part of a Moko Consulting project.

 SPDX-LICENSE-IDENTIFIER: GPL-3.0-or-later

 This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the IMPLIED WARRANTY of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 
 You should have received a copy of the GNU General Public License (./LICENSE.md).
 
 # FILE INFORMATION 
 DEFGROUP: 
 INGROUP: MokoWaaSBrand.Documentation
 REPO: https://github.com/mokoconsulting-tech/mokowaasbrand
 PATH: ./CHANGELOG.md
 VERSION: 01.06.00
 BRIEF: Version history using `Keep a Changelog`
-->

# Changelog

All notable changes to the MokoWaaSBrand plugin will be documented in this file.

The format is based on [Keep a Changelog](https://keepachangelog.com/en/1.0.0/),
and this project adheres to [Semantic Versioning](https://semver.org/spec/v2.0.0.html).

## [Unreleased]

### Planned
- Additional language override strings for extended Joomla components
- Custom branding for media manager
- Enhanced configuration options

## [01.06.00] - 2026-02-26

### Fixed
- **Critical**: Fixed "Class 'PlgSystemMokoWaaSBrand' not found" error by implementing proper Joomla 5.x namespace structure
  - Created namespaced class `Moko\Plugin\System\MokoWaaSBrand\Extension\MokoWaaSBrand`
  - Moved class to `Extension/` directory at plugin root level
  - Updated service provider to use fully qualified class name
  - Updated manifest with correct namespace path (`.` instead of `src`)
- Removed old non-namespaced `mokowaasbrand.php` file
- Updated plugin manifest to include `Extension` folder in files

### Changed
- Plugin now uses proper Joomla 5.x PSR-4 autoloading with namespaces
- Class structure follows Joomla 5.x plugin conventions with Extension subdirectory
- Updated documentation to reflect new class structure

### Technical
- Namespace: `Moko\Plugin\System\MokoWaaSBrand\Extension\MokoWaaSBrand`
- Class file location: `Extension/MokoWaaSBrand.php`
- Service provider properly imports and instantiates namespaced class
- Manifest declares namespace with path="." for plugin root

## [01.05.00] - 2026-02-26

### Added
- Comprehensive frontend language overrides (35+ new strings):
  - User authentication branding (login, logout, registration, password reset)
  - Search functionality with MokoWaaS branding
  - Contact form branding
  - Error page customization
  - Frontend module branding
  - Meta generator tag with MokoWaaS branding
- Frontend override file expanded from 58 to 93 lines for complete user-facing coverage

### Changed
- **Directory structure reorganization**: Plugin code relocated to standard Joomla plugin path
  - Moved plugin files from `src/` to `src/plugins/system/mokowaasbrand/`
  - Language overrides remain in standard Joomla locations:
    - Frontend: `src/language/overrides/`
    - Administrator: `src/administrator/language/overrides/`
- **Naming standardization**: Removed hyphen from all project references
  - Changed from "MokoWaaS-Brand" to "MokoWaaSBrand" across 26 files
  - Updated 74 occurrences in documentation, code, and configuration
- Updated all documentation to reflect new directory structure:
  - README.md repository structure diagram
  - Build guide directory references
  - All file PATH headers in source files
- Updated `validate_manifest.sh` script to reference new manifest location

### Fixed
- Corrected PATH comments in plugin language files to reflect new structure
- Updated file header paths in `mokowaasbrand.php`, `mokowaasbrand.xml`, and `provider.php`

### Technical
- Improved adherence to Joomla plugin directory conventions
- Clearer separation between plugin code and language override files
- Better organization for long-term maintainability
- All path references updated for consistency

## [01.04.00] - 2026-02-22

### Added
- Complete Joomla 5.x system plugin implementation with modern architecture
- Main plugin class (`src/plugins/system/mokowaasbrand/mokowaasbrand.php`) with event handlers:
  - `onAfterInitialise` event hook for framework initialization
  - `onAfterRoute` event hook for routing integration
- Plugin manifest (`src/plugins/system/mokowaasbrand/mokowaasbrand.xml`) with Joomla 5.x namespace support
  - Namespace: `Moko\Plugin\System\MokoWaaSBrand`
  - Configuration parameter for enabling/disabling branding
- Dependency injection service provider (`src/plugins/system/mokowaasbrand/services/provider.php`)
  - DI container registration for Joomla 5.x compatibility
- Plugin language files in `src/plugins/system/mokowaasbrand/language/en-GB/`:
  - `plg_system_mokowaasbrand.ini` - Plugin UI strings
  - `plg_system_mokowaasbrand.sys.ini` - System/installation strings
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
- Moved plugin code to `/src/plugins/system/mokowaasbrand/` directory for better organization
- Aligned repository structure with release deployment pipeline
- Improved packaging workflow

### Added
- Release deployment pipeline integration
- Automated build and validation scripts

## [1.0.0] - 2025-12-11

### Added
- Initial release of MokoWaaSBrand plugin
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
- [Repository](https://github.com/mokoconsulting-tech/mokowaasbrand) - Project repository

---

**Note:** For detailed technical documentation, see the `/docs/` directory and [README.md](README.md).
