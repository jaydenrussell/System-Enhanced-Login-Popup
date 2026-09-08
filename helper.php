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

		// Fallback: if getActive() is null, try Itemid from the current request
		if (!$currentId) {
			$currentId = $app->input->getInt('Itemid');
		}

		// 1. Check redirect_map for matching source_itemid
		$targetId = self::getDynamicTargetId($params);

		if ($targetId > 0) {
			return self::buildItemidUrl($targetId);
		}

		// 2. Fallback: return to current page
		return self::getCurrentPageUrl();
	}

	/**
	 * Find the matching redirect_map target Itemid for the current page.
	 *
	 * @param   JRegistry  $params  plugin parameters
	 *
	 * @return int  matched target Itemid, 0 when no rule matches
	 */
	private static function getDynamicTargetId($params) {
		$app        = JFactory::getApplication();
		$menu       = $app->getMenu();
		$active     = $menu->getActive();
		$currentId  = $active ? (int) $active->id : 0;

		// Fallback: if getActive() is null, try Itemid from the current request
		if (!$currentId) {
			$currentId = $app->input->getInt('Itemid');
		}

		$redirectMap = $params->get('redirect_map', array());

		// Joomla can persist an extension subform in several shapes depending
		// on CMS version and how the params were saved: a list of objects, a
		// list of associative arrays, a JSON string of either, or an object
		// with numeric keys holding the rows. Normalize everything to a plain
		// list of associative arrays before matching.
		if (is_string($redirectMap)) {
			$redirectMap = json_decode($redirectMap, true);
		}

		if (is_object($redirectMap)) {
			$redirectMap = get_object_vars($redirectMap);
		}

		if (!is_array($redirectMap)) {
			$redirectMap = array();
		}

		foreach ($redirectMap as $rule) {
			if (is_object($rule)) {
				$rule = get_object_vars($rule);
			}

			if (!is_array($rule)) {
				continue;
			}

			$sourceId = isset($rule['source_itemid']) ? (int) $rule['source_itemid'] : 0;
			$targetId = isset($rule['target_itemid']) ? (int) $rule['target_itemid'] : 0;

			if ($sourceId > 0 && $targetId > 0 && $sourceId === $currentId) {
				return $targetId;
			}
		}

		return 0;
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
		return base64_encode(self::getCurrentPageInternalUrl());
	}

	/**
	 * Get the current page as a raw internal URL (fallback).
	 *
	 * @return string  internal URL (e.g. index.php?Itemid=101)
	 */
	private static function getCurrentPageInternalUrl() {
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

		return $url;
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
		$url    = null;

		if ($itemid = (int) $params->get($type)) {
			$db    = JFactory::getDbo();
			$query = $db->getQuery(true)
				->select($db->quoteName('link'))
				->from($db->quoteName('#__menu'))
				->where($db->quoteName('published') . '=1')
				->where($db->quoteName('id') . '=' . $db->quote($itemid));

			$db->setQuery($query);

			if ($db->loadResult()) {
				$url = base64_encode('index.php?Itemid=' . $itemid);
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

	/**
	 * Build the JSON-safe client configuration consumed by init.js.
	 *
	 * All values are normalized (int casts, defaults). The returned array is
	 * emitted as a base64 data attribute on the popup markup and decoded by the
	 * browser at DOM ready, which removes any dependency on script/config load
	 * ordering in the document <head>.
	 *
	 * @param   JRegistry  $params  plugin parameters
	 *
	 * @return  array
	 */
	public static function getClientConfig($params) {
		return array(
			'selector'         => (string) $params->get('selector', 'a[href="#login"], a[href="#logout"]'),
			'offset_top'       => (int) $params->get('offset_top', 50),
			'modal_position'   => (string) $params->get('modal_position', 'center'),
			'modal_top_offset' => (int) $params->get('modal_top_offset', 50),
			'unblur_header'    => (int) $params->get('unblur_header', 1),
			'unblur_selector'  => (string) $params->get('unblur_selector', ''),
			'unblur_zindex'    => (int) $params->get('unblur_zindex', 2002),
		);
	}


	/**
	 * Encode an array as a safe base64 JSON payload for a data attribute.
	 *
	 * base64 output contains only [A-Za-z0-9+/=], so it cannot break out of an
	 * HTML attribute; JSON_HEX_* flags additionally neutralize any </script>
	 * sequence should the payload ever be inlined directly into a script block.
	 *
	 * @param   array  $data  data to encode
	 *
	 * @return  string
	 */
	public static function encodeClientConfig($data) {
		$json = json_encode(
			(array) $data,
			JSON_HEX_TAG | JSON_HEX_AMP | JSON_HEX_APOS | JSON_HEX_QUOT
		);

		return base64_encode($json);
	}

	/**
	 * Return a validated logo path, or an empty string if the configured value
	 * is not a safe http(s) or root-relative path.
	 *
	 * Prevents javascript:/data: URI injection through the (admin-set) logo
	 * parameter being rendered into an <img src>.
	 *
	 * @param   string  $logo  configured logo value
	 *
	 * @return  string
	 */
	public static function getSafeLogo($logo) {
		$logo = trim((string) $logo);

		if ($logo === '') {
			return '';
		}

		// Root-relative paths (/, images/, etc.) or absolute http(s) URLs only.
		if (preg_match('~^https?://~i', $logo)) {
			return $logo;
		}

		// Reject protocol-relative URLs (e.g. //evil.com/x).
		if (strpos($logo, '//') === 0) {
			return '';
		}

		if (!preg_match('~^(/|\./|\.\./|[a-z0-9_./-]+$)~i', $logo)) {
			return '';
		}

		// Reject any scheme-looking prefix (e.g. javascript:, data:, vbscript:).
		if (preg_match('~^[a-z][a-z0-9+.-]*:~i', $logo)) {
			return '';
		}

		return $logo;
	}
}
