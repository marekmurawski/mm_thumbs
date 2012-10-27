<?php
/* Security measure */
if (!defined('IN_CMS')) {
  exit();
}

//ini_set( 'display_errors', 1 );
error_reporting( -1 );
set_error_handler( array( 'Error', 'captureNormal' ) );
set_exception_handler( array( 'Error', 'captureException' ) );
register_shutdown_function( array( 'Error', 'captureShutdown' ) );

class Error
{
    private static $errors = array();
    
    public static function captureNormal( $number, $message, $file, $line )
    { self::$errors[] = '<tr><td>MESSAGE:</td><td>' . $message .'</td></tr>'; }
    
    public static function captureException( $exception )
    {
        echo '<pre>';
        print_r( $exception );
        echo '</pre>';
    }
    
    public static function captureShutdown( )
    {
        $error = error_get_last( );
        if( $error || count(self::$errors)>0 ) {            
          self::$errors[] = '<tr><td>MESSAGE:</td><td>' . $error['message'] .'</td></tr>';          
          $message = __('Errors while activating plugin:') .'<table>'. implode(PHP_EOL, self::$errors) . '</table>';
          Flash::set('error',$message); 
          echo $message;          
        } else { 
          Flash::set('success',__('Successfully activated mmThumbs plugin')); 
        }
        
    }
}

function copyr($source, $dest) {
    if (is_file($source)) {
      //chmod($dest, 0777);
      return copy($source, $dest);
    }
    if (!is_dir($dest)) {
      mkdir($dest);
    }
    chmod($dest, 0777);

    $dir = dir($source);
    while (false !== $entry = $dir->read()) {
      if ($entry == '.' || $entry == '..') {
        continue;
      }

      if ($dest !== "$source/$entry") {
        copyr("$source/$entry", "$dest/$entry");
      }
    }
    $dir->close();
    return true;
}

$srcDir = PLUGINS_ROOT . '/mm_thumbs/lib/thmm';
$destDir = CMS_ROOT . '/thmm';

copyr($srcDir, $destDir);

$data = '
RewriteEngine On
RewriteBase '.URI_PUBLIC.'thmm/
RewriteRule . index.php [L]

# Prevent viewing of the error log file in its default location
<Files slir-error-log>
Order Deny,Allow
Deny from All
</Files>
';


file_put_contents(CMS_ROOT . '/thmm/.htaccess', $data);


$data = '<?php

require_once \'slirconfigdefaults.class.php\';

class SLIRConfig extends SLIRConfigDefaults
{
	/**
	 * Whether SLIR should generate and output images from error messages
	 * 
	 * @since 2.0
	 * @var boolean
	 */
	public static $errorImages	= TRUE;

	/**
	 * Absolute path to the web root (location of files when visiting
	 * http://domainname.com/) (no trailing slash)
	 * 
	 * @since 2.0
	 * @var string
	 */
	public static $documentRoot	= "'.CMS_ROOT.'";

	/**
	 * Path to SLIR (no trailing slash)
	 * 
	 * @since 2.0
	 * @var string
	 */
	//public static $SLIRDir	= \'/thmm\' ;

	/**
	 * Name of directory to store cached files in (no trailing slash)
	 * 
	 * @since 2.0
	 * @var string
	 */
	//public static $cacheDirName	= \'/thmm/cache\';

	/**
	 * Absolute path to cache directory. This directory must be world-readable,
	 * writable by the web server, and must end with SLIR_CACHE_DIR_NAME (no
	 * trailing slash). Ideally, this should be located outside of the web tree.
	 * 
	 * @var string
	 */
	public static $cacheDir	= "'.CMS_ROOT.'/thmm/cache";
    


	public static $defaultCropper	= SLIR::CROP_CLASS_CENTERED;
    
	public static function init()
	{
		// This must be the last line of this function
		parent::init();
	}
}

SLIRConfig::init();
';


file_put_contents(CMS_ROOT . '/thmm/slirconfig.class.php', $data);

exit();