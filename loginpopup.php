<?php
/**
 * @copyright	Copyright (c) 2014 Skyline Technology Ltd (http://extstore.com). All rights reserved.
 * @copyright	Copyright (c) 2026 Jayden Russell. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

jimport('joomla.plugin.plugin');

/**
 * System - Login Popup Plugin
 *
 * @package		Joomla.Plugin
 * @subpackage	ExtStore.LoginPopup
 */
class plgSystemLoginPopup extends JPlugin {

	/**
	 * Constructor.
	 *
	 * @param 	$subject
	 * @param	array $config
	 */
	function __construct(&$subject, $config = array()) {
		parent::__construct($subject, $config);
	}

	/**
	 * onAfterRoute hook.
	 */
	function onAfterRoute() {
		if (JFactory::getApplication()->isSite()) {
			JHtml::_('behavior.keepalive');
			JHtml::_('jquery.framework');

			JHtml::_('script', 'plg_system_loginpopup/script.js', false, true);
			JHtml::_('script', 'plg_system_loginpopup/init.js', false, true);
			JHtml::_('stylesheet', 'plg_system_loginpopup/style.css', false, true);

			$layout = $this->params->get('layout', 'default');

			if ($layout === 'modern') {
				JHtml::_('stylesheet', 'plg_system_loginpopup/modern.css', false, true);
				JHtml::_('script', 'plg_system_loginpopup/modern.js', false, true);
			}
		}
	}

	/**
	 * onAfterRender hook.
	 */
	function onAfterRender() {
		$app = JFactory::getApplication();

		if ($app->isSite()) {
			$this->loadLanguage();
			$user = JFactory::getUser();

			$theme = $this->params->get('layout', 'default');

			if ($user->id) {
				$layout = 'logout';
			} else {
				$layout = 'login';
			}

			if ($theme === 'modern') {
				$layout .= '_modern';
			}

			$html  = JLayoutHelper::render($layout, $this->params, dirname(__FILE__) . '/layouts');
			$body  = $app->getBody();

			if (!is_string($body) || $body === '') {
				// Nothing to inject into; do not silently swallow a render error.
				return;
			}

			// Case-insensitive to tolerate </BODY> emitted by some templates /
			// minifiers. A callback is used so any "$" sequences in the injected
			// HTML are never misinterpreted as replacement backreferences. Track
			// whether we actually replaced anything.
			$replaced = 0;

			$body = preg_replace_callback(
				'~</body[^>]*>~i',
				function ($m) use ($html) {
					return $html . $m[0];
				},
				$body,
				-1,
				$replaced
			);

			if ($body === null) {
				// preg failure (e.g. backtrack limit on shared hosting).
				JLog::add('plg_system_loginpopup: body injection regex failed', JLog::WARNING, 'jerror');
				return;
			}

			if ($replaced === 0) {
				JLog::add('plg_system_loginpopup: no </body> found, popup not injected', JLog::WARNING, 'jerror');
			}

			$app->setBody($body);
		}
	}
}
