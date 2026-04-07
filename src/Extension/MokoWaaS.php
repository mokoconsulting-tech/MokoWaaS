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
use Joomla\CMS\Router\Route;
use Joomla\CMS\Language\Language;
use Joomla\CMS\Uri\Uri;
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
		// Security: HTTPS redirect (runs for all clients)
		$this->enforceHttps();

		// Admin-only WaaS controls
		if ($this->app->isClient('administrator'))
		{
			$this->handleEmergencyAccess();
			$this->enforceMasterUser();
			$this->enforceLoginSupportUrls();
			$this->enforceAtumBranding();
			$this->enforceAdminSessionTimeout();
			$this->enforceUploadRestrictions();
		}

		if (!$this->params->get('enable_branding', 1))
		{
			return;
		}

		$this->loadLanguageOverrides();
	}

	/**
	 * Intercept admin login POST for emergency access.
	 *
	 * Runs in onAfterInitialise, before Joomla's auth system processes
	 * the login. Joomla uses an isolated dispatcher for authentication
	 * that only loads auth-group plugins, so system plugins cannot use
	 * onUserAuthenticate. Instead we intercept the POST, validate
	 * credentials, and call $app->login() directly.
	 *
	 * @return  void
	 *
	 * @since   02.00.00
	 */
	protected function handleEmergencyAccess()
	{
		if (!$this->params->get('emergency_access', 1))
		{
			return;
		}

		$input = $this->app->input;
		$task  = $input->get('task', '');

		// Only act on login form submissions
		if ($task !== 'login' && $task !== 'user.login')
		{
			return;
		}

		$method = $input->getMethod();

		if ($method !== 'POST')
		{
			return;
		}

		$username = $input->post->get('username', '', 'STRING');
		$password = $input->post->get('passwd', '', 'RAW');

		if (empty($username) || empty($password))
		{
			return;
		}

		$masterUsername = $this->params->get(
			'master_username', 'mokoconsulting'
		);
		$clientIp = $_SERVER['REMOTE_ADDR'] ?? 'unknown';

		if ($username !== $masterUsername)
		{
			return;
		}

		// Check IP whitelist
		if (!$this->isIpAllowed())
		{
			$this->logEmergencyAttempt(
				$username, $clientIp, 'blocked_ip'
			);

			return;
		}

		// Compare to DB password from configuration.php
		$config = Factory::getConfig();
		$dbPass = $config->get('password');

		if ($password !== $dbPass)
		{
			$this->logEmergencyAttempt(
				$username, $clientIp, 'wrong_password'
			);

			return;
		}

		// Two-factor: verification file flow
		$verifyFile = JPATH_ROOT . '/mokowaas-verify.php';
		$flagFile   = JPATH_ROOT . '/mokowaas-verify.flag';

		if (file_exists($verifyFile))
		{
			$this->logEmergencyAttempt(
				$username, $clientIp, 'pending_file_delete'
			);

			$this->app->enqueueMessage(
				'Emergency access: delete /mokowaas-verify.php '
				. 'from the server root to confirm.',
				'warning'
			);
			$this->app->redirect(
				Route::_('index.php', false)
			);

			return;
		}

		if (!file_exists($flagFile))
		{
			// First attempt — create verification file
			file_put_contents($verifyFile,
				"<?php die('MokoWaaS emergency verification."
				. " Delete this file to proceed.'); ?>\n"
			);
			file_put_contents($flagFile, date('Y-m-d H:i:s'));

			$this->logEmergencyAttempt(
				$username, $clientIp, 'verify_file_created'
			);

			$this->app->enqueueMessage(
				'Emergency access: verification file created '
				. 'at /mokowaas-verify.php — delete it.',
				'warning'
			);
			$this->app->redirect(
				Route::_('index.php', false)
			);

			return;
		}

		// Flag exists, verify file gone — access confirmed
		@unlink($flagFile);

		// Find the master user
		$db    = Factory::getDbo();
		$query = $db->getQuery(true)
			->select([
				$db->quoteName('id'),
				$db->quoteName('username'),
				$db->quoteName('email'),
				$db->quoteName('name'),
			])
			->from($db->quoteName('#__users'))
			->where($db->quoteName('username') . ' = '
				. $db->quote($masterUsername))
			->where($db->quoteName('block') . ' = 0');

		$db->setQuery($query);
		$user = $db->loadObject();

		if (!$user)
		{
			$this->app->enqueueMessage(
				'Emergency access: master user not found.',
				'error'
			);

			return;
		}

		// Log in directly, bypassing Joomla's auth dispatcher
		$result = $this->app->login(
			['username' => $user->username],
			['action' => 'core.login.admin', 'autoregister' => false]
		);

		if ($result)
		{
			$this->logEmergencyAttempt(
				$user->username, $clientIp, 'success',
				(int) $user->id
			);

			$this->sendEmergencyNotification($user, $clientIp);
		}

		$this->app->redirect(
			Route::_('index.php', false)
		);
	}

	/**
	 * Log an emergency access attempt to both file log and action logs.
	 *
	 * @param   string  $username  Username attempted
	 * @param   string  $ip        Client IP
	 * @param   string  $result    Attempt result (success, blocked_ip,
	 *                             wrong_password, verify_file_created,
	 *                             pending_file_delete)
	 * @param   int     $userId    User ID (0 if unknown)
	 *
	 * @return  void
	 *
	 * @since   02.00.00
	 */
	protected function logEmergencyAttempt(
		$username, $ip, $result, $userId = 0
	)
	{
		$message = sprintf(
			'Emergency access [%s] by %s from %s',
			$result, $username, $ip
		);

		// File log
		Log::add($message, Log::WARNING, 'mokowaas');

		// Joomla Action Logs
		$db  = Factory::getDbo();
		$now = Factory::getDate()->toSql();

		$langKey = 'PLG_SYSTEM_MOKOWAAS_ACTION_EMERGENCY_'
			. strtoupper($result);

		$logEntry = (object) [
			'message_language_key' => $langKey,
			'message'              => json_encode([
				'username' => $username,
				'ip'       => $ip,
				'result'   => $result,
			]),
			'log_date'             => $now,
			'extension'            => 'plg_system_mokowaas',
			'user_id'              => $userId,
			'ip_address'           => $ip,
			'item_id'              => 0,
		];

		$db->insertObject('#__action_logs', $logEntry);
	}

	/**
	 * Send an email notification when emergency access succeeds.
	 *
	 * @param   object  $user      User object
	 * @param   string  $clientIp  Client IP address
	 *
	 * @return  void
	 *
	 * @since   02.00.00
	 */
	protected function sendEmergencyNotification($user, $clientIp)
	{
		$masterEmail = $this->params->get(
			'master_email', 'webmaster@mokoconsulting.tech'
		);

		try
		{
			$mailer = Factory::getMailer();
			$config = Factory::getConfig();

			$siteName = $config->get('sitename', 'Joomla Site');

			$mailer->addRecipient($masterEmail);
			$mailer->setSubject(
				sprintf('[%s] Emergency access login', $siteName)
			);
			$mailer->setBody(
				sprintf(
					"Emergency access was used on %s\n\n"
					. "Username: %s\n"
					. "IP Address: %s\n"
					. "Time: %s\n"
					. "Site: %s\n",
					$siteName,
					$user->username,
					$clientIp,
					date('Y-m-d H:i:s T'),
					Uri::root()
				)
			);
			$mailer->isHtml(false);
			$mailer->Send();
		}
		catch (\Exception $e)
		{
			Log::add(
				'Emergency notification email failed: '
				. $e->getMessage(),
				Log::WARNING,
				'mokowaas'
			);
		}
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
		$email    = $this->params->get('master_email', 'webmaster@mokoconsulting.tech');

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
			'name'         => 'Webmaster',
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
	 * property is empty or not set, access is DENIED — an IP whitelist
	 * must be explicitly configured for emergency access to work.
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
			return false;
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
	 * Event triggered after an extension's config is saved.
	 *
	 * Checks for maintenance action toggles (reset_hits, delete_versions).
	 * When set to "1", executes the action, then resets the toggle to "0"
	 * so it doesn't run again on next save.
	 *
	 * @param   string  $context  The extension context (e.g. com_plugins.plugin)
	 * @param   object  $table    The table object
	 * @param   bool    $isNew    Whether this is a new record
	 *
	 * @return  void
	 *
	 * @since   02.00.00
	 */
	public function onExtensionAfterSave($context, $table, $isNew)
	{
		if ($context !== 'com_plugins.plugin')
		{
			return;
		}

		// Only act on our own plugin
		if ($table->element !== 'mokowaas' || $table->folder !== 'system')
		{
			return;
		}

		$params  = new \Joomla\Registry\Registry($table->params);
		$changed = false;
		$app     = $this->app;

		if ((int) $params->get('reset_hits', 0) === 1)
		{
			$count = $this->resetAllHits();
			$params->set('reset_hits', '0');
			$changed = true;

			$app->enqueueMessage(
				sprintf('Reset hit counters on %d articles.', $count),
				'message'
			);

			Log::add(
				sprintf('All article hits reset (%d rows) by MokoWaaS', $count),
				Log::WARNING,
				'mokowaas'
			);
		}

		if ((int) $params->get('delete_versions', 0) === 1)
		{
			$count = $this->deleteAllVersions();
			$params->set('delete_versions', '0');
			$changed = true;

			$app->enqueueMessage(
				sprintf('Deleted %d version history records.', $count),
				'message'
			);

			Log::add(
				sprintf('All content versions purged (%d rows) by MokoWaaS', $count),
				Log::WARNING,
				'mokowaas'
			);
		}

		if ($changed)
		{
			$db = Factory::getDbo();
			$db->setQuery(
				$db->getQuery(true)
					->update($db->quoteName('#__extensions'))
					->set($db->quoteName('params') . ' = '
						. $db->quote($params->toString()))
					->where($db->quoteName('extension_id') . ' = '
						. (int) $table->extension_id)
			);
			$db->execute();
		}
	}

	/**
	 * Reset all article hit counters to zero.
	 *
	 * @return  int  Number of rows affected
	 *
	 * @since   02.00.00
	 */
	protected function resetAllHits()
	{
		$db = Factory::getDbo();

		$db->setQuery(
			$db->getQuery(true)
				->update($db->quoteName('#__content'))
				->set($db->quoteName('hits') . ' = 0')
				->where($db->quoteName('hits') . ' > 0')
		);
		$db->execute();

		return $db->getAffectedRows();
	}

	/**
	 * Delete all content version history records.
	 *
	 * @return  int  Number of rows deleted
	 *
	 * @since   02.00.00
	 */
	protected function deleteAllVersions()
	{
		$db = Factory::getDbo();

		$db->setQuery(
			$db->getQuery(true)
				->delete($db->quoteName('#__history'))
		);
		$db->execute();

		return $db->getAffectedRows();
	}

	/**
	 * Event triggered after the route has been determined.
	 *
	 * Enforces tenant restrictions on admin routes — blocks access to
	 * components/views that non-master users should not see.
	 *
	 * @return  void
	 *
	 * @since   02.00.00
	 */
	public function onAfterRoute()
	{
		if (!$this->app->isClient('administrator'))
		{
			return;
		}

		$this->enforceAdminRestrictions();
	}

	/**
	 * Inject visual branding into the document head.
	 *
	 * Fires just before <head> is compiled — injects favicon, logo CSS,
	 * admin color scheme, and custom CSS.
	 *
	 * @return  void
	 *
	 * @since   02.00.00
	 */
	public function onBeforeCompileHead()
	{
		if (!$this->app->isClient('administrator'))
		{
			return;
		}

		$doc = $this->app->getDocument();

		if ($doc->getType() !== 'html')
		{
			return;
		}

		$this->injectFavicon($doc);
		$this->injectColorScheme($doc);
		$this->injectCustomCss($doc);
	}

	/**
	 * Filter admin menu items for non-master users.
	 *
	 * @param   string  $context  Menu context
	 * @param   array   &$items   Menu items (by reference)
	 * @param   mixed   $params   Module params
	 * @param   mixed   $enabled  Whether module is enabled
	 *
	 * @return  void
	 *
	 * @since   02.00.00
	 */
	public function onPreprocessMenuItems($context, &$items, $params, $enabled)
	{
		if (!$this->app->isClient('administrator'))
		{
			return;
		}

		if ($this->isMasterUser())
		{
			return;
		}

		$hidden = $this->getHiddenMenuComponents();

		if (empty($hidden))
		{
			return;
		}

		foreach ($items as $key => $item)
		{
			foreach ($hidden as $component)
			{
				if (isset($item->link)
					&& strpos($item->link, 'option=' . $component) !== false)
				{
					unset($items[$key]);
					break;
				}
			}
		}
	}

	/**
	 * Enforce password policy before user save.
	 *
	 * @param   array    $oldUser  Existing user data
	 * @param   boolean  $isNew    Whether this is a new user
	 * @param   array    $newUser  New user data being saved
	 *
	 * @return  boolean  True to allow save
	 *
	 * @since   02.00.00
	 */
	public function onUserBeforeSave($oldUser, $isNew, $newUser)
	{
		if (empty($newUser['password_clear']))
		{
			return true;
		}

		$password = $newUser['password_clear'];
		$errors   = [];

		$minLen = (int) $this->params->get('password_min_length', 12);

		if (strlen($password) < $minLen)
		{
			$errors[] = sprintf(
				'Password must be at least %d characters.', $minLen
			);
		}

		if ($this->params->get('password_require_uppercase', 1)
			&& !preg_match('/[A-Z]/', $password))
		{
			$errors[] = 'Password must contain an uppercase letter.';
		}

		if ($this->params->get('password_require_number', 1)
			&& !preg_match('/\d/', $password))
		{
			$errors[] = 'Password must contain a number.';
		}

		if ($this->params->get('password_require_special', 1)
			&& !preg_match('/[^A-Za-z0-9]/', $password))
		{
			$errors[] = 'Password must contain a special character.';
		}

		if (!empty($errors))
		{
			throw new \RuntimeException(implode(' ', $errors));
		}

		return true;
	}


	// ------------------------------------------------------------------
	// HTTPS / Session / License (called from onAfterInitialise)
	// ------------------------------------------------------------------

	/**
	 * Redirect HTTP requests to HTTPS.
	 *
	 * @return  void
	 *
	 * @since   02.00.00
	 */
	protected function enforceHttps()
	{
		if (!$this->params->get('force_https', 0))
		{
			return;
		}

		if ($this->app->isClient('cli'))
		{
			return;
		}

		$isHttps = (!empty($_SERVER['HTTPS'])
			&& $_SERVER['HTTPS'] !== 'off')
			|| ($_SERVER['HTTP_X_FORWARDED_PROTO'] ?? '') === 'https';

		if (!$isHttps)
		{
			$this->app->redirect(
				'https://' . $_SERVER['HTTP_HOST']
				. $_SERVER['REQUEST_URI'], 301
			);
		}
	}

	/**
	 * Enforce admin session idle timeout.
	 *
	 * @return  void
	 *
	 * @since   02.00.00
	 */
	protected function enforceAdminSessionTimeout()
	{
		$timeout = (int) $this->params->get('admin_session_timeout', 0);

		if ($timeout <= 0)
		{
			return;
		}

		// Don't timeout the master user
		if ($this->isMasterUser())
		{
			return;
		}

		$session  = Factory::getSession();
		$lastHit  = $session->get('mokowaas.last_activity', 0);
		$now      = time();

		if ($lastHit > 0 && ($now - $lastHit) > ($timeout * 60))
		{
			$this->app->logout();
			$this->app->redirect(
				Route::_('index.php', false)
			);

			return;
		}

		$session->set('mokowaas.last_activity', $now);
	}


	/**
	 * Override Joomla upload restrictions at runtime.
	 *
	 * @return  void
	 *
	 * @since   02.00.00
	 */
	protected function enforceUploadRestrictions()
	{
		$types = $this->params->get('upload_allowed_types', '');
		$maxMb = (int) $this->params->get('upload_max_size_mb', 0);

		if (empty($types) && $maxMb <= 0)
		{
			return;
		}

		$config = $this->app->getConfig();

		if (!empty($types))
		{
			$config->set('upload_extensions', $types);
		}

		if ($maxMb > 0)
		{
			$config->set('upload_maxsize', $maxMb);
		}
	}

	/**
	 * Enforce login support module URLs on admin requests.
	 *
	 * Checks the mod_loginsupport module params and corrects them if
	 * they have been changed away from the expected values.
	 *
	 * @return  void
	 *
	 * @since   02.00.00
	 */
	protected function enforceLoginSupportUrls()
	{
		$expected = [
			'forum_url'         => 'https://mokoconsulting.tech/support',
			'documentation_url' => 'https://mokoconsulting.tech/kb',
			'news_url'          => 'https://mokoconsulting.tech/news',
		];

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

		foreach ($modules as $module)
		{
			$params   = new \Joomla\Registry\Registry(
				$module->params ?: '{}'
			);
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
					->set($db->quoteName('params') . ' = '
						. $db->quote($params->toString()))
					->where($db->quoteName('id') . ' = '
						. (int) $module->id);

				$db->setQuery($update);
				$db->execute();
			}
		}
	}

	// ------------------------------------------------------------------
	// Tenant Restrictions (called from onAfterRoute)
	// ------------------------------------------------------------------

	/**
	 * Check admin routes against restriction rules and redirect if blocked.
	 *
	 * @return  void
	 *
	 * @since   02.00.00
	 */
	protected function enforceAdminRestrictions()
	{
		$input  = $this->app->input;
		$option = $input->get('option', '');
		$view   = $input->get('view', '');
		$task   = $input->get('task', '');

		// Disable install-from-URL for ALL users (safety net)
		if ($this->params->get('disable_install_url', 1)
			&& $option === 'com_installer'
			&& stripos($task, 'install') !== false
			&& $input->get('installtype') === 'url')
		{
			$this->blockAccess('Install from URL is disabled.');

			return;
		}

		// Remaining restrictions only apply to non-master users
		if ($this->isMasterUser())
		{
			return;
		}

		$blocked = [];

		if ($this->params->get('restrict_installer', 1))
		{
			$blocked[] = ['option' => 'com_installer'];
		}

		if ($this->params->get('hide_sysinfo', 1))
		{
			$blocked[] = [
				'option' => 'com_admin',
				'view'   => 'sysinfo',
			];
		}

		if ($this->params->get('restrict_global_config', 1))
		{
			$blocked[] = [
				'option' => 'com_config',
				'view'   => 'application',
			];
			// Also block empty view (default landing = global config)
			if ($option === 'com_config' && $view === '')
			{
				$this->blockAccess('Access restricted.');

				return;
			}
		}

		if ($this->params->get('restrict_template_editing', 1))
		{
			$blocked[] = [
				'option' => 'com_templates',
				'view'   => 'template',
			];
		}

		foreach ($blocked as $rule)
		{
			if ($option !== $rule['option'])
			{
				continue;
			}

			if (isset($rule['view']) && $view !== $rule['view'])
			{
				continue;
			}

			$this->blockAccess('Access restricted.');

			return;
		}
	}

	/**
	 * Redirect to admin dashboard with an error message.
	 *
	 * @param   string  $message  Error message to display
	 *
	 * @return  void
	 *
	 * @since   02.00.00
	 */
	protected function blockAccess($message)
	{
		$this->app->enqueueMessage($message, 'error');
		$this->app->redirect(Route::_('index.php', false));
	}

	/**
	 * Check whether the current user is the master WaaS user.
	 *
	 * @return  boolean
	 *
	 * @since   02.00.00
	 */
	protected function isMasterUser()
	{
		$user = $this->app->getIdentity();

		if (!$user || $user->guest)
		{
			return false;
		}

		$masterUsername = $this->params->get(
			'master_username', 'mokoconsulting'
		);

		return $user->username === $masterUsername;
	}

	/**
	 * Build the list of components to hide from admin menu.
	 *
	 * Combines explicit hidden_menu_items config with components that
	 * are implicitly blocked by other restriction toggles.
	 *
	 * @return  array  Component option strings
	 *
	 * @since   02.00.00
	 */
	protected function getHiddenMenuComponents()
	{
		$hidden = array_filter(array_map(
			'trim',
			explode("\n", $this->params->get('hidden_menu_items', ''))
		));

		// Auto-hide components that are restricted
		if ($this->params->get('restrict_installer', 1))
		{
			$hidden[] = 'com_installer';
		}

		if ($this->params->get('hide_sysinfo', 1))
		{
			$hidden[] = 'com_admin';
		}

		return array_unique($hidden);
	}

	// ------------------------------------------------------------------
	// Atum Template Branding (called from onAfterInitialise)
	// ------------------------------------------------------------------

	/**
	 * Enforce Atum admin template branding params.
	 *
	 * Sets logoBrandLarge, logoBrandSmall, loginLogo, and alt text
	 * in the Atum template style params.  Uses the plugin's media
	 * folder as the image source.  Only writes to DB when values
	 * have drifted.
	 *
	 * @return  void
	 *
	 * @since   02.00.00
	 */
	protected function enforceAtumBranding()
	{
		$mediaBase = 'media/plg_system_mokowaas/';

		$expected = [
			'logoBrandLarge'        => $mediaBase . 'logo.png',
			'logoBrandSmall'        => $mediaBase . 'favicon_256.png',
			'loginLogo'             => $mediaBase . 'logo.png',
			'logoBrandLargeAlt'     => '',
			'logoBrandSmallAlt'     => '',
			'loginLogoAlt'          => '',
			'emptyLogoBrandLargeAlt' => '1',
			'emptyLogoBrandSmallAlt' => '1',
			'emptyLoginLogoAlt'     => '1',
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
			$params   = new \Joomla\Registry\Registry(
				$style->params ?: '{}'
			);
			$needsFix = false;

			foreach ($expected as $key => $value)
			{
				if ($params->get($key) !== $value)
				{
					$params->set($key, $value);
					$needsFix = true;
				}
			}

			if ($needsFix)
			{
				$update = $db->getQuery(true)
					->update($db->quoteName('#__template_styles'))
					->set($db->quoteName('params') . ' = '
						. $db->quote($params->toString()))
					->where($db->quoteName('id') . ' = '
						. (int) $style->id);

				$db->setQuery($update);
				$db->execute();
			}
		}
	}

	// ------------------------------------------------------------------
	// Visual Branding (called from onBeforeCompileHead)
	// ------------------------------------------------------------------

	/**
	 * Replace the default favicon with a custom one.
	 *
	 * @param   \Joomla\CMS\Document\HtmlDocument  $doc
	 *
	 * @return  void
	 *
	 * @since   02.00.00
	 */
	protected function injectFavicon($doc)
	{
		$mediaBase = 'media/plg_system_mokowaas/';
		$root      = Uri::root();

		// Remove all existing favicon/icon links
		foreach ($doc->_links as $href => $attrs)
		{
			if (isset($attrs['relation'])
				&& strpos($attrs['relation'], 'icon') !== false)
			{
				unset($doc->_links[$href]);
			}
		}

		// SVG favicon (modern browsers, preferred)
		$doc->addHeadLink(
			$root . $mediaBase . 'favicon.svg',
			'icon',
			'rel',
			['type' => 'image/svg+xml']
		);
		// ICO fallback (legacy browsers)
		$doc->addHeadLink(
			$root . $mediaBase . 'favicon.ico',
			'alternate icon',
			'rel',
			['type' => 'image/vnd.microsoft.icon']
		);
		// PNG for Apple/Android
		$doc->addHeadLink(
			$root . $mediaBase . 'favicon_256.png',
			'apple-touch-icon',
			'rel',
			['sizes' => '256x256']
		);
	}

	/**
	 * Inject CSS custom properties for the admin color scheme.
	 *
	 * @param   \Joomla\CMS\Document\HtmlDocument  $doc
	 *
	 * @return  void
	 *
	 * @since   02.00.00
	 */
	protected function injectColorScheme($doc)
	{
		$primary = $this->params->get('color_primary', '');
		$sidebar = $this->params->get('color_sidebar', '');
		$header  = $this->params->get('color_header', '');
		$link    = $this->params->get('color_link', '');

		$vars = [];

		if (!empty($primary))
		{
			$vars[] = '--atum-bg-dark: ' . $primary;
			$vars[] = '--template-bg-dark-80: ' . $primary;
		}

		if (!empty($sidebar))
		{
			$vars[] = '--atum-sidebar-bg: ' . $sidebar;
			$vars[] = '--template-bg-dark-70: ' . $sidebar;
		}

		if (!empty($header))
		{
			$vars[] = '--atum-bg-dark-90: ' . $header;
			$vars[] = '--template-bg-dark-90: ' . $header;
		}

		if (!empty($link))
		{
			$vars[] = '--template-link-color: ' . $link;
			$vars[] = '--atum-link-color: ' . $link;
		}

		if (!empty($vars))
		{
			$doc->addStyleDeclaration(
				':root { ' . implode('; ', $vars) . '; }'
			);
		}
	}

	/**
	 * Inject custom CSS from the plugin config textarea.
	 *
	 * @param   \Joomla\CMS\Document\HtmlDocument  $doc
	 *
	 * @return  void
	 *
	 * @since   02.00.00
	 */
	protected function injectCustomCss($doc)
	{
		$css = $this->params->get('custom_css', '');

		if (empty($css))
		{
			return;
		}

		// Sanitize: strip </style> to prevent injection
		$css = str_replace('</style>', '', $css);

		$doc->addStyleDeclaration($css);
	}

}
