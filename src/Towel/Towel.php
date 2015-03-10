<?php

namespace Towel;

class Towel
{
  /**
   * Gets Available applications
   *
   * return Array : Applications names.
   */
  static public function getApps() {
    static $applications = array();

    if (!empty($applications)) {
      return $applications;
    }

    $appsDir = APP_ROOT_DIR . '/Application';
    $content = scandir($appsDir);

    foreach ($content as $item) {
      if (is_dir($appsDir . '/' . $item) && $item != '.' && $item != '..') {
        $application = array();
        $application['name'] = $item;
        $application['path'] = $appsDir . '/' . $item;
        $applications[$item] = $application;
      }
    }

    $application = array();
    $application['name'] = basename(APP_FW_DIR);
    $application['path'] = APP_FW_DIR;
    $applications['Towel'] = $application;

    return $applications;
  }
}