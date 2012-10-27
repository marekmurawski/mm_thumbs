<?php

if (!defined('IN_CMS')) {
  exit();
}

Plugin::setInfos(array(
                        'id' => 'mm_thumbs',
                        'title' => 'mmThumbs',
                        'description' => __('Generates cached resized/cropped pictures.'),
                        'version' => '0.0.2',
                        'license' => 'GPL',
                        'author' => 'Marek Murawski',
                        'website' => 'http://marekmurawski.pl/',
                        'require_wolf_version' => '0.7.3'
));
/**
 * Set to false if you don't want image thumbnails in file manager
 */

$integrate_with_file_manager = true;

if ($integrate_with_file_manager) {
  Observer::observe('dispatch_route_found', 'include_file_manager_extension');

  function include_file_manager_extension($uri) {
    if (strpos($uri, 'plugin/file_manager') !== false)
      Plugin::addJavascript('mm_thumbs', 'js/file_manager_thumbs.js');
  }

}
Plugin::addController('mm_thumbs', __('Mmthumbs'), 'admin_edit', false);