<?php
/**
 * ForgeIgniter
 *
 * A user friendly, modular content management system.
 * Forged on CodeIgniter - http://codeigniter.com
 *
 * @package		ForgeIgniter
 * @author		ForgeIgniter Team
 * @copyright	Copyright (c) 2015, ForgeIgniter
 * @license		http://forgeigniter.com/license
 * @link		http://forgeigniter.com/
 * @since		Hal Version 1.0
 * @filesource
 */

// ------------------------------------------------------------------------

class Admin extends MX_Controller {

	// set defaults
	var $includes_path = '/includes/admin';				// path to includes for header and footer
	var $redirect = '/admin/dashboard';
	var $permissions = array();
	
	function __construct()
	{
		parent::__construct();
		
		// get siteID, if available
		if (defined('SITEID'))
		{
			$this->siteID = SITEID;
		}
		
		$this->load->library('flexi_auth');	

		// Check user is logged in as an admin.
		// For security, admin users should always sign in via Password rather than 'Remember me'.

		
		// Define a global variable to store data that is then used by the end view page.
		$this->data = null;

	}

	function index()
	{
		redirect(site_url($this->redirect));
	}

	function dashboard($days = '')
	{
		
		/*
		// logout if not admin
		if ($this->session->userdata('session_user') && !$this->permission->permissions)
		{
			show_error('Sorry, you do not have permission to administer this website. Please go back or '.anchor('/admin/logout', 'log out').'.');
		}
		if (!$this->session->userdata('session_admin'))
		{
			redirect('/admin/login/'.$this->core->encode($this->uri->uri_string()));
		}
		*/
		// logout if not admin
		if ($this->flexi_auth->is_logged_in_via_password() || !$this->flexi_auth->is_admin()) 
		{
			
		}

		// load model and libs
		$this->load->model('halogy_model', 'halogy');
		$this->load->library('parser');

		// show any errors that have resulted from a redirect
		if ($days === 'permissions')
		{
			$this->form_validation->set_error('Sorry, you do not have permissions to do what you just tried to do.');
		}

		// set message
		$output['message'] = '';

		// get new blog comments
		$newComments = $this->halogy->get_blog_new_comments();
		if ($newComments)
		{
			$output['message'] .= '<p>You have <strong>'.$newComments.' new pending comment(s).</strong> You can <a href="'.site_url('/admin/blog/comments').'">view your comments here</a>.</p>';
		}

		// get new blog comments
		$newTickets = $this->halogy->get_new_tickets();
		if ($newTickets)
		{
			$output['message'] .= '<p>You have <strong>'.$newTickets.' new ticket(s).</strong> You can <a href="'.site_url('/admin/webforms/tickets').'">view your tickets here</a>.</p>';
		}		

		// get new orders
		if (@in_array('shop', $this->permission->sitePermissions))
		{
			$this->load->model('shop/shop_model', 'shop');

			if ($newOrders = $this->shop->get_new_orders())
			{
				$output['message'] .= '<p>You have <strong>'.sizeof($newOrders).' new order(s).</strong> You can <a href="'.site_url('/admin/shop/orders').'">view your orders here</a>.</p>';
			}
		}
		
		// look to see if there are any pages
		if (!$this->halogy->get_num_pages())
		{
			// import default template for new sites
			$this->load->model('sites_model', 'sites');
			$this->sites->add_templates($this->siteID);			
			$output['message'] = '<p><strong>Congratulations</strong> - your new site is set up and ready to go!</strong> You can view your site <a href="'.site_url('/').'">here</a>.</p>';
		}
		else
		{
			// set error if default password is still used
			$user = $this->core->lookup_user($this->session->userdata('userID'));
			if ($user['password'] == 'f35364bc808b079853de5a1e343e7159')
			{
				$this->form_validation->set_error('You are still using the default Superuser password. You can change your password <a href="'.site_url('/admin/users/edit/'.$this->session->userdata('userID')).'">here</a>');
			}
		}
		
		// Is Install still there ?
		if(file_exists(FCPATH."ForgeIgniter\install\index.php"))
		{
				$this->form_validation->set_error('Please delete ForgeIgniter/install folder.');
				
		}

		// get stats
		$data['recentActivity'] = $this->halogy->get_recent_activity();		
		$data['todaysActivity'] = $this->halogy->get_activity('today');
		$data['yesterdaysActivity'] = $this->halogy->get_activity('yesterday');		
		$output['activity'] = $this->parser->parse('activity_ajax', $data, TRUE);

		// get stats
		$output['days'] = (is_numeric($days)) ? $days : '30';
		$output['numPageViews'] = $this->halogy->get_num_page_views();
		$output['numPages'] = $this->halogy->get_num_pages();
		$output['quota'] = $this->site->get_quota();
		$output['numUsers'] = ($count = $this->halogy->get_num_users()) ? $count : 0;
		$output['numUsersToday'] = ($count = $this->halogy->get_num_users_today()) ? $count : 0;
		$output['numUsersYesterday'] = ($count = $this->halogy->get_num_users_yesterday()) ? $count : 0;
		$output['numUsersWeek'] = ($count = $this->halogy->get_num_users_week()) ? $count : 0;
		$output['numUsersLastWeek'] = ($count = $this->halogy->get_num_users_last_week()) ? $count : 0;		
		$output['numBlogPosts'] = $this->halogy->get_blog_posts_count();
		$output['popularPages'] = $this->halogy->get_popular_pages();
		$output['popularBlogPosts'] = $this->halogy->get_popular_blog_posts();
		$output['popularShopProducts'] = $this->halogy->get_popular_shop_products();
				
		$this->load->view($this->includes_path.'/header');
		$this->load->view('dashboard', $output);
		$this->load->view($this->includes_path.'/footer');		
	}

