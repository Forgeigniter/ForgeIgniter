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

class MX_Lang extends CI_Lang
{
    public function load($langfile, $lang = '', $return = false, $add_suffix = true, $alt_path = '', $_module = '')
    {
        if (is_array($langfile)) {
            foreach ($langfile as $_lang) {
                $this->load($_lang);
            }
            return $this->language;
        }

        $deft_lang = CI::$APP->config->item('language');
        $idiom = ($lang === '') ? $deft_lang : $lang;

        if (in_array($langfile.'_lang'.EXT, $this->is_loaded, true)) {
            return $this->language;
        }

        $_module or $_module = CI::$APP->router->fetch_module();
        [$path, $_langfile] = Modules::find($langfile.'_lang', $_module, 'language/'.$idiom.'/');

        if ($path === false) {
            if ($lang = parent::load($langfile, $lang, $return, $add_suffix, $alt_path)) {
                return $lang;
            }
        } else {
            if ($lang = Modules::load_file($_langfile, $path, 'lang')) {
                if ($return) {
                    return $lang;
                }
                $this->language = array_merge($this->language, $lang);
                $this->is_loaded[] = $langfile.'_lang'.EXT;
                unset($lang);
            }
        }

        return $this->language;
    }
}
