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
 * PATH: /src/script.php
 * BRIEF: Installation script for MokoWaaS plugin
 * NOTE: Handles installation, update, and uninstallation tasks including language override deployment
 */

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Installer\InstallerAdapter;
use Joomla\CMS\Installer\InstallerScriptInterface;
use Joomla\CMS\Language\Text;
use Joomla\CMS\Log\Log;
use Joomla\Filesystem\File;
use Joomla\Filesystem\Folder;

/**
 * Installation script for MokoWaaS plugin
 *
 * This script handles the installation and uninstallation of language override files
 * to Joomla's global language override directories.
 *
 * @since  02.00.00
 */
class plgSystemMokoWaaSInstallerScript implements InstallerScriptInterface
{
	/**
	 * Minimum Joomla version required to install the extension.
	 *
	 * @var    string
	 * @since  02.00.00
	 */
	private $minimumJoomla = '5.0.0';

	/**
	 * Minimum PHP version required to install the extension.
	 *
	 * @var    string
	 * @since  02.00.00
	 */
	private $minimumPhp = '8.1.0';

	/**
	 * Language tags supported by this plugin.
	 *
	 * @var    array
	 * @since  02.00.00
	 */
	private $languageTags = ['en-GB', 'en-US'];

	/**
	 * Called before any type of action.
	 *
	 * @param   string            $type     Which action is happening (install|uninstall|discover_install|update)
	 * @param   InstallerAdapter  $adapter  The object responsible for running this script
	 *
	 * @return  boolean  True on success
	 *
	 * @since   02.00.00
	 */
	public function preflight($type, $adapter): bool
	{
		// Check minimum Joomla version
		if (version_compare(JVERSION, $this->minimumJoomla, '<'))
		{
			Factory::getApplication()->enqueueMessage(
				sprintf('This extension requires Joomla %s or later.', $this->minimumJoomla),
				'error'
			);
			return false;
		}

		// Check minimum PHP version
		if (version_compare(PHP_VERSION, $this->minimumPhp, '<'))
		{
			Factory::getApplication()->enqueueMessage(
				sprintf('This extension requires PHP %s or later.', $this->minimumPhp),
				'error'
			);
			return false;
		}

		return true;
	}

	/**
	 * Called after any type of action.
	 *
	 * @param   string            $type     Which action is happening (install|uninstall|discover_install|update)
	 * @param   InstallerAdapter  $adapter  The object responsible for running this script
	 *
	 * @return  boolean  True on success
	 *
	 * @since   02.00.00
	 */
	public function postflight($type, $adapter): bool
	{
		// Only install overrides on install or update
		if ($type === 'install' || $type === 'update')
		{
			$this->installLanguageOverrides();
		}

		return true;
	}

	/**
	 * Called on installation.
	 *
	 * @param   InstallerAdapter  $adapter  The object responsible for running this script
	 *
	 * @return  boolean  True on success
	 *
	 * @since   02.00.00
	 */
	public function install(InstallerAdapter $adapter): bool
	{
		return true;
	}

	/**
	 * Called on update.
	 *
	 * @param   InstallerAdapter  $adapter  The object responsible for running this script
	 *
	 * @return  boolean  True on success
	 *
	 * @since   02.00.00
	 */
	public function update(InstallerAdapter $adapter): bool
	{
		return true;
	}

	/**
	 * Called on uninstallation.
	 *
	 * @param   InstallerAdapter  $adapter  The object responsible for running this script
	 *
	 * @return  boolean  True on success
	 *
	 * @since   02.00.00
	 */
	public function uninstall(InstallerAdapter $adapter): bool
	{
		// Remove language overrides on uninstall
		$this->uninstallLanguageOverrides();

		return true;
	}

	/**
	 * Install language override files to Joomla's global override directories.
	 *
	 * This method copies the plugin's language override files to Joomla's global
	 * language override directories where they will be automatically loaded by Joomla.
	 *
	 * @return  void
	 *
	 * @since   02.00.00
	 */
	private function installLanguageOverrides()
	{
		$app = Factory::getApplication();
		$pluginPath = JPATH_PLUGINS . '/system/mokowaas';

		// Install frontend overrides
		foreach ($this->languageTags as $tag)
		{
			$source = $pluginPath . '/language/overrides/' . $tag . '.override.ini';
			$dest = JPATH_ROOT . '/language/overrides/' . $tag . '.override.ini';

			if (file_exists($source))
			{
				// Ensure destination directory exists
				$destDir = dirname($dest);
				if (!is_dir($destDir))
				{
					Folder::create($destDir);
				}

				// Read existing overrides if they exist
				$existingOverrides = [];
				if (file_exists($dest))
				{
					$existingOverrides = $this->parseLanguageFile($dest);
				}

				// Read plugin overrides
				$pluginOverrides = $this->parseLanguageFile($source);

				// Merge overrides (plugin overrides take precedence)
				$mergedOverrides = array_merge($existingOverrides, $pluginOverrides);

				// Write merged overrides
				if ($this->writeLanguageFile($dest, $mergedOverrides))
				{
					$app->enqueueMessage(
						sprintf('Installed frontend language overrides for %s', $tag),
						'message'
					);
				}
				else
				{
					$app->enqueueMessage(
						sprintf('Failed to install frontend language overrides for %s', $tag),
						'warning'
					);
				}
			}
		}

		// Install administrator overrides
		foreach ($this->languageTags as $tag)
		{
			$source = $pluginPath . '/administrator/language/overrides/' . $tag . '.override.ini';
			$dest = JPATH_ADMINISTRATOR . '/language/overrides/' . $tag . '.override.ini';

			if (file_exists($source))
			{
				// Ensure destination directory exists
				$destDir = dirname($dest);
				if (!is_dir($destDir))
				{
					Folder::create($destDir);
				}

				// Read existing overrides if they exist
				$existingOverrides = [];
				if (file_exists($dest))
				{
					$existingOverrides = $this->parseLanguageFile($dest);
				}

				// Read plugin overrides
				$pluginOverrides = $this->parseLanguageFile($source);

				// Merge overrides (plugin overrides take precedence)
				$mergedOverrides = array_merge($existingOverrides, $pluginOverrides);

				// Write merged overrides
				if ($this->writeLanguageFile($dest, $mergedOverrides))
				{
					$app->enqueueMessage(
						sprintf('Installed administrator language overrides for %s', $tag),
						'message'
					);
				}
				else
				{
					$app->enqueueMessage(
						sprintf('Failed to install administrator language overrides for %s', $tag),
						'warning'
					);
				}
			}
		}
	}