	function stats($limit = 30)
	{		
		// logout if not admin
		if ($this->session->userdata('session_admin'))
		{
			$visitations = 0;
			$signups = 0;
	
			$this->db->select("COUNT(*) as visitations, UNIX_TIMESTAMP(MIN(date))*1000 as dateMicro, DATE_FORMAT(date,'%y%m%d') as dateFmt", FALSE);
			$this->db->where('siteID', $this->siteID);
			$this->db->where('date >=', "DATE_SUB(CONCAT(CURDATE(), ' 00:00:00'), INTERVAL ".$this->db->escape($limit)." DAY)", FALSE);
			$this->db->order_by('dateFmt', 'desc');
			$this->db->group_by('dateFmt');
			
			$query = $this->db->get('tracking');
	
			if ($query->num_rows())
			{
				$visitations = array();
	
				$i=0;			
				$result = $query->result_array();
				foreach($result as $row)
				{
					$i++;
					$visitations[$i] = '['.$row['dateMicro'].','.$row['visitations'].']';
				}
				$visitations = implode(',', $visitations);
			}
			
			$this->db->select("COUNT(*) as signups, UNIX_TIMESTAMP(MIN(dateCreated))*1000 as dateMicro, DATE_FORMAT(dateCreated,'%y%m%d') as dateFmt", FALSE);
			$this->db->where('siteID', $this->siteID);
			$this->db->where('dateCreated >=', "DATE_SUB(CONCAT(CURDATE(), ' 00:00:00'), INTERVAL ".$this->db->escape($limit)." DAY)", FALSE);
			$this->db->order_by('dateFmt', 'desc');
			$this->db->group_by('dateFmt');
			
			$query = $this->db->get('users');
	
			if ($query->num_rows())
			{
				$signups = array();
	
				$i=0;			
				$result = $query->result_array();
				foreach($result as $row)
				{
					$i++;
					$signups[$i] = '['.$row['dateMicro'].','.$row['signups'].']';
				}
				$signups = implode(',', $signups);
			}
	
			$this->output->set_output('{ "visits" : ['.$visitations.'] ,  "signups" : ['.$signups.'] }');
		}
	}

