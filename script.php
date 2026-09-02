<?php
/**
 * @copyright	Copyright (c) 2026 Jayden Russell. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 *
 * System - Login Popup plugin install script.
 *
 * Registers the extension's update site with the Joomla! updater so that
 * future releases are offered by the native "Extension Update" view.
 *
 * Joomla 3.x does not reliably register the <updateserver> manifest tag on
 * install for all configurations, so we register it explicitly here. This is
 * idempotent: if the update site already exists for this location, it is not
 * duplicated (matching JInstaller's own duplicate-check behavior).
 */

defined('_JEXEC') or die;

/**
 * Uses the legacy JFactory API (available across all of Joomla 3.x) for
 * maximum compatibility with the 3.[0-9] platform targeted by updates.xml.
 */

/**
 * Install script class for plg_system_loginpopup.
 */
class plgSystemLoginPopupInstallerScript
{
	/**
	 * Update site location (stable, always points at the newest release feed).
	 *
	 * @var    string
	 */
	private $updateSiteLocation = 'https://github.com/jaydenrussell/System-Enhanced-Login-Popup/releases/latest/download/updates.xml';

	/**
	 * Update site type.
	 *
	 * @var    string
	 */
	private $updateSiteType = 'extension';

	/**
	 * Update site name.
	 *
	 * @var    string
	 */
	private $updateSiteName = 'Enhanced Login Popup Update Site';

	/**
	 * Method to run after an install or update.
	 *
	 * @param   string            $type    Type of change (install/uninstall/update).
	 * @param   \JInstaller|Joomla\CMS\Installer\Installer  $parent  Parent installer object.
	 *
	 * @return  void
	 */
	public function postflight($type, $parent)
	{
		if (in_array($type, array('install', 'update'), true))
		{
			$this->registerUpdateSite();
		}
	}

	/**
	 * Register the update site for this plugin.
	 *
	 * Inserted into #__update_sites and linked to the extension in
	 * #__update_sites_extensions. Skips if the location already exists.
	 *
	 * @return  void
	 */
	private function registerUpdateSite()
	{
		$db    = JFactory::getDbo();
		$query = $db->getQuery(true);

		// 1. Find the extension_id of this plugin.
		$query->select($db->quoteName('extension_id'))
			->from($db->quoteName('#__extensions'))
			->where($db->quoteName('name') . ' = ' . $db->quote('plg_system_loginpopup'));

		$db->setQuery($query);
		$extensionId = (int) $db->loadResult();

		if ($extensionId <= 0)
		{
			return;
		}

		// 2. Check whether the update site location is already registered.
		$query = $db->getQuery(true);
		$query->select($db->quoteName('update_site_id'))
			->from($db->quoteName('#__update_sites'))
			->where($db->quoteName('location') . ' = ' . $db->quote($this->updateSiteLocation));

		$db->setQuery($query);
		$updateSiteId = (int) $db->loadResult();

		if ($updateSiteId <= 0)
		{
			// 3. Insert a new update site row (columns match the Joomla 3.x
			// #__update_sites schema: update_site_id, name, type, location,
			// enabled, last_check_timestamp, extra_query).
			$query = $db->getQuery(true);
			$query->insert($db->quoteName('#__update_sites'))
				->columns(
					array(
						$db->quoteName('name'),
						$db->quoteName('type'),
						$db->quoteName('location'),
						$db->quoteName('enabled'),
						$db->quoteName('last_check_timestamp'),
						$db->quoteName('extra_query'),
					)
				)
				->values(
					implode(
						',',
						array(
							$db->quote($this->updateSiteName),
							$db->quote($this->updateSiteType),
							$db->quote($this->updateSiteLocation),
							1,
							0,
							$db->quote(''),
						)
					)
				);

			$db->setQuery($query);
			$db->execute();

			$updateSiteId = (int) $db->insertid();
		}

		// 4. Link the update site to the extension (idempotent).
		$query = $db->getQuery(true);
		$query->select('COUNT(*)')
			->from($db->quoteName('#__update_sites_extensions'))
			->where($db->quoteName('update_site_id') . ' = ' . (int) $updateSiteId)
			->where($db->quoteName('extension_id') . ' = ' . (int) $extensionId);

		$db->setQuery($query);

		if ((int) $db->loadResult() === 0)
		{
			$query = $db->getQuery(true);
			$query->insert($db->quoteName('#__update_sites_extensions'))
				->columns(
					array(
						$db->quoteName('update_site_id'),
						$db->quoteName('extension_id'),
					)
				)
				->values((int) $updateSiteId . ',' . (int) $extensionId);

			$db->setQuery($query);
			$db->execute();
		}
	}
}
