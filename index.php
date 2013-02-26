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
    public static function xmp_read_block( $file, $chunkSize ) {
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


    /*
      $xmp_parsed = ee_extract_exif_from_pscs_xmp ("CRW_0016b_preview.jpg",1);

      function ee_extract_exif_from_pscs_xmp ($filename,$printout=0) {

      // very straightforward one-purpose utility function which
      // reads image data and gets some EXIF data (what I needed) out from its XMP tags (by Adobe Photoshop CS)
      // returns an array with values
      // code by Pekka Saarinen http://photography-on-the.net

      ob_start();
      readfile($filename);
      $source = ob_get_contents();
      ob_end_clean();

      $xmpdata_start = strpos($source,"<x:xmpmeta");
      $xmpdata_end = strpos($source,"</x:xmpmeta>");
      $xmplenght = $xmpdata_end-$xmpdata_start;
      $xmpdata = substr($source,$xmpdata_start,$xmplenght+12);

      $xmp_parsed = array();

      $regexps = array(
      array("name" => "DC creator", "regexp" => "/<dc:creator>\s*<rdf:Seq>\s*<rdf:li>.+<\/rdf:li>\s*<\/rdf:Seq>\s*<\/dc:creator>/"),
      array("name" => "TIFF camera model", "regexp" => "/<tiff:Model>.+<\/tiff:Model>/"),
      array("name" => "TIFF maker", "regexp" => "/<tiff:Make>.+<\/tiff:Make>/"),
      array("name" => "EXIF exposure time", "regexp" => "/<exif:ExposureTime>.+<\/exif:ExposureTime>/"),
      array("name" => "EXIF f number", "regexp" => "/<exif:FNumber>.+<\/exif:FNumber>/"),
      array("name" => "EXIF aperture value", "regexp" => "/<exif:ApertureValue>.+<\/exif:ApertureValue>/"),
      array("name" => "EXIF exposure program", "regexp" => "/<exif:ExposureProgram>.+<\/exif:ExposureProgram>/"),
      array("name" => "EXIF iso speed ratings", "regexp" => "/<exif:ISOSpeedRatings>\s*<rdf:Seq>\s*<rdf:li>.+<\/rdf:li>\s*<\/rdf:Seq>\s*<\/exif:ISOSpeedRatings>/"),
      array("name" => "EXIF datetime original", "regexp" => "/<exif:DateTimeOriginal>.+<\/exif:DateTimeOriginal>/"),
      array("name" => "EXIF exposure bias value", "regexp" => "/<exif:ExposureBiasValue>.+<\/exif:ExposureBiasValue>/"),
      array("name" => "EXIF metering mode", "regexp" => "/<exif:MeteringMode>.+<\/exif:MeteringMode>/"),
      array("name" => "EXIF focal lenght", "regexp" => "/<exif:FocalLength>.+<\/exif:FocalLength>/"),
      array("name" => "AUX lens", "regexp" => "/<aux:Lens>.+<\/aux:Lens>/")
      );

      foreach ($regexps as $key => $k) {
      $name         = $k["name"];
      $regexp     = $k["regexp"];
      unset($r);
      preg_match ($regexp, $xmpdata, $r);
      $xmp_item = "";
      $xmp_item = @$r[0];
      array_push($xmp_parsed,array("item" => $name, "value" => $xmp_item));
      }

      if ($printout == 1) {
      foreach ($xmp_parsed as $key => $k) {
      $item         = $k["item"];
      $value         = $k["value"];
      print "<br><b>" . $item . ":</b> " . $value;
      }
      }

      return ($xmp_parsed);

      }
     */

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
        return $data;

    }


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