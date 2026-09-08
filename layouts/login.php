<?php
/**
 * @copyright	Copyright (c) 2014 Skyline Technology Ltd (http://extstore.com). All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

require_once JPATH_SITE . '/components/com_users/helpers/route.php';
require_once dirname(dirname(__FILE__)) . '/helper.php';

$return				= PlgSystemLoginPopupHelper::getReturnURL($displayData, 'login');
$twofactormethods	= PlgSystemLoginPopupHelper::getTwoFactorMethods();
$clientConfig		= PlgSystemLoginPopupHelper::encodeClientConfig(PlgSystemLoginPopupHelper::getClientConfig($displayData));
$isCb				= PlgSystemLoginPopupHelper::isComprofiler($displayData);
$loginAction		= $isCb
	? JRoute::_('index.php?option=com_comprofiler&view=login', true)
	: JRoute::_('index.php?option=com_users&task=user.login', true, $displayData->get('usesecure'));
?>

<div id="lp-overlay"></div>
<div id="lp-popup" class="lp-wrapper" data-lp-config="<?php echo htmlspecialchars($clientConfig, ENT_QUOTES, 'UTF-8'); ?>">
	<div class="lp-register-intro">
		<?php echo JText::_('PLG_SYSTEM_LOGINPOPUP_REGISTER_INTRO'); ?>
		<a href="<?php echo JRoute::_('index.php?option=com_users&view=registration&Itemid=' . UsersHelperRoute::getRegistrationRoute()); ?>"><?php echo JText::_('PLG_SYSTEM_LOGINPOPUP_REGISTER_NOW'); ?></a>
	</div>
	<button class="lp-close" type="button" title="Close (Esc)">×</button>

	<form action="<?php echo $loginAction; ?>" method="post" class="lp-form">
		<h3><?php echo JText::_('PLG_SYSTEM_LOGINPOPUP_FORM_TITLE'); ?></h3>
		<div class="lp-field-wrapper">
			<label for="lp-username"><?php echo JText::_('PLG_SYSTEM_LOGINPOPUP_USERNAME'); ?> *</label>
			<input type="text" id="lp-username" class="lp-input-text lp-input-username" name="username" placeholder="<?php echo JText::_('PLG_SYSTEM_LOGINPOPUP_USERNAME'); ?>" required="true" />
		</div>
		<div class="lp-field-wrapper">
			<label for="lp-password"><?php echo JText::_('PLG_SYSTEM_LOGINPOPUP_PASSWORD'); ?> *</label>
			<input type="password" id="lp-password" class="lp-input-text lp-input-password" name="<?php echo $isCb ? 'passwd' : 'password'; ?>" placeholder="<?php echo JText::_('PLG_SYSTEM_LOGINPOPUP_PASSWORD'); ?>" required="true" />
		</div>

		<?php if (count($twofactormethods) > 1) : ?>
			<div class="lp-field-wrapper">
				<label for="lp-secretkey"><?php echo JText::_('JGLOBAL_SECRETKEY'); ?></label>
				<input type="text" id="lp-secretkey" autocomplete="off" class="lp-input-text" name="secretkey" placeholder="<?php echo JText::_('JGLOBAL_SECRETKEY'); ?>" />
			</div>
		<?php endif; ?>

		<?php if (JPluginHelper::isEnabled('system', 'remember')) : ?>
			<div class="lp-field-wrapper">
				<input type="checkbox" id="lp-remember" class="lp-input-checkbox" name="remember" />
				<label for="lp-remember"><?php echo JText::_('PLG_SYSTEM_LOGINPOPUP_REMEMBER_ME'); ?></label>
			</div>
		<?php endif; ?>


		<div class="lp-button-wrapper clearfix">
			<div class="lp-left">
				<button type="submit" class="lp-button"><?php echo JText::_('JLOGIN'); ?></button>
			</div>

			<ul class="lp-right lp-link-wrapper">
				<li>
					<a href="<?php echo JRoute::_('index.php?option=com_users&view=remind&Itemid=' . UsersHelperRoute::getRemindRoute()); ?>"><?php echo JText::_('PLG_SYSTEM_LOGINPOPUP_FORGOT_USERNAME'); ?></a>
				</li>
				<li><a href="<?php echo JRoute::_('index.php?option=com_users&view=reset&Itemid=' . UsersHelperRoute::getResetRoute()); ?>"><?php echo JText::_('PLG_SYSTEM_LOGINPOPUP_FORGOT_PASSWORD'); ?></a></li>
			</ul>
		</div>

		<?php if ($isCb) : ?>
			<input type="hidden" name="option" value="com_comprofiler" />
			<input type="hidden" name="view" value="login" />
			<input type="hidden" name="op2" value="login" />
			<input type="hidden" name="message" value="0" />
			<input type="hidden" name="loginfrom" value="loginmodule" />
		<?php else : ?>
			<input type="hidden" name="option" value="com_users" />
			<input type="hidden" name="task" value="user.login" />
		<?php endif; ?>
		<input type="hidden" name="return" value="<?php echo $return; ?>" />
		<?php echo JHtml::_('form.token'); ?>
	</form>
</div>