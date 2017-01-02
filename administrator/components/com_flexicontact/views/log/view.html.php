<?php
/********************************************************************
Product		: Flexicontact
Date		: 29 November 2016
Copyright	: Les Arbres Design 2010-2016
Contact		: http://www.lesarbresdesign.info
Licence		: GNU General Public License
*********************************************************************/
defined('_JEXEC') or die('Restricted Access');

class FlexicontactViewLog extends JViewLegacy
{

//-------------------------------------------------------------------------------
// Show the list of log records
//
function display($tpl = null)
{
	Flexicontact_Utility::viewStart();
	JToolBarHelper::title(LAFC_COMPONENT_NAME.': '.JText::_('COM_FLEXICONTACT_LOG'), 'flexicontact.png');
	JToolBarHelper::deleteList('','delete_log');
	
// get the order states				

	$app = JFactory::getApplication();
	$filter_order = $app->getUserStateFromRequest(LAFC_COMPONENT.'.filter_order', 'filter_order', 'date_time');
	$filter_order_Dir = $app->getUserStateFromRequest(LAFC_COMPONENT.'.filter_order_Dir', 'filter_order_Dir', 'desc');
	$lists['order_Dir'] = $filter_order_Dir;
	$lists['order'] = $filter_order;

// get the current filters	
		
	$filter_date = $app->getUserStateFromRequest(LAFC_COMPONENT.'.filter_date','filter_date',LAFC_LOG_LAST_28_DAYS,'int');

// make the filter lists

	$date_filters = array(
		LAFC_LOG_ALL            => JText::_('COM_FLEXICONTACT_LOG_ALL'),
		LAFC_LOG_LAST_7_DAYS    => JText::_('COM_FLEXICONTACT_LOG_LAST_7_DAYS'),
		LAFC_LOG_LAST_28_DAYS   => JText::_('COM_FLEXICONTACT_LOG_LAST_28_DAYS'),
		LAFC_LOG_LAST_12_MONTHS => JText::_('COM_FLEXICONTACT_LOG_LAST_12_MONTHS')
		);
    
	$limit = $app->getUserStateFromRequest('global.list.limit', 'limit', $app->getCfg('list_limit'), 'int');
	$list_limits = array('10' => '10', '20' => '20', '50' => '50', '100' => '100', '0' => JText::_('JALL'));
	$limit_list_html = Flexicontact_Utility::make_list('limit', $limit, $list_limits, 0, 'onchange="submitform();"');

	$lists['date_filters'] = Flexicontact_Utility::make_list('filter_date', $filter_date, $date_filters, 0, 'onchange="submitform( );"');					

	$numrows = count($this->log_list);

// Show the list

	?>
	<form action="index.php" method="post" name="adminForm" id="adminForm">
	<input type="hidden" name="option" value="<?php echo LAFC_COMPONENT ?>" />
	<input type="hidden" name="task" value="log_list" />
	<input type="hidden" name="boxchecked" value="0" />
	<input type="hidden" name="view" value="log_list" />
	<input type="hidden" name="filter_order" value="<?php echo $lists['order']; ?>" />
	<input type="hidden" name="filter_order_Dir" value="<?php echo $lists['order_Dir']; ?>" />
    <?php
    echo "\n".'<div>&nbsp;<div style="float:left">'; 
	if ($this->logging)
		echo JText::_('COM_FLEXICONTACT_LOGGING').': '.JText::_('JYES');
    else
		echo JText::_('COM_FLEXICONTACT_LOGGING').': '.JText::_('JNO');
	echo '</div>'; 
	echo "\n".'<div style="float:right">'; 
    echo $lists['date_filters'];
    echo ' '.$limit_list_html;
    echo ' <button class="btn btn-primary" onclick="'."
            document.getElementById('filter_date').value='".LAFC_LOG_LAST_28_DAYS."';
            this.form.submit();".'">'.JText::_('JSEARCH_RESET').'</button>';
	echo '</div></div>'; 

    ?>
	<table class="table table-striped">
	<thead>
	<tr>
		<th style="width:20px; text-align:center;"><input type="checkbox" name="toggle" value="" onclick="Joomla.checkAll(this);" /></th>
		<th style="white-space:nowrap;">
			<?php echo JHTML::_('grid.sort', 'COM_FLEXICONTACT_DATE_TIME', 'datetime', $lists['order_Dir'], $lists['order']); ?></th>
		<th style="white-space:nowrap;">
			<?php echo JHTML::_('grid.sort', 'COM_FLEXICONTACT_NAME', 'name', $lists['order_Dir'], $lists['order']); ?></th>
		<th style="white-space:nowrap;">
			<?php echo JHTML::_('grid.sort', 'COM_FLEXICONTACT_EMAIL', 'email', $lists['order_Dir'], $lists['order']); ?></th>
		<th style="white-space:nowrap;">
			<?php echo JHTML::_('grid.sort', 'COM_FLEXICONTACT_ADMIN_SUBJECT', 'subject', $lists['order_Dir'], $lists['order']); ?></th>
		<th style="white-space:nowrap;"><?php echo JText::_('COM_FLEXICONTACT_MESSAGE'); ?></th>
		<th style="white-space:nowrap;"><?php echo JText::_('COM_FLEXICONTACT_STATUS'); ?></th>
	</tr>
	</thead>

	<tfoot>
	<tr>
		<td colspan="15">
			<?php echo $this->pagination->getListFooter(); ?>
		</td>
	</tr>
	</tfoot>
	
	<tbody>
	<?php
	for ($i=0; $i < $numrows; $i++) 
		{
		$row = $this->log_list[$i];
		$link = LAFC_COMPONENT_LINK.'&task=log_detail&id='.$row->id;
		$checked = JHTML::_('grid.id', $i, $row->id);
		$date = JHTML::link($link, $row->datetime);
		$name = self::filter_text($row->name);
		$subject = self::filter_text($row->subject);
		$message = self::filter_text($row->short_message);
		$status_main = $this->_status($row->status_main);
		$status_copy = $this->_status($row->status_copy);

		echo '<tr>';
		echo '<td style="text-align:center;">'.$checked.'</td>';
		echo "<td>$date</td>
				<td>$name</td>
				<td>$row->email</td>
				<td>$subject</td>
				<td>$message</td>
				<td>$status_main $status_copy</td>
				</tr>\n";
		}
	echo '</tbody></table></form>';
	Flexicontact_Utility::viewEnd();
}

//-------------------------------------------------------------------------------
// Show a single log record
//
function edit($tpl = null)
{
	Flexicontact_Utility::viewStart();
	JToolBarHelper::title(LAFC_COMPONENT_NAME.': '.JText::_('COM_FLEXICONTACT_LOG'), 'flexicontact.png');
	JToolBarHelper::cancel('log_list');

?>
	<form action="index.php" method="post" name="adminForm" id="adminForm" >
	<input type="hidden" name="option" value="com_flexicontact" />
	<input type="hidden" name="task" value="cancel" />
	</form>
<?php

	$list_prompt = (isset($this->config_data->list_prompt)) ? $this->config_data->list_prompt : JText::_('COM_FLEXICONTACT_LIST').' '.JText::_('COM_FLEXICONTACT_DATA');

	echo '<table class="table table-striped table-condensed table-bordered width-auto">';
	echo "\n".'<tr><td style=width:25%;"><strong>'.JText::_('COM_FLEXICONTACT_DATE_TIME').'</strong></td><td>'.$this->log_data->datetime.'</td></tr>';
	echo "\n".'<tr><td><strong>'.JText::_('COM_FLEXICONTACT_NAME').'</strong></td><td>'.$this->log_data->name.'</td></tr>';
	echo "\n".'<tr><td><strong>'.JText::_('COM_FLEXICONTACT_EMAIL').'</strong></td><td>'.$this->log_data->email.'</td></tr>';
	echo "\n".'<tr><td><strong>'.JText::_('COM_FLEXICONTACT_ADMIN_SUBJECT').'</strong></td><td>'.$this->log_data->subject.'</td></tr>';

	echo "\n".'<tr><td style="vertical-align:top;"><strong>'.JText::_('COM_FLEXICONTACT_MESSAGE').'</strong></td>';
	$message = nl2br($this->log_data->message);
	if (substr($message, 0, 6) == '<br />')
    	$message = substr($message, 6);
	echo "\n".'<td style="white-space:normal;">'.$message.'</td></tr>';
	
	if ($this->log_data->list_choice)	
		echo "\n".'<tr><td><strong>'.$list_prompt.'</strong></td><td>'.$this->log_data->list_choice.'</td></tr>';
	if ($this->log_data->field1)
		echo "\n".'<tr><td><strong>'.$this->config_data->field_prompt1.'</strong></td><td>'.$this->log_data->field1.'</td></tr>';
	if ($this->log_data->field2)
		echo "\n".'<tr><td><strong>'.$this->config_data->field_prompt2.'</strong></td><td>'.$this->log_data->field2.'</td></tr>';
	if ($this->log_data->field3)
		echo "\n".'<tr><td><strong>'.$this->config_data->field_prompt3.'</strong></td><td>'.$this->log_data->field3.'</td></tr>';
	if ($this->log_data->field4)
		echo "\n".'<tr><td><strong>'.$this->config_data->field_prompt4.'</strong></td><td>'.$this->log_data->field4.'</td></tr>';
	if ($this->log_data->field5)
		echo "\n".'<tr><td><strong>'.$this->config_data->field_prompt5.'</strong></td><td>'.$this->log_data->field5.'</td></tr>';

	echo "\n".'<tr><td><strong>'.JText::_('COM_FLEXICONTACT_ADMIN_EMAIL').'</strong></td><td>'.$this->log_data->admin_email.'</td></tr>';
	echo "\n".'<tr><td><strong>'.JText::_('COM_FLEXICONTACT_ADMIN_EMAIL_FROM').'</strong></td><td>'.$this->log_data->admin_from_email.'</td></tr>';
	echo "\n".'<tr><td><strong>'.JText::_('COM_FLEXICONTACT_ADMIN_REPLY_TO').'</strong></td><td>'.$this->log_data->admin_reply_to_email.'</td></tr>';
	
// for records since version 8.08, show user email choice
// records from before 8.08 have 'config_show_copy' (and 'show_copy') set to 99

	if ($this->log_data->config_show_copy != 99)
		{
		switch ($this->log_data->config_show_copy)
			{
			case LAFC_COPYME_NEVER:	
				$user_email_choice = JText::_('COM_FLEXICONTACT_NEVER'); 
				break;
			case LAFC_COPYME_CHECKBOX: 
				if ($this->log_data->show_copy == 1)
					$user_email_choice = JText::_('COM_FLEXICONTACT_CHECKBOX_CHECKED');
				else
					$user_email_choice = JText::_('COM_FLEXICONTACT_CHECKBOX_NOT_CHECKED'); 
					break;
			case LAFC_COPYME_ALWAYS: $user_email_choice = JText::_('COM_FLEXICONTACT_ALWAYS'); 
				break;
			default: $user_email_choice = '';
			}
		echo "\n".'<tr><td><strong>'.JText::_('COM_FLEXICONTACT_USER_EMAIL_OPTION').'</strong></td><td>'.$user_email_choice.'</td></tr>';
		}

	if ($this->log_data->status_copy == '0')
		$user_email_to = '';
	else
		$user_email_to = $this->log_data->email;
	echo "\n".'<tr><td><strong>'.JText::_('COM_FLEXICONTACT_USER_EMAIL').'</strong></td><td>'.$user_email_to.'</td></tr>';
	echo "\n".'<tr><td><strong>'.JText::_('COM_FLEXICONTACT_USER_EMAIL_FROM').'</strong></td><td>'.$this->log_data->user_from_email.'</td></tr>';

	echo "\n".'<tr><td><strong>'.JText::_('COM_FLEXICONTACT_IP_ADDRESS').'</strong></td><td>'.$this->log_data->ip.'</td></tr>';
	echo "\n".'<tr><td><strong>'.JText::_('COM_FLEXICONTACT_BROWSER').'</strong></td><td>'.$this->log_data->browser_string.'</td></tr>';
	echo "\n".'<tr><td><strong>'.JText::_('COM_FLEXICONTACT_STATUS').'</strong></td><td>'.$this->_status($this->log_data->status_main).'</td></tr>';
	echo "\n".'<tr><td><strong>'.JText::_('COM_FLEXICONTACT_STATUS_COPY').'</strong></td><td>'.$this->_status($this->log_data->status_copy).'</td></tr>';
	echo '</table>';
	Flexicontact_Utility::viewEnd();
}

function _status($status)
{
	if ($status == '0')		// '0' status means no mail was sent
		return '';
	if ($status == '1')		// '1' means email was sent ok
		return '<img src="'.LAFC_ADMIN_ASSETS_URL.'tick.png" alt="" />';
	return $status;			// anything else was an error
}

function filter_text($text)
{
	$clean_text = substr($text, 0, 60);
	$clean_text = str_replace('<br />', ' ', $clean_text);
	$clean_text = strip_tags($clean_text);
	$clean_text = str_replace(array('<','>'), '', $clean_text);
	return $clean_text;
}

}