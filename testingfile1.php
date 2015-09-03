<?php

echo "<pre>";
//////////////////////////////////////////////////////////
ini_set('display_errors', 1);
error_reporting(E_ALL);
require_once MAGENTO_BASE_URL . 'app/Mage.php';
Mage::app();
//////////////////////////////////////////////////////////

$existing_product_skus_in_magento = array();

foreach (Mage::getModel('catalog/product')->getCollection() as $product) {
  $existing_product_skus_in_magento[] = $product->getSku();
}

echo "<h1>Connection to ftp via php</h1>";
echo " http://kvz.io/blog/2007/07/24/make-ssh-connections-with-php/   <br />http://stackoverflow.com/questions/2172289/connection-to-secure-ftp-server-from-php<br />"
 . "http://php.net/manual/ro/function.ftp-get.php <br /><br /><br /><br />";


echo "<br />----------------------------------------------------------------------------<br />";



//TODAY FTP PWD : RkTY9H
//www/timedudeapi.cust21.reea.net
getCsvInputFromFtp();

function getCsvInputFromFtp(){
 
  $ftp_config = array(
    'local_file' => 'local.csv',
    'server_file' => 'input.csv',
    'ftp_server' => 'timedudeapi.cust21.reea.net',
    'ftp_username' => 'devel',
    'ftp_user_pass' => 'RkTY9H',
    'ftp_file_path' => 'www/timedudeapi.cust21.reea.net',
  );

$conn_id = ftp_connect($ftp_config['ftp_server']);
$login_result = ftp_login($conn_id, $ftp_config['ftp_username'], $ftp_config['ftp_user_pass']);

if (ftp_chdir($conn_id, $ftp_config['ftp_file_path'])) {
  echo "Ftp dir found. <br />";
}

$get_csv_file = ftp_get($conn_id, $ftp_config['local_file'], $ftp_config['server_file'], FTP_BINARY);
if ($get_csv_file){
  ftp_close($conn_id);  
  return true;
} 
ftp_close($conn_id);  
return false;
}
