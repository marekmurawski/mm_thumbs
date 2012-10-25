<?php

/* Security measure */
if (!defined('IN_CMS')) { exit(); }

class MmThumbsController extends PluginController {

    public function __construct() {
        $this->setLayout('backend');
        $this->assignToLayout('sidebar', new View('../../plugins/mm_thumbs/views/sidebar'));
    }

    public function index() {
        $this->settings();
    }

    function settings() {
        $this->display('mm_thumbs/views/settings', Plugin::getAllSettings('mmthumbs'));
    }
}