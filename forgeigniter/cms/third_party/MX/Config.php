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

class MX_Config extends CI_Config
{
    public function load($file = '', $use_sections = false, $fail_gracefully = false, $_module = '')
    {
        if (in_array($file, $this->is_loaded, true)) {
            return $this->item($file);
        }

        $_module or $_module = CI::$APP->router->fetch_module();
        [$path, $file] = Modules::find($file, $_module, 'config/');

        if ($path === false) {
            parent::load($file, $use_sections, $fail_gracefully);
            return $this->item($file);
        }

        if ($config = Modules::load_file($file, $path, 'config')) {
            /* reference to the config array */
            $current_config =& $this->config;

            if ($use_sections === true) {
                if (isset($current_config[$file])) {
                    $current_config[$file] = array_merge($current_config[$file], $config);
                } else {
                    $current_config[$file] = $config;
                }
            } else {
                $current_config = array_merge($current_config, $config);
            }

            $this->is_loaded[] = $file;
            unset($config);
            return $this->item($file);
        }
    }
}
