<?php
/********************************************************************
Product		: Flexicontact
Date		: 29 November 2016
Copyright	: Les Arbres Design 2009-2016
Contact		: http://www.lesarbresdesign.info
Licence		: GNU General Public License
*********************************************************************/
defined('_JEXEC') or die('Restricted Access');

require_once JPATH_COMPONENT_ADMINISTRATOR .'/helpers/flexicontact_helper.php';

// load our css

$document = JFactory::getDocument();
$document->addStyleSheet(LAFC_SITE_CSS_URL.'?'.filemtime(LAFC_SITE_CSS_PATH));

jimport('joomla.application.component.controller');
require_once( JPATH_COMPONENT.'/controller.php' );
$controller = new FlexicontactController();

$jinput = JFactory::getApplication()->input;
$task = $jinput->get('task', '', 'STRING');

$controller->execute($task);

$controller->redirect();
