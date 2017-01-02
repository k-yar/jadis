<?php
/********************************************************************
Product	   : Flexicontact
Date       : 11 October 2016
Copyright  : Les Arbres Design 2009-2016
Contact	   : http://www.lesarbresdesign.info
Licence	   : GNU General Public License
*********************************************************************/
defined('_JEXEC') or die('Restricted access');

class FlexicontactViewConfig_List extends JViewLegacy
{
function display($tpl = null)
{
	Flexicontact_Utility::viewStart();
	JToolBarHelper::title(LAFC_COMPONENT_NAME.': '.JText::_('COM_FLEXICONTACT_CONFIGURATION'), 'flexicontact.png');
	if (JFactory::getUser()->authorise('core.admin', 'com_flexicontact'))
		JToolBarHelper::preferences('com_flexicontact');

// Set up the configuration links

	$config_table = array(
		array(
			'link' => LAFC_COMPONENT_LINK.'&task=config&view=config_general',
			'icon' => 'config_general.gif',
			'name' => 'COM_FLEXICONTACT_CONFIG_GENERAL_NAME',
			'desc' => 'COM_FLEXICONTACT_CONFIG_GENERAL_DESC'),
		array(
			'link' => LAFC_COMPONENT_LINK.'&task=config&view=config_template&param1=admin_template',
			'icon' => 'config_email_a.gif',
			'name' => 'COM_FLEXICONTACT_CONFIG_ADMIN_EMAIL_NAME',
			'desc' => 'COM_FLEXICONTACT_CONFIG_ADMIN_EMAIL_DESC'),
		array(
			'link' => LAFC_COMPONENT_LINK.'&task=config&view=config_template&param1=user_template',
			'icon' => 'config_email_u.gif',
			'name' => 'COM_FLEXICONTACT_CONFIG_USER_EMAIL_NAME',
			'desc' => 'COM_FLEXICONTACT_CONFIG_USER_EMAIL_DESC'),
		array(
			'link' => LAFC_COMPONENT_LINK.'&task=config&view=config_confirm',
			'icon' => 'config_text.gif',
			'name' => 'COM_FLEXICONTACT_CONFIG_CONFIRM_NAME',
			'desc' => 'COM_FLEXICONTACT_CONFIG_CONFIRM_DESC'),
		array(
			'link' => LAFC_COMPONENT_LINK.'&task=config&view=config_fields',
			'icon' => 'config_fields.gif',
			'name' => 'COM_FLEXICONTACT_CONFIG_FIELDS_NAME',
			'desc' => 'COM_FLEXICONTACT_CONFIG_FIELDS_DESC'),
		array(
			'link' => LAFC_COMPONENT_LINK.'&task=config&view=config_text&param1=page_text',
			'icon' => 'config_text_top.gif',
			'name' => 'COM_FLEXICONTACT_V_TOP_TEXT',
			'desc' => 'COM_FLEXICONTACT_CONFIG_TOP_BOTTOM_TEXT_DESC'),
		array(
			'link' => LAFC_COMPONENT_LINK.'&task=config&view=config_text&param1=bottom_text',
			'icon' => 'config_text_bottom.gif',
			'name' => 'COM_FLEXICONTACT_V_BOTTOM_TEXT',
			'desc' => 'COM_FLEXICONTACT_CONFIG_TOP_BOTTOM_TEXT_DESC'),
		array(
			'link' => LAFC_COMPONENT_LINK.'&task=config&view=config_captcha',
			'icon' => 'config_captcha.gif',
			'name' => 'COM_FLEXICONTACT_CAPTCHA_CONFIG',
			'desc' => 'COM_FLEXICONTACT_CAPTCHA_CONFIG_DESC'),
		array(
			'link' => LAFC_COMPONENT_LINK.'&task=config&view=config_css',
			'icon' => 'config_css.gif',
			'name' => 'COM_FLEXICONTACT_CONFIG_CSS_NAME',
			'desc' => 'COM_FLEXICONTACT_CONFIG_CSS_DESC')
		);

	// show the list
	?>
	<form action="index.php" method="post" name="adminForm" id="adminForm">
	<input type="hidden" name="option" value="<?php echo LAFC_COMPONENT ?>" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="view" value="config_list" />
	<table class="table table-striped">
	<thead>
		<tr>
			<th style="width:5%;"></th>
			<th style="width:20%; white-space:nowrap;"><?php echo  JText::_('COM_FLEXICONTACT_CONFIG_NAME'); ?></th>
			<th style="width:75%; white-space:nowrap;"><?php echo  JText::_('COM_FLEXICONTACT_CONFIG_DESC'); ?></th>
		</tr>
	</thead>

	<?php
	foreach ($config_table as $config)
		{
		$icon = '<img src="'.LAFC_ADMIN_ASSETS_URL.$config['icon'].'" alt="" />';
		echo "<tr>
				<td>$icon</td>
				<td>".JHTML::link($config['link'], JText::_($config['name']))."</td>
				<td>".JText::_($config['desc'])."</td>
			</tr>";
		}
	echo '</table></form>';
	Flexicontact_Utility::viewEnd();
	}
}