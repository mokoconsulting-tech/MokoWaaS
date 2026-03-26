<!--
 Copyright (C) 2025 Moko Consulting <hello@mokoconsulting.tech>
 This file is part of a Moko Consulting project.
 SPDX-LICENSE-IDENTIFIER: GPL-3.0-or-later
 This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 3 of the License, or (at your option) any later version.
 This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the IMPLIED WARRANTY of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 You should have received a copy of the GNU General Public License (./LICENSE.md).

 # FILE INFORMATION
 DEFGROUP: Joomla.Plugin
 INGROUP: MokoWaaS.Contributing
 REPO: https://github.com/mokoconsulting-tech/mokowaas
 VERSION: 01.03.00
 PATH: /CONTRIBUTING.md
 BRIEF: Contribution guidelines for the MokoWaaS plugin
-->

# Contributing to MokoWaaS (VERSION: 01.03.00)

## Overview
Contributions to the MokoWaaS plugin follow standardized development, governance, and quality control expectations defined by Moko Consulting. This document outlines contribution requirements, acceptable change types, branch management, testing expectations, and release readiness standards.

## 1. Contribution Workflow
All contributions must follow the established workflow:
1. Fork the repository or create a feature branch (if internal).
2. Ensure your environment matches the supported Joomla and PHP versions.
3. Implement changes following coding, documentation, and metadata standards.
4. Validate plugin functionality locally.
5. Submit a Pull Request (PR) for review.

## 2. Branching Model
- `main`: Production stable branch.
- `develop`: Aggregates work for the next minor release.
- `feature/*`: New enhancements or changes.
- `bugfix/*`: Hotfixes and corrections.

Internal teams must coordinate with governance before creating major feature branches.

## 3. Coding and Documentation Standards
All code must:
- Follow [MokoStandards](https://github.com/mokoconsulting-tech/MokoStandards) coding standards
- Include the unified SPDX license header
- Include a FILE INFORMATION metadata block
- Avoid deprecated Joomla APIs
- Preserve load order compatibility with other system plugins

Documentation must:
- Include metadata
- Maintain revision history
- Use consistent formatting as defined by Moko documentation standards

## 4. Testing Requirements
Before submitting a PR, contributors must verify:
- Plugin installs successfully in Joomla 5.x
- No load errors appear in logs
- Branding replacements appear as expected
- Terminology strings are correct
- No regressions in administrator UI

Automated testing coverage will expand as part of future roadmap enhancements.

## 5. Pull Request Requirements
A PR must include:
- Description of change
- Screenshots for UI related updates
- Version updates when appropriate
- Notes for documentation changes
- Reference to related issues or tasks

PRs lacking required information may be flagged or delayed.

## 6. Release Versioning
Changes must follow semantic versioning:
- MAJOR: Structural branding or architectural changes
- MINOR: Feature updates or terminology expansion
- PATCH: Bug fixes or language corrections

Version updates must be reflected in:
- Manifest files
- PHP headers
- Documentation metadata

## 7. Code Review Standards
Reviewers validate:
- Code quality and clarity
- Compliance with [MokoStandards](https://github.com/mokoconsulting-tech/MokoStandards) coding standards
- Impact to templates and WaaS branding rules
- Backwards compatibility expectations

## Revision History
| Date | Author | Description |
| ------ | -------- | ----------- |
| 2025-12-11 | Jonathan Miller (@jmiller-moko) | Initial creation of contribution guidelines |
