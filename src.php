<?php
date_default_timezone_set("Asia/Bangkok");
error_reporting(0);

function connect_api($url){
	$ch = curl_init($url);
	curl_setopt($ch, CURLOPT_SSL_VERIFYPEER, 0);
	curl_setopt($ch, CURLOPT_SSL_VERIFYHOST, 0); 
	curl_setopt($ch, CURLOPT_USERAGENT, "Mozilla/5.0 (Windows; U; Windows NT 5.1; th; rv:1.9.2.12) Gecko/20101026 Firefox/3.6.12");
	curl_setopt($ch, CURLOPT_HEADER, 0);
	@curl_setopt ($ch, CURLOPT_FOLLOWLOCATION, 1);
	curl_setopt($ch, CURLOPT_RETURNTRANSFER, 1); 
	$respone=curl_exec($ch);
	curl_close($ch);
	return $respone;
}
$database_host=$database_set['host'];
$database_user=$database_set['user'];
$database_password=$database_set['password'];
$database_db_name=$database_set['db_name'];
$database_table=$database_set['user_table'];
$database_user_field=$database_set['user_field'];
$database_point_field=$database_set['point_field'];

$connectionInfo = array("Database" => $database_db_name, "UID" => $database_user, "PWD" => $database_password);

$connect_db=array(
'1'=>'$conn=mysql_connect($database_host,$database_user,$database_password) or die("connect Mysql database error! ตรวจสอบการตั้งค่า Database");
	mysql_select_db($database_db_name) or die("Select database error! ตรวจสอบการตั้งค่า Database");',
	
'2'=>'$conn=mysqli_connect($database_host,$database_user,$database_password,$database_db_name) or die("Error Mysqli Database is not connect! ตรวจสอบการตั้งค่า Database");',

'3'=>'mssql_connect($database_host,$database_user,$database_password) or die("Mssql Database not Connect.. ตรวจสอบการตั้งค่า Database");
	mssql_select_db ($database_db_name) or die("Mssql Select database error!");',
	
'4'=>'$conn=odbc_connect(\'Driver={SQL Server};Server=\' .$database_host. \';Database=\' . $database_db_name. \';\' ,$database_user, $database_password) or die(\'Error Odbc Mssql Database is not connect!\');',

'5'=>'$conn=sqlsrv_connect($database_host,$connectionInfo) or die("sqlsrv_connect to Mssql server Error ตรวจสอบการตั้งค่า Database");',

);
eval($connect_db[$database_set['database_type']]);
?>