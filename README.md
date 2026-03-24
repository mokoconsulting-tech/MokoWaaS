<!--
 Copyright (C) 2025 Moko Consulting <hello@mokoconsulting.tech>

 This file is part of a Moko Consulting project.

 SPDX-LICENSE-IDENTIFIER: GPL-3.0-or-later

 This program is free software; you can redistribute it and modify it under the terms of the GNU General Public License version 3 or later.

 This program is distributed in the hope that it will be useful but without warranty.

 You should have received a copy of the GNU General Public License in LICENSE.md.

 # FILE INFORMATION
 DEFGROUP: Joomla.Plugin
 INGROUP: MokoWaaS-Brand
 REPO: https://github.com/mokoconsulting-tech/mokowaasbrand
 VERSION: 01.04.01
 PATH: /README.md
 BRIEF: Rebranding plugin for MokoWaaS platform
 NOTE: Internal WaaS identity abstraction layer
-->

# MokoWaaS-Brand Plugin

[![Version](https://img.shields.io/badge/version-01.04.00-blue.svg)](https://github.com/mokoconsulting-tech/mokowaasbrand)
[![License](https://img.shields.io/badge/license-GPL--3.0--or--later-green.svg)](LICENSE.md)
[![Joomla](https://img.shields.io/badge/Joomla-5.x-orange.svg)](https://www.joomla.org)
[![PHP](https://img.shields.io/badge/PHP-8.1%2B-blue.svg)](https://www.php.net)

MokoWaaS-Brand is a Joomla 5.x system plugin that provides a comprehensive identity override layer for the MokoWaaS platform. It ensures consistent branding, terminology, and user experience across all Joomla administrative and frontend interfaces.

## Table of Contents

- [Overview](#overview)
- [Features](#features)
- [System Requirements](#system-requirements)
- [Installation](#installation)
- [Configuration](#configuration)
- [Technical Implementation](#technical-implementation)
- [Repository Structure](#repository-structure)
- [Development](#development)
- [Documentation](#documentation)
- [Support](#support)
- [License](#license)
- [Changelog](#changelog)

## Overview

The MokoWaaS-Brand plugin operationalizes a unified naming convention, brand-controlled visuals, and enforced terminology across all tenant sites. This ensures consistent service delivery within the WaaS (Website as a Service) framework by abstracting all upstream Joomla identifiers behind MokoWaaS-compliant terminology.

## Features

- **Comprehensive Language Overrides**: 57+ language strings replacing Joomla branding with MokoWaaS terminology
- **Administrator & Frontend Coverage**: Applies branding across both Joomla backend and frontend
- **Joomla 5.x Compatible**: Built using modern Joomla 5.x architecture with dependency injection
- **Event-Driven Architecture**: Utilizes Joomla system events for optimal integration
- **Configurable**: Enable/disable branding functionality through plugin parameters
- **Multi-Language Support**: Currently supports en-GB and en-US locales
- **Governance Compliant**: Aligned with [MokoStandards](https://github.com/mokoconsulting-tech/MokoStandards)

## System Requirements

- **Joomla**: 5.x or higher
- **PHP**: 8.1 or higher
- **Extensions**: Standard Joomla PHP extensions
- **Permissions**: Write access to language override directories

## Installation

### Method 1: Via Joomla Extension Manager (Recommended)

1. Download the latest release package from the releases page
2. Log into your Joomla Administrator panel
3. Navigate to **System → Extensions → Install**
4. Click **Upload Package File**
5. Select the downloaded `.zip` file
6. Click **Upload & Install**
7. Navigate to **System → Plugins**
8. Search for "MokoWaaS Brand"
9. Enable the plugin
10. Clear Joomla cache

### Method 2: Manual Installation

1. Extract the plugin package
2. Upload contents to your Joomla installation's `/tmp` directory
3. Install via Joomla Extension Manager → Install from Folder
4. Enable the plugin as described above

### Post-Installation

After installation, verify the branding is active:
- Check the administrator footer for "Powered by MokoWaaS"
- Verify the control panel shows "Welcome to MokoWaaS!"
- Clear browser cache if branding doesn't appear immediately

### Automatic Updates

This plugin supports Joomla's automatic update system. Once installed:

1. Navigate to **System → Update → Extensions**
2. The plugin will automatically check for updates from the MokoWaaS-Brand update server
3. When a new version is available, it will appear in the update list
4. Click **Update** to install the latest version

The update server URL is configured in the plugin manifest and points to:
```
https://raw.githubusercontent.com/mokoconsulting-tech/MokoWaaSBrand/main/updates.xml
```

Updates are published automatically when new releases are created through the GitHub release workflow.

## Configuration

The plugin provides the following configuration options accessible through **System → Plugins → System - MokoWaaS Brand**:

### Parameters

- **Enable Branding** (Yes/No)
  - Default: Yes
  - Description: Master toggle for all branding functionality
  - When disabled, all branding overrides are bypassed

Configuration options are intentionally limited to preserve WaaS brand integrity and prevent tenant-level deviation from platform standards.

## Technical Implementation

### Architecture

The plugin follows Joomla 5.x system plugin architecture:

```
PlgSystemMokoWaaSBrand
├── Event Handlers
│   ├── onAfterInitialise  - Framework initialization hook
│   └── onAfterRoute       - Route determination hook
├── Dependency Injection
│   └── ServiceProvider    - DI container registration
└── Language Integration
    └── Native Override System - Joomla's built-in override mechanism
```

### Core Components

1. **mokowaasbrand.php**
   - Main plugin class extending `CMSPlugin`
   - Implements system event handlers
   - Namespace: `Moko\Plugin\System\MokoWaaSBrand`

2. **mokowaasbrand.xml**
   - Plugin manifest defining metadata and structure
   - Joomla 5.x namespace configuration
   - File and folder definitions

3. **services/provider.php**
   - Dependency injection service provider
   - Registers plugin with Joomla's DI container
   - Joomla 5.x compatibility layer

4. **language/en-GB/**
   - Plugin-specific language strings
   - Installation and configuration UI text

5. **language/overrides/**
   - Frontend language override files
   - Replaces Joomla terminology with MokoWaaS branding

6. **administrator/language/overrides/**
   - Administrator language override files
   - Backend-specific branding replacements

### Language Override Integration

The plugin leverages Joomla's native language override system rather than programmatically loading strings. Language override files are placed in standard Joomla locations:

- Frontend: `language/overrides/{locale}.override.ini`
- Administrator: `administrator/language/overrides/{locale}.override.ini`

Joomla automatically loads these overrides during initialization, ensuring optimal performance and compatibility.

## Repository Structure

```
mokowaasbrand/
├── src/                              # Plugin source files
│   ├── mokowaasbrand.php            # Main plugin class
│   ├── mokowaasbrand.xml            # Plugin manifest
│   ├── services/
│   │   └── provider.php             # DI service provider
│   ├── language/
│   │   ├── en-GB/                   # Plugin language files
│   │   └── overrides/               # Frontend language overrides
│   └── administrator/
│       └── language/
│           └── overrides/           # Admin language overrides
├── docs/                            # Documentation
│   ├── index.md                     # Documentation index
│   ├── plugin-basic.md              # Plugin overview
│   ├── guides/                      # Operational guides
│   └── reference/                   # Reference materials
├── scripts/                         # Build and validation scripts
│   ├── validate_manifest.sh
│   ├── verify_changelog.sh
│   └── update_changelog.sh
├── .github/                         # GitHub workflows
│   └── workflows/
│       ├── build.yml
│       ├── ci.yml
│       └── release_from_version.yml
├── CHANGELOG.md                     # Version history
├── README.md                        # This file
├── LICENSE.md                       # GPL-3.0-or-later license
├── CONTRIBUTING.md                  # Contribution guidelines
└── CODE_OF_CONDUCT.md              # Community guidelines
```

## Development

### Building the Plugin

Build the installable plugin package from source:

```bash
cd src
zip -r ../mokowaasbrand_v01.04.00.zip . -x "*.git*"
```

### Running Validation Scripts

```bash
# Validate plugin manifest
./scripts/validate_manifest.sh

# Verify changelog format
./scripts/verify_changelog.sh
```

### PHP Syntax Validation

```bash
cd src
find . -name "*.php" -exec php -l {} \;
```

### Automated Build via GitHub Actions

The repository includes automated workflows:

- **build.yml**: Creates ZIP package on release
- **ci.yml**: Runs validation checks on pull requests
- **release_from_version.yml**: Automates release process

## Documentation

Comprehensive documentation is available in the `/docs` directory:

- **[Plugin Overview](docs/plugin-basic.md)**: Detailed plugin documentation
- **[Installation Guide](docs/guides/installation-guide.md)**: Step-by-step installation
- **[Build Guide](docs/guides/build-guide.md)**: Building and packaging
- **[Configuration Guide](docs/guides/configuration-guide.md)**: Configuration options
- **[Operations Guide](docs/guides/operations-guide.md)**: Operational procedures
- **[Troubleshooting Guide](docs/guides/troubleshooting-guide.md)**: Common issues

All documentation follows the [MokoStandards](https://github.com/mokoconsulting-tech/MokoStandards) documentation framework.

## Support

### Getting Help

- **Documentation**: Check the `/docs` directory for detailed guides
- **Issues**: Submit issues through the GitHub issue tracker
- **Service Support**: For operational issues, submit a ticket through the Moko Consulting service channel

### Reporting Issues

When reporting issues, include:
- Joomla version
- PHP version
- Plugin version
- Steps to reproduce
- Expected vs actual behavior
- Relevant error messages or logs

## License

This project is licensed under the GNU General Public License version 3 or later (GPL-3.0-or-later).

See [LICENSE.md](LICENSE.md) for the full license text.

## Versioning

This extension follows the [MokoStandards](https://github.com/mokoconsulting-tech/MokoStandards) version governance model using semantic versioning: `MAJOR.MINOR.PATCH`

Current version: **01.04.00**

## Changelog

See [CHANGELOG.md](CHANGELOG.md) for a complete version history.

### Recent Changes (v01.04.00 - 2026-02-22)

- Added complete Joomla 5.x system plugin implementation
- Created main plugin class with event handlers
- Implemented plugin manifest with Joomla 5.x namespace support
- Added dependency injection service provider
- Created plugin language files
- Integrated with language override system
- Enhanced language overrides (57+ strings)
- Fixed typo in error messages (OCCURRED)

## Contributing

We welcome contributions! Please read [CONTRIBUTING.md](CONTRIBUTING.md) for details on:

- Code of conduct
- Development workflow
- Coding standards
- Pull request process
- Documentation requirements

## Acknowledgments

- Built for the MokoWaaS platform
- Follows [MokoStandards](https://github.com/mokoconsulting-tech/MokoStandards)
- Designed for Joomla 5.x architecture
- Maintained by Moko Consulting

---

**Moko Consulting** | [Website](https://mokoconsulting.tech) | [Email](mailto:hello@mokoconsulting.tech)