	function activity_ajax()
	{
		// logout if not admin
		if ($this->session->userdata('session_admin'))
		{
			// load model
			$this->load->model('halogy_model', 'halogy');
	
			// get stats
			$output['recentActivity'] = $this->halogy->get_recent_activity();		
			$output['todaysActivity'] = $this->halogy->get_activity('today');
			$output['yesterdaysActivity'] = $this->halogy->get_activity('yesterday');
	
			$this->load->view('activity_ajax', $output);
		}
	}

	function tracking()
	{
		// logout if not admin
		if (!$this->session->userdata('session_admin'))
		{
			redirect('/admin/login/'.$this->core->encode($this->uri->uri_string()));
		}
		
		$this->load->view($this->includes_path.'/header');
		$this->load->view('tracking');
		$this->load->view($this->includes_path.'/footer');
	}

	function tracking_ajax()
	{
		// logout if not admin
		if ($this->session->userdata('session_admin'))
		{		
			$output = $this->core->viewall('tracking', null, array('trackingID', 'desc'));
	
			$this->load->view('tracking_ajax', $output);
		}
	}
	
	/*
	function login($redirect = '')
	{
		// load libs etc
		$this->load->library('auth');
		
		if (!$this->session->userdata('session_admin'))
		{
			if ($_POST)
			{	
				// set redirect to default if not given
				if ($redirect == '')
				{
					$redirect = $this->redirect;
				}
				else
				{
					$redirect = $this->core->decode($redirect);
				}
				
				// set admin session name, if given
				if ($this->auth->login($this->input->post('username'), $this->input->post('password'), 'session_user'))
				{
					// for use with ce
					if ($this->session->userdata('groupID') != 0 && $this->permission->get_group_permissions($this->session->userdata('groupID')))
					{
						$this->session->set_userdata('session_admin', TRUE);
					}
					
					// update quota
					$quota = $this->site->get_quota();
					$this->core->set['quota'] = ($quota > 0) ? (floor($quota / $this->site->plans['storage'] * 100)) : 0;
					$this->core->update('sites', array('siteID' => $this->siteID));

					redirect($redirect);
				}

				// get error message
				else
				{
					$this->form_validation->set_error($this->auth->error);
				}
			}
		}
		else
		{
			if ($redirect != '')
			{
				redirect($redirect);
			}
		}
		
		// view
		$this->load->view($this->includes_path.'/header');
		$this->load->view('login');
		$this->load->view($this->includes_path.'/footer');	
	}


	/**
	 * login
	 * Login page used by all user types to log into their account.
	 * This demo includes 3 example accounts that can be logged into via using either their email address or username. The login details are provided within the view page.
	 * Users without an account can register for a new account.
	 * Note: This page is only accessible to users who are not currently logged in, else they will be redirected.
	 */ 
    function login()
    {
		if ($this->flexi_auth->is_logged_in_via_password() || $this->flexi_auth->is_admin()) 
		{
			// Set a custom error message.
			$this->flexi_auth->set_error_message('You must login as an admin to access this area.', TRUE);
			$this->session->set_flashdata('message', $this->flexi_auth->get_messages());
			redirect('admin/dashboard');
		}
		
		// If 'Login' form has been submited, attempt to log the user in.
		if ($this->input->post('login_user'))
		{
			$this->load->model('auth_model');
			$this->auth_model->login();
		}
			
		// CAPTCHA Example
		// Check whether there are any existing failed login attempts from the users ip address and whether those attempts have exceeded the defined threshold limit.
		// If the user has exceeded the limit, generate a 'CAPTCHA' that the user must additionally complete when next attempting to login.
		if ($this->flexi_auth->ip_login_attempts_exceeded())
		{
			/**
			 * reCAPTCHA
			 * http://www.google.com/recaptcha
			 * To activate reCAPTCHA, ensure the 'recaptcha()' function below is uncommented and then comment out the 'math_captcha()' function further below.
			 *
			 * A boolean variable can be passed to 'recaptcha()' to set whether to use SSL or not.
			 * When displaying the captcha in a view, if the reCAPTCHA theme has been set to one of the template skins (See https://developers.google.com/recaptcha/docs/customization),
			 *  then the 'recaptcha()' function generates all the html required.
			 * If using a 'custom' reCAPTCHA theme, then the custom html must be PREPENDED to the code returned by the 'recaptcha()' function.
			 * Again see https://developers.google.com/recaptcha/docs/customization for a template 'custom' html theme. 
			 * 
			 * Note: To use this example, you will also need to enable the recaptcha examples in 'models/auth_model.php', and 'views/demo/login_view.php'.
			*/
			$this->data['captcha'] = $this->flexi_auth->recaptcha(FALSE);
						
			/**
			 * flexi auths math CAPTCHA
			 * Math CAPTCHA is a basic CAPTCHA style feature that asks users a basic maths based question to validate they are indeed not a bot.
			 * For flexibility on CAPTCHA presentation, the 'math_captcha()' function only generates a string of the equation, see the example below.
			 * 
			 * To activate math_captcha, ensure the 'math_captcha()' function below is uncommented and then comment out the 'recaptcha()' function above.
			 *
			 * Note: To use this example, you will also need to enable the math_captcha examples in 'models/auth_model.php', and 'views/demo/login_view.php'.
			*/
			# $this->data['captcha'] = $this->flexi_auth->math_captcha(FALSE);
		}
				
		// Get any status message that may have been set.
		$this->data['message'] = (! isset($this->data['message'])) ? $this->session->flashdata('message') : $this->data['message'];	
		
		// view
		$this->load->view($this->includes_path.'/header');
		$this->load->view('login',$this->data);
		$this->load->view($this->includes_path.'/footer');	
		
		
    }
	
	
	
