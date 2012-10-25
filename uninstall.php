<?php

/*
 * Wolf CMS - Content Management Simplified. <http://www.wolfcms.org>
 * Copyright (C) 2008-2010 Martijn van der Kleijn <martijn.niji@gmail.com>
 *
 * This file is part of Wolf CMS. Wolf CMS is licensed under the GNU GPLv3 license.
 * Please see license.txt for the full license text.
 */

/* Security measure */
if (!defined('IN_CMS')) {
  exit();
}




// ------------ lixlpixel recursive PHP functions -------------
// recursive_remove_directory( directory to delete, empty )
// expects path to directory and optional TRUE / FALSE to empty
// ------------------------------------------------------------
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

// ------------------------------------------------------------
$rmDir = CMS_ROOT . '/thmm';

recursive_remove_directory($rmDir);

exit();