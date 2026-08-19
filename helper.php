<?php
/**
 * @copyright	Copyright (c) 2014 Skyline Technology Ltd (http://extstore.com). All rights reserved.
 * @copyright	Copyright (c) 2026 Jayden Russell. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;


class PlgSystemLoginPopupHelper {
	/**
	 * Retrieve the url where the user should be returned after logging in.
	 *
	 * When redirect_enabled is Yes, applies dynamic redirect logic:
	 *   1. Check redirect_map for matching source_itemid
	 *   2. Fallback to current page
	 *
	 * When redirect_enabled is No, falls back to original static behavior.
	 *
	 * @param   JRegistry  $params  plugin parameters
	 * @param   string     $type    return type ('login' or 'logout')
	 *
	 * @return string  base64-encoded internal URL
	 */
	public static function getReturnURL($params, $type) {
		$app    = JFactory::getApplication();
		$router = $app::getRouter();

		// Dynamic redirect only applies to login, not logout
		if ($type === 'login' && (int) $params->get('redirect_enabled', 0) === 1) {
			return self::getDynamicReturnURL($params);
		}

		// Original static behavior
		return self::getStaticReturnURL($params, $type);
	}

	/**
	 * Build a dynamic return URL based on redirect_map rules.
	 *
	 * @param   JRegistry  $params  plugin parameters
	 *
	 * @return string  base64-encoded internal URL
	 */
	private static function getDynamicReturnURL($params) {
		$app        = JFactory::getApplication();
		$menu       = $app->getMenu();
		$active     = $menu->getActive();
		$currentId  = $active ? (int) $active->id : 0;

		// 1. Check redirect_map for matching source_itemid
		$redirectMap = $params->get('redirect_map', array());

		if (!empty($redirectMap) && is_array($redirectMap)) {
			foreach ($redirectMap as $rule) {
				$sourceId = isset($rule->source_itemid) ? (int) $rule->source_itemid : 0;
				$targetId = isset($rule->target_itemid) ? (int) $rule->target_itemid : 0;

				if ($sourceId > 0 && $targetId > 0 && $sourceId === $currentId) {
					return self::buildItemidUrl($targetId);
				}
			}
		}

		// 2. Fallback: return to current page
		return self::getCurrentPageUrl();
	}

	/**
	 * Build a base64-encoded internal URL from a menu item ID.
	 *
	 * @param   int  $itemid  menu item ID
	 *
	 * @return string  base64-encoded URL
	 */
	private static function buildItemidUrl($itemid) {
		$itemid = (int) $itemid;
		if ($itemid <= 0) {
			return self::getCurrentPageUrl();
		}

		$url = 'index.php?Itemid=' . $itemid;

		return base64_encode($url);
	}

	/**
	 * Get the current page as a base64-encoded internal URL (fallback).
	 *
	 * @return string  base64-encoded URL
	 */
	private static function getCurrentPageUrl() {
		$app    = JFactory::getApplication();
		$router = $app::getRouter();
		$url    = null;

		$uri   = clone JUri::getInstance();
		$vars  = $router->parse($uri);
		unset($vars['lang']);

		if ($router->getMode() == JROUTER_MODE_SEF) {
			if (isset($vars['Itemid'])) {
				$itemid = $vars['Itemid'];
				$menu   = $app->getMenu();
				$item   = $menu->getItem($itemid);
				unset($vars['Itemid']);

				if (isset($item) && $vars == $item->query) {
					$url = 'index.php?Itemid=' . $itemid;
				} else {
					$url = 'index.php?' . JUri::buildQuery($vars) . '&Itemid=' . $itemid;
				}
			} else {
				$url = 'index.php?' . JUri::buildQuery($vars);
			}
		} else {
			$url = 'index.php?' . JUri::buildQuery($vars);
		}

		return base64_encode($url);
	}

	/**
	 * Original static return URL logic (used when redirect_enabled is No).
	 *
	 * @param   JRegistry  $params  plugin parameters
	 * @param   string     $type    return type
	 *
	 * @return string  base64-encoded URL
	 */
	private static function getStaticReturnURL($params, $type) {
		$app    = JFactory::getApplication();
		$router = $app::getRouter();
		$url    = null;

		if ($itemid = (int) $params->get($type)) {
			$db    = JFactory::getDbo();
			$query = $db->getQuery(true)
				->select($db->quoteName('link'))
				->from($db->quoteName('#__menu'))
				->where($db->quoteName('published') . '=1')
				->where($db->quoteName('id') . '=' . $db->quote($itemid));

			$db->setQuery($query);

			if ($link = $db->loadResult()) {
				if ($router->getMode() == JROUTER_MODE_SEF) {
					$url = 'index.php?Itemid=' . $itemid;
				} else {
					$url = $link . '&Itemid=' . $itemid;
				}
			}
		}

		if (!$url) {
			$url = self::getCurrentPageUrl();
		}

		return $url;
	}

	/**
	 * Get list of available two factor methods
	 *
	 * @return array
	 */
	public static function getTwoFactorMethods() {
		require_once JPATH_ADMINISTRATOR . '/components/com_users/helpers/users.php';

		return UsersHelper::getTwoFactorMethods();
	}
}