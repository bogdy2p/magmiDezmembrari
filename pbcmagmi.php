<?php

require_once('parsecsv.lib.php');
require_once('configuration.php');

class PbcMagmi {

  /**
   * Setup of the ftp configuration array
   * @var type 
   */
  public $ftp_config = array(
    'server_file' => 'input.csv', //The name of the file on the ftp server.
    'local_file' => INPUTS_FOLDER . 'FtpInput_' . CURRENT_DATE . '.csv',
    'ftp_server' => 'timedudeapi.cust21.reea.net', //The adress of the ftp server
    'ftp_username' => 'devel', // Ftp user's username
    'ftp_user_pass' => 'zwkx6Z', //Ftp user's password
    'ftp_file_path' => 'www/timedudeapi.cust21.reea.net', //The path to the file on the ftp server
  );

  /**
   * Custom column names to be added in the output csv
   * @var type 
   */
  public $columns_to_be_added = array(
    'YEAR',
    'CCM',
    'POWER',
    'HORSEPOWER',
    'ENGCODE'
  );

  /**
   * Column names required in the magmi output csv
   * @var type 
   */
  public $test_default_columns_for_magmi = array(
    'attribute_set',
    'type',
    'store',
    'att_eby_title',
    'att_eby_subtitle',
    'description',
    'manage_stock',
    'use_config_manage_stock',
    'status',
    'visibility',
    'categories',
    'tax_class_id',
    'thumbnail',
    'small_image',
    'image',
    'media_gallery',
    'att_amz_title',
    'decor_type',
    'marca_masina',
    'rulaj_kilometri',
    'tip_combustibil',
  );

  /**
   * Construct function.
   */
  public function __construct($config) {
    $this->config = $config;
    $this->inputFileName = $this->getCsvInputFromFtp($this->ftp_config);
    $this->csv = new parseCSV($this->inputFileName);
    $this->csv->auto($this->inputFileName);
    $this->column_titles = $this->csv->titles;
    $this->csv_data = $this->csv->data;
    $this->output_data = array();
  }

//End of construct Function
  /**
   * Adds the columns to the output file
   * @param type $column_names_to_be_added_array
   * @return \PbcMagmi
   */
  function addColumnsToTitles($column_names_to_be_added_array) {
    foreach ($column_names_to_be_added_array as $column_name) {
      $this->csv->titles[] = $column_name;
    }
    return $this;
  }

  //End of addColumnsToTitles Function
  /**
   * Adds the test default columns to the output file
   * @param type $test_default_columns
   * @return \PbcMagmi
   */
  function addTestDefaultColumnsToTitles($test_default_columns) {
    foreach ($test_default_columns as $column_name) {
      $this->csv->titles[] = $column_name;
    }
    return $this;
  }

