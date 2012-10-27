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
        echo '<pre>' . print_r( $exception, true) . '</pre>';
        self::$errors[] = print_r( $exception, true);
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


$data = '<?php

require_once "slirconfigdefaults.class.php";

class SLIRConfig extends SLIRConfigDefaults
{
	public static $errorImages	= FALSE;
	public static $documentRoot	= "'.CMS_ROOT.'";
	public static $SLIRDir	= "/thmm";
	public static $cacheDirName	= "/thmm/cache";
	public static $cacheDir	= "'.CMS_ROOT.'/thmm/cache";
    
	public static function init()
	{
		// This must be the last line of this function
		parent::init();
	}
}

SLIRConfig::init();
';
$configFile = PLUGINS_ROOT . '/mm_thumbs/lib/thmm/slirconfig.class.php';
chmod($configFile, 0777);
file_put_contents($configFile, $data);





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


exit();