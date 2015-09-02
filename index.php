<?php 

require_once('parsecsv.lib.php');

$csv = new parseCSV();
$csv->auto('input.csv');
echo"<pre>";


$csv_column_titles = $csv->titles;

$csv_data = $csv->data;
// print_r($csv_data);

//ADD COLUMN TITLES AFTER IN PLACE OF THE 15'TH ONE ( 4 columns)
//PART 1 . SPLIT THE ARRAY OF COLUMNS IN TWO COLUMN SETS (FIRST 15 AND THE REST , EXCLUDE THE 'EXPL' COLUMN);

// $first_15 = array();
// $last_columns = array();

// for ($i=0;$i<15;$i++){
// 	$first_15[$i] = $csv_column_titles[$i];
// }
// for ($i=16;$i< count($csv_column_titles);$i++){
// 	$last_columns[] = $csv_column_titles[$i];
// }
// //PART 2 . ADD THE NEW COLUMNS TO THE FIRST 15 ARRAY
$columns_to_add = array(
		'YEAR','CCM','POWER','HORSEPOWER','ENGCODE'
	);

foreach ($columns_to_add as $column_name){
	$csv->titles[] = $column_name;
}





// $year = array();
// $capacity = array();
// $power = array();
// $horsepower = array();
// $enginecode = array();

// print_r($csv->data);

foreach ($csv->data as $value) {

	// print_r($key);
	// print_r($value);
	// $year[$key] = null;
	// $capacity[$key] = null;
	// $power[$key] = null;
	// $horsepower[$key] = null;
	// $enginecode[$key] = null;

		$yearmatch = preg_match("(((19|20)\d{2})|((19|20)\d{2}\.))", $value['EXPL'], $yearmatches);
		if($yearmatch){
			$year = substr($yearmatches[0], 0,4);
		} else {
			$year = "NULL";
		}
		$capacitymatch = preg_match("([0-9]{3,5}CC)", $value['EXPL'],$capacitymatches);
		if($capacitymatch){
			$capacity = $capacitymatches[0];
		} else {
			$capacity = "NULL";
		}
		$powermatch = preg_match("([0-9]{1,4}KW)", $value['EXPL'], $powermatches);
		if($powermatch) {
			$power = $powermatches[0];
		}else {
			$power = "NULL";
		}
		$horsepowermatch = preg_match("([0-9]{1,4}CP)", $value['EXPL'], $horsepowermatches);
		if($horsepowermatch) {
			$horsepower = $horsepowermatches[0];
		}else {
			$horsepower = "NULL";
		}
		$enginecodematch = preg_match("([A-Z0-9]{12,25})", $value['EXPL'], $enginecodematches);
		if($enginecodematch) {
			$enginecode = $enginecodematches[0];
		}else {
			$enginecode = "NULL";
		}


		$value['YEAR'] 		= $year;

		// print_r($value);
		// die();

		$value['CAPACITY']    = $capacity;
		$value['POWER'] 	 	= $power;
		$value['HORSEPOWER']  = $horsepower;
		$value['ENGCODE']     = $enginecode;
		// array_pop($csv->data);
		// print_r($csv);

		// die('dead');
		// $csv->save('output.csv',$csv->data);
}


# then we output the file to the browser as a downloadable file...
$csv->save('output.csv',$csv->data);
// print_r($csv);
?>
<!-- public function save($file = null, $data = array(), $append = false, $fields = array()) {
        if (empty($file)) {
            $file = &$this->file;
        }

        $mode = ($append) ? 'at' : 'wt';
        $is_php = (preg_match('/\.php$/i', $file)) ? true : false;

        return $this->_wfile($file, $this->unparse($data, $fields, $append, $is_php), $mode);
    } -->