  /**
   * Converts the EXPL field of each item into different values and outputs
   * the new data array.
   * @param type $data_array
   * @return \PbcMagmi
   */
  function expandExplanaitionField($data_array) {
    foreach ($data_array as $key => $value) {

      $year = "NULL";
      $capacity = "NULL";
      $power = "NULL";
      $horsepower = "NULL";
      $enginecode = "NULL";


      if (isset($value['EXPL'])) {
        $yearmatch = preg_match("(((19|20)\d{2})|((19|20)\d{2}\.))", $value['EXPL'], $yearmatches);
        if ($yearmatch) {
          $year = substr($yearmatches[0], 0, 4);
        }

        $capacitymatch = preg_match("([0-9]{3,5}CC)", $value['EXPL'], $capacitymatches);
        if ($capacitymatch) {
          $capacity = $capacitymatches[0];
        }

        $powermatch = preg_match("([0-9]{1,4}KW)", $value['EXPL'], $powermatches);
        if ($powermatch) {
          $power = $powermatches[0];
        }

        $horsepowermatch = preg_match("([0-9]{1,4}CP)", $value['EXPL'], $horsepowermatches);
        if ($horsepowermatch) {
          $horsepower = $horsepowermatches[0];
        }

        $enginecodematch = preg_match("([A-Z0-9]{12,25})", $value['EXPL'], $enginecodematches);
        if ($enginecodematch) {
          $enginecode = $enginecodematches[0];
        }
      }

      if ($value['ID'] != "") {

        $value['YEAR'] = $year;
        $value['CAPACITY'] = $capacity;
        $value['POWER'] = $power;
        $value['HORSEPOWER'] = $horsepower;
        $value['ENGCODE'] = $enginecode;

        $output_csv_data['sku'] = $value['ID'];
        $output_csv_data['name'] = $value['DENUMIRE'];
        $output_csv_data['price'] = $value['PRET_LISTA'];
        $output_csv_data['qty'] = $value['STOC_CURENT'];
        $output_csv_data['year'] = $value['YEAR'];
        $output_csv_data['capacity'] = $value['CAPACITY'];
        $output_csv_data['power'] = $value['POWER'];
        $output_csv_data['horsepower'] = $value['HORSEPOWER'];
        $output_csv_data['engcode'] = $value['ENGCODE'];
        $output_csv_data['is_in_stock'] = $value['PRODUS_ACTIV'];

        //THIS WILL BE THE DEFAULT VALUES HARDCODED BECAUSE THEY DONT EXIST

        $output_csv_data['attribute_set'] = 'Bloc Motor';
        $output_csv_data['type'] = 'simple';
        $output_csv_data['store'] = 'admin';
        $output_csv_data['att_eby_title'] = $value['DENUMIRE'];
        $output_csv_data['att_eby_subtitle'] = $value['DENUMIRE'] . ' Subtitle';
        $output_csv_data['description'] = $value['EXPL'];
        $output_csv_data['manage_stock'] = 1;
        $output_csv_data['use_config_manage_stock'] = 1;
        $output_csv_data['status'] = 1;
        //Override Manage Stock And Config Manage Stock
        // If Product Is Not In Hydra Stock
        if (($value['STOC_CURENT'] == 0) || ($value['STOC_CURENT'] == NULL)) {
//          print_r($value['STOC_CURENT']);
          $output_csv_data['manage_stock'] = 0;
          $output_csv_data['use_config_manage_stock'] = 0;
          $output_csv_data['status'] = 3;
        }

        $output_csv_data['visibility'] = 'Catalog, Search';
        $output_csv_data['categories'] = 'Motor + Tansmisie/Piese Motor';
        $output_csv_data['tax_class_id'] = 'None';
        $output_csv_data['thumbnail'] = '';
        $output_csv_data['small_image'] = '';
        $output_csv_data['image'] = '';
        $output_csv_data['media_gallery'] = '';
        $output_csv_data['att_amz_title'] = '';
        $output_csv_data['decor_type'] = $this->randomizeCustomAttributeValues('decor_type');
        $output_csv_data['marca_masina'] = $this->randomizeCustomAttributeValues('marca_masina');
        $output_csv_data['rulaj_kilometri'] = $this->randomizeCustomAttributeValues('rulaj_kilometri');
        $output_csv_data['tip_combustibil'] = $this->randomizeCustomAttributeValues('tip_combustibil');

        $this->output_data[] = $output_csv_data;
      }
    }
    return $this;
  }

  /**
   * Generate pseudo-random data rows & values up to a specified number.
   * @param type $number
   * @return \PbcMagmi
   */
  function addrowstodata($number) {

    $minimum = 700000;
    $maximum = 700000 + $number;

    for ($nextid = $minimum; $nextid < $maximum; $nextid++) {

      $newRow = array(
        'ID' => $nextid,
        'ID_UM_MINIMA' => NULL,
        'COD_INTERN' => NULL,
        'DENUMIRE' => 'DenumireProdus' . $nextid,
        'PRET_LISTA' => rand(1, 1000),
        'PRET_VANZ_MAG' => '',
        'GRAMAJ_UNITATEA_MINIMA' => '',
        'ORDINE_AFISARE' => '',
        'XDISC_PROMO' => '',
        'XTVA' => '',
        'XADAOS' => '',
        'PRODUS_ACTIV' => 1,
        'ACOPERIRE' => '18',
        'COD_BARE' => '',
        'COMENT' => '',
        'EXPL' => '2001,2500CC,55KW,66CP,WVWZZZ1HZ' . $nextid,
        'PRET_LISTA_EURO' => '',
        'PAGINA_CATALOG' => '',
        'LINK_DISK' => '',
        'FIELD_1' => '',
        'FIELD_2' => '',
        'STOC_COMANDAT_CLN' => '',
        'STOC_COMANDAT_FUR' => '',
        'STOC_CURENT' => rand(1, 10),
        'STOC_REZERVAT' => '',
        'STOC_ASIGURAT' => '',
        'PARENT_ID' => '',
        'SOURCE_ID' => '',
      );

      $this->csv->data[] = $newRow;
    }

    return $this;
  }

