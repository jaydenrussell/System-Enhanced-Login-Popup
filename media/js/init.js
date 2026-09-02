/**
 * @copyright	Copyright (c) 2014 Skyline Technology Ltd (http://extstore.com). All rights reserved.
 * @copyright	Copyright (c) 2026 Jayden Russell. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 *
 * Login Popup initialization - reads config from window.LoginPopupConfig
 */

(function() {
	'use strict';

	if (typeof window.LoginPopupConfig === 'undefined') {
		return;
	}

	var config = window.LoginPopupConfig;

	ExtStore = window.ExtStore || {};
	ExtStore.LoginPopup = ExtStore.LoginPopup || {};

	ExtStore.LoginPopup.offset_top = config.offset_top || 0;
	ExtStore.LoginPopup.modal_position = config.modal_position || 'center';
	ExtStore.LoginPopup.modal_top_offset = config.modal_top_offset || 50;
	ExtStore.LoginPopup.unblur_header = config.unblur_header || 0;
	ExtStore.LoginPopup.unblur_selector = config.unblur_selector || '';

	jQuery(document).ready(function() {
		if (config.selector) {
			jQuery(config.selector).on('click', function(event) {
				ExtStore.LoginPopup.open();
				event.stopPropagation();
				event.preventDefault();
			});
		}

		jQuery('#lp-overlay, .lp-close').on('click', function() {
			ExtStore.LoginPopup.close();
		});
	});
})();
