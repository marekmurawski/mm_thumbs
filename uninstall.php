<?php

/* Security measure */
if (!defined('IN_CMS')) {
  exit();
}

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

function recursive_remove_directory($directory, $empty = FALSE) {
  if (substr($directory, -1) == '/') {
    $directory = substr($directory, 0, -1);
  }
  if (!file_exists($directory) || !is_dir($directory)) {
    return FALSE;
  } elseif (is_readable($directory)) {
    $handle = opendir($directory);
    while (FALSE !== ($item = readdir($handle))) {
      if ($item != '.' && $item != '..') {
        $path = $directory . '/' . $item;
        if (is_dir($path)) {
          recursive_remove_directory($path);
        } else {
          unlink($path);
        }
      }
    }
    closedir($handle);
    if ($empty == FALSE) {
      if (!rmdir($directory)) {
        return FALSE;
      }
    }
  }
  return TRUE;
}

$rmDir = CMS_ROOT . '/thmm';

recursive_remove_directory($rmDir);

exit();