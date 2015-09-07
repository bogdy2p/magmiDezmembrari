<?php

/**
 * PbcMagmi Script Configuration
 */
$config = array(
  'script_verbose' => false,
  'days_to_keep_log_files' => 2,
  'testing_mode' => true,
  'override_php_default_limits' => false,
  'generate_random_values' => false,
  'number_to_generate' => 0,
  'memory_limit' => '1024M',
  'max_execution_time' => 3600,
  'display_errors' => 1,
  'outputfile_filename_ext' => "outputfile.csv",
);

define("FTP_SERVER","timedudeapi.cust21.reea.net");
define("FTP_USERNAME","devel");
define("FTP_USER_PASS","E8gV1k");
define("FTP_FILEPATH","www/timedudeapi.cust21.reea.net");

/**
 * Script required Constants
 */
define("WEB_ROOT_DIRECTORY", "/var/www/html/");
define("MAGENTO_BASE_URL", WEB_ROOT_DIRECTORY . "magentostudy/");
define("MAGMI_BASE_URL", MAGENTO_BASE_URL . "magimprt/");
define("MAGENTO_VAR_IMPORT_FOLDER", MAGENTO_BASE_URL . "var/import/");
define("INPUTS_FOLDER", __DIR__ . '/logs/inputs_from_ftp/');
define("OUTPUTS_FOLDER", __DIR__ . '/logs/csv_outputs/');
define("PIESE_MASINI_CSVS", __DIR__ . '/logs/piese_masini/');
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
