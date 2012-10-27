<?php

require_once 'slirconfigdefaults.class.php';

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
	public static $documentRoot	= NULL;

	/**
	 * Path to SLIR (no trailing slash)
	 * 
	 * @since 2.0
	 * @var string
	 */
	public static $SLIRDir	= '/thmm' ;

	/**
	 * Name of directory to store cached files in (no trailing slash)
	 * 
	 * @since 2.0
	 * @var string
	 */
	public static $cacheDirName	= '/thmm/cache';

	/**
	 * Absolute path to cache directory. This directory must be world-readable,
	 * writable by the web server, and must end with SLIR_CACHE_DIR_NAME (no
	 * trailing slash). Ideally, this should be located outside of the web tree.
	 * 
	 * @var string
	 */
	public static $cacheDir	= NULL;
	public static $defaultCropper	= SLIR::CROP_CLASS_CENTERED;
    
	public static function init()
	{
		// This must be the last line of this function
		parent::init();
	}
}

SLIRConfig::init();