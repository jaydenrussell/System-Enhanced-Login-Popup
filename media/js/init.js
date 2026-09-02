/**
 * @copyright	Copyright (c) 2014 Skyline Technology Ltd (http://extstore.com). All rights reserved.
 * @copyright	Copyright (c) 2026 Jayden Russell. All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 *
 * Login Popup initialization.
 *
 * Config is read from the popup markup's data-lp-config attribute (base64 JSON)
 * at DOM ready. Because the config travels with the markup (which is injected
 * into the body by the onAfterRender plugin event), init does NOT depend on the
 * ordering of <script> tags / inline declarations in the document <head>.
 */

(function() {
	'use strict';

	function readConfig() {
		var popup = document.getElementById('lp-popup');

		if (!popup) {
			return null;
		}

		var encoded = popup.getAttribute('data-lp-config');

		if (!encoded) {
			return null;
		}

		try {
			var json = atob(encoded);

			return JSON.parse(json);
		} catch (e) {
			return null;
		}
	}

	function init() {
		if (typeof ExtStore === 'undefined' || typeof window.jQuery === 'undefined') {
			return;
		}

		var config = readConfig();

		if (!config) {
			return;
		}

		ExtStore.LoginPopup = ExtStore.LoginPopup || {};

		ExtStore.LoginPopup.offset_top = config.offset_top || 0;
		ExtStore.LoginPopup.modal_position = config.modal_position || 'center';
		ExtStore.LoginPopup.modal_top_offset = config.modal_top_offset || 50;
		ExtStore.LoginPopup.unblur_header = config.unblur_header || 0;
		ExtStore.LoginPopup.unblur_selector = config.unblur_selector || '';
		ExtStore.LoginPopup.unblur_zindex = config.unblur_zindex || 2002;

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
	}

	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', init);
	} else {
		init();
	}
})();
