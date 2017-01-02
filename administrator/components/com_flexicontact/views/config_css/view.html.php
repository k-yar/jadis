<?php
/********************************************************************
Product		: Flexicontact
Date		: 29 November 2016
Copyright	: Les Arbres Design 2010-2016
Contact		: http://www.lesarbresdesign.info
Licence		: GNU General Public License
*********************************************************************/
defined('_JEXEC') or die('Restricted Access');

class FlexicontactViewConfig_Css extends JViewLegacy
{
function display($tpl = null)
{
	JToolBarHelper::title(LAFC_COMPONENT_NAME.': '.JText::_('COM_FLEXICONTACT_CONFIG_CSS_NAME'), 'flexicontact.png');
	JToolBarHelper::apply('apply_css');
	JToolBarHelper::save('save_css');
	JToolBarHelper::cancel();
	
	if (!file_exists(LAFC_SITE_CSS_PATH)) 
		{ 
		$app = JFactory::getApplication();
		$app->redirect(LAFC_COMPONENT_LINK.'&task=config',
			JText::_('COM_FLEXICONTACT_CSS_MISSING').' ('.LAFC_SITE_CSS_PATH.')', 'error');
		return;
		}
		
	if (!is_readable(LAFC_SITE_CSS_PATH)) 
		{ 
		$app = JFactory::getApplication();
		$app->redirect(LAFC_COMPONENT_LINK.'&task=config',
			JText::_('COM_FLEXICONTACT_CSS_NOT_READABLE').' ('.LAFC_SITE_CSS_PATH.')', 'error'); 
		return;
		}

	if (!is_writable(LAFC_SITE_CSS_PATH)) 
		{ 
		$app = JFactory::getApplication();
		$app->redirect(LAFC_COMPONENT_LINK.'&task=config',
			JText::_('COM_FLEXICONTACT_CSS_NOT_WRITEABLE').' ('.LAFC_SITE_CSS_PATH.')', 'error'); 
		return;
		}
		
	$css_contents = @file_get_contents(LAFC_SITE_CSS_PATH);

	Flexicontact_Utility::viewStart();
	
?>
	<form action="index.php" method="post" name="adminForm" id="adminForm" >
	<input type="hidden" name="option" value="<?php echo LAFC_COMPONENT; ?>" />
	<input type="hidden" name="task" value="" />
	<input type="hidden" name="view" value="config_css" />
<?php 
	
	echo '<table><tr><td>';
	echo '<textarea name="css_contents" rows="25" cols="125">'.$css_contents.'</textarea>';
	echo '</td><td style="vertical-align:top;">';
	echo Flexicontact_Utility::make_info('www.w3schools.com/css','http://www.w3schools.com/css/default.asp');
	echo '</td></tr></table></form>';

	Flexicontact_Utility::viewEnd();
}


}