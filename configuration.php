<?php

/**
 * PbcMagmi Script Configuration
 */
$config = array(
  'script_verbose' => true,
  'days_to_keep_log_files' => 2,
  'testing_mode' => true,
  'override_php_default_limits' => false,
  'generate_random_values' => true,
  'number_to_generate' => 1234,
  'memory_limit' => '1024M',
  'max_execution_time' => 3600,
  'display_errors' => 1,
);


/**
 * Script required Constants
 */
define("WEB_ROOT_DIRECTORY","/var/www/html/");
define("MAGENTO_BASE_URL", WEB_ROOT_DIRECTORY."magentostudy/");
define("MAGMI_BASE_URL", MAGENTO_BASE_URL."magimprt/");
define("INPUTS_FOLDER", __DIR__ . '/files_logs/inputs_from_ftp/');
define("OUTPUTS_FOLDER", __DIR__ . '/files_logs/csv_outputs/');
define("CURRENT_DATE", time());


if ($config['override_php_default_limits']) {
  ini_set('memory_limit', '1024M');
  ini_set('max_execution_time', 3600);
}

if ($config['testing_mode']) {
  ini_set('memory_limit', '1024M');
  ini_set('max_execution_time', 3600);
  ini_set('display_errors', 1);
  error_reporting(E_ALL);
}
