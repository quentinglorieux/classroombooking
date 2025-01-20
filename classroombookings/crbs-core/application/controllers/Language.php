<?php
defined('BASEPATH') OR exit('No direct script access allowed');

class Language extends MY_Controller {
    
    public function switch($lang = 'english') {
        // Validate language
        $lang = in_array($lang, ['english', 'french']) ? $lang : 'english';

        // Set language in the session
        $this->session->set_userdata('site_language', $lang);
    
        redirect($_SERVER['HTTP_REFERER'] ?? base_url());
    }
}