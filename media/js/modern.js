/**
 * @copyright	Copyright (c) 2014 Skyline Technology Ltd (http://extstore.com). All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 *
 * Modern Layout JS - Simcoe Curling Club
 * Password toggle, autocomplete, modal positioning, unblur selector
 */

(function() {
	'use strict';

	function lpModernInit() {
		// Password show/hide toggle
		var passToggle = document.getElementById('lp-pass-toggle');
		var passInput = document.getElementById('lp-password');

		if (passToggle && passInput) {
			passToggle.addEventListener('click', function() {
				var isPassword = passInput.type === 'password';
				passInput.type = isPassword ? 'text' : 'password';

				var eyeOpen = passToggle.querySelector('.lp-eye-open');
				var eyeClosed = passToggle.querySelector('.lp-eye-closed');

				if (eyeOpen && eyeClosed) {
					eyeOpen.style.display = isPassword ? 'none' : 'block';
					eyeClosed.style.display = isPassword ? 'block' : 'none';
				}

				passToggle.setAttribute('aria-label',
					isPassword ? 'Hide password' : 'Show password'
				);
				passToggle.setAttribute('title',
					isPassword ? 'Hide password' : 'Show password'
				);
			});
		}

		// Ensure autocomplete attributes are set (backup for password managers)
		var usernameInput = document.getElementById('lp-username');
		if (usernameInput) {
			usernameInput.setAttribute('autocomplete', 'username');
		}
		if (passInput) {
			passInput.setAttribute('autocomplete', 'current-password');
		}

		// Keyboard: Escape closes the modal
		document.addEventListener('keydown', function(e) {
			if (e.key === 'Escape') {
				var popup = document.getElementById('lp-popup');
				if (popup && popup.classList.contains('lp-open')) {
					if (typeof ExtStore !== 'undefined' && ExtStore.LoginPopup) {
						ExtStore.LoginPopup.close();
					}
				}
			}
		});

		// Extend ExtStore.LoginPopup with modern positioning and unblur
		if (typeof ExtStore !== 'undefined' && ExtStore.LoginPopup) {
			var originalOpen = ExtStore.LoginPopup.open;

			ExtStore.LoginPopup.open = function() {
				originalOpen.call(this);

				// Apply modal positioning
				this.applyModalPosition();

				// Apply unblur (boost selected elements above overlay)
				this.applyUnblur();

				// Focus first input
				setTimeout(function() {
					var userField = document.getElementById('lp-username');
					if (userField) {
						userField.focus();
					}
				}, 100);
			};

			ExtStore.LoginPopup.applyModalPosition = function() {
				var popup = jQuery('#lp-popup');
				if (!popup.length) return;

				var windowHeight = jQuery(window).height();
				var popupHeight = popup.outerHeight();
				var position = this.modal_position || 'center';
				var topOffset = parseInt(this.modal_top_offset) || 50;

				var top;
				switch (position) {
					case 'top':
						top = topOffset;
						break;
					case 'center':
						top = (windowHeight - popupHeight) / 2 - topOffset;
						break;
					case 'bottom':
						top = windowHeight - popupHeight - topOffset;
						break;
					case 'custom':
					default:
						top = topOffset;
						break;
				}

				// Ensure modal doesn't go off-screen
				if (top < 0) top = 0;
				if (top + popupHeight > windowHeight) top = Math.max(0, windowHeight - popupHeight);

				popup.css('top', top + 'px');
			};

			ExtStore.LoginPopup.applyUnblur = function() {
				var selector = this.unblur_selector;
				var doUnblur = this.unblur_header === 1;
				if (!selector) return;

				var elements;
				try {
					elements = document.querySelectorAll(selector);
				} catch (e) {
					return;
				}
				elements.forEach(function(el) {
					// Store original z-index
					var originalZ = window.getComputedStyle(el).zIndex;
					el.dataset.originalZIndex = originalZ === 'auto' ? '' : originalZ;
					
					if (doUnblur) {
						// Yes = boost selected elements ABOVE the overlay (2000)
						// and modal (2001). The target z-index is configurable
						// via the unblur_zindex plugin parameter.
						el.style.zIndex = this.unblur_zindex || 2002;
					} else {
						// No = leave as-is (overlay at 2000 will cover it naturally)
					}
					el.style.transition = 'z-index 0.3s ease';
				});
			};

			// Override close to restore unblurred elements
			var originalClose = ExtStore.LoginPopup.close;
			ExtStore.LoginPopup.close = function() {
				originalClose.call(this);

				if (this.unblur_selector) {
					var selector = this.unblur_selector;
				var elements;
				try {
					elements = document.querySelectorAll(selector);
				} catch (e) {
					return;
				}
				elements.forEach(function(el) {
					var originalZ = el.dataset.originalZIndex;
						if (originalZ !== undefined) {
							el.style.zIndex = originalZ === '' ? '' : originalZ;
						}
						el.style.transition = '';
						delete el.dataset.originalZIndex;
					});
				}
			};
		}
	}

	// Run when DOM is ready
	if (document.readyState === 'loading') {
		document.addEventListener('DOMContentLoaded', lpModernInit);
	} else {
		lpModernInit();
	}
})();