<?php
/********************************************************************
Product		: FlexicontactPlus
Date		: 29 November 2016
Copyright	: Les Arbres Design 2010-2016
Contact		: http://www.lesarbresdesign.info
Licence		: GNU General Public License
*********************************************************************/
defined('_JEXEC') or die('Restricted Access');

class FlexicontactController extends JControllerLegacy
{
function __construct()
{
	parent::__construct();
	$this->registerTask('save', 'apply');
	$this->registerTask('save_css', 'apply_css');
    $this->jinput = JFactory::getApplication()->input;
}

function display($cachable = false, $urlparams = false)
{
    $this->config();
}

function config()
{
	Flexicontact_Utility::addSubMenu('config');
	$view_name = $this->jinput->get('view', 'config_list', 'STRING');
	$view = $this->getView($view_name, 'html');
	$param1 = $this->jinput->get('param1', '', 'STRING');
	switch ($view_name)
		{
		case 'config_general':		// these options need the config data, the rest don't
		case 'config_template':
		case 'config_fields':
		case 'config_confirm':
		case 'config_text':
		case 'config_captcha':
			$config_model = $this->getModel('config');
			$config_data = $config_model->getData();
			$view->config_data = $config_data;
			$view->param1 = $param1;
			break;
		}
	$view->display();
}

function config_images()
{
	Flexicontact_Utility::addSubMenu('images');
	$view = $this->getView('config_images', 'html');
	$view->display();
}

function log_list()
{
	Flexicontact_Utility::addSubMenu('log');
	$view = $this->getView('log', 'html');
	
	$config_model = $this->getModel('config');
	$config_data = $config_model->getData();

	$logging = (isset($config_data->logging)) ? $config_data->logging : 0;
	$view->logging = $logging;
	
	$log_model = $this->getModel('log');
	$log_list = $log_model->getList();
	$view->log_list = $log_list;
	
	$pagination = $log_model->getPagination();
	$view->pagination =	$pagination;
	
	$view->display();
}

function log_detail()
{
	Flexicontact_Utility::addSubMenu('log');
	$view = $this->getView('log', 'html');

	$id = $this->jinput->get('id', '', 'INT');
	$log_model = $this->getModel('log');
	$log_data = $log_model->getOne($id);
	$view->log_data = $log_data;

	$config_model = $this->getModel('config');
	$config_data = $config_model->getData();
	$view->config_data = $config_data;

	$view->edit();
}

function delete_log()
{
	$log_model = $this->getModel('log');
	$cids = $this->jinput->get('cid', array(0), 'ARRAY');
	foreach ($cids as $id)
		$log_model->delete($id);
	$this->setRedirect(LAFC_COMPONENT_LINK."&task=log_list");
}

function cancel()
{
	$this->setRedirect(LAFC_COMPONENT_LINK."&task=config");
}

function delete_image()
{
	$cids = $this->jinput->get('cid', array(0), 'ARRAY');
	foreach ($cids as $file_name)
		@unlink(LAFC_SITE_IMAGES_PATH.'/'.$file_name);
	$this->setRedirect(LAFC_COMPONENT_LINK."&task=config_images&view=config_images");
}

function apply()									// save changes to component config
{	
	$task = $this->jinput->get('task', '', 'STRING');		// 'save' or 'apply'
	$view = $this->jinput->get('view', '', 'STRING');		// could be one of several
	$param1 = $this->jinput->get('param1', '', 'STRING');	// 'user_template', 'admin_template', 'page_text', 'bottom_text', etc
	$config_model = $this->getModel('config');
	$stored = $config_model->store($view, $param1);
	
	if ($stored)
		{
        if ($task == 'apply')
            $this->setRedirect(LAFC_COMPONENT_LINK."&task=config&view=$view&param1=$param1",JText::_('COM_FLEXICONTACT_SAVED'));
        else
            $this->setRedirect(LAFC_COMPONENT_LINK."&task=config",JText::_('COM_FLEXICONTACT_SAVED'));
        }
    else
        $this->config();
        
}   

function apply_css()								// save changes to front end css
{
	$task = $this->jinput->get('task', '', 'STRING');		// 'save_css' or 'apply_css'
	$css_contents = $_POST['css_contents'];
	if (strlen($css_contents) == 0)
		$this->setRedirect(LAFC_COMPONENT_LINK."&task=config");
	$css_path = JPATH_COMPONENT_SITE.'/assets/com_flexicontact.css';
	$length_written = file_put_contents ($css_path, $css_contents);
	if ($length_written == 0)
		$msg = JText::_('COM_FLEXICONTACT_NOT_SAVED');
	else
		$msg = JText::_('COM_FLEXICONTACT_SAVED');
	if ($task == 'apply_css')
		$this->setRedirect(LAFC_COMPONENT_LINK."&task=config&view=config_css",$msg);
	else
		$this->setRedirect(LAFC_COMPONENT_LINK."&task=config",$msg);
}

function about()
{
	Flexicontact_Utility::addSubMenu('about');
	$view = $this->getView('about', 'html');
	$view->display();
}


}
