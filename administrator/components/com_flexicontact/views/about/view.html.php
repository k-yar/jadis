<?php
/********************************************************************
Product    : Flexicontact
Date       : 29 November 2016
Copyright  : Les Arbres Design 2009-2016
Contact	   : http://www.lesarbresdesign.info
Licence    : GNU General Public License
*********************************************************************/
defined('_JEXEC') or die('Restricted access');

class FlexicontactViewAbout extends JViewLegacy
{
function display($tpl = null)
{
	Flexicontact_Utility::viewStart();
	JToolBarHelper::title(LAFC_COMPONENT_NAME.': '.JText::_('COM_FLEXICONTACT_ABOUT'), 'flexicontact.png');
    	
// get the installed version

	$xml_array = JInstaller::parseXMLInstallFile(JPATH_COMPONENT_ADMINISTRATOR.'/flexicontact.xml');
	$component_version = $xml_array['version'];

// build the help screen

	$about['name'] = LAFC_COMPONENT_NAME;
	$about['prefix'] = 'COM_FLEXICONTACT';
	$about['current_version'] = $component_version;
    $about['latest_version'] = $this->get_version('flexicontact');      // get the latest version
	$about['reference'] = 'flexicontact';
	$about['link_version'] = "http://www.lesarbresdesign.info/version-history/flexicontact";
	$about['link_doc'] = "http://www.lesarbresdesign.info/extensions/flexicontact";
	$about['link_rating'] = "http://extensions.joomla.org/extensions/contacts-and-feedback/contact-forms/9743";
	
	$this->draw_about($about);

	Flexicontact_Utility::viewEnd();
}

//------------------------------------------------------------------------------
// draw the about screen - this is the same in all our components
//
function draw_about($about)
{
	echo '<h3>'.$about['name'].': '.JText::_($about['prefix'].'_HELP_TITLE').'</h3>';

	if ($about['link_rating'] != '')
		{
		echo '<p><span style="font-size:120%;font-weight:bold;">'.JText::_($about['prefix'].'_HELP_RATING').' ';
		echo JHTML::link($about['link_rating'], 'Joomla Extensions Directory', 'target="_blank"').'</span></p>';
		}

	echo '<table class="table table-striped table-bordered width-auto">';
	
	echo '<tr><td>'.JText::_($about['prefix'].'_VERSION').'</td>';
	echo '<td>'.$about['current_version'].'</td></tr>';
	
	if ($about['latest_version'] != '')
		echo '<tr><td>'.JText::_($about['prefix'].'_LATEST_VERSION').'</td><td>'.$about['latest_version'].'</td></tr>';

	echo '<tr><td>'.JText::_($about['prefix'].'_HELP_CHECK').'</td>';
	echo '<td>'.JHTML::link($about['link_version'], 'Les Arbres Design - '.$about['name'], 'target="_blank"').'</td></tr>';

	$pdf_icon = JHTML::_('image', JURI::root().'administrator/components/com_'.$about['reference'].'/assets/pdf_16.gif','');
	echo '<tr><td>'.$pdf_icon.' '.JText::_($about['prefix'].'_HELP_DOC').'</td>';
	echo '<td>'.JHTML::link($about['link_doc'], "www.lesarbresdesign.info", 'target="_blank"').'</td></tr>';

	$link_jed = "http://extensions.joomla.org/extensions/owner/chrisguk";
	$link_ext = "http://www.lesarbresdesign.info/";

	echo '<tr><td>'.JText::_($about['prefix'].'_HELP_LES_ARBRES').'</td>';
	echo '<td>'.JHTML::link("http://www.lesarbresdesign.info/", 'Les Arbres Design', 'target="_blank"').'</td></tr>';
		
	if (!empty($about['extra']))
		foreach($about['extra'] as $row)
			echo '<tr><td>'.$row['left'].'</td><td>'.$row['right'].'</td></tr>';

	echo '</table>';
}
	
//------------------------------------------------------------------------------
// get the latest version info
//
function get_version($product)
{
    $url = 'http://www.lesarbresdesign.info/jupdate?product='.$product.'&src=about';
    try
        {
        $http = JHttpFactory::getHttp();
        $response = $http->get($url, array(), 20);
        }
    catch (RuntimeException $e)
        {
        return '';
        }
    $version = self::str_between($response->body,'<version>','</version>');
	return $version;
}
				
function str_between($string, $start, $end)
{
    $string = ' '.$string;
    $pos = strpos($string, $start);
    if ($pos == 0)
        return '';
    $pos += strlen($start);
    $len = strpos($string, $end, $pos) - $pos;
    return substr($string, $pos, $len);
}
				
			
}