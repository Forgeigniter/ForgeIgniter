<?php defined('BASEPATH') OR exit('No direct script access allowed');

/**
 * ForgeIgniter
 *
 * A user friendly, modular content management system.
 * Forged on CodeIgniter - http://codeigniter.com
 *
 * @package		ForgeIgniter
 * @author		ForgeIgniter Team
 * @copyright	Copyright (c) 2014 - 2016 ForgeIgniter
 * @license		http://forgeigniter.com/license
 * @link		http://forgeigniter.com/
 * @since		Hal Version 1.0
 * @version		0.1
 *
 * MY_Form_validation Class
 *
 * Extends Form_Validation library
 *
 * Adds one validation rule, "unique" and accepts a
 * parameter, the name of the table and column that
 * you are checking, specified in the forum table.column
 *
 */
 
class MY_Form_validation extends CI_Form_validation {

	function __construct($rules = array())
	{
		parent::__construct($rules);
		
		// set password error
		$this->set_message('matches', 'The passwords do not match.');	    
	}

	// --------------------------------------------------------------------

	/**
	 * Unique
	 *
	 * @access	public
	 * @param	string
	 * @param	field
	 * @return	bool
	 */

	function unique($str, $field)
	{
		$CI =& get_instance();
		list($table, $column) = preg_split("/\./", $field, 2);

		// for shop
		$fields = $CI->db->list_fields($table);
		if (in_array('siteID', $fields) && $table != 'sites')
		{
			$CI->db->where('siteID', $CI->site->config['siteID']);
		}
		if (in_array('deleted', $fields))
		{
			$CI->db->where('deleted', 0);
		}

		$CI->form_validation->set_message('unique', 'The %s that you requested is already taken, please try another.');

		$CI->db->select('COUNT(*) dupe');
		$query = $CI->db->get_where($table, array($column => $str));
		$row = $query->row();
		
		return ($row->dupe > 0) ? FALSE : TRUE;
	}

	function really_unique($str, $field)
	{
		$CI =& get_instance();
		list($table, $column) = preg_split("/\./", $field, 2);

		$CI->form_validation->set_message('really_unique', 'The %s that you requested is already taken, please try another.');

		$CI->db->select('COUNT(*) dupe');
		$query = $CI->db->get_where($table, array($column => $str));
		$row = $query->row();
		
		return ($row->dupe > 0) ? FALSE : TRUE;
	}

	function set_error($error = '')
	{
		if (empty($error))
		{
			return FALSE;
		}
		else
		{
			$CI =& get_instance();

			$CI->form_validation->_error_array['custom_error'] = $error;

			return TRUE;
		}
	}
	
    // Check identity is available
    protected function identity_available($identity, $user_id = FALSE)
    {
		if (!$this->CI->flexi_auth->identity_available($identity, $user_id))
		{
			$status_message = $this->CI->lang->line('form_validation_duplicate_identity');
			$this->CI->form_validation->set_message('identity_available', $status_message);
			return FALSE;
		}
        return TRUE;
    }
  
    // Check email is available
    protected function email_available($email, $user_id = FALSE)
    {
		if (!$this->CI->flexi_auth->email_available($email, $user_id))
		{
			$status_message = $this->CI->lang->line('form_validation_duplicate_email');
			$this->CI->form_validation->set_message('email_available', $status_message);
			return FALSE;
		}
        return TRUE;
    }
  
    // Check username is available
    protected function username_available($username, $user_id = FALSE)
    {
		if (!$this->CI->flexi_auth->username_available($username, $user_id))
		{
			$status_message = $this->CI->lang->line('form_validation_duplicate_username');
			$this->CI->form_validation->set_message('username_available', $status_message);
			return FALSE;
		}
        return TRUE;
    }
  
    // Validate a password matches a specific users current password.
    protected function validate_current_password($current_password, $identity)
    {
		if (!$this->CI->flexi_auth->validate_current_password($current_password, $identity))
		{
			$status_message = $this->CI->lang->line('form_validation_current_password');
			$this->CI->form_validation->set_message('validate_current_password', $status_message);
			return FALSE;
		}
        return TRUE;
    }
	
    // Validate Password
     protected function validate_password($password)
    {
		$password_length = strlen($password);
		$min_length = $this->CI->flexi_auth->min_password_length();

		// Check password length is valid and that the password only contains valid characters.
		if ($password_length >= $min_length && $this->CI->flexi_auth->valid_password_chars($password))
		{
			return TRUE;
		}
		
		$status_message = $this->CI->lang->line('password_invalid');
		$this->CI->form_validation->set_message('validate_password', $status_message);
		return FALSE;
    }
 
    // Validate reCAPTCHA
    protected function validate_recaptcha()
    {
		if (!$this->CI->flexi_auth->validate_recaptcha())
		{
			$status_message = $this->CI->lang->line('captcha_answer_invalid');
			$this->CI->form_validation->set_message('validate_recaptcha', $status_message);
			return FALSE;
		}
        return TRUE;
    }
 
    // Validate Math Captcha
    protected function validate_math_captcha($input)
    {
		if (!$this->CI->flexi_auth->validate_math_captcha($input))
		{
			$status_message = $this->CI->lang->line('captcha_answer_invalid');
			$this->CI->form_validation->set_message('validate_math_captcha', $status_message);
			return FALSE;
		}
        return TRUE;
    }
	
}

?>