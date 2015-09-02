<?php

require_once('parsecsv.lib.php');
// ini_set('display_errors', 1);

class PbcMagmi {

  public $columns_to_be_added = array(
    'YEAR',
    'CCM',
    'POWER',
    'HORSEPOWER',
    'ENGCODE'
  );
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

  public function __construct($inputFilename) {
    $this->inputFileName = $inputFilename;
    $this->csv = new parseCSV($this->inputFileName);
    $this->csv->auto($this->inputFileName);


    $this->column_titles = $this->csv->titles;
    $this->csv_data = $this->csv->data;
    $this->output_data = array();
 
  }

//End of construct Function
  function addColumnsToTitles($column_names_to_be_added_array) {
    foreach ($column_names_to_be_added_array as $column_name) {
      $this->csv->titles[] = $column_name;
    }
    return $this;
  }

  //End of addColumnsToTitles Function

  function addTestDefaultColumnsToTitles($test_default_columns) {
    foreach ($test_default_columns as $column_name) {
      $this->csv->titles[] = $column_name;
    }
    return $this;
  }

  function expandExplanaitionField($data_array) {
    foreach ($data_array as $key => $value) {


      $yearmatch = preg_match("(((19|20)\d{2})|((19|20)\d{2}\.))", $value['EXPL'], $yearmatches);
      if ($yearmatch) {
        $year = substr($yearmatches[0], 0, 4);
      }
      else {
        $year = "NULL";
      }
      $capacitymatch = preg_match("([0-9]{3,5}CC)", $value['EXPL'], $capacitymatches);
      if ($capacitymatch) {
        $capacity = $capacitymatches[0];
      }
      else {
        $capacity = "NULL";
      }
      $powermatch = preg_match("([0-9]{1,4}KW)", $value['EXPL'], $powermatches);
      if ($powermatch) {
        $power = $powermatches[0];
      }
      else {
        $power = "NULL";
      }
      $horsepowermatch = preg_match("([0-9]{1,4}CP)", $value['EXPL'], $horsepowermatches);
      if ($horsepowermatch) {
        $horsepower = $horsepowermatches[0];
      }
      else {
        $horsepower = "NULL";
      }
      $enginecodematch = preg_match("([A-Z0-9]{12,25})", $value['EXPL'], $enginecodematches);
      if ($enginecodematch) {
        $enginecode = $enginecodematches[0];
      }
      else {
        $enginecode = "NULL";
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
        $output_csv_data['visibility'] = 'Catalog, Search';
        $output_csv_data['categories'] = 'Motor + Tansmisie/Piese Motor';
        $output_csv_data['tax_class_id'] = 'None';
        $output_csv_data['thumbnail'] = '';
        $output_csv_data['small_image'] = '';
        $output_csv_data['image'] = '';
        $output_csv_data['media_gallery'] = '';
        $output_csv_data['att_amz_title'] = '';
        $output_csv_data['decor_type'] = '1400-1600cmc';
        $output_csv_data['marca_masina'] = 'Dacia';
        $output_csv_data['rulaj_kilometri'] = '50-100000';
        $output_csv_data['tip_combustibil'] = 'Benzina';





        $this->output_data[] = $output_csv_data;
      }
    }
    return $this;
  }

//End of expandExplanaitionField Function

  function saveTheOutput($filename, $output_data) {
    if($output_data != NULL){
      // var_dump($output_data);
    if ($this->output_data[0] != NULL) {
      $outputCSV = new parseCSV();
      $newcolumns = array_keys($this->output_data[0]);
      $outputCSV->titles = $newcolumns;
      $outputCSV->data = $this->output_data;
      $outputCSV->save($filename, $this->output_data);
      return $this;
      }
    }
  }

  function modifyFileMode($file){
    $fp = fopen($file, 'w');
    if($fp) {
      chmod($file, 0775);
    }

  }

  //End of saveTheOutput Function
}


$test = new PbcMagmi(__DIR__.'/input.csv');

$test->addColumnsToTitles($test->columns_to_be_added);

$test->addTestDefaultColumnsToTitles($test->test_default_columns_for_magmi);
$output_data = $test->expandExplanaitionField($test->csv->data);
$test->saveTheOutput(__DIR__.'/outputfile.csv', $output_data);
$test->saveTheOutput('/var/www/html/magentostudy/var/import/outputfile.csv',$output_data);
$test->modifyFileMode('/var/www/html/magentostudy/var/import/outputfile.csv');



?>