<?php
/********************************************************************
Product		: Flexicontact
Date		: 31 March 2016
Copyright	: Les Arbres Design 2010-2016
Contact		: http://www.lesarbresdesign.info
Licence		: GNU General Public License
*********************************************************************/
defined('_JEXEC') or die('Restricted Access');

class FlexicontactViewConfig_Confirm extends JViewLegacy
{
function display($tpl = null)
{
	Flexicontact_Utility::viewStart();
	JToolBarHelper::title(LAFC_COMPONENT_NAME.': '.JText::_('COM_FLEXICONTACT_CONFIG_CONFIRM_NAME'), 'flexicontact.png');
	JToolBarHelper::apply();
	JToolBarHelper::save();
	JToolBarHelper::cancel();
	
	$editor = JFactory::getEditor();
?>
	<form action="index.php" method="post" name="adminForm" id="adminForm" >
	<input type="hidden" name="option" value="<?php echo LAFC_COMPONENT; ?>" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="view" value="config_confirm" />
<?php

	echo "\n<div><strong>".JText::_('COM_FLEXICONTACT_LINK').'</strong>';
	echo ' <input type="text" size="60" name="confirm_link" value="'.$this->config_data->confirm_link.'" /> ';
	echo Flexicontact_Utility::make_info(JText::_('COM_FLEXICONTACT_CONFIRM_LINK_DESC'));
	echo '</div>';

	echo "\n<div><strong>".JText::_('COM_FLEXICONTACT_TEXT').'</strong></div>';
	echo "\n".'<div style="padding:10px 15px 0 0;">';
	echo "\n".$editor->display('confirm_text', htmlspecialchars($this->config_data->confirm_text, ENT_QUOTES),'98%','300px','70','20',array('pagebreak', 'readmore', 'article', 'module'));
	echo "\n".'</div></form>';

	Flexicontact_Utility::viewEnd();
}

}