<?php
/**
 * Copyright (C) 2025 Moko Consulting <hello@mokoconsulting.tech>
 *
 * SPDX-LICENSE-IDENTIFIER: GPL-3.0-or-later
 *
 * FILE INFORMATION
 * DEFGROUP: Joomla.Plugin
 * INGROUP: MokoWaaS
 * VERSION: 02.00.00
 * PATH: /src/Field/AllowedIpsField.php
 * BRIEF: Custom form field that displays the current IP whitelist
 */

namespace Moko\Plugin\System\MokoWaaS\Field;

defined('_JEXEC') or die;

use Joomla\CMS\Factory;
use Joomla\CMS\Form\FormField;

class AllowedIpsField extends FormField
{
	protected $type = 'AllowedIps';

	protected function getInput()
	{
		$config     = Factory::getApplication()->getConfig();
		$allowedRaw = $config->get('mokowaas_allowed_ips', '');
		$currentIp  = $_SERVER['REMOTE_ADDR'] ?? 'unknown';

		if (empty($allowedRaw))
		{
			$status  = '<span class="badge bg-danger">Not configured</span>';
			$ipList  = '<em>No IPs set — emergency access is blocked.</em>';
		}
		else
		{
			$ips     = array_map('trim', explode(',', $allowedRaw));
			$status  = '<span class="badge bg-success">'
				. count($ips) . ' IP(s) configured</span>';
			$ipItems = [];

			foreach ($ips as $ip)
			{
				$match = ($ip === $currentIp)
					? ' <span class="badge bg-info">your IP</span>'
					: '';
				$ipItems[] = '<code>' . htmlspecialchars($ip)
					. '</code>' . $match;
			}

			$ipList = implode(', ', $ipItems);
		}

		$yourIp = '<code>' . htmlspecialchars($currentIp) . '</code>';

		return '<div class="alert alert-info mb-0">'
			. '<strong>IP Whitelist:</strong> ' . $status . '<br>'
			. '<strong>Allowed IPs:</strong> ' . $ipList . '<br>'
			. '<strong>Your current IP:</strong> ' . $yourIp . '<br>'
			. '<small class="text-muted">Set <code>public '
			. '$mokowaas_allowed_ips = \'1.2.3.4,5.6.7.8\';</code>'
			. ' in configuration.php to change.</small>'
			. '</div>';
	}

	protected function getLabel()
	{
		return '';
	}
}
