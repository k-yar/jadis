<?php
/********************************************************************
Product		: Flexicontact
Date		: 29 November 2016
Copyright	: Les Arbres Design 2009-2016
Contact		: http://www.lesarbresdesign.info
Licence		: GNU General Public License
*********************************************************************/
defined('_JEXEC') or die('Restricted Access');

class FlexicontactViewContact extends JViewLegacy
{

//---------------------------------------------------------------------------------------------------------
// display the responsive contact form
//
function display($tpl = null)
{
// get the menu item parameters

	$app = JFactory::getApplication('site');
	$document = JFactory::getDocument();
	$menu_params =  $app->getParams();

	if (isset($menu_params->pageclass_sfx))
		echo "\n".'<div class="fc_page'.$menu_params->pageclass_sfx.'">';
	else
		echo "\n".'<div class="fc_page">';

// if there is a page heading in Page Display Options, draw it in H1
	
	if ($menu_params->get('show_page_heading', '0'))						// In menu item Page Display options
		if ($menu_params->get('page_heading', '') != '')
			echo "\n<h1>".$menu_params->get('page_heading', '').'</h1>';

// if there is a page heading in Basic Options, draw it in H2

	if ($menu_params->get('page_hdr', '') != '')							// Basic Options
		echo "\n<h2>".$menu_params->get('page_hdr', '').'</h2>';

// set meta data, if any

	if ($menu_params->get('menu-meta_description'))
		$document->setDescription($menu_params->get('menu-meta_description'));

	if ($menu_params->get('menu-meta_keywords'))
		$document->setMetadata('keywords', $menu_params->get('menu-meta_keywords'));

	if ($menu_params->get('robots'))
		$document->setMetadata('robots', $menu_params->get('robots'));

// add sitename to page title if configured

	if ($app->getCfg('sitename_pagetitles', 0) == 1)
		{
		$title = JText::sprintf('JPAGETITLE', $app->getCfg('sitename'), $menu_params->get('page_title', ''));
		$document->setTitle($title);
		}
	if ($app->getCfg('sitename_pagetitles', 0) == 2)
		{
		$title = JText::sprintf('JPAGETITLE', $menu_params->get('page_title', ''), $app->getCfg('sitename'));
		$document->setTitle($title);
		}
		
// draw top text, if any

	if (!empty($this->config_data->page_text))		// top text
		{
		JPluginHelper::importPlugin('content');
		$page_text = JHtml::_('content.prepare', $this->config_data->page_text);
		echo "\n".'<div>'.$page_text.'</div>';
		}

// if the menu item has a width parameter, put the form in an extra div of that width

    $form_width = $menu_params->get('form_width', '');
    if ($form_width != '')
        echo "\n".'<div style="max-width:'.$form_width.'; margin-left:auto;margin-right:auto;">';

	if (!empty($errors))								// if validation failed
		{
		echo '<span class="fc_error">'.JText::_('COM_FLEXICONTACT_MESSAGE_NOT_SENT').'</span>';
		if (isset($errors['top']))
			echo '<br /><span class="fc_error">'.$errors['top'].'</span>';
		}
	
// start the form

	echo "\n".'<form name="fc_form" action="" method="post" class="fc_form">';
	echo JHTML::_('form.token');
	echo '<input type="hidden" name="option" value="'.LAFC_COMPONENT.'" />';
	echo '<input type="hidden" name="task" value="send" />';
	echo '<div class="fc_outer">';

// from name

	echo "\n".'<div class="fc_line"><label for="from_name" class="fc_left">'.JText::_('COM_FLEXICONTACT_FROM_NAME').'</label>';
	echo "\n".'<input type="text" class="fc_input" name="from_name" id="from_name" value="'.$this->escape($this->post_data->from_name).'" />'.
				self::get_error('from_name');
	echo "\n".'</div>';

// from email address

	echo "\n".'<div class="fc_line"><label for="from_email" class="fc_left">'.JText::_('COM_FLEXICONTACT_FROM_ADDRESS').'</label>';
	echo "\n".'<input type="email" class="fc_input" name="from_email" id="from_email" value="'.$this->escape($this->post_data->from_email).'" />'.
				self::get_error('from_email');
	echo "\n".'</div>';

// subject

	if ($this->config_data->show_subject)
		{
		echo "\n".'<div class="fc_line"><label for="subject" class="fc_left">'.JText::_('COM_FLEXICONTACT_SUBJECT').'</label>';
		echo "\n".'<input type="text" class="fc_input" name="subject" id="subject" value="'.$this->escape($this->post_data->subject).'" />'.
					self::get_error('subject');
		echo "\n".'</div>';
		}

// the select list

	if ($this->config_data->list_opt != 'disabled')
		{
		$list_html = Flexicontact_Utility::make_list('list1',$this->post_data->list1, $this->config_data->list_array);
		echo "\n".'<div class="fc_line"><label for="list1" class="fc_left">'.$this->config_data->list_prompt.'</label>';
		echo "\n".$list_html.self::get_error('list');
		echo "\n".'</div>';
		}

// the five optional fields

	for ($i=1; $i<=5; $i++)
		{
		$opt_name = 'field_opt'.$i;
		$prompt_name = 'field_prompt'.$i;
		$field_name = 'field'.$i;
		if ($this->config_data->$opt_name == 'disabled')
			continue;
		if ($this->config_data->$prompt_name == '')
			$this->config_data->$prompt_name = '&nbsp;';
		echo "\n".'<div class="fc_line"><label for="'.$field_name.'" class="fc_left">'.$this->config_data->$prompt_name.'</label>';
		echo "\n".'<input type="text" class="fc_input" name="'.$field_name.'" id="'.$field_name.'" value="'.$this->escape($this->post_data->$field_name).'" />'.
					self::get_error($field_name);
		echo "\n".'</div>';
		}

// the main text area

	if ($this->config_data->area_opt != 'disabled')
		{
		if ($this->config_data->area_prompt == '')
			$this->config_data->area_prompt = '&nbsp;';
		$size = self::size($this->config_data->area_width, 'cols');
		echo "\n".'<div class="fc_line fc_msg"><label for="area_data" class="fc_left fc_textarea">'.$this->config_data->area_prompt.'</label>';
		echo "\n".'<textarea class="fc_input" name="area_data" id="area_data" rows="'.$this->config_data->area_height.'" '.$size.'>'.$this->escape($this->post_data->area_data).'</textarea>'.
					self::get_error('area_data');
		echo "\n".'</div>';
		}

// the "send me a copy" checkbox

	if ($this->config_data->show_copy == LAFC_COPYME_CHECKBOX)
		{
		if ($this->post_data->copy_me)
			$checked = 'checked = "checked"';
		else
			$checked = '';
		$checkbox = '<input type="checkbox" class="fc_input" name="copy_me" id="copy_me" value="1" '.$checked.'/>';
		echo "\n".'<div class="fc_line fc_lcb">';
		echo "\n".$checkbox;
		echo "\n".'<label for="copy_me" class="fc_right">'.JText::_('COM_FLEXICONTACT_COPY_ME').'</label>';
		echo "\n".'</div>';
		}
	
// the agreement required checkbox

	$send_button_state = '';
	if ($this->config_data->agreement_prompt != '')
		{
		if ($this->post_data->agreement_check)
			$checked = 'checked = "checked"';
		else
			{
			$send_button_state = 'disabled="disabled"';
			$checked = '';
			}
		$onclick = ' onclick="if(this.checked==true){form.send_button.disabled=false;}else{form.send_button.disabled=true;}"';
		$checkbox = '<input type="checkbox" class="fc_input" name="agreement_check" id="agreement_check" value="1" '.$checked.$onclick.'/>';
		if (($this->config_data->agreement_name != '') and ($this->config_data->agreement_link != ''))
			{
			$popup = 'onclick="window.open('."'".$this->config_data->agreement_link."', 'fcagreement', 'width=640,height=480,scrollbars=1,location=0,menubar=0,resizable=1'); return false;".'"';
			$link_text = $this->config_data->agreement_prompt.' '.JHTML::link($this->config_data->agreement_link, $this->config_data->agreement_name, 'target="_blank" '.$popup);
			}
		else
			$link_text = $this->config_data->agreement_prompt;
		echo "\n".'<div class="fc_line fc_lcb">';
		echo "\n".$checkbox;
		echo "\n".'<label for="agreement_check" class="fc_right">'.' '.$link_text.'</label>';
		echo "\n".'</div>';
		}

// the magic word

	if ($this->config_data->magic_word != '')
		{
		echo "\n".'<div class="fc_line fc_magic"><label for="magic_word" class="fc_left">'.$this->config_data->magic_word_prompt.'</label>';
		echo "\n".'<input type="text" class="fc_input" name="magic_word" id="magic_word" value="'.$this->escape($this->post_data->magic_word).'" />'.
					self::get_error('magic_word');
		echo "\n".'</div>';
		}

// the image captcha

	if ($this->config_data->num_images > 0)
		{
		require_once(LAFC_HELPER_PATH.'/flexi_captcha.php');
		echo Flexi_captcha::show_image_captcha($this->config_data, self::get_error('imageTest'));
		}
        
// include the Joomla captcha plugin, if configured

	if ($this->config_data->joomla_captcha != 0)
		{
        $global_config_captcha = JFactory::getConfig()->get('captcha');
        if ($global_config_captcha != '0')
            {
            $captcha_plugin = JCaptcha::getInstance($global_config_captcha);
            if (is_object($captcha_plugin))
                {
                echo '<div class="fc_line fc_jcap">';
                echo $captcha_plugin->display('fcjcap', 'fcjcap', 'fcjcap');
                if (isset($this->errors['jcaptcha']))
                    echo self::get_error('jcaptcha');
                echo '</div>';
                }
            }
        }

// the send button

	echo "\n".'<div class="fc_line fc_send">';
	echo '<input type="submit" class="'.$this->config_data->button_class.'" id="send_button" name="send_button" '.$send_button_state.' value="'.JText::_('COM_FLEXICONTACT_SEND_BUTTON').'" />';
	echo "\n".'</div>';

	echo '</div>';					// class="fc_outer"
	echo "\n</form>";

// if the menu item has a width parameter, close the extra div

    if ($form_width != '')
        echo "\n".'</div>';
        
// bottom text

	if (!empty($this->config_data->bottom_text))
		{
		JPluginHelper::importPlugin('content');
		$bottom_text = JHtml::_('content.prepare', $this->config_data->bottom_text);
		echo "\n".'<div>'.$bottom_text.'</div>';
		}
		
	echo "\n</div>";				// class="fc_page"
}

//---------------------------------------------------------------------------------------------------------
// Get and format an error message
//
function get_error($field_name)
{
	if (isset($this->errors[$field_name]))
		return '<span class="fc_error">'.$this->errors[$field_name].'</span>';
	else
		return '';
}

//-------------------------------------------------------------------------------
// field widths can be:
// 0 or blank => nothing at all
// 99 => size="99" style="width:auto !important;"
// 99px => style="width:99px !important;"
// 99em => style="width:99em !important;"
// 99% => style="width:99% !important;"
//
static function size($width, $attribute='size')
{
	if (empty($width))
		return '';
	if (strpos($width, 'px'))
		return ' style="width:'.$width.' !important;"';
	if (strpos($width, 'em'))
		return ' style="width:'.$width.' !important;"';
	if (strpos($width, '%'))
		return ' style="width:'.$width.' !important;"';
	return ' '.$attribute.'="'.$width.'" style="width:auto !important;"';
}



	
}
?>