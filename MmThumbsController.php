<?php

/* Security measure */
if ( !defined( 'IN_CMS' ) ) {
    exit();
}


class MmThumbsController extends PluginController {

    public function __construct() {
        $this->setLayout( 'backend' );
        $this->assignToLayout( 'sidebar', new View( '../../plugins/mm_thumbs/views/sidebar' ) );

    }


    public function index() {
        $this->settings();

    }


    function settings() {
        $requestDir = CMS_ROOT . DS . 'thmm' . DS . 'cache' . DS . 'request' . DS;
        $renderDir  = CMS_ROOT . DS . 'thmm' . DS . 'cache' . DS . 'rendered' . DS;

        if ( $dir = opendir( $requestDir ) ) {
            $i    = 0;
            while ( false !== ($file = readdir( $dir )) ) {
                if ( !in_array( $file, array( '.', '..' ) ) and !is_dir( $file ) )
                    $i++;
            }
            $requestCount = $i;
        }

        if ( $dir = opendir( $renderDir ) ) {
            $i    = 0;
            while ( false !== ($file = readdir( $dir )) ) {
                if ( !in_array( $file, array( '.', '..' ) ) and !is_dir( $file ) )
                    $i++;
            }
            $renderCount = $i;
        }
        $this->display( 'mm_thumbs/views/settings', array(
                    'settings'     => Plugin::getAllSettings( 'mmthumbs' ),
                    'renderCount'  => $renderCount,
                    'requestCount' => $requestCount,
        ) );

    }


}