	/*
	function logout($redirect = '')
	{
		// load libs etc
		$this->load->library('auth');
		
		// set redirect to default if not given
		if ($redirect == '')
		{
			$redirect = '';
		}
		else
		{
			$redirect = $this->core->decode($redirect);
		}
		$this->auth->logout($redirect);
	}
	*/
	
	/**
	 * logout
	 * This example logs the user out of all sessions on all computers they may be logged into.
	 * In this demo, this page is accessed via a link on the demo header once a user is logged in.
	 */
	function logout() 
	{
		// By setting the logout functions argument as 'TRUE', all browser sessions are logged out.
		$this->flexi_auth->logout(TRUE);
		
		// Set a message to the CI flashdata so that it is available after the page redirect.
		$this->session->set_flashdata('message', $this->flexi_auth->get_messages());		
 
		redirect('auth');
    }
	
	/**
	 * logout_session
	 * This example logs the user only out of their CURRENT browser session (e.g. Internet Cafe), but no other logged in sessions (e.g. Home and Work).
	 * In this demo, this controller method is actually not linked to. It is included here as an example of logging a user out of only their current session.
	 */
	function logout_session() 
	{
		// By setting the logout functions argument as 'FALSE', only the current browser session is logged out.
		$this->flexi_auth->logout(FALSE);

		// Set a message to the CI flashdata so that it is available after the page redirect.
		$this->session->set_flashdata('message', $this->flexi_auth->get_messages());		
        
		redirect('auth');
    }

	//

