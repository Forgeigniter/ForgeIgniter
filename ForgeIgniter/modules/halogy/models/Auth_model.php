<?php defined('BASEPATH') OR exit('No direct script access allowed');

class Auth_model extends CI_Model {
	
	// The following method prevents an error occurring when $this->data is modified.
	// Error Message: 'Indirect modification of overloaded property Demo_cart_admin_model::$data has no effect'.
	public function &__get($key)
	{
		$CI =& get_instance();
		return $CI->$key;
	}

	###++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++###	
	// Login
	###++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++++###	

	/**
	 * login
	 * Validate the submitted login details and attempt to log the user into their account.
	 */
	function login()
	{
		$this->load->library('form_validation');

		// Set validation rules.
		$this->form_validation->set_rules('login_identity', 'Identity (Email / Login)', 'required');
		$this->form_validation->set_rules('login_password', 'Password', 'required');

		// If failed login attempts from users IP exceeds limit defined by config file, validate captcha.
		if ($this->flexi_auth->ip_login_attempts_exceeded())
		{
			/**
			 * reCAPTCHA
			 * http://www.google.com/recaptcha
			 * To activate reCAPTCHA, ensure the 'recaptcha_response_field' validation below is uncommented and then comment out the 'login_captcha' validation further below.
			 *
			 * The custom validation rule 'validate_recaptcha' can be found in '../libaries/MY_Form_validation.php'.
			 * The form field name used by 'reCAPTCHA' is 'recaptcha_response_field', this field name IS NOT editable.
			 * 
			 * Note: To use this example, you will also need to enable the recaptcha examples in 'controllers/auth.php', and 'views/demo/login_view.php'.
			*/
			$this->form_validation->set_rules('recaptcha_response_field', 'Captcha Answer', 'required|validate_recaptcha');				
			
			/**
			 * flexi auths math CAPTCHA
			 * Math CAPTCHA is a basic CAPTCHA style feature that asks users a basic maths based question to validate they are indeed not a bot.
			 * To activate Math CAPTCHA, ensure the 'login_captcha' validation below is uncommented and then comment out the 'recaptcha_response_field' validation above.
			 * 
			 * The field value submitted as the answer to the math captcha must be submitted to the 'validate_math_captcha' validation function.
			 * The custom validation rule 'validate_math_captcha' can be found in '../libaries/MY_Form_validation.php'.
			 * 
			 * Note: To use this example, you will also need to enable the math_captcha examples in 'controllers/auth.php', and 'views/demo/login_view.php'.
			*/
			# $this->form_validation->set_rules('login_captcha', 'Captcha Answer', 'required|validate_math_captcha['.$this->input->post('login_captcha').']');				
		}
		
		// Run the validation.
		if ($this->form_validation->run())
		{
			// Check if user wants the 'Remember me' feature enabled.
			$remember_user = ($this->input->post('remember_me') == 1);
	
			// Verify login data.
			$this->flexi_auth->login($this->input->post('login_identity'), $this->input->post('login_password'), $remember_user);

			// Save any public status or error messages (Whilst suppressing any admin messages) to CI's flash session data.
			$this->session->set_flashdata('message', $this->flexi_auth->get_messages());

			// Reload page, if login was successful, sessions will have been created that will then further redirect verified users.
			redirect('auth');
		}
		else
		{	
			// Set validation errors.
			$this->data['message'] = validation_errors('<p class="error_msg">', '</p>');
			
			return FALSE;
		}
	}

}
