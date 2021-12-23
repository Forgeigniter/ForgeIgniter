<?php
/**
 * Modular Extensions - HMVC
 *
 * Adapted from the CodeIgniter Core Classes
 * @link	http://codeigniter.com
 *
 * Description:
 * This library extends the CodeIgniter CI_Controller class and creates an application
 * object allowing use of the HMVC design pattern.
 *
 * @license /licenses/mx.txt
 * @copyright	Copyright (c) 2015 Wiredesignz
 * @version 	5.6.2 - Unofficial
 *
 **/
defined('BASEPATH') or exit('No direct script access allowed');

/** load the CI class for Modular Extensions **/
require __DIR__ .'/Base.php';

class MX_Controller
{
    public $autoload = array();

    public function __construct()
    {
        $class = str_replace(CI::$APP->config->item('controller_suffix'), '', get_class($this));
        log_message('debug', $class. ' MX_Controller Initialized');
        Modules::$registry[strtolower($class)] = $this;

        /* copy a loader instance and initialize */
        $this->load = clone load_class('Loader');
        $this->load->initialize($this);

        /* autoload module items */
        $this->load->_autoloader($this->autoload);
    }

    public function __get($class)
    {
        return CI::$APP->$class;
    }
}