	function site()
	{
		// logout if not admin
		if (!$this->session->userdata('session_admin'))
		{
			redirect('/admin/login/'.$this->core->encode($this->uri->uri_string()));
		}

		// check they are administrator
		if ($this->session->userdata('groupID') != $this->site->config['groupID'] && $this->session->userdata('groupID') >= 0)
		{
			redirect('/admin/dashboard/permissions');
		}
		
		// set object ID
		$objectID = array('siteID' => $this->siteID);

		// get values
		$output['data'] = $this->core->get_values('sites', $objectID);

		// set defaults
		$output['data']['shopVariation1'] = ($this->input->post('shopVariation1')) ? $this->input->post('shopVariation1') : $this->site->config['shopVariation1'];
		$output['data']['shopVariation2'] = ($this->input->post('shopVariation2')) ? $this->input->post('shopVariation2') : $this->site->config['shopVariation2'];
		$output['data']['shopVariation3'] = ($this->input->post('shopVariation3')) ? $this->input->post('shopVariation3') : $this->site->config['shopVariation3'];
		$output['data']['emailHeader'] = ($this->input->post('emailHeader')) ? $this->input->post('emailHeader') : $this->site->config['emailHeader'];
		$output['data']['emailFooter'] = ($this->input->post('emailFooter')) ? $this->input->post('emailFooter') : $this->site->config['emailFooter'];
		$output['data']['emailTicket'] = ($this->input->post('emailTicket')) ? $this->input->post('emailTicket') : $this->site->config['emailTicket'];
		$output['data']['emailAccount'] = ($this->input->post('emailAccount')) ? $this->input->post('emailAccount') : $this->site->config['emailAccount'];
		$output['data']['emailOrder'] = ($this->input->post('emailOrder')) ? $this->input->post('emailOrder') : $this->site->config['emailOrder'];
		$output['data']['emailDispatch'] = ($this->input->post('emailDispatch')) ? $this->input->post('emailDispatch') : $this->site->config['emailDispatch'];
		$output['data']['emailDonation'] = ($this->input->post('emailDonation')) ? $this->input->post('emailDonation') : $this->site->config['emailDonation'];
		$output['data']['emailSubscription'] = ($this->input->post('emailSubscription')) ? $this->input->post('emailSubscription') : $this->site->config['emailSubscription'];
				
		// handle post
		if (count($_POST))
		{
			// check some things aren't being posted
			if ($this->input->post('siteID') || $this->input->post('siteDomain') || $this->input->post('groupID'))
			{
				show_error('You do not have permission to change those things.');
			}
			
			// required
			$this->core->required = array(
				'siteName' => array('label' => 'Name of Site', 'rules' => 'required|trim'),
				'siteURL' => array('label' => 'URL', 'rules' => 'required|trim'),
				'siteEmail' => array('label' => 'Email', 'rules' => 'required|valid_email|trim'),
			);	
	
			// set date
			$this->core->set['dateModified'] = date("Y-m-d H:i:s");
			
			// update
			if ($this->core->update('sites', $objectID))
			{
				// where to redirect to
				$output['message'] = '<p>Your details have been updated.</p>';
			}
		}

		// get permission groups
		$output['groups'] = $this->permission->get_groups();
		
		// templates
		$this->load->view($this->includes_path.'/header');
		$this->load->view('site',$output);
		$this->load->view($this->includes_path.'/footer');
	}
	
	function setup()
	{
		echo 'tset';
	}
	
	function backup()
	{
		// check permissions for this page
		if ($this->session->userdata('groupID') >= 0)
		{
			redirect('/admin/dashboard');
		}	

		$filename = 'halogy_backup_'.date('Y-m-d_H-i', time());
		
		// Set up our default preferences
		$prefs = array(
							'tables'		=> $this->db->list_tables(),
							'ignore'		=> array('ha_ci_sessions', 'ha_captcha', 'ha_permissions', 'ha_zipcodes'),
							'filename'		=> $filename.'.sql',
							'format'		=> 'gzip', // gzip, zip, txt
							'add_drop'		=> FALSE,
							'add_insert'	=> TRUE,
							'newline'		=> "\n"
						);

		// Is the encoder supported?  If not, we'll either issue an
		// error or use plain text depending on the debug settings
		if (($prefs['format'] == 'gzip' AND ! @function_exists('gzencode'))
		 OR ($prefs['format'] == 'zip'  AND ! @function_exists('gzcompress')))
		{
			if ($this->db->db_debug)
			{
				return $this->db->display_error('db_unsuported_compression');
			}
		
			$prefs['format'] = 'txt';
		}

		// Load the Zip class and output it
		$this->load->library('zip');
		$this->zip->add_data($prefs['filename'], $this->_backup($prefs));
		$backup = $this->zip->get_zip();
				
		// Load the download helper and send the file to your desktop
		$this->load->helper('download');
		force_download($filename.'.zip', $backup); 		
	}

