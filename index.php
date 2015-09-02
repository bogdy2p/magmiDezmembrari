<?php

require_once('parsecsv.lib.php');

ini_set('display_errors', 1);

//$csv = new parseCSV();
//$csv->auto('input.csv');

//$output_data = array();
$column_names_to_be_added_array = array(
  'YEAR', 'CCM', 'POWER', 'HORSEPOWER', 'ENGCODE'
);

class PbcMagmi {

  public $columns_to_be_added = array(
    'YEAR',
    'CCM',
    'POWER',
    'HORSEPOWER',
    'ENGCODE'
  );

  public function __construct($inputFilename) {
    $this->inputFileName = $inputFilename;
    $this->csv = new parseCSV();
    $this->csv->auto($this->inputFileName);
    $this->column_titles = $this->csv->titles;
    $this->csv_data = $this->csv->data;
    $this->output_data = array();
  }

  function addColumnsToTitles($column_names_to_be_added_array) {
    foreach ($column_names_to_be_added_array as $column_name) {
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

      $this->output_data[] = $output_csv_data;
    }
    return $this;
  }

//End of expandExplanaitionField

  function saveTheOutput($filename, $output_data) {

    if ($this->output_data[0] != NULL) {
      $outputCSV = new parseCSV();
      $newcolumns = array_keys($this->output_data[0]);
      $outputCSV->titles = $newcolumns;
      $outputCSV->data = $this->output_data;
      $outputCSV->save($filename, $this->output_data);
//    console.log("wasdasd");
      return $this;
    }
  }

}


$asd = new PbcMagmi('input.csv');
$asd->addColumnsToTitles($asd->columns_to_be_added);
$output_data = $asd->expandExplanaitionField($asd->csv->data);
$asd->saveTheOutput('asdoutputfilename.csv', $output_data);

//echo "<pre>";
//print_r($csv->titles);
//foreach ($column_names_to_be_added_array as $column_name) {
//  $csv->titles[] = $column_name;
//}
//print_r($csv->titles);
//
//
//foreach ($csv->data as $key => $value) {
////
////
////  $yearmatch = preg_match("(((19|20)\d{2})|((19|20)\d{2}\.))", $value['EXPL'], $yearmatches);
////  if ($yearmatch) {
////    $year = substr($yearmatches[0], 0, 4);
////  }
////  else {
////    $year = "NULL";
////  }
////  $capacitymatch = preg_match("([0-9]{3,5}CC)", $value['EXPL'], $capacitymatches);
////  if ($capacitymatch) {
////    $capacity = $capacitymatches[0];
////  }
////  else {
////    $capacity = "NULL";
////  }
////  $powermatch = preg_match("([0-9]{1,4}KW)", $value['EXPL'], $powermatches);
////  if ($powermatch) {
////    $power = $powermatches[0];
////  }
////  else {
////    $power = "NULL";
////  }
////  $horsepowermatch = preg_match("([0-9]{1,4}CP)", $value['EXPL'], $horsepowermatches);
////  if ($horsepowermatch) {
////    $horsepower = $horsepowermatches[0];
////  }
////  else {
////    $horsepower = "NULL";
////  }
////  $enginecodematch = preg_match("([A-Z0-9]{12,25})", $value['EXPL'], $enginecodematches);
////  if ($enginecodematch) {
////    $enginecode = $enginecodematches[0];
////  }
////  else {
////    $enginecode = "NULL";
////  }
////
////  $value['YEAR'] = $year;
////  $value['CAPACITY'] = $capacity;
////  $value['POWER'] = $power;
////  $value['HORSEPOWER'] = $horsepower;
////  $value['ENGCODE'] = $enginecode;
////
////  $output_csv_data['sku'] = $value['ID'];
////  $output_csv_data['name'] = $value['DENUMIRE'];
////  $output_csv_data['price'] = $value['PRET_LISTA'];
////  $output_csv_data['qty'] = $value['STOC_CURENT'];
////  $output_csv_data['year'] = $value['YEAR'];
////  $output_csv_data['capacity'] = $value['CAPACITY'];
////  $output_csv_data['power'] = $value['POWER'];
////  $output_csv_data['horsepower'] = $value['HORSEPOWER'];
////  $output_csv_data['engcode'] = $value['ENGCODE'];
////
////  $output_data[] = $output_csv_data;
//}
//
///**
// * Output the data into the "outputForMagmi.csv" file.
// */
//$outputCSV = new parseCSV();
//$newcolumns = array_keys($output_csv_data);
//$outputCSV->titles = $newcolumns;
//$outputCSV->data = $output_data;
//$outputCSV->save('outputForMagmi.csv', $output_data);
?>