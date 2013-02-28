<?php

if ( !defined( 'IN_CMS' ) ) {
    exit();
}

if ( !defined( 'DEFAULT_THMM' ) ) {
    define( 'DEFAULT_THMM', 'w200-h200-c1:1' );
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
    public static function listFiles( $path = 'public', $args = array( ) ) {

        $path      = trim( $path, '/' );
        $core_path = CMS_ROOT . DS . $path;
        $files     = self::getFiles( $path );


        $thmm = array_key_exists( 'thm', $args ) ? $args['thm'] : 'w100';
        echo '<ul>';
        foreach ( $files as $file ) {
            $pathinfo = pathinfo( $file );

            echo '<li><a class="mm_popup" href="' . URL_PUBLIC . $path . DS . $file . '" rel="' . URL_PUBLIC . $path . DS . $file . '"target="_blank">';
            echo '<img src="' . URL_PUBLIC . 'thmm' . DS . $thmm . DS . $path . DS . $file . '"/>';
            if ( in_array( $pathinfo['extension'], array( 'jpg', 'jpeg' ) ) ) {
                $finfo = self::getImageFileInfo( $core_path . DS . $file );
                //echo '<br/>' . $finfo['original_filename'];
            }
            //else
            //    echo '<br/>' . $pathinfo['filename'];
            echo '</li>';
        }
        echo '</ul>';

    }


    public static function getPath( $file, $args = NULL ) {

        if ( is_array( $args ) ) {
            $thmm = (array_key_exists( 'thmm', $args )) ? $args['thmm'] : DEFAULT_THMM;
        } else {
            $thmm = trim( $args, '/' );
        }

        $file = trim( $file, '/' );
        // Backend?
        //if (defined(CMS_BACKEND)) {
        return(URL_PUBLIC . 'thmm' . DS . $thmm . DS . $file);
        //} else {

    }


    /**
     *
     * @param type $file
     * @param type $chunkSize
     * @return type
     * @throws RuntimeException
     */
    public static function xmp_read_block( $file, $chunkSize = 8192, $distanceLimit = 262144 ) {
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
        $distance = 0;

        while ( ($chunk = fread( $file_pointer, $chunkSize )) !== FALSE ) {

            if ( $chunk === "" ) {
                break;
            }
            $distance += $chunkSize;
            if ( $distance > $distanceLimit )
                break;

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


        if ( $try_exif && ($exif_data = exif_read_data( $file, NULL, true, false )) ) {
            if ( isset( $exif_data['IFD0'] ) ) {
                $ifd0                = $exif_data['IFD0'];
                $data['title']       = (isset( $ifd0['Title'] )) ? $ifd0['Title'] : null;
                $data['description'] = (isset( $ifd0['ImageDescription'] )) ? $ifd0['ImageDescription'] : null;

                $data['copyright'] = (isset( $ifd0['Artist'] )) ? $ifd0['Artist'] : null;
                $data['copyright'] .= (isset( $ifd0['Copyright'] )) ? ' - ' . $ifd0['Copyright'] : null;

                $data['tags']              = (isset( $ifd0['Keywords'] )) ? $ifd0['Keywords'] : null;
            }
            $data['original_filename'] = pathinfo( $file, PATHINFO_FILENAME );
            $data['width']             = (isset( $exif_data['COMPUTED']['Width'] )) ? $exif_data['COMPUTED']['Width'] : null;
            $data['height']            = (isset( $exif_data['COMPUTED']['Height'] )) ? $exif_data['COMPUTED']['Height'] : null;
        }

        if ( $try_xmp && ($xmp_data = self::xmp_read_block( $file )) ) {

            $xmp_data = str_replace( ':', '_', $xmp_data );
            libxml_use_internal_errors( false );
            $sxe      = new SimpleXMLElement( $xmp_data );
            if ( $sxe !== false ) {
                $tags         = $sxe->xpath( '//dc_subject/rdf_Bag' );
                if ( !empty( $tags ) )
                    $data['tags'] = (array) $tags[0]->rdf_li;
                    $data['tags'] = implode(', ', $data['tags']);

                $description         = $sxe->xpath( '//dc_description/rdf_Alt' );
                if ( !empty( $description ) )
                    $data['description'] = (string) $description[0]->rdf_li;

                $title         = $sxe->xpath( '//dc_title/rdf_Alt' );
                if ( !empty( $title ) )
                    $data['title'] = (string) $title[0]->rdf_li;

                $copyright         = $sxe->xpath( '//dc_rights/rdf_Alt' );
                $creator           = $sxe->xpath( '//dc_creator/rdf_Seq' );
                $data['copyright'] = (string) $copyright[0]->rdf_li;
                if ( !empty( $creator ) )
                    $data['copyright'] .= ' - ' . (string) $creator[0]->rdf_li;
            }
        }
        return $data;

    }


    /**
     * Creator
     *  XMP - dc:creator
     *  XMP - tiff.artist
     *  Exif IFD0 -  Artist
     *
     *  Tags
     *  XMP - dc:subject
     *  IPTC IIM - Keywords
     *  Exif IFD0 Microsoft.XP.Keywords
     *  XMP - MicrosoftPhoto:LastKeywordXMP
     *
     *  Title
     *  XMP - dc:title
     *  Exif 37510 UserComment
     *  Exif 270 - Description
     *  XMP - dc:description
     *  IPTC IIM - Caption
     *  XMP - exif:UserComment
     *
     *  Subject	?
     *  Date taken
     *  Exif 36867 - DateTimeOriginal
     *  IPTC IIM - Date Taken
     *  XMP - xmp:CreateDate
     *  Exif 36868 - Digitization Date/Time
     *  Exif - Original Date/Time
     *  Rating	XMP - xmp:rating
     *  Size	Exif
     *
     *  Date created
     *  XMP - exif:DateTimeOriginal
     *
     *  Date modified	xmp:ModifyDate
     *
     *  Comments
     *  Exif 40092 - XPComment
     *  Exif 37510 - UserComment
     *  XMP - exif:UserComment
     *
     *  Camera maker
     *  Exif 271 - Make
     *  XMP - tiff:Make
     *
     *  Camera model
     *  Exif 272 - Camera Model
     *  XMP - tiff:Model
     *  XMP - xmp:CreatorTool
     *
     *  Copyright
     *  Exif 33432 - Copyright
     *  IPTC IIM - copyright notice
     *  XMP - xmp:dc.rights
     *
     *  f stop	Exif Sub IFD - Lens F-Number
     *  Exposure time	Exif Sub IFD - Exposure Time
     */

    /**
     * getFiles
     *
     * Retrieves an array of all files
     */
    private static function getFiles( $path ) {
        $scandir = scandir( $path );
        foreach ( $scandir as $k => $v ) {
            if ( !preg_match( '/(png|jp?g|gif)$/i', $v ) ) {
                unset( $scandir[$k] );
            }
        }
        return $scandir;

    }


}