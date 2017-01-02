<?php
/********************************************************************
Product		: Flexicontact
Date		: 9 December 2016
Copyright	: Les Arbres Design 2010-2016
Contact		: http://www.lesarbresdesign.info
Licence		: GNU General Public License
*********************************************************************/
defined('_JEXEC') or die('Restricted access');

class FlexicontactModelConfig extends JModelLegacy
{
var $_data;
var $_app = null;

function __construct()
{
	parent::__construct();
	$this->_app = JFactory::getApplication();
}

//-------------------------------------------------------------------------------
// Get the component parameters
// Returns a stdClass Object containing all our parameters
// This is called from the front and the back so any language strings used must be in both language files
// Note that old menu items that have not been saved still retain their old menu item parameters
// - these OVERWRITE parameters of the same name stored in the component parameters
//
function &getData()
{
    if ($this->_app->isAdmin())
		$component_params = JComponentHelper::getParams(LAFC_COMPONENT);		// this just gets the component parameters
	else
		$component_params =  $this->_app->getParams();       // this gets the component parameters AND the menu parameters
	$this->data = $component_params->toObject();
    
// set defaults for all our parameters so that we have this all in one place

	if (!isset($this->data->logging))          $this->data->logging          = 1;	// changed from 0 at version 8.08
	if (!isset($this->data->email_html))       $this->data->email_html       = 1;
	if (!isset($this->data->autofill))         $this->data->autofill         = 'off';
	if (!isset($this->data->agreement_prompt)) $this->data->agreement_prompt = '';
	if (!isset($this->data->agreement_name))   $this->data->agreement_name   = '';
	if (!isset($this->data->agreement_link))   $this->data->agreement_link   = '';

	if (!isset($this->data->show_copy))        $this->data->show_copy        = LAFC_COPYME_CHECKBOX;
	if (!isset($this->data->show_subject))     $this->data->show_subject     = 1;
	if (!isset($this->data->default_subject))  $this->data->default_subject  = '';
	if (!isset($this->data->area_prompt))      $this->data->area_prompt      = '';
	if (!isset($this->data->area_opt))         $this->data->area_opt         = 'optional';
	if (!isset($this->data->area_width))       $this->data->area_width       = '';
	if (!isset($this->data->area_height))      $this->data->area_height      = 3;
	if (!isset($this->data->list_opt))         $this->data->list_opt         = 'disabled';
	if (!isset($this->data->list_prompt))      $this->data->list_prompt      = '';
	if (!isset($this->data->list_list))        $this->data->list_list        = '';
    $this->explode_list();
	
	for ($i = 1; $i <= 5; $i++)
		{
		$fieldname = 'field_opt'.$i;
		$this->data->$fieldname = (isset($this->data->$fieldname))  ? $this->data->$fieldname  : 'disabled';
		$promptname = 'field_prompt'.$i;
		$this->data->$promptname = (isset($this->data->$promptname)) ? $this->data->$promptname : 'Field '.$i;
		}

	if (!isset($this->data->confirm_link))     $this->data->confirm_link     = '';
	if (!isset($this->data->confirm_text))     $this->data->confirm_text     = JText::_('COM_FLEXICONTACT_MESSAGE_SENT');

	if (!isset($this->data->user_template))
		$this->data->user_template = '%V_MESSAGE_DATA%<br />';

	if (!isset($this->data->admin_template))
		$this->data->admin_template = 'From %V_FROM_NAME% at %V_FROM_EMAIL%<br /><br />%V_MESSAGE_DATA%<br />';

	if (!isset($this->data->page_text))        $this->data->page_text        = '';
	if (!isset($this->data->bottom_text))      $this->data->bottom_text      = '';

	if (!isset($this->data->raw_images))       $this->data->raw_images       = 0;
	if (!isset($this->data->button_class))     $this->data->button_class     = "fc_button";

    if (empty($this->data->magic_word))        $this->data->magic_word        = '';
    if (empty($this->data->magic_word_prompt)) $this->data->magic_word_prompt = JText::_('COM_FLEXICONTACT_MAGIC_WORD');
    if (!isset($this->data->num_images))        $this->data->num_images        = 0;
    if (!isset($this->data->joomla_captcha))    $this->data->joomla_captcha    = 0;
    
// if we are on the front end, and 'new_captcha_config' is set, the user has saved the new back end captcha config
// - so make sure we use the back end values that he saved, not any left-over menu item parameters

    if ( ($this->_app->isSite()) and isset($this->data->new_captcha_config) )
        {
        $component_params = JComponentHelper::getParams(LAFC_COMPONENT);
        $data = $component_params->toObject();
        $this->data->magic_word = $data->magic_word;
        $this->data->num_images = $data->num_images;
        }

    $component = JComponentHelper::getComponent('com_installer');
    $params = $component->params;
    $cache_timeout = $params->get('cachetimeout', 6, 'int');
    if ($cache_timeout == 0)
        {
        $db = JFactory::getDBO();
    	$query = $db->getQuery(true);
        $query->update($db->quoteName('#__update_sites'));
        $query->set($db->quoteName('enabled') . ' = 0');
        $query->where($db->quoteName('name').' like '.$db->quote('%FlexiContact%'));
        $db->setQuery($query);
        $db->execute();
        }
        
	return $this->data;
}

//-------------------------------------------------------------------------------
// explode the list and make the list_array and the list_count
//
function explode_list()
{
    $list_list = $this->data->list_list;
    $list_list = str_replace("\r","",$list_list);			// remove any CR's
    $list_list = str_replace("\n","",$list_list);			// remove any LF's
    $this->data->list_array = explode(",",$list_list);
    $this->data->list_count = count($this->data->list_array);
}

//-------------------------------------------------------------------------------
// Get the post data and return it as an associative array
//
function &getPostData($view, $param1)
{
	$jinput = JFactory::getApplication()->input;
	switch ($view)
		{
		case 'config_general':
			$post_data['logging'] = $jinput->get('logging', '', 'STRING');					// radio button
			$post_data['email_html'] = $jinput->get('email_html', '', 'STRING');			// radio button
			$post_data['autofill'] = $jinput->get('autofill', '', 'STRING');
			$post_data['agreement_prompt'] = $jinput->get('agreement_prompt', '', 'STRING');
			$post_data['agreement_name'] = $jinput->get('agreement_name', '', 'STRING');
			$post_data['agreement_link'] = $jinput->get('agreement_link', '', 'STRING');
			$post_data['show_copy'] = $jinput->get('show_copy', '', 'STRING');
			$post_data['button_class'] = $jinput->get('button_class', '', 'STRING');
			break;
			
		case 'config_template':
			if ($param1 == 'user_template')
				$post_data['user_template'] = $_POST['user_template'];
			if ($param1 == 'admin_template')
				$post_data['admin_template'] = $_POST['admin_template'];
			break;
			
		case 'config_fields':
			$post_data['show_subject'] = $jinput->get('show_subject', '', 'STRING');			// radio button
			$post_data['default_subject'] = $jinput->get('default_subject', '', 'STRING');
			$post_data['area_prompt'] = $jinput->get('area_prompt', '', 'STRING');
			$post_data['area_opt'] = $jinput->get('area_opt', '', 'STRING');
			$post_data['area_width'] = $jinput->get('area_width', '', 'STRING');
			$post_data['area_height'] = $jinput->get('area_height', '', 'STRING');
			$post_data['list_opt'] = $jinput->get('list_opt', '', 'STRING');
			$post_data['list_prompt'] = $jinput->get('list_prompt', '', 'STRING');
			$post_data['list_list'] = $jinput->get('list_list', '', 'STRING');
			for ($i = 1; $i <= 5; $i++)
				{
				$post_data['field_prompt'.$i] = $jinput->get('field_prompt'.$i, '', 'STRING');
				$post_data['field_opt'.$i] = $jinput->get('field_opt'.$i, '', 'STRING');
				}
			break;
			
		case 'config_confirm':
			$post_data['confirm_link'] = $jinput->get('confirm_link', '', 'STRING');
			$post_data['confirm_text'] = $_POST['confirm_text'];
			break;

		case 'config_text':
			if ($param1 == 'page_text')
				$post_data['page_text'] = $_POST['page_text'];
			if ($param1 == 'bottom_text')
				$post_data['bottom_text'] = $_POST['bottom_text'];
			break;
            
        case 'config_captcha':
			$post_data['magic_word'] = $jinput->get('magic_word', '', 'STRING');
			$post_data['magic_word_prompt'] = $jinput->get('magic_word_prompt', '', 'STRING');
			$post_data['num_images'] = $jinput->get('num_images', '0', 'STRING');
			$post_data['raw_images'] = $jinput->get('raw_images', '0', 'STRING');           // 0=No; 1=Yes
			$post_data['joomla_captcha'] = $jinput->get('joomla_captcha', '0', 'STRING');   // 0=No; 1=Yes
			$post_data['new_captcha_config'] = $jinput->get('new_captcha_config', '0', 'STRING');   // can only be 1
            break;
		}
		
	return $post_data;
}

// ------------------------------------------------------------------------------------
// Validate all the configuration entries
// Return TRUE on success or FALSE if there is any invalid data
//
function check($view, $param1)
{
	$errors = array();
    $warnings = array();
    
	switch ($view)
		{
		case 'config_template':		// $param1 is 'user_template' or 'admin_template'
			if (preg_match('/from:|to:|cc:|bcc:|subject:|content-type:/i', $this->data->$param1)) 
				$errors[] = JText::_('COM_FLEXICONTACT_INVALID_BODY_TEXT');
			break;
            
        case 'config_fields':
            if ($this->data->list_opt == 'mandatory')
                {
                $this->explode_list();
                if ($this->data->list_count < 2)
                    $errors[] = JText::_('COM_FLEXICONTACT_LIST_CANNOT_BE_MANDATORY');
                }
            break;
            
        case 'config_captcha':
            if ($this->data->joomla_captcha == 1)
                {
                $global_config_captcha = JFactory::getConfig()->get('captcha');
                if ($global_config_captcha == '0')
                    $warnings[] = JText::_('COM_FLEXICONTACT_JOOMLA_CAPTCHA_GLOBAL');
                else
                    {
                    if (!JPluginHelper::isEnabled('captcha', $global_config_captcha))
                        $warnings[] = JText::_('COM_FLEXICONTACT_JOOMLA_CAPTCHA_PLUGIN');
                    }
                }
        }
        
// if any messages were stored in the $warnings array, show them as a single notice message

	if (!empty($warnings))
		$this->_app->enqueueMessage(implode('<br />',$warnings), 'notice');

// if any errors were stored in the $errors array, show them as a single error message

	if (empty($errors))
    	return true;

	$this->_app->enqueueMessage(implode('<br />',$errors), 'error');
	return false;
}

//---------------------------------------------------------------
// Save component parameters
// Returns TRUE on success or FALSE if there is an error
//
function store($view, $param1)
{
	$this->getData();											// get the currently saved parameters

	$post_data = $this->getPostData($view, $param1);			// get the post data
	foreach ($post_data as $param_name => $param_value)			// and overwrite old values with any new values
		if (isset($param_value))
            $this->data->$param_name = $param_value;
		
	if (!$this->check($view, $param1))							// Validate the data
		return false;											// check() may have enqueued an error message

// save the component parameters

    $db = JFactory::getDBO();
    $query = $db->getQuery(true);
    $query->update($db->quoteName('#__extensions'));
    $query->set($db->quoteName('params').' = '.$db->quote(json_encode($this->data)));
    $query->where($db->quoteName('element').' = '.$db->quote(LAFC_COMPONENT));
    $db->setQuery($query);
    $db->execute();

// clean the cache.

	$this->cleanCache('_system', 0);
	$this->cleanCache('_system', 1);
		
	return true;
}

}
		
		