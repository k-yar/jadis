<?php
/********************************************************************
Product		: Flexicontact
Date		: 31 March 2016
Copyright	: Les Arbres Design 2010-2016
Contact		: http://www.lesarbresdesign.info
Licence		: GNU General Public License
*********************************************************************/
defined('_JEXEC') or die('Restricted access');

class FlexicontactModelLog extends JModelLegacy
{
var $_data;
var $_app = null;
var $_pagination = null;

function __construct()
{
	parent::__construct();
	$this->_app = JFactory::getApplication();
}

//-------------------------------------------------------------------------------
// get an existing row
// return false if we couldn't find it
//
function getOne($id)
{
    $query = $this->_db->getQuery(true);
    $query->select(array('*'));
    $query->from($this->_db->quoteName('#__flexicontact_log'));
    $query->where($this->_db->quoteName('id') . ' = '. $this->_db->quote($id));
    try
		{
		$this->_db->setQuery($query);
		$this->_data = $this->_db->loadObject();
		}
	catch (RuntimeException $e)
		{
	    $this->ladb_error_text = $e->getMessage();
	    $this->ladb_error_code = $e->getCode();
        Flexicontact_Utility::log_error($this->ladb_error_text);
		return false;
		}
	return $this->_data;
}

//---------------------------------------------------------------
//
function store($email_data)
{
    $query = $this->_db->getQuery(true);
    $columns = array('datetime', 'name', 'email', 'admin_email', 'admin_from_email', 'user_from_email', 'admin_reply_to_email',
			'subject', 'message', 'config_show_copy', 'show_copy', 'status_main', 'status_copy', 
			'ip', 'browser_id', 'browser_string', 'list_choice', 'field1', 'field2', 'field3', 'field4', 'field5');
    $values = array('NOW()',
			$this->_db->Quote($email_data->from_name),
			$this->_db->Quote($email_data->from_email),
			$this->_db->Quote($email_data->admin_email),
			$this->_db->Quote($email_data->admin_from_email),
			$this->_db->Quote($email_data->user_from_email),
			$this->_db->Quote($email_data->admin_reply_to_email),
			$this->_db->Quote($email_data->subject),
			$this->_db->Quote($email_data->area_data),
			$this->_db->Quote($email_data->config_show_copy),
			$this->_db->Quote($email_data->show_copy),
			$this->_db->Quote($email_data->status_main),
			$this->_db->Quote($email_data->status_copy),
			$this->_db->Quote($email_data->ip),
			$this->_db->Quote($email_data->browser_id),
			$this->_db->Quote($email_data->browser_string),
			$this->_db->Quote($email_data->list_choice),
			$this->_db->Quote($email_data->field1),
			$this->_db->Quote($email_data->field2),
			$this->_db->Quote($email_data->field3),
			$this->_db->Quote($email_data->field4),
			$this->_db->Quote($email_data->field5));
    $query->insert($this->_db->quoteName('#__flexicontact_log'));
    $query->columns($this->_db->quoteName($columns));
    $query->values(implode(',', $values));
	try
		{
		$this->_db->setQuery($query);
		$this->_db->execute();
        $result = true;
		}
	catch (RuntimeException $e)
		{
	    $this->ladb_error_text = $e->getMessage();
	    $this->ladb_error_code = $e->getCode();
        $result = false;
        Flexicontact_Utility::log_error($this->ladb_error_text);
		}
	return $result;
}

//-------------------------------------------------------------------------------
// Return a pointer to our pagination object
// This should normally be called after getList()
//
function &getPagination()
{
	if ($this->_pagination == Null)
		$this->_pagination = new JPagination(0,0,0);
	return $this->_pagination;
}

//-------------------------------------------------------------------------------
// Get the list of logs for the log list screen
//
function &getList()
{
		
// get the filter states, order states, and pagination variables

	$filter_date = $this->_app->getUserStateFromRequest(LAFC_COMPONENT.'.filter_date','filter_date',LAFC_LOG_LAST_28_DAYS,'int');
	$limit = $this->_app->getUserStateFromRequest('global.list.limit', 'limit', $this->_app->getCfg('list_limit'), 'int');
	$limitstart = $this->_app->getUserStateFromRequest(LAFC_COMPONENT.'.limitstart', 'limitstart', 0, 'int');
	$limitstart = ($limit != 0 ? (floor($limitstart / $limit) * $limit) : 0); // In case limit has been changed
	$filter_order = $this->_app->getUserStateFromRequest(LAFC_COMPONENT.'.filter_order', 'filter_order', 'datetime');
	$filter_order_Dir = $this->_app->getUserStateFromRequest(LAFC_COMPONENT.'.filter_order_Dir', 'filter_order_Dir', 'desc');

// build the where clause

	$query_where = "1 ";

	switch ($filter_date)
		{
		case LAFC_LOG_ALL:
			break;
		case LAFC_LOG_LAST_7_DAYS:
			$query_where .= "AND ".$this->_db->quoteName('datetime')." >= DATE_SUB(CURRENT_DATE(), INTERVAL 7 DAY)";
			break;
		case LAFC_LOG_LAST_28_DAYS:
			$query_where .= "AND ".$this->_db->quoteName('datetime')." >= DATE_SUB(CURRENT_DATE(), INTERVAL 28 DAY)";
			break;
		case LAFC_LOG_LAST_12_MONTHS:
			$query_where .= "AND ".$this->_db->quoteName('datetime')." >= DATE_SUB(CURRENT_DATE(), INTERVAL 12 MONTH)";
		}	

// order by

	switch ($filter_order)							// validate column name
		{
		case 'name':
		case 'email':
		case 'subject':
			break;
		default:
			$filter_order = 'datetime';
		}

	if (strcasecmp($filter_order_Dir,'ASC') != 0)	// validate 'asc' or 'desc'
		$filter_order_Dir = 'DESC';

	$query_order = $this->_db->quoteName($filter_order).' '.$filter_order_Dir;

// get the total row count

    $query = $this->_db->getQuery(true);
    $query->select(array('count(*)'));
    $query->from($this->_db->quoteName('#__flexicontact_log'));
    $query->where($query_where);
	try
		{
		$this->_db->setQuery($query);
		$total = $this->_db->loadResult();
		}
	catch (RuntimeException $e)
		{
	    $this->ladb_error_text = $e->getMessage();
	    $this->ladb_error_code = $e->getCode();
        $total = false;
		}
        
	if ($total === false)
		{
		$this->_app->enqueueMessage($this->ladb_error_text, 'error');
		return $total;
		}

// setup the pagination object

	$this->_pagination = new JPagination($total, $limitstart, $limit);

// get the data, within the limits

    $query = $this->_db->getQuery(true);
    $query->select(array(
        $this->_db->quoteName('id'),
        $this->_db->quoteName('datetime'),
        $this->_db->quoteName('name'),
        $this->_db->quoteName('email'),
        $this->_db->quoteName('subject'),
        'SUBSTRING('.$this->_db->quoteName('message').',1,60) AS short_message',
        $this->_db->quoteName('status_main'),
        $this->_db->quoteName('status_copy') ));
    $query->from($this->_db->quoteName('#__flexicontact_log'));
    $query->where($query_where);
    $query->order($query_order);
    
    try
		{
		$this->_db->setQuery($query, $limitstart, $limit);
		$this->_data = $this->_db->loadObjectList();
		}
	catch (RuntimeException $e)
		{
	    $this->ladb_error_text = $e->getMessage();
	    $this->ladb_error_code = $e->getCode();
        $this->_data = false;
		}
	
	if ($this->_data === false)
		{
		$this->_app->enqueueMessage($this->ladb_error_text, 'error');
		return $this->_data;
		}
		
	return $this->_data;
}

//-------------------------------------------------------------------------------
// delete a log entry
//
function delete($id)
{
    $query = $this->_db->getQuery(true);
    $query->delete($this->_db->quoteName('#__flexicontact_log'));
    $query->where(array($this->_db->quoteName('id').' = '.$this->_db->Quote($id)));
	try
		{
		$this->_db->setQuery($query);
		$this->_db->execute();
        $result = true;
		}
	catch (RuntimeException $e)
		{
	    $this->ladb_error_text = $e->getMessage();
	    $this->ladb_error_code = $e->getCode();
        $result = false;
		}
    
	if ($result === false)
		$this->_app->enqueueMessage($this->ladb_error_text, 'error');
}


}
		
		