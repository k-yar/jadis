<?php
/**
 * EBR - Easybook Reloaded for Joomla! 3.x
 * License: GNU/GPL - http://www.gnu.org/copyleft/gpl.html
 * Author: Viktor Vogel
 * Projectsite: https://joomla-extensions.kubik-rubik.de/ebr-easybook-reloaded
 *
 * @license GNU/GPL
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU General Public License as published by
 * the Free Software Foundation, either version 3 of the License, or
 * (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU General Public License for more details.
 *
 * You should have received a copy of the GNU General Public License
 * along with this program.  If not, see <http://www.gnu.org/licenses/>.
 */
defined('_JEXEC') OR die('Restricted access');

class EasybookReloadedControllerEntryGB extends JControllerLegacy
{
	protected $input;

	function __construct()
	{
		parent::__construct();
		$this->registerTask('add', 'edit');
		$this->registerTask('apply', 'save');
		$this->input = JFactory::getApplication()->input;
	}

	function edit()
	{
		$this->input->set('view', 'entrygb');
		$this->input->set('layout', 'form');
		$this->input->set('hidemainmenu', 1);

		parent::display();
	}

	function save()
	{
		JSession::checkToken() OR jexit('Invalid Token');

		$model = $this->getModel('entrygb');

		if($model->store())
		{
			$msg = JText::_('COM_EASYBOOKRELOADED_ENTRY_SAVED');
			$type = 'message';
		}
		else
		{
			$msg = JText::_('COM_EASYBOOKRELOADED_ERROR_SAVING_ENTRY');
			$type = 'error';
		}

		if($this->task == 'apply')
		{
			$this->setRedirect('index.php?'.$this->input->getString('url_current'), $msg, $type);
		}
		else
		{
			$this->setRedirect('index.php?option=com_easybookreloaded&task=gb', $msg, $type);
		}
	}

	function remove()
	{
		JSession::checkToken() OR jexit('Invalid Token');

		$model = $this->getModel('entrygb');

		if(!$model->delete())
		{
			$msg = JText::_('COM_EASYBOOKRELOADED_ERROR_ENTRY_COULD_NOT_BE_DELETED');
			$type = 'error';
		}
		else
		{
			$msg = JText::_('COM_EASYBOOKRELOADED_ENTRY_DELETED');
			$type = 'message';
		}

		$this->setRedirect(JRoute::_('index.php?option=com_easybookreloaded&task=gb', false), $msg, $type);
	}

	function cancel()
	{
		$msg = JText::_('COM_EASYBOOKRELOADED_OPERATION_CANCELLED');
		$this->setRedirect('index.php?option=com_easybookreloaded&task=gb', $msg, 'notice');
	}
}
