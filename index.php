<?php

require_once('parsecsv.lib.php');

$csv = new parseCSV();
$csv->auto('input.csv');

$csv_column_titles = $csv->titles;

$csv_data = $csv->data;
//Instantiate the OutPut Array
$output_data = array();


$columns_to_be_added = array(
  'YEAR', 'CCM', 'POWER', 'HORSEPOWER', 'ENGCODE'
);

foreach ($columns_to_be_added as $column_name) {
  $csv->titles[] = $column_name;
}



foreach ($csv->data as $key => $value) {


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

  $output_data[] = $output_csv_data;
}




//    [0] => ID
//    [1] => ID_UM_MINIMA
//    [2] => COD_INTERN
//    [3] => DENUMIRE
//    [4] => PRET_LISTA
//    [5] => PRET_VANZ_MAG
//    [6] => GRAMAJ_UNITATEA_MINIMA
//    [7] => ORDINE_AFISARE
//    [8] => XDISC_PROMO
//    [9] => XTVA
//    [10] => XADAOS
//    [11] => PRODUS_ACTIV
//    [12] => ACOPERIRE
//    [13] => COD_BARE
//    [14] => COMENT
//    [15] => EXPL
//    [16] => PRET_LISTA_EURO
//    [17] => PAGINA_CATALOG
//    [18] => LINK_DISK
//    [19] => FIELD_1
//    [20] => FIELD_2
//    [21] => STOC_COMANDAT_CLN
//    [22] => STOC_COMANDAT_FUR
//    [23] => STOC_CURENT
//    [24] => STOC_REZERVAT
//    [25] => STOC_ASIGURAT
//    [26] => PARENT_ID
//    [27] => SOURCE_ID
# then we output the file to the browser as a downloadable file...
//$csv->save('output.csv', $output_csv_data);


//var_dump($output_data);

$outputCSV = new parseCSV();
$newcolumns = array_keys($output_csv_data);
$outputCSV->titles = $newcolumns;
$outputCSV->data = $output_data;
$outputCSV->save('outputForMagmi.csv', $output_data);

?>