  /**
   * Fetch the existing product sku's array in Magento
   * @return type
   */
  function getExistingProductSkusInMagento() {
    require_once MAGENTO_BASE_URL . 'app/Mage.php';
    Mage::app();
    $existing_product_skus_in_magento = array();

    foreach (Mage::getModel('catalog/product')->getCollection() as $product) {
      $existing_product_skus_in_magento[] = $product->getSku();
    }
    return $existing_product_skus_in_magento;
  }

  function getExistingProductSkusInParsedCSV() {
    $existing_product_skus_in_parsed_csv = array();
    foreach ($this->csv->data as $csv_row) {
      $existing_product_skus_in_parsed_csv[] = $csv_row['ID'];
    }
    return $existing_product_skus_in_parsed_csv;
  }

  function getDifferenceBetweenCSVandMagentoDB() {

    $db_array_items = $this->getExistingProductSkusInMagento();
    $csv_array_items = $this->getExistingProductSkusInParsedCSV();

    $difference = count($db_array_items) - count($csv_array_items);
    $array_difference = array();

    if ($difference > 0) {
      $array_difference = array_diff($db_array_items, $csv_array_items);
      return $array_difference;
    }
    return null;
  }

  /**
   * Randomizing Custom Attributes 
   * @param type $attribute_name
   * @return string
   */
  function randomizeCustomAttributeValues($attribute_name) {

    $random_value = rand(0, 10);
    $decor_types = array(
      '1000-1400cmc', '1400-1600cmc', '1600-2000cmc', '2000-2600cmc',
    );
    $marci_masina = array(
      'Volkswagen', 'Dacia', 'BMW', 'Honda', 'Renault', 'Trabant'
    );
    $rulaje_kilometri = array(
      '0-20000', '>250000', '20-50000', '50-100000', '100-150000', '150-250000',
    );
    $tip_combustibil = array(
      'Benzina', 'Diesel', 'GPL', 'Electric',
    );
    $all_arrays = array('decor_type', 'marca_masina', 'rulaj_kilometri', 'tip_combustibil');

    $all_arrays['decor_type'] = $decor_types;
    $all_arrays['marca_masina'] = $marci_masina;
    $all_arrays['rulaj_kilometri'] = $rulaje_kilometri;
    $all_arrays['tip_combustibil'] = $tip_combustibil;
    $selected = $all_arrays[$attribute_name];
    $selected_length = count($selected);
    $random_choice_number = $random_value % $selected_length;
    $actual_random_choice = $selected[$random_choice_number];

    return $actual_random_choice;
  }

//End of expandExplanaitionField Function
  /**
   * Saves the new converted output csv file.
   * @param type $filename
   * @param type $output_data
   * @return \PbcMagmi
   */
  function saveTheOutput($filename, $output_data) {
    if ($output_data != NULL) {
      if ($this->output_data[0] != NULL) {
        $outputCSV = new parseCSV();
        $newcolumns = array_keys($this->output_data[0]);
        $outputCSV->titles = $newcolumns;
        $outputCSV->data = $this->output_data;
        $outputCSV->save($filename, $this->output_data);
        echo "Output file succesfully saved in : ";
        printf("\n");
        echo $filename . ' file.';
        return $this;
      }
    }
  }

  /**
   * Modifies file mode.
   * @param type $file
   */
  function modifyFileMode($file) {
    chmod($file, 0775);
  }

  //End of saveTheOutput Function

  /**
   * Estabilish a ftp connection and download the input csv file required.
   * @param type $ftp_config
   * @return boolean
   */
  public function getCsvInputFromFtp($ftp_config) {

    $conn_id = ftp_connect($ftp_config['ftp_server']);
    $login_result = ftp_login($conn_id, $ftp_config['ftp_username'], $ftp_config['ftp_user_pass']);
    if (ftp_chdir($conn_id, $ftp_config['ftp_file_path'])) {
      if ($this->config['script_verbose']) {
        echo "Ftp Succesfully Accessed. \n";
      }
    }
    $get_csv_file = ftp_get($conn_id, $ftp_config['local_file'], $ftp_config['server_file'], FTP_BINARY);
    if ($get_csv_file) {
      if ($this->config['script_verbose']) {
        echo "Ftp file locally saved into : \n" . $ftp_config['local_file'] . " \n\n";
      }
      ftp_close($conn_id);
      return $ftp_config['local_file'];
    }
    ftp_close($conn_id);
    return false;
  }

