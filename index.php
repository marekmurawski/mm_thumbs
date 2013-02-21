<?php

if ( !defined( 'IN_CMS' ) ) {
    exit();
}

Plugin::setInfos( array(
            'id'                   => 'mm_thumbs',
            'title'                => 'mmThumbs',
            'description'          => __( 'Generates cached resized/cropped pictures.' ),
            'version'              => '0.0.2',
            'license'              => 'GPL',
            'author'               => 'Marek Murawski',
            'website'              => 'http://marekmurawski.pl/',
            'require_wolf_version' => '0.7.3'
) );
/**
 * Set to false if you don't want image thumbnails in file manager
 */
$integrate_with_file_manager = true;

if ( $integrate_with_file_manager ) {
    Observer::observe( 'dispatch_route_found', 'include_file_manager_extension' );

    function include_file_manager_extension( $uri ) {
        if ( strpos( $uri, 'plugin/file_manager' ) !== false )
            Plugin::addJavascript( 'mm_thumbs', 'js/file_manager_thumbs.js' );

    }


}
Plugin::addController( 'mm_thumbs', __( 'Mmthumbs' ), 'admin_edit', false );


class mmThumbs {

    /**
     * Display list of files in $path (relative to CMS_ROOT)
     * with thumbnails as links to original files
     *
     * @param string $path
     * @param Array $args
     */
    public static function listFiles( $path = '/public', $args = array( ) ) {

        $path      = trim( $path, '/' );
        $core_path = CMS_ROOT . DS . $path;
        $files     = self::getFiles( $path );

        //echo '<pre>' . print_r($files, true) . '</pre>';
        $thmm = array_key_exists( 'thmm', $args ) ? $args['thmm'] : 'w100';
        echo '<ul>';
        foreach ( $files as $file ) {
            echo '<li><a class="mm_popup" href="' . URL_PUBLIC . $path . DS . $file . '" rel="' . URL_PUBLIC . $path . DS . $file . '"target="_blank">';
            echo '<img src="' . URL_PUBLIC . 'thmm' . DS . $thmm . DS . URL_PUBLIC . $path . DS . $file . '"/>';
            $finfo = self::getImageFileInfo( $core_path . DS . $file );
            echo '<pre>' . print_r($finfo['width'] , true ) . '</pre>';
            echo '</li>';
        }
        echo '</ul>';

    }

    /**
     * getImageFileInfo
     *
     * Retrieves an array of file information
     *      title
     *      description
     *      copyright
     *      tags
     *      height
     *      width
     *
     * @param string $file
     * @return Array
     */
    public static function getImageFileInfo( $file ) {
        $data     = array(
                    'title'       => NULL,
                    'description' => NULL,
                    'copyright'   => NULL,
                    'tags'        => NULL,
                    'height'      => NULL,
                    'width'       => NULL,
        );
        //if ( $exif_data = exif_read_data( $file, NULL, true ) ) {
        if ( $sizeinfo = getimagesize( $file, $exif ) ) {
            //echo '<pre>' . print_r( $sizeinfo, true ) . '</pre>';
            if ( isset( $exif['APP13'] ) ) {
                $iptc                = iptcparse( $exif['APP13'] );
                //echo '<pre>' . print_r( $iptc, true ) . '</pre>';
                $data['title']       = (isset( $iptc['2#105'][0] )) ? $iptc['2#105'][0] : null;
                $data['description'] = (isset( $iptc['2#120'][0] )) ? $iptc['2#120'][0] : null;
                $data['copyright']   = (isset( $iptc['2#080'][0] )) ? $iptc['2#080'][0] : null;
                $data['copyright']   .= (isset( $iptc['2#116'][0] )) ? ' - ' . $iptc['2#116'][0] : null;
                $data['tags']   = (isset( $iptc['2#025'] )) ? $iptc['2#025'] : null;
            }
            $data['original_filename'] = pathinfo($file,PATHINFO_FILENAME);
            $data['height'] = (isset( $sizeinfo[1] )) ? $sizeinfo[1] : null;
            $data['width']  = (isset( $sizeinfo[0] )) ? $sizeinfo[0] : null;
        }
        return $data;

    }


    /**
     * getFiles
     *
     * Retrieves an array of all files
     */
    private static function getFiles( $path ) {
        $scandir = scandir( $path );
        //echo '<pre>' . print_r($scandir, true) . '</pre>';
        foreach ( $scandir as $k => $v ) {
            if ( !preg_match( '/(png|jp?g|gif)$/i', $v ) ) {
                unset( $scandir[$k] );
            }
        }
        return $scandir;

    }


}