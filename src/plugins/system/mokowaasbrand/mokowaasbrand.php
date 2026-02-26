<?php
/**
 * Copyright (C) 2025 Moko Consulting <hello@mokoconsulting.tech>
 *
 * This file is part of a Moko Consulting project.
 *
 * SPDX-LICENSE-IDENTIFIER: GPL-3.0-or-later
 *
 * This program is free software; you can redistribute it and/or modify it under the terms of the GNU General Public License as published by the Free Software Foundation; either version 3 of the License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful, but WITHOUT ANY WARRANTY; without even the IMPLIED WARRANTY of MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License (./LICENSE.md).
 *
 * FILE INFORMATION
 * DEFGROUP: Joomla.Plugin
 * INGROUP: MokoWaaS-Brand
 * REPO: https://github.com/mokoconsulting-tech/mokowaasbrand
 * VERSION: 01.04.00
 * PATH: /src/plugins/system/mokowaasbrand/mokowaasbrand.php
 * BRIEF: Main plugin file for MokoWaaS-Brand system plugin
 * NOTE: Handles Joomla system events for rebranding functionality
 */

defined('_JEXEC') or die;

use Joomla\CMS\Plugin\CMSPlugin;

/**
 * MokoWaaS Brand System Plugin
 *
 * This plugin rebrands the Joomla system interface with MokoWaaS identity.
 * It applies language overrides and ensures consistent branding across the platform.
 *
 * @since  01.04.00
 */
class PlgSystemMokoWaaSBrand extends CMSPlugin
{
	/**
	 * Load the language file on instantiation.
	 *
	 * @var    boolean
	 * @since  01.04.00
	 */
	protected $autoloadLanguage = true;

	/**
	 * Application object
	 *
	 * @var    \Joomla\CMS\Application\CMSApplication
	 * @since  01.04.00
	 */
	protected $app;

	/**
	 * Event triggered after the framework has loaded and the application initialise method has been called.
	 *
	 * This plugin relies on Joomla's native language override system. Language override files
	 * placed in the standard Joomla override directories will be automatically loaded by Joomla.
	 *
	 * @return  void
	 *
	 * @since   01.04.00
	 */
	public function onAfterInitialise()
	{
		if (!$this->params->get('enable_branding', 1))
		{
			return;
		}

		// Language overrides are handled by Joomla's core system
		// Additional branding functionality can be added here if needed
	}

	/**
	 * Event triggered after the route has been determined.
	 *
	 * @return  void
	 *
	 * @since   01.04.00
	 */
	public function onAfterRoute()
	{
		if (!$this->params->get('enable_branding', 1))
		{
			return;
		}

		// Apply additional branding logic if needed
	}
}
