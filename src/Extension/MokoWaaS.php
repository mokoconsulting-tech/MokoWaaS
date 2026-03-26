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
 * INGROUP: MokoWaaS
 * REPO: https://github.com/mokoconsulting-tech/mokowaas
 * VERSION: 02.00.00
 * PATH: /src/Extension/MokoWaaS.php
 * NOTE: Handles Joomla system events for rebranding functionality
 */

namespace Moko\Plugin\System\MokoWaaS\Extension;

defined('_JEXEC') or die;

use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\CMS\Language\Language;

/**
 * MokoWaaS Brand System Plugin
 *
 * This plugin rebrands the Joomla system interface with MokoWaaS identity.
 * It applies language overrides and ensures consistent branding across the platform.
 *
 * @since  01.04.00
 */
class MokoWaaS extends CMSPlugin
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
	 * This method loads language override files from the plugin directory to rebrand Joomla
	 * with MokoWaaS identity. The override files replace core Joomla language strings.
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

		// Load language overrides
		$this->loadLanguageOverrides();
	}

	/**
	 * Build the placeholder → value map from plugin params.
	 *
	 * @return  array  Associative array of placeholder => replacement value
	 *
	 * @since   02.00.00
	 */
	protected function getPlaceholders()
	{
		return [
			'{{BRAND_NAME}}'   => $this->params->get('brand_name', 'MokoWaaS'),
			'{{COMPANY_NAME}}' => $this->params->get('company_name', 'Moko Consulting'),
			'{{SUPPORT_URL}}'  => $this->params->get('support_url', 'https://mokoconsulting.tech'),
		];
	}

	/**
	 * Load language override templates and inject resolved strings into Joomla.
	 *
	 * Reads the override template shipped with the plugin, replaces
	 * {{BRAND_NAME}}, {{COMPANY_NAME}} and {{SUPPORT_URL}} with the
	 * values from plugin params, then injects the resolved strings into
	 * the active Language object.
	 *
	 * @return  void
	 *
	 * @since   02.00.00
	 */
	protected function loadLanguageOverrides()
	{
		$language    = $this->app->getLanguage();
		$tag         = $language->getTag();
		$pluginPath  = JPATH_PLUGINS . '/system/mokowaas';
		$isAdmin     = $this->app->isClient('administrator');

		$overridePath = $isAdmin
			? $pluginPath . '/administrator/language/overrides/' . $tag . '.override.ini'
			: $pluginPath . '/language/overrides/' . $tag . '.override.ini';

		if (!file_exists($overridePath))
		{
			return;
		}

		$strings      = $this->parseLanguageFile($overridePath);
		$placeholders = $this->getPlaceholders();

		foreach ($strings as $key => $value)
		{
			$language->_strings[$key] = str_replace(
				array_keys($placeholders),
				array_values($placeholders),
				$value
			);
		}
	}

	/**
	 * Parse a language INI file and return the raw strings (with placeholders).
	 *
	 * @param   string  $filePath  The path to the language file
	 *
	 * @return  array  Array of language strings (key => raw value)
	 *
	 * @since   02.00.00
	 */
	protected function parseLanguageFile($filePath)
	{
		$strings = [];

		if (!file_exists($filePath))
		{
			return $strings;
		}

		$content = file_get_contents($filePath);
		$lines   = explode("\n", $content);

		foreach ($lines as $line)
		{
			$line = trim($line);

			if ($line === '' || $line[0] === ';')
			{
				continue;
			}

			if (preg_match('/^([A-Z0-9_]+)="(.+)"$/i', $line, $matches))
			{
				$strings[strtoupper($matches[1])] = $matches[2];
			}
		}

		return $strings;
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
