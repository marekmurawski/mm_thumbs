<?php

require_once "slirconfigdefaults.class.php";

class SLIRConfig extends SLIRConfigDefaults
{
	public static $errorImages	= FALSE;
	public static $documentRoot	= "D:/xampp/wolfcms";
	public static $SLIRDir          = "/thmm";
	public static $cacheDirName	= "/thmm/cache";
	public static $cacheDir         = "D:/xampp/wolfcms/thmm/cache";
    
	public static function init()
	{
		// This must be the last line of this function
		parent::init();
	}
}

SLIRConfig::init();
