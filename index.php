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
            echo '<img src="' . URL_PUBLIC . 'thmm' . DS . $thmm . DS . $path . DS . $file . '"/>';
            $finfo = self::getImageFileInfo( $core_path . DS . $file );
            //echo '<pre>' . print_r( $finfo, true ) . '</pre>';
            echo '</li>';
        }
        echo '</ul>';

    }


    /**
     *
     * @param type $file
     * @param type $chunkSize
     * @return type
     * @throws RuntimeException
     */
    public static function xmp_read_data( $file, $chunkSize ) {
        if ( !is_int( $chunkSize ) ) {
            throw new RuntimeException( 'Expected integer value for argument #2 (chunk_size)' );
        }

        if ( ($file_pointer = fopen( $file, 'r' )) === FALSE ) {
            throw new RuntimeException( 'Could not open file for reading' );
        }

        $startTag = '<x:xmpmeta';
        $endTag   = '</x:xmpmeta>';
        $buffer   = NULL;
        $hasXmp   = FALSE;

        while ( ($chunk = fread( $file_pointer, $chunkSize )) !== FALSE ) {

            if ( $chunk === "" ) {
                break;
            }

            $buffer .= $chunk;
            $startPosition = strpos( $buffer, $startTag );
            $endPosition   = strpos( $buffer, $endTag );

            if ( $startPosition !== FALSE && $endPosition !== FALSE ) {
                $buffer = substr( $buffer, $startPosition, $endPosition - $startPosition + 12 );
                $hasXmp = TRUE;
                break;
            } elseif ( $startPosition !== FALSE ) {
                $buffer = substr( $buffer, $startPosition );
                $hasXmp = TRUE;
            } elseif ( strlen( $buffer ) > (strlen( $startTag ) * 2) ) {
                $buffer = substr( $buffer, strlen( $startTag ) );
            }
        }

        fclose( $file_pointer );
        return ($hasXmp) ? $buffer : NULL;

    }


    /**
     * get Hash
     *
     * @param string $filename
     * @return string SHA1 of file or false on file not found
     */
    public static function getHash( $filename ) {
        if ( !file_exists( $filename ) )
            return false;
        return sha1_file( $filename );

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
    public static function getImageFileInfo( $file, $try_xmp = true, $try_exif = true ) {
        $data = array(
                    'title'       => NULL,
                    'description' => NULL,
                    'copyright'   => NULL,
                    'tags'        => NULL,
                    'height'      => NULL,
                    'width'       => NULL,
        );
        ;


//        if ( $try_xmp && ($xmp_data = self::xmp_read_data( $file, 8192 )) ) {
//            $xmp_data = str_replace( ':', '', $xmp_data );
//
//            //echo '<pre>' . print_r( html_encode($xmp_data), true ) . '</pre>';
//            $xmp = simplexml_load_string( $xmp_data );
//
//            print gettype( $xmp->xpath( '/' ) );
//            $title = $xmp->xpath( '/' );
//            echo '<pre>' . print_r( $title, true ) . '</pre>';
//
//
//
//
//            echo '<pre>' . print_r( $xmp, true ) . '</pre>';
//        }


        if ( $try_exif && ($exif_data = exif_read_data( $file, NULL, true, false )) ) {
            //echo '<pre>' . print_r( $exif_data, true ) . '</pre>';
            if ( isset( $exif_data['IFD0'] ) ) {
                $ifd0                = $exif_data['IFD0'];
                //echo '<pre>' . print_r( $ifd0, true ) . '</pre>';
                $data['title']       = (isset( $ifd0['Title'] )) ? $ifd0['Title'] : null;
                $data['description'] = (isset( $ifd0['ImageDescription'] )) ? $ifd0['ImageDescription'] : null;

                $data['copyright'] = (isset( $ifd0['Artist'] )) ? $ifd0['Artist'] : null;
                $data['copyright'] .= (isset( $ifd0['Copyright'] )) ? ' - ' . $ifd0['Copyright'] : null;

                $data['tags']              = (isset( $ifd0['Keywords'] )) ? $ifd0['Keywords'] : null;
//                $data['encoding'] =iconv('UTF-8', 'ASCII//TRANSLIT//IGNORE', $data['copyright']);
            }
            $data['original_filename'] = pathinfo( $file, PATHINFO_FILENAME );
            $data['width']             = (isset( $exif_data['COMPUTED']['Width'] )) ? $exif_data['COMPUTED']['Width'] : null;
            $data['height']            = (isset( $exif_data['COMPUTED']['Height'] )) ? $exif_data['COMPUTED']['Height'] : null;
            //echo '<pre>' . print_r( $data, true ) . '</pre>';
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