<?php
/**
 * @copyright	Copyright (c) 2014 Skyline Technology Ltd (http://extstore.com). All rights reserved.
 * @license		GNU General Public License version 2 or later; see LICENSE.txt
 */

require_once JPATH_SITE . '/components/com_users/helpers/route.php';
require_once dirname(dirname(__FILE__)) . '/helper.php';

$return			= PlgSystemLoginPopupHelper::getReturnURL($displayData, 'logout');
$user			= JFactory::getUser();
$clientConfig	= PlgSystemLoginPopupHelper::encodeClientConfig(PlgSystemLoginPopupHelper::getClientConfig($displayData));

$logo = PlgSystemLoginPopupHelper::getSafeLogo($displayData->get('logo', ''));
if ($logo === '') {
	$logo = 'images/Logo/scc_logo.png';
}
?>

<div id="lp-overlay"></div>
<div id="lp-popup" class="lp-wrapper lp-modern" data-lp-config="<?php echo htmlspecialchars($clientConfig, ENT_QUOTES, 'UTF-8'); ?>">
	<button class="lp-close" type="button" title="Close (Esc)">&times;</button>

	<div class="lp-modern-logo" id="lp-modern-logo">
		<img src="<?php echo htmlspecialchars(JRoute::_($logo), ENT_QUOTES, 'UTF-8'); ?>" alt="Club Logo" />
	</div>

	<form action="<?php echo JRoute::_('index.php?option=com_users&task=user.logout', true); ?>" method="post" class="lp-form">
		<?php if ($displayData->get('greeting')) : ?>
			<div class="lp-modern-greeting">
				<?php echo JText::sprintf('PLG_SYSTEM_LOGINPOPUP_HINAME', htmlspecialchars($displayData->get('name') == 0 ? $user->get('name') : $user->get('username'))); ?>
			</div>
		<?php endif; ?>

		<div class="lp-button-wrapper">
			<button type="submit" class="lp-button"><?php echo JText::_('JLOGOUT'); ?></button>
		</div>

		<input type="hidden" name="option" value="com_users" />
		<input type="hidden" name="task" value="user.logout" />
		<input type="hidden" name="return" value="<?php echo $return; ?>" />
		<?php echo JHtml::_('form.token'); ?>
	</form>
</div>
