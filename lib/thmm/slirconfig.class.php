<?php

require_once "slirconfigdefaults.class.php";

class SLIRConfig extends SLIRConfigDefaults
{
	public static $errorImages	= FALSE;
	public static $documentRoot	= "/home2/murekpro/public_html/marekmurawski.pl";
	public static $SLIRDir	= "/thmm";
	public static $cacheDirName	= "/thmm/cache";
	public static $cacheDir	= "/home2/murekpro/public_html/marekmurawski.pl/thmm/cache";
    
	public static function init()
	{
		// This must be the last line of this function
		parent::init();
	}
}

SLIRConfig::init();
