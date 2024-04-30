<?php
header('Content-Type: application/json');
ob_start();
session_start();
include("config.php");
include("src.php");
$content = file_get_contents('php://input');
$arrJson = json_decode($content, true);
if($content){
	$check_api=connect_api($url_api."?username=".$tmweasy['user']."&password=".$tmweasy['password']."&con_id=".$tmweasy["con_id"]."&method=confirm&tmw_message=".$arrJson['message']."&tmw_secret=".$truewallet['secret_key']);
	$check_api=json_decode($check_api,true);
	if($check_api['status']==1){
		
		//เมื่อชำระเงินสำเร็จ ----------------------------------------------------------------------------------------------------------------------------------------
		$money_total=$check_api['amount']+0; //จำนวนเงินที่ได้รับ
		$point=$database_set["truewallet"]["mul"]*$money_total;
		$ref1=$check_api['ref1']; //ref1 id ลูกค้า
		$database_update=array(
			'1'=>'mysql_query("update $database_table set $database_point_field = $database_point_field + $point where $database_user_field = \'$ref1\' ");',
			'2'=>'mysqli_query($conn,"update $database_table set $database_point_field = $database_point_field + $point where $database_user_field = \'$ref1\' ");',
			'3'=>'mssql_query("update $database_table set $database_point_field = $database_point_field + $point where $database_user_field = \'$ref1\' ");',
			'4'=>'odbc_exec($conn,"update $database_table set $database_point_field = $database_point_field + $point where $database_user_field = \'$ref1\' ");',
			'5'=>'sqlsrv_query($conn,"update $database_table set $database_point_field = $database_point_field + $point where $database_user_field = \'$ref1\' ");',
		);
		eval($database_update[$database_set['database_type']]);
		
		//-----------------------------------------------------------------------------------
		
		
		$ch_date_cl=file_get_contents("dateclear.txt");
		if($ch_date_cl!=date("d")){
			file_put_contents("paid_id.txt","");
			file_put_contents("dateclear.txt",date("d"));
		}
		$lastdata=file_get_contents("paid_id.txt");
		$lastdata=json_decode($lastdata,true);
		
		$lastdata[$check_api['id_pay']]=time();
		
		$lastdata=json_encode($lastdata);
		file_put_contents("paid_id.txt",$lastdata);
		echo json_encode(array("status"=>1));
		
	}else{
		$myfile = fopen("error_log.txt", "a");
		$txt = date("Y-m-d H:i:s")." ".$check_api['msg']."\r\n";
		fwrite($myfile, $txt);
		fclose($myfile);
		
		echo json_encode(array("status"=>"0","msg"=>$check_api['msg']),JSON_UNESCAPED_UNICODE);
	}
}else{
	echo json_encode(array("status"=>"ready"));
}
?>