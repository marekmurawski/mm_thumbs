<?php

/* Security measure */
if ( !defined( 'IN_CMS' ) ) {
    exit();
}

//ini_set( 'display_errors', 1 );
error_reporting( -1 );
set_error_handler( array( 'Error', 'captureNormal' ) );
set_exception_handler( array( 'Error', 'captureException' ) );
register_shutdown_function( array( 'Error', 'captureShutdown' ) );


class Error {

    public static $errors = array( );

    public static function captureNormal( $number, $message, $file, $line ) {
        self::$errors[] = '<tr><td>MESSAGE:</td><td>' . $message . '</td></tr>';

    }


    public static function captureException( $exception ) {
        echo '<pre>' . print_r( $exception, true ) . '</pre>';
        self::$errors[] = print_r( $exception, true );

    }


    public static function captureShutdown() {
        $error = error_get_last();
        if ( $error || count( self::$errors ) > 0 ) {
            self::$errors[] = '<tr><td>MESSAGE:</td><td>' . $error['message'] . '</td></tr>';
            $message = __( 'Errors while activating plugin:' ) . '<table>' . implode( PHP_EOL, self::$errors ) . '</table>';
            Flash::set( 'error', $message );
            echo $message;
        } else {
            Flash::set( 'success', __( 'Successfully activated mmThumbs plugin' ) );
        }

    }


}


function copyr( $source, $dest ) {
    if ( is_file( $source ) ) {
        //chmod($dest, 0777);
        return copy( $source, $dest );
    }
    if ( !is_dir( $dest ) ) {
        mkdir( $dest );
    }
    chmod( $dest, 0777 );

    $dir   = dir( $source );
    while ( false !== $entry = $dir->read() ) {
        if ( $entry == '.' || $entry == '..' ) {
            continue;
        }

        if ( $dest !== "$source/$entry" ) {
            copyr( "$source/$entry", "$dest/$entry" );
        }
    }
    $dir->close();
    return true;

}


$conf_errorImages = 'FALSE';

$conf_documentRoot = '"' . CMS_ROOT . '"';
$conf_documentRoot = str_replace( '\\', '/', $conf_documentRoot );

$conf_slirDir = '"' . DS . 'thmm' . '"';
$conf_slirDir = str_replace( '\\', '/', $conf_slirDir );

$conf_cacheDirName = '"' . DS . 'thmm' . DS . 'cache' . '"';
$conf_cacheDirName = str_replace( '\\', '/', $conf_cacheDirName );

$conf_cacheDir = '"' . CMS_ROOT . DS . 'thmm' . DS . 'cache' . '"';
$conf_cacheDir = str_replace( '\\', '/', $conf_cacheDir );



$data       = '<?php

require_once "slirconfigdefaults.class.php";

class SLIRConfig extends SLIRConfigDefaults
{
	public static $errorImages	= ' . $conf_errorImages . ';
	public static $documentRoot	= ' . $conf_documentRoot . ';
	public static $SLIRDir          = ' . $conf_slirDir . ';
	public static $cacheDirName	= ' . $conf_cacheDirName . ';
	public static $cacheDir         = ' . $conf_cacheDir . ';
    
	public static function init()
	{
		// This must be the last line of this function
		parent::init();
	}
}

SLIRConfig::init();
';
$configFile = PLUGINS_ROOT . '/mm_thumbs/lib/thmm/slirconfig.class.php';
chmod( $configFile, 0777 );
file_put_contents( $configFile, $data );





$srcDir  = PLUGINS_ROOT . '/mm_thumbs/lib/thmm';
$destDir = CMS_ROOT . '/thmm';

copyr( $srcDir, $destDir );

$data = '
RewriteEngine On
RewriteBase ' . URI_PUBLIC . 'thmm/
RewriteRule . index.php [L]

# Prevent viewing of the error log file in its default location
<Files slir-error-log>
Order Deny,Allow
Deny from All
</Files>
';


file_put_contents( CMS_ROOT . '/thmm/.htaccess', $data );


exit();