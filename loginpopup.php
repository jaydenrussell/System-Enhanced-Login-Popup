<?php
/**
 * @copyright	Copyright (c) 2014 Skyline Technology Ltd (http://extstore.com). All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

// no direct access
defined('_JEXEC') or die;

jimport('joomla.plugin.plugin');

/**
 * System - Login Popup Plugin
 *
 * @package		Joomla.Plugin
 * @subpakage	ExtStore.LoginPopup
 */
class plgSystemLoginPopup extends JPlugin {

	/**
	 * Constructor.
	 *
	 * @param 	$subject
	 * @param	array $config
	 */
	function __construct(&$subject, $config = array()) {
		// call parent constructor
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
			JHtml::_('stylesheet', 'plg_system_loginpopup/style.css', false, true);

			$layout = $this->params->get('layout', 'default');

			if ($layout === 'modern') {
				JHtml::_('stylesheet', 'plg_system_loginpopup/modern.css', false, true);
				JHtml::_('script', 'plg_system_loginpopup/modern.js', false, true);
			}

			$selector	= str_replace('\'', '"', $this->params->get('selector', 'a[href="#login"], a[href="#logout"]'));
			$offsetTop	= (int) $this->params->get('offset_top', 50);
			$modalPosition = $this->params->get('modal_position', 'center');
			$modalTopOffset = (int) $this->params->get('modal_top_offset', 50);
			$unblurHeader = (int) $this->params->get('unblur_header', 1);
			$unblurSelector = $this->params->get('unblur_selector', '#astroid-header, #astroid-sticky-header, .astroid-header, .astroid-topbar, header, .navbar, .astroid-module-position.top-header-navbar');

			$script	= <<<SCRIPT
jQuery(document).ready(function() {
	ExtStore.LoginPopup.offset_top	= $offsetTop;
	ExtStore.LoginPopup.modal_position = '$modalPosition';
	ExtStore.LoginPopup.modal_top_offset = $modalTopOffset;
	ExtStore.LoginPopup.unblur_header = $unblurHeader;
	ExtStore.LoginPopup.unblur_selector = '$unblurSelector';

	jQuery('$selector').click(function(event) {
		ExtStore.LoginPopup.open();

		event.stopPropagation();
		event.preventDefault();
	});

	jQuery('#lp-overlay, .lp-close').click(function() {
		ExtStore.LoginPopup.close();
	});
});
SCRIPT;
			JFactory::getDocument()->addScriptDeclaration($script);
		}
	}

	/**
	 * onAfterRender hook.
	 */
	function onAfterRender() {
		$app	= JFactory::getApplication();

		if ($app->isSite()) {
			$this->loadLanguage();
			$user	= JFactory::getUser();

			$theme = $this->params->get('layout', 'default');

			if ($user->id) {
				$layout		= 'logout';
			} else {
				$layout		= 'login';
			}

			if ($theme === 'modern') {
				$layout .= '_modern';
			}

			$html	= JLayoutHelper::render($layout, $this->params, dirname(__FILE__) . '/layouts');
			$body	= $app->getBody();
			$body	= preg_replace('~</body[^>]*>~', $html . '$0', $body);

			$app->setBody($body);
		}
	}
}