<?php
/********************************************************************
Product    : FlexicontactPlus
Date       : 11 October 2016
Copyright  : Les Arbres Design 2010-2016
Contact    : http://www.lesarbresdesign.info
Licence    : GNU General Public License
*********************************************************************/
defined('_JEXEC') or die('Restricted Access');

class FlexicontactViewConfig_Captcha extends JViewLegacy
{
function display($tpl = null)
{
	Flexicontact_Utility::viewStart();
	JToolBarHelper::title(LAFC_COMPONENT_NAME.': '.JText::_('COM_FLEXICONTACT_CAPTCHA_CONFIG'), 'flexicontact.png');
	JToolBarHelper::apply();
	JToolBarHelper::save();
	JToolBarHelper::cancel();
?>
	<form action="index.php" method="post" name="adminForm" id="adminForm" class="form-horizontal form-inline">
	<input type="hidden" name="option" value="<?php echo LAFC_COMPONENT; ?>" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="view" value="config_captcha" />
<?php

// populate data and render the config_general.xml form

	JForm::addFieldPath(LAFC_FORMS_PATH);
	$form = JForm::getInstance('config_general', LAFC_FORMS_PATH.'/config_captcha.xml');
	$field_sets = $form->getFieldsets();
	foreach ($field_sets as $fieldset_name => $fieldset)	
        echo Flexicontact_Utility::renderFieldSet($form, $fieldset, $this->config_data);

	echo '</form>';
	Flexicontact_Utility::viewEnd();
}

}