  /**
   * For each Hydra Deleted Items , if they exist in Magento , hide them.
   */
  function setUnavailableItemsAsHiddenInMagento() {

    $items_to_be_set_with_stock0 = $this->getDifferenceBetweenCSVandMagentoDB();

    if ($items_to_be_set_with_stock0) {
      foreach ($items_to_be_set_with_stock0 as $item_unavaillable) {
        $product = Mage::getModel('catalog/product');
        $id = Mage::getModel('catalog/product')->getResource()->getIdBySku($item_unavaillable);
        if ($id) {
          $stock_item = Mage::getModel('cataloginventory/stock_item')->loadByProduct($id);
          $stock_item->setData('is_in_stock', 0);
          $stock_item->setData('manage_stock', 0);
          try {
            $stock_item->save();
          }
          catch (Exception $ex) {
            echo "{$ex}";
          }
        }
      }
      if ($config['script_verbose']) {
        echo "\n" . count($items_to_be_set_with_stock0) . " Items have been set as not_in_stock (is_in_stock = 0) \n";
      }
    }
  }

  /**
   * For each Hydra Deleted Items , if they exist in Magento , DISABLE them.
   */
  public function setUnavailableItemsDisabledInMagento() {
    $items_to_be_set_with_stock0 = $this->getDifferenceBetweenCSVandMagentoDB();

    print_r($items_to_be_set_with_stock0);
    echo "MUST BE IMPLEMENTED";
    echo "THIS SHOULD RUN A CUSTOM QUERY ON THE PRODUCTS TABLE , FOR EACH PRODUCT IT AND SET IT TO BE DISABLED (SHOULD BE FASTER)";
  }

  /**
   * Calculates a integer time variable 
   * @param integer $days
   * @param integer $hours
   * @param integer $minutes
   * @param integer $seconds
   * @return integer 
   */
  public function calculateTimeDifference($days, $hours, $minutes, $seconds) {
    $time_difference = 0;

    if ($seconds != 0) {
      $time_difference = $time_difference + $seconds;
    }
    if ($minutes != 0) {
      $time_difference = $time_difference + $minutes * 60;
    }
    if ($hours != 0) {
      $time_difference = $time_difference + $hours * 60 * 60;
    }
    if ($days != 0) {
      $time_difference = $time_difference + $days * 60 * 60 * 24;
    }

    return $time_difference;
  }

  /**
   * Function that searches the INPUTS_FOLDER and OUTPUTS Folder for files and
   * deletes the files that have the timestamp older than the current date (by 
   * the difference set);
   * @param type $number_of_days
   */
  public function deleteLogsOlderThanXDays($number_of_days) {

    $current_date = time();
    $time_difference = $this->calculateTimeDifference($number_of_days, 0, 0, 1);

    $files_to_delete = array();
    $folders_to_search = array(
      INPUTS_FOLDER,
      OUTPUTS_FOLDER,
    );

    foreach ($folders_to_search as $folder) {
      if ($handle = opendir($folder)) {
        while (false !== ($entry = readdir($handle))) {
          if ($folder == INPUTS_FOLDER) {
            $timestampcsv = substr($entry, 9);
          }
          else {
            $timestampcsv = substr($entry, 7);
          }
          $actualtimestamp = substr($timestampcsv, 0, 10);
          $integertimestamp = intval($actualtimestamp);
          if ($current_date > ($integertimestamp + $time_difference)) {
            if (strlen($entry) > 3) {
              $files_to_delete[] = $folder . $entry;
            }
          }
        }
        closedir($handle);
      }
    }
    foreach ($files_to_delete as $file) {
      $filepath = $file;
      $delete = unlink($filepath);
      if ($this->config['script_verbose']) {
        echo "\n - Deleted $filepath";
      }
    }
  }

}

/**
 * Actually Start Running The Script
 */
$test = new PbcMagmi($config);
$test->addColumnsToTitles($test->columns_to_be_added);
$test->addTestDefaultColumnsToTitles($test->test_default_columns_for_magmi);
if ($config['testing_mode']) {
  if ($config['generate_random_values']) {
    $test->addrowstodata($config['number_to_generate']);
  }
}


$output_data = $test->expandExplanaitionField($test->csv->data);
$test->saveTheOutput(OUTPUTS_FOLDER . 'Output_' . CURRENT_DATE . '.csv', $output_data);
$test->modifyFileMode(OUTPUTS_FOLDER . 'Output_' . CURRENT_DATE . '.csv');
if ($config['script_verbose']) {
  echo "\n\nStarting to verify each unavaillable product... \n(This might take up-to 10 minutes)...";
}
$test->setUnavailableItemsAsHiddenInMagento();

$test->setUnavailableItemsDisabledInMagento();

$test->deleteLogsOlderThanXDays($config['days_to_keep_log_files']);
?>