	/**
	 * Remove language override files from Joomla's global override directories.
	 *
	 * This method removes the plugin's language overrides from Joomla's global
	 * language override directories on uninstallation.
	 *
	 * @return  void
	 *
	 * @since   02.00.00
	 */
	private function uninstallLanguageOverrides()
	{
		$app = Factory::getApplication();
		$pluginPath = JPATH_PLUGINS . '/system/mokowaas';

		// Remove frontend overrides
		foreach ($this->languageTags as $tag)
		{
			$source = $pluginPath . '/language/overrides/' . $tag . '.override.ini';
			$dest = JPATH_ROOT . '/language/overrides/' . $tag . '.override.ini';

			if (file_exists($source) && file_exists($dest))
			{
				// Read plugin overrides
				$pluginOverrides = $this->parseLanguageFile($source);

				// Read existing overrides
				$existingOverrides = $this->parseLanguageFile($dest);

				// Remove plugin overrides from existing
				foreach (array_keys($pluginOverrides) as $key)
				{
					unset($existingOverrides[$key]);
				}

				// Write remaining overrides or delete file if empty
				if (!empty($existingOverrides))
				{
					$this->writeLanguageFile($dest, $existingOverrides);
				}
				else
				{
					File::delete($dest);
				}

				$app->enqueueMessage(
					sprintf('Removed frontend language overrides for %s', $tag),
					'message'
				);
			}
		}

		// Remove administrator overrides
		foreach ($this->languageTags as $tag)
		{
			$source = $pluginPath . '/administrator/language/overrides/' . $tag . '.override.ini';
			$dest = JPATH_ADMINISTRATOR . '/language/overrides/' . $tag . '.override.ini';

			if (file_exists($source) && file_exists($dest))
			{
				// Read plugin overrides
				$pluginOverrides = $this->parseLanguageFile($source);

				// Read existing overrides
				$existingOverrides = $this->parseLanguageFile($dest);

				// Remove plugin overrides from existing
				foreach (array_keys($pluginOverrides) as $key)
				{
					unset($existingOverrides[$key]);
				}

				// Write remaining overrides or delete file if empty
				if (!empty($existingOverrides))
				{
					$this->writeLanguageFile($dest, $existingOverrides);
				}
				else
				{
					File::delete($dest);
				}

				$app->enqueueMessage(
					sprintf('Removed administrator language overrides for %s', $tag),
					'message'
				);
			}
		}
	}

	/**
	 * Parse a language INI file and return the strings as an associative array.
	 *
	 * @param   string  $filePath  The path to the language file
	 *
	 * @return  array  Array of language strings (key => value)
	 *
	 * @since   02.00.00
	 */
	private function parseLanguageFile($filePath)
	{
		$strings = [];

		if (!file_exists($filePath))
		{
			return $strings;
		}

		$content = file_get_contents($filePath);
		$lines = explode("\n", $content);

		foreach ($lines as $line)
		{
			$line = trim($line);

			// Skip empty lines and comments
			if (empty($line) || $line[0] === ';')
			{
				continue;
			}

			// Parse KEY="VALUE" format
			if (preg_match('/^([A-Z0-9_]+)="(.+)"$/i', $line, $matches))
			{
				$key = strtoupper($matches[1]);
				$value = $matches[2];
				$strings[$key] = $value;
			}
		}

		return $strings;
	}

	/**
	 * Write language strings to an INI file.
	 *
	 * @param   string  $filePath  The path to the language file
	 * @param   array   $strings   Array of language strings (key => value)
	 *
	 * @return  boolean  True on success, false on failure
	 *
	 * @since   02.00.00
	 */
	private function writeLanguageFile($filePath, $strings)
	{
		if (empty($strings))
		{
			return false;
		}

		$content = "; MokoWaaS Language Overrides\n";
		$content .= "; Generated by MokoWaaS Plugin\n";
		$content .= "; Last updated: " . date('Y-m-d H:i:s') . "\n\n";

		foreach ($strings as $key => $value)
		{
			// Escape quotes in value
			$value = str_replace('"', '\"', $value);
			$content .= strtoupper($key) . '="' . $value . '"' . "\n";
		}

		return File::write($filePath, $content);
	}
}
