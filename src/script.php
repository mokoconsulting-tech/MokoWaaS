<?php
/**
 * Copyright (C) 2025 Moko Consulting <hello@mokoconsulting.tech>
 *
 * This file is part of a Moko Consulting project.
 *
 * SPDX-LICENSE-IDENTIFIER: GPL-3.0-or-later
 *
 * This program is free software; you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation; either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful, but
 * WITHOUT ANY WARRANTY; without even the IMPLIED WARRANTY of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE. See the GNU
 * General Public License for more details.
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
			$this->updateLoginSupportUrls();
			$this->updateAtumBranding();
			$this->registerActionLogExtension();
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
		// Auto-enable the plugin on first install
		$db = Factory::getDbo();
		$db->setQuery(
			$db->getQuery(true)
				->update($db->quoteName('#__extensions'))
				->set($db->quoteName('enabled') . ' = 1')
				->where($db->quoteName('element') . ' = '
					. $db->quote('mokowaas'))
				->where($db->quoteName('folder') . ' = '
					. $db->quote('system'))
				->where($db->quoteName('type') . ' = '
					. $db->quote('plugin'))
		);
		$db->execute();

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
		$this->uninstallLanguageOverrides();
		$this->unregisterActionLogExtension();

		return true;
	}

	/** Sentinel comment that marks the start of MokoWaaS overrides inside a Joomla override file. */
	private const BLOCK_START = '; ===== BEGIN MokoWaaS Overrides (do not edit this block) =====';

	/** Sentinel comment that marks the end of MokoWaaS overrides inside a Joomla override file. */
	private const BLOCK_END = '; ===== END MokoWaaS Overrides =====';

	/**
	 * Build the placeholder → value map from the plugin's saved params.
	 *
	 * On first install the params row may not exist yet, so every value
	 * falls back to a sensible default.
	 *
	 * @return  array  Associative array of placeholder => replacement value
	 *
	 * @since   02.00.00
	 */
	private function getPlaceholders()
	{
		$params = $this->getPluginParams();

		return [
			'{{BRAND_NAME}}'   => $params->get('brand_name', 'MokoWaaS'),
			'{{COMPANY_NAME}}' => $params->get('company_name', 'Moko Consulting'),
			'{{SUPPORT_URL}}'  => $params->get('support_url', 'https://mokoconsulting.tech'),
		];
	}

	/**
	 * Load the plugin's saved params from the database.
	 *
	 * @return  \Joomla\Registry\Registry
	 *
	 * @since   02.00.00
	 */
	private function getPluginParams()
	{
		$db    = Factory::getDbo();
		$query = $db->getQuery(true)
			->select($db->quoteName('params'))
			->from($db->quoteName('#__extensions'))
			->where($db->quoteName('element') . ' = ' . $db->quote('mokowaas'))
			->where($db->quoteName('folder') . ' = ' . $db->quote('system'))
			->where($db->quoteName('type') . ' = ' . $db->quote('plugin'));

		$db->setQuery($query);
		$json = $db->loadResult();

		return new \Joomla\Registry\Registry($json ?: '{}');
	}

	/**
	 * Resolve placeholders in an array of language strings.
	 *
	 * @param   array  $strings  Key/value pairs (values may contain {{…}} tokens)
	 *
	 * @return  array  The same array with placeholders replaced
	 *
	 * @since   02.00.00
	 */
	private function resolvePlaceholders(array $strings)
	{
		$placeholders = $this->getPlaceholders();
		$search       = array_keys($placeholders);
		$replace      = array_values($placeholders);

		foreach ($strings as $key => $value)
		{
			$strings[$key] = str_replace($search, $replace, $value);
		}

		return $strings;
	}

	/**
	 * Install language override files to Joomla's global override directories.
	 *
	 * Reads each source override template shipped with the plugin, resolves
	 * {{BRAND_NAME}} etc. from plugin params, then merges the resolved keys
	 * into the destination file inside a clearly delimited block.  Existing
	 * overrides outside the block are never touched.
	 *
	 * @return  void
	 *
	 * @since   02.00.00
	 */
	private function installLanguageOverrides()
	{
		$app = Factory::getApplication();
		$pluginPath = JPATH_PLUGINS . '/system/mokowaas';

		$overrideSets = [
			// [source folder relative to plugin, Joomla destination base]
			['language/overrides', JPATH_ROOT . '/language/overrides', 'frontend'],
			['administrator/language/overrides', JPATH_ADMINISTRATOR . '/language/overrides', 'administrator'],
		];

		foreach ($overrideSets as [$sourceDir, $destDir, $label])
		{
			foreach ($this->languageTags as $tag)
			{
				$source = $pluginPath . '/' . $sourceDir . '/' . $tag . '.override.ini';
				$dest   = $destDir . '/' . $tag . '.override.ini';

				if (!file_exists($source))
				{
					continue;
				}

				if (!is_dir($destDir))
				{
					Folder::create($destDir);
				}

				$pluginOverrides = $this->resolvePlaceholders($this->parseLanguageFile($source));

				if (empty($pluginOverrides))
				{
					continue;
				}

				if ($this->mergeOverridesIntoFile($dest, $pluginOverrides))
				{
					$app->enqueueMessage(
						sprintf('Installed %s language overrides for %s', $label, $tag),
						'message'
					);
				}
				else
				{
					$app->enqueueMessage(
						sprintf('Failed to install %s language overrides for %s', $label, $tag),
						'warning'
					);
				}
			}
		}
	}

	/**
	 * Update the mod_loginsupport module params to point to
	 * Moko Consulting URLs at install time.
	 *
	 * @return  void
	 *
	 * @since   02.00.00
	 */
	private function updateLoginSupportUrls()
	{
		$db    = Factory::getDbo();
		$query = $db->getQuery(true)
			->select([$db->quoteName('id'), $db->quoteName('params')])
			->from($db->quoteName('#__modules'))
			->where($db->quoteName('module') . ' = '
				. $db->quote('mod_loginsupport'));

		$db->setQuery($query);
		$modules = $db->loadObjectList();

		if (empty($modules))
		{
			return;
		}

		$supportUrls = [
			'forum_url'         => 'https://mokoconsulting.tech/support',
			'documentation_url' => 'https://mokoconsulting.tech/kb',
			'news_url'          => 'https://mokoconsulting.tech/news',
		];

		foreach ($modules as $module)
		{
			$params = new \Joomla\Registry\Registry(
				$module->params ?: '{}'
			);

			foreach ($supportUrls as $key => $url)
			{
				$params->set($key, $url);
			}

			$update = $db->getQuery(true)
				->update($db->quoteName('#__modules'))
				->set($db->quoteName('params') . ' = '
					. $db->quote($params->toString()))
				->where($db->quoteName('id') . ' = '
					. (int) $module->id);

			$db->setQuery($update);
			$db->execute();
		}

		Factory::getApplication()->enqueueMessage(
			'Updated login support URLs.', 'message'
		);
	}

	/**
	 * Set Atum admin template branding params at install time.
	 *
	 * @return  void
	 *
	 * @since   02.00.00
	 */
	private function updateAtumBranding()
	{
		$mediaBase = 'media/plg_system_mokowaas/';

		$expected = [
			'logoBrandLarge'         => $mediaBase . 'logo.png',
			'logoBrandSmall'         => $mediaBase . 'favicon_256.png',
			'loginLogo'              => $mediaBase . 'logo.png',
			'logoBrandLargeAlt'      => '',
			'logoBrandSmallAlt'      => '',
			'loginLogoAlt'           => '',
			'emptyLogoBrandLargeAlt' => '1',
			'emptyLogoBrandSmallAlt' => '1',
			'emptyLoginLogoAlt'      => '1',
			'hue'                    => 'hsl(219, 44%, 18%)',
			'special-color'          => '#1a2744',
			'link-color'             => '#2ecc71',
		];

		$db    = Factory::getDbo();
		$query = $db->getQuery(true)
			->select([$db->quoteName('id'), $db->quoteName('params')])
			->from($db->quoteName('#__template_styles'))
			->where($db->quoteName('template') . ' = '
				. $db->quote('atum'))
			->where($db->quoteName('client_id') . ' = 1');

		$db->setQuery($query);
		$styles = $db->loadObjectList();

		if (empty($styles))
		{
			return;
		}

		foreach ($styles as $style)
		{
			$params = new \Joomla\Registry\Registry(
				$style->params ?: '{}'
			);

			foreach ($expected as $key => $value)
			{
				$params->set($key, $value);
			}

			$update = $db->getQuery(true)
				->update($db->quoteName('#__template_styles'))
				->set($db->quoteName('params') . ' = '
					. $db->quote($params->toString()))
				->where($db->quoteName('id') . ' = '
					. (int) $style->id);

			$db->setQuery($update);
			$db->execute();
		}

		Factory::getApplication()->enqueueMessage(
			'Updated Atum template branding.', 'message'
		);
	}

	/**
	 * Register the plugin in #__action_logs_extensions so it appears
	 * as a filterable extension in System > Action Logs.
	 *
	 * @return  void
	 *
	 * @since   02.00.00
	 */
	private function registerActionLogExtension()
	{
		$db    = Factory::getDbo();
		$query = $db->getQuery(true)
			->select('COUNT(*)')
			->from($db->quoteName('#__action_logs_extensions'))
			->where($db->quoteName('extension') . ' = '
				. $db->quote('plg_system_mokowaas'));

		$db->setQuery($query);

		if ((int) $db->loadResult() > 0)
		{
			return;
		}

		$row = (object) ['extension' => 'plg_system_mokowaas'];
		$db->insertObject('#__action_logs_extensions', $row);

		Factory::getApplication()->enqueueMessage(
			'Registered MokoWaaS in Action Logs.', 'message'
		);

		// Register content type config for display formatting
		$configQuery = $db->getQuery(true)
			->select('COUNT(*)')
			->from($db->quoteName('#__action_log_config'))
			->where($db->quoteName('type_alias') . ' = '
				. $db->quote('plg_system_mokowaas'));

		$db->setQuery($configQuery);

		if ((int) $db->loadResult() === 0)
		{
			$config = (object) [
				'type_title'          => 'MokoWaaS',
				'type_alias'          => 'plg_system_mokowaas',
				'id_holder'           => '',
				'title_holder'        => '',
				'table_name'          => '',
				'text_prefix'         => 'PLG_SYSTEM_MOKOWAAS',
			];

			$db->insertObject('#__action_log_config', $config);
		}
	}

	/**
	 * Remove the plugin from #__action_logs_extensions on uninstall.
	 *
	 * @return  void
	 *
	 * @since   02.00.00
	 */
	private function unregisterActionLogExtension()
	{
		$db = Factory::getDbo();

		$db->setQuery(
			$db->getQuery(true)
				->delete($db->quoteName('#__action_logs_extensions'))
				->where($db->quoteName('extension') . ' = '
					. $db->quote('plg_system_mokowaas'))
		);
		$db->execute();

		$db->setQuery(
			$db->getQuery(true)
				->delete($db->quoteName('#__action_log_config'))
				->where($db->quoteName('type_alias') . ' = '
					. $db->quote('plg_system_mokowaas'))
		);
		$db->execute();
	}

	/**
	 * Remove only MokoWaaS overrides from Joomla's global override files.
	 *
	 * Strips the delimited MokoWaaS block and any duplicate keys that appear
	 * outside the block (safety net for upgrades from older versions that wrote
	 * keys inline).  All other content is preserved verbatim.
	 *
	 * @return  void
	 *
	 * @since   02.00.00
	 */
	private function uninstallLanguageOverrides()
	{
		$app = Factory::getApplication();
		$pluginPath = JPATH_PLUGINS . '/system/mokowaas';

		$overrideSets = [
			['language/overrides', JPATH_ROOT . '/language/overrides', 'frontend'],
			['administrator/language/overrides', JPATH_ADMINISTRATOR . '/language/overrides', 'administrator'],
		];

		foreach ($overrideSets as [$sourceDir, $destDir, $label])
		{
			foreach ($this->languageTags as $tag)
			{
				$source = $pluginPath . '/' . $sourceDir . '/' . $tag . '.override.ini';
				$dest   = $destDir . '/' . $tag . '.override.ini';

				if (!file_exists($dest))
				{
					continue;
				}

				$pluginKeys = array_keys($this->parseLanguageFile($source));

				if ($this->removeOverridesFromFile($dest, $pluginKeys))
				{
					$app->enqueueMessage(
						sprintf('Removed %s language overrides for %s', $label, $tag),
						'message'
					);
				}
			}
		}
	}

	/**
	 * Merge plugin overrides into an existing Joomla override file.
	 *
	 * The method:
	 *  1. Reads the destination file (if it exists) and preserves every line.
	 *  2. Strips any previous MokoWaaS block so it can be rewritten cleanly.
	 *  3. Collects keys that exist outside the block (user-set overrides).
	 *  4. Appends a MokoWaaS block containing only keys NOT already
	 *     defined by the user — existing customisations are never touched.
	 *
	 * @param   string  $dest       Absolute path to the Joomla override file
	 * @param   array   $overrides  Key/value pairs to inject
	 *
	 * @return  boolean  True on success
	 *
	 * @since   02.00.00
	 */
	private function mergeOverridesIntoFile($dest, array $overrides)
	{
		$existingLines = [];

		if (file_exists($dest))
		{
			$existingLines = file($dest, FILE_IGNORE_NEW_LINES);
		}

		// Strip any previous MokoWaaS block
		$existingLines = $this->stripMokoWaaSBlock($existingLines);

		// Collect keys already defined outside the block (user overrides)
		$userKeys = [];

		foreach ($existingLines as $line)
		{
			$trimmed = trim($line);

			if ($trimmed !== '' && $trimmed[0] !== ';')
			{
				if (preg_match('/^([A-Z0-9_]+)\s*=/i', $trimmed, $m))
				{
					$userKeys[] = strtoupper($m[1]);
				}
			}
		}

		// Remove trailing blank lines so the block starts cleanly
		while (!empty($existingLines)
			&& trim(end($existingLines)) === '')
		{
			array_pop($existingLines);
		}

		// Build the MokoWaaS block — skip keys the user already set
		$block   = [];
		$block[] = '';
		$block[] = self::BLOCK_START;
		$block[] = '; Auto-generated on '
			. date('Y-m-d H:i:s') . ' — do not edit manually.';

		foreach ($overrides as $key => $value)
		{
			if (!in_array(strtoupper($key), $userKeys, true))
			{
				$block[] = strtoupper($key) . '="' . $value . '"';
			}
		}

		$block[] = self::BLOCK_END;
		$block[] = '';

		$content = implode("\n", array_merge($existingLines, $block));

		return File::write($dest, $content);
	}

	/**
	 * Remove MokoWaaS overrides from an existing Joomla override file.
	 *
	 * Strips the delimited block and any stray keys that match, then rewrites
	 * the file.  If the file would be empty (or comments-only) it is deleted.
	 *
	 * @param   string  $dest  Absolute path to the override file
	 * @param   array   $keys  The override keys to remove (uppercase)
	 *
	 * @return  boolean  True on success
	 *
	 * @since   02.00.00
	 */
	private function removeOverridesFromFile($dest, array $keys)
	{
		if (!file_exists($dest))
		{
			return true;
		}

		$lines = file($dest, FILE_IGNORE_NEW_LINES);

		// Strip the MokoWaaS block
		$lines = $this->stripMokoWaaSBlock($lines);

		// Also strip any stray keys that match (legacy installs)
		$upperKeys = array_map('strtoupper', $keys);
		$cleaned   = [];

		foreach ($lines as $line)
		{
			$trimmed = trim($line);

			if ($trimmed !== '' && $trimmed[0] !== ';')
			{
				if (preg_match('/^([A-Z0-9_]+)\s*=/i', $trimmed, $m))
				{
					if (in_array(strtoupper($m[1]), $upperKeys, true))
					{
						continue;
					}
				}
			}

			$cleaned[] = $line;
		}

		// Check whether any real keys remain
		$hasKeys = false;

		foreach ($cleaned as $line)
		{
			$trimmed = trim($line);

			if ($trimmed !== '' && $trimmed[0] !== ';')
			{
				$hasKeys = true;
				break;
			}
		}

		if (!$hasKeys)
		{
			return File::delete($dest);
		}

		return File::write($dest, implode("\n", $cleaned) . "\n");
	}

	/**
	 * Remove the MokoWaaS sentinel block from an array of file lines.
	 *
	 * @param   array  $lines  Lines of the file (no trailing newlines)
	 *
	 * @return  array  Lines with the block removed
	 *
	 * @since   02.00.00
	 */
	private function stripMokoWaaSBlock(array $lines)
	{
		$out     = [];
		$inBlock = false;

		foreach ($lines as $line)
		{
			if (trim($line) === self::BLOCK_START)
			{
				$inBlock = true;
				continue;
			}

			if (trim($line) === self::BLOCK_END)
			{
				$inBlock = false;
				continue;
			}

			if (!$inBlock)
			{
				$out[] = $line;
			}
		}

		return $out;
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
		$lines   = explode("\n", $content);

		foreach ($lines as $line)
		{
			$line = trim($line);

			// Skip empty lines and comments
			if ($line === '' || $line[0] === ';')
			{
				continue;
			}

			// Parse KEY="VALUE" format
			if (preg_match('/^([A-Z0-9_]+)="(.+)"$/i', $line, $matches))
			{
				$strings[strtoupper($matches[1])] = $matches[2];
			}
		}

		return $strings;
	}
}