	function _backup($params = array())
	{
		if (count($params) == 0)
		{
			return FALSE;
		}

		// Extract the prefs for simplicity
		extract($params);
	
		// Build the output
		$output = '';
		foreach ((array)$tables as $table)
		{
			// Is the table in the "ignore" list?
			if (in_array($table, (array)$ignore, TRUE))
			{
				continue;
			}

			// Get the table schema
			$query = $this->db->query("SHOW CREATE TABLE `".$this->db->database.'`.'.$table);
			
			// No result means the table name was invalid
			if ($query === FALSE)
			{
				continue;
			}
			
			// Write out the table schema
			$output .= '#'.$newline.'# TABLE STRUCTURE FOR: '.$table.$newline.'#'.$newline.$newline;

 			if ($add_drop == TRUE)
 			{
				$output .= 'DROP TABLE IF EXISTS '.$table.';'.$newline.$newline;
			}
			
			$i = 0;
			$result = $query->result_array();
			foreach ($result[0] as $val)
			{
				if ($i++ % 2)
				{ 					
					$output .= $val.';'.$newline.$newline;
				}
			}
			
			// If inserts are not needed we're done...
			if ($add_insert == FALSE)
			{
				continue;
			}

			// Grab all the data from the current table
			$query = $this->db->query("SELECT * FROM $table WHERE siteID = ".$this->siteID);
			
			if ($query->num_rows() == 0)
			{
				continue;
			}
		
			// Fetch the field names and determine if the field is an
			// integer type.  We use this info to decide whether to
			// surround the data with quotes or not
			
			$i = 0;
			$field_str = '';
			$is_int = array();
			while ($field = mysql_fetch_field($query->result_id))
			{
				// Most versions of MySQL store timestamp as a string
				$is_int[$i] = (in_array(
										strtolower(mysql_field_type($query->result_id, $i)),
										array('tinyint', 'smallint', 'mediumint', 'int', 'bigint'), //, 'timestamp'), 
										TRUE)
										) ? TRUE : FALSE;
										
				// Create a string of field names
				$field_str .= '`'.$field->name.'`, ';
				$i++;
			}
			
			// Trim off the end comma
			$field_str = preg_replace( "/, $/" , "" , $field_str);
			
			
			// Build the insert string
			foreach ($query->result_array() as $row)
			{
				$val_str = '';
			
				$i = 0;
				foreach ($row as $v)
				{
					// Is the value NULL?
					if ($v === NULL)
					{
						$val_str .= 'NULL';
					}
					else
					{
						// Escape the data if it's not an integer
						if ($is_int[$i] == FALSE)
						{
							$val_str .= $this->db->escape($v);
						}
						else
						{
							$val_str .= $v;
						}					
					}					
					
					// Append a comma
					$val_str .= ', ';
					$i++;
				}
				
				// Remove the comma at the end of the string
				$val_str = preg_replace( "/, $/" , "" , $val_str);
								
				// Build the INSERT string
				$output .= 'INSERT INTO '.$table.' ('.$field_str.') VALUES ('.$val_str.');'.$newline;
			}
			
			$output .= $newline.$newline;
		}

		return $output;
	}

}