<?php
/**
 * @copyright	Copyright (c) 2014 Skyline Technology Ltd (http://extstore.com). All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

require_once JPATH_SITE . '/components/com_users/helpers/route.php';
require_once JPATH_PLUGINS . '/system/loginpopup/helper.php';

$return				= PlgSystemLoginPopupHelper::getReturnURL($displayData, 'login');
$twofactormethods	= PlgSystemLoginPopupHelper::getTwoFactorMethods();

$logo = $displayData->get('logo', '');
if (empty($logo)) {
	$logo = 'images/Logo/scc_logo.png';
}

// Custom title
$customTitleEnabled = $displayData->get('custom_title_enabled', 1);
$customTitleText = $displayData->get('custom_title_text', 'Welcome Back');

// Remember me display option
$rememberDisplay = $displayData->get('remember_display', 'show_checked');

// Signup text
$signupText = $displayData->get('signup_text', 'New to SCC?');
$signupLinkText = $displayData->get('signup_link_text', 'Sign up');

// Logo size
$logoSize = $displayData->get('logo_size', 'medium');

// Modal position
$modalPosition = $displayData->get('modal_position', 'center');
?>

<div id="lp-overlay"></div>
<div id="lp-popup" class="lp-wrapper lp-modern" data-position="<?php echo $modalPosition; ?>">
	<button class="lp-close" type="button" title="Close (Esc)">&times;</button>

	<div class="lp-modern-logo" id="lp-modern-logo">
		<img src="<?php echo JRoute::_($logo); ?>" alt="Club Logo" class="lp-logo-size-<?php echo $logoSize; ?>" />
	</div>

	<form action="<?php echo JRoute::_('index.php', true, $displayData->get('usesecure')); ?>" method="post" class="lp-form" autocomplete="on">
		<?php if ($customTitleEnabled) : ?>
			<h3><?php echo $customTitleText; ?></h3>
		<?php endif; ?>

		<div class="lp-field-wrapper">
			<label for="lp-username"><?php echo JText::_('PLG_SYSTEM_LOGINPOPUP_USERNAME'); ?></label>
			<input type="text" id="lp-username" class="lp-input-text lp-input-username" name="username" placeholder="<?php echo JText::_('PLG_SYSTEM_LOGINPOPUP_USERNAME_OR_EMAIL'); ?>" autocomplete="username" required="true" />
		</div>

		<div class="lp-field-wrapper lp-password-wrap">
			<label for="lp-password"><?php echo JText::_('PLG_SYSTEM_LOGINPOPUP_PASSWORD'); ?></label>
			<input type="password" id="lp-password" class="lp-input-text lp-input-password" name="password" placeholder="<?php echo JText::_('PLG_SYSTEM_LOGINPOPUP_PASSWORD'); ?>" autocomplete="current-password" required="true" />
			<button type="button" class="lp-pass-toggle" id="lp-pass-toggle" aria-label="<?php echo JText::_('PLG_SYSTEM_LOGINPOPUP_MODERN_SHOW_PASS'); ?>" title="<?php echo JText::_('PLG_SYSTEM_LOGINPOPUP_MODERN_SHOW_PASS'); ?>">
				<svg class="lp-eye-open" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round"><path d="M1 12s4-8 11-8 11 8 11 8-4 8-11 8-11-8-11-8z"/><circle cx="12" cy="12" r="3"/></svg>
				<svg class="lp-eye-closed" xmlns="http://www.w3.org/2000/svg" width="20" height="20" viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2" stroke-linecap="round" stroke-linejoin="round" style="display:none;"><path d="M17.94 17.94A10.07 10.07 0 0 1 12 20c-7 0-11-8-11-8a18.45 18.45 0 0 1 5.06-5.94M9.9 4.24A9.12 9.12 0 0 1 12 4c7 0 11 8 11 8a18.5 18.5 0 0 1-2.16 3.19m-6.72-1.07a3 3 0 1 1-4.24-4.24"/><line x1="1" y1="1" x2="23" y2="23"/></svg>
			</button>
		</div>

		<?php if (count($twofactormethods) > 1) : ?>
			<div class="lp-field-wrapper">
				<label for="lp-secretkey"><?php echo JText::_('JGLOBAL_SECRETKEY'); ?></label>
				<input type="text" id="lp-secretkey" autocomplete="off" class="lp-input-text" name="secretkey" placeholder="<?php echo JText::_('JGLOBAL_SECRETKEY'); ?>" />
			</div>
		<?php endif; ?>

		<?php if (JPluginHelper::isEnabled('system', 'remember')) : ?>
			<div class="lp-field-wrapper lp-remember-wrap">
				<?php
					$showRemember = true;
					$rememberChecked = false;
					if ($rememberDisplay === 'show_checked') {
						$rememberChecked = true;
					} elseif ($rememberDisplay === 'show_unchecked') {
						$rememberChecked = false;
					} elseif ($rememberDisplay === 'hide_checked') {
						$showRemember = false;
						$rememberChecked = true;
					} elseif ($rememberDisplay === 'hide_unchecked') {
						$showRemember = false;
						$rememberChecked = false;
					}
				?>
				<?php if ($showRemember) : ?>
					<label class="lp-checkbox-label lp-remember-inline">
						<input type="checkbox" id="lp-remember" class="lp-input-checkbox" name="remember" <?php echo $rememberChecked ? 'checked="checked"' : ''; ?> />
						<span class="lp-checkmark"></span>
						<?php echo JText::_('PLG_SYSTEM_LOGINPOPUP_REMEMBER_ME'); ?>
					</label>
				<?php endif; ?>
				<?php if (!$showRemember && $rememberChecked) : ?>
					<input type="hidden" name="remember" value="1" />
				<?php endif; ?>
			</div>
		<?php endif; ?>

		<div class="lp-button-wrapper">
			<div class="lp-left lp-forgot-wrap">
				<a href="<?php echo JRoute::_('index.php?option=com_users&view=remind&Itemid=' . UsersHelperRoute::getRemindRoute()); ?>" class="lp-modern-forgot"><?php echo JText::_('PLG_SYSTEM_LOGINPOPUP_MODERN_FORGOT'); ?></a>
			</div>
			<div class="lp-left">
				<button type="submit" class="lp-button"><?php echo JText::_('JLOGIN'); ?></button>
			</div>
		</div>

		<div class="lp-modern-signup">
			<span><?php echo htmlspecialchars($signupText, ENT_QUOTES, 'UTF-8'); ?></span>
			<a href="<?php echo JRoute::_('index.php?option=com_users&view=registration&Itemid=' . UsersHelperRoute::getRegistrationRoute()); ?>"><?php echo htmlspecialchars($signupLinkText, ENT_QUOTES, 'UTF-8'); ?></a>
		</div>

		<input type="hidden" name="option" value="com_users" />
		<input type="hidden" name="task" value="user.login" />
		<input type="hidden" name="return" value="<?php echo $return; ?>" />
		<?php echo JHtml::_('form.token'); ?>
	</form>
</div>