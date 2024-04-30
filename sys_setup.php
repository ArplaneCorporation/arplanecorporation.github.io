<?php
if(!$config||!$tmweasy['user']){
	if(!$thismain){
		die();
	}
	echo '
	<!DOCTYPE html>
<html lang="en" >
<head>
<meta charset="UTF-8">
</head>
<body>
	<h1>Setup..</h1>';
	$createconfig=@file_put_contents("config.php"," ");
	if(!$createconfig){
		echo "<h2 style='color:red'>chmod 777 กรุณาเพิ่มสิทธิ  การเขียน ใน Folder ก่อน</h2>";
	}else{
		if($_POST["tmweasyuser"]&&$_POST["tmweasypass"]){
			$chuser=$check_api=connect_api("http://tmwallet.thaighost.net/apiwallet.php?username=".$_POST["tmweasyuser"]."&password=".$_POST["tmweasypass"]."&json=1");
			$check_api=json_decode($check_api,true);
			if($check_api['Status']!="ready"){
				echo "<h2 style='color:red'>".$check_api['Msg']." : ตรวจสอบความถูกต้องของข้อมูล </h2>";
			}else{
				$_SESSION["tmweasyuser"]=$_POST["tmweasyuser"];
				$_SESSION["tmweasypass"]=$_POST["tmweasypass"];
				header( "location:index.php?setup=2" );
			}
		}
		if(is_numeric($_POST['tmw_mobile'])&&strlen($_POST['tmw_mobile'])==10){
			$_SESSION["tmw_mobile"]=$_POST["tmw_mobile"];
			$_SESSION["tmw_name"]=$_POST["tmw_name"];
			$_SESSION["tmw_secret"]=$_POST["tmw_secret"];
			$_SESSION["tmw_conid"]=$_POST["tmw_conid"];
			$_SESSION["tmw_ppay"]=$_POST["tmw_ppay"];
			
			$_SESSION["type_ppay"]=$_POST["type_ppay"];
			
			$_SESSION["select_amount"]=$_POST["select_amount"];
			$_SESSION["list_amount"]=$_POST["list_amount"];
		
			header( "location:index.php?setup=3" );
		}
		if($_POST['setdb']=="true"){
			header( "location:index.php?setup=4" );
		}else if($_POST['setdb']=="false"){
$config_data='<?php
			
	$url_api="http://tmwallet.thaighost.net/apitmh.php";

	$tmweasy["user"]="'.$_SESSION["tmweasyuser"].'";
	$tmweasy["password"]="'.$_SESSION["tmweasypass"].'";
	$tmweasy["con_id"]="'.$_SESSION["tmw_conid"].'";//ได้จากหน้า setting บน tmweasy กดเปิด Truewallet hook

	$truewallet["secret_key"]="'.$_SESSION["tmw_secret"].'";//secret key ที่ได้จากหน้าตั้งค่า web hook บนทรูวอเลท
	$truewallet["mobile"]="'.$_SESSION["tmw_mobile"].'";//เบอร์ทรูวอเลท ตัวเลขเท่านั้น
	$truewallet["name"]="'.$_SESSION["tmw_name"].'"; //ฃื่อบัญชีวอเลท
	$truewallet["promptpay_ewall_id"]="'.$_SESSION["tmw_ppay"].'"; //เลขพร้อมเพย์บนทรูวอเลท

	$paytype["truewallet"]=true;
	$paytype["promptpay"]='.tf($_SESSION["type_ppay"]).';
	
	$select_amount='.$_SESSION["select_amount"].';
	$list_amount=array('.$_SESSION["list_amount"].');
	
	$database_set["truewallet"]["mul"]=1; //ตัวคูณเครดิตร
	
	$database_set["database_type"]=0;//ตั้งค่าชนิด ฐานข้อมูล 0=ไม่เชื่อมฐานข้อมูล พัฒนาเพิ่มเอง | 1=mysql | 2=mysqli | 3=mssql | 4=odbc | 5=sqlsrv
	$database_set["host"]="";
	$database_set["user"]="";
	$database_set["password"]="";
	$database_set["db_name"]="";
	$database_set["user_table"]="";
	$database_set["user_field"]="";
	$database_set["point_field"]="";

?>';
				@file_put_contents("config.php",$config_data);
				header( "location:index.php");
		}
		if($_POST['setdb_type']){
			$_SESSION["setdb_type"]=$_POST["setdb_type"];
			header( "location:index.php?setup=5" );
		}
		if($_POST['db_server']){
			$database_host=$_POST['db_server'];
			$database_user=$_POST['db_user'];
			$database_password=$_POST['db_pass'];
		
			$connectionInfo = array("UID" => $database_user, "PWD" => $database_password);

			$connect_db=array(
			'1'=>'$conn=mysql_connect($database_host,$database_user,$database_password);',
				
			'2'=>'$conn=mysqli_connect($database_host,$database_user,$database_password);',

			'3'=>'$conn=mssql_connect($database_host,$database_user,$database_password);',
				
			'4'=>'$conn=odbc_connect(\'Driver={SQL Server};Server=\' .$database_host. \';Database=\' . $database_db_name. \';\' ,$database_user, $database_password);',

			'5'=>'$conn=sqlsrv_connect($database_host,$connectionInfo);',

			);
			
			eval($connect_db[$_SESSION["setdb_type"]]);
			if(!$conn){
				echo "<h2 style='color:red'>การเชื่อมต่อฐานข้อมูลไม่ถูกต้อง ตรวจสอบการป้อนข้อมูล</h2>";
			}else{
				$select_db=array(
				'1'=>'$dblist=mysql_query("show databases");',
					
				'2'=>'$dblist=mysqli_query($conn,"show databases");',

				'3'=>'$dblist=mssql_query("SELECT * FROM sys.databases");',
					
				'4'=>'$dblist=odbc_exec($conn,"SELECT * FROM sys.databases");',

				'5'=>'$dblist=sqlsrv_query($conn,"SELECT * FROM sys.databases");',

				);
			
			
			eval($select_db[$_SESSION["setdb_type"]]);
			$ii=0;
			$sql_fetch_array=array(
				'1'=>'while($dbl=mysql_fetch_array($dblist)){
					$dbname_array[$ii]=$dbl["Database"];
					$ii++;
				}',
				'2'=>'while($dbl=mysqli_fetch_array($dblist)){
					$dbname_array[$ii]=$dbl["Database"];
					$ii++;
				}',
				'3'=>'while($dbl=mssql_fetch_array($dblist)){
					$dbname_array[$ii]=$dbl["name"];
					$ii++;
				}',
				'4'=>'while($dbl=odbc_fetch_array($dblist)){
					$dbname_array[$ii]=$dbl["name"];
					$ii++;
				}',
				'5'=>'while($dbl=sqlsrv_fetch_array($dblist)){
					$dbname_array[$ii]=$dbl["name"];
					$ii++;
				}',
				);
			eval($sql_fetch_array[$_SESSION["setdb_type"]]);
			
			$_SESSION["conn"]=$conn;
			$_SESSION["db_server"]=$_POST['db_server'];
			$_SESSION["db_user"]=$_POST['db_user'];
			$_SESSION["db_pass"]=$_POST['db_pass'];
			
			$_SESSION["dbname_array"]=$dbname_array;
			header( "location:index.php?setup=6" );
			}
		}
		if($_POST['setdb_db']){
			$database_host=$_SESSION["db_server"];
			$database_user=$_SESSION["db_user"];
			$database_password=$_SESSION["db_pass"];
			$database_db_name=$_POST['setdb_db'];
		
			$connectionInfo = array("Database" => $database_db_name,"UID" => $database_user, "PWD" => $database_password);

			$connect_db=array(
			'1'=>'$conn=mysql_connect($database_host,$database_user,$database_password);
				mysql_select_db($database_db_name);',
				
			'2'=>'$conn=mysqli_connect($database_host,$database_user,$database_password,$database_db_name);',

			'3'=>'$conn=mssql_connect($database_host,$database_user,$database_password);
				mssql_select_db($database_db_name);',
				
			'4'=>'$conn=odbc_connect(\'Driver={SQL Server};Server=\' .$database_host. \';Database=\' . $database_db_name. \';\' ,$database_user, $database_password);',

			'5'=>'$conn=sqlsrv_connect($database_host,$connectionInfo);',

			);
			
			eval($connect_db[$_SESSION["setdb_type"]]);
			
			$select_table=array(
				'1'=>'$tblist=mysql_query("show TABLES ");',
					
				'2'=>'$tblist=mysqli_query($conn,"show TABLES ");',

				'3'=>'$tblist=mssql_query("SELECT * FROM sys.tables");',
					
				'4'=>'$tblist=odbc_exec($conn,"SELECT * FROM sys.tables");',

				'5'=>'$tblist=sqlsrv_query($conn,"SELECT * FROM sys.tables");',

			);
			eval($select_table[$_SESSION["setdb_type"]]);
			
			$ii=0;
			$sql_fetch_array=array(
				'1'=>'while($dbl=mysql_fetch_array($tblist)){
					$tablename_array[$ii]=$dbl["Tables_in_".$database_db_name];
					$ii++;
				}',
				'2'=>'while($dbl=mysqli_fetch_array($tblist)){
					$tablename_array[$ii]=$dbl["Tables_in_".$database_db_name];
					$ii++;
				}',
				'3'=>'while($dbl=mssql_fetch_array($tblist)){
					$tablename_array[$ii]=$dbl["name"];
					$ii++;
				}',
				'4'=>'while($dbl=odbc_fetch_array($tblist)){
					$tablename_array[$ii]=$dbl["name"];
					$ii++;
				}',
				'5'=>'while($dbl=sqlsrv_fetch_array($tblist)){
					$tablename_array[$ii]=$dbl["name"];
					$ii++;
				}',
				);
			eval($sql_fetch_array[$_SESSION["setdb_type"]]);
			
			$_SESSION["tablename_array"]=$tablename_array;
			$_SESSION['setdb_db']=$_POST['setdb_db'];
			$_SESSION["conn"]=$conn;
			header( "location:index.php?setup=7" );
			
		}
		
		if($_POST['set_tb']){
			$database_host=$_SESSION["db_server"];
			$database_user=$_SESSION["db_user"];
			$database_password=$_SESSION["db_pass"];
			$database_db_name=$_SESSION['setdb_db'];
			
			$set_tb=$_POST['set_tb'];
		
			$connectionInfo = array("Database" => $database_db_name,"UID" => $database_user, "PWD" => $database_password);

			$connect_db=array(
			'1'=>'$conn=mysql_connect($database_host,$database_user,$database_password);
				mysql_select_db($database_db_name);',
				
			'2'=>'$conn=mysqli_connect($database_host,$database_user,$database_password,$database_db_name);',

			'3'=>'$conn=mssql_connect($database_host,$database_user,$database_password);
				mssql_select_db($database_db_name);',
				
			'4'=>'$conn=odbc_connect(\'Driver={SQL Server};Server=\' .$database_host. \';Database=\' . $database_db_name. \';\' ,$database_user, $database_password);',

			'5'=>'$conn=sqlsrv_connect($database_host,$connectionInfo);',

			);
			
			eval($connect_db[$_SESSION["setdb_type"]]);
			
			$select_fd=array(
				'1'=>'$fdlist=mysql_query("SHOW COLUMNS FROM $set_tb");',
					
				'2'=>'$fdlist=mysqli_query($conn,"SHOW COLUMNS FROM $set_tb");',

				'3'=>'$fdlist=mssql_query("select COLUMN_NAME from INFORMATION_SCHEMA.COLUMNS where TABLE_NAME=\'$set_tb\'");',
					
				'4'=>'$fdlist=odbc_exec($conn,"select COLUMN_NAME from INFORMATION_SCHEMA.COLUMNS where TABLE_NAME=\'$set_tb\'");',

				'5'=>'$fdlist=sqlsrv_query($conn,"select COLUMN_NAME from INFORMATION_SCHEMA.COLUMNS where TABLE_NAME=\'$set_tb\'");',

			);
			eval($select_fd[$_SESSION["setdb_type"]]);
			
			$ii=0;
			$sql_fetch_array=array(
				'1'=>'while($dbl=mysql_fetch_array($fdlist)){
					$fdname_array[$ii]=$dbl["Field"];
					$ii++;
				}',
				'2'=>'while($dbl=mysqli_fetch_array($fdlist)){
					$fdname_array[$ii]=$dbl["Field"];
					$ii++;
				}',
				'3'=>'while($dbl=mssql_fetch_array($fdlist)){
					$fdname_array[$ii]=$dbl["COLUMN_NAME"];
					$ii++;
				}',
				'4'=>'while($dbl=odbc_fetch_array($fdlist)){
					$fdname_array[$ii]=$dbl["COLUMN_NAME"];
					$ii++;
				}',
				'5'=>'while($dbl=sqlsrv_fetch_array($fdlist)){
					$fdname_array[$ii]=$dbl["COLUMN_NAME"];
					$ii++;
				}',
				);
			eval($sql_fetch_array[$_SESSION["setdb_type"]]);
			
			$_SESSION["fdname_array"]=$fdname_array;
			$_SESSION['set_tb']=$_POST['set_tb'];
			header( "location:index.php?setup=8" );
			
		}
		if($_POST['set_fd_user']){
			$_SESSION['set_fd_user']=$_POST['set_fd_user'];
			header( "location:index.php?setup=9" );
		}
		if($_POST['set_fd_point']){
			$_SESSION['set_fd_point']=$_POST['set_fd_point'];
			header( "location:index.php?setup=10" );
		}
		if($_POST['mul']){
			$config_data='<?php	
	$url_api="http://tmwallet.thaighost.net/apitmh.php";

	$tmweasy["user"]="'.$_SESSION["tmweasyuser"].'";
	$tmweasy["password"]="'.$_SESSION["tmweasypass"].'";
	$tmweasy["con_id"]="'.$_SESSION["tmw_conid"].'";//ได้จากหน้า setting บน tmweasy กดเปิด Truewallet hook

	$truewallet["secret_key"]="'.$_SESSION["tmw_secret"].'";//secret key ที่ได้จากหน้าตั้งค่า web hook บนทรูวอเลท
	$truewallet["mobile"]="'.$_SESSION["tmw_mobile"].'";//เบอร์ทรูวอเลท ตัวเลขเท่านั้น
	$truewallet["name"]="'.$_SESSION["tmw_name"].'"; //ฃื่อบัญชีวอเลท
	$truewallet["promptpay_ewall_id"]="'.$_SESSION["tmw_ppay"].'"; //เลขพร้อมเพย์บนทรูวอเลท

	$paytype["truewallet"]=true;
	$paytype["promptpay"]='.tf($_SESSION["type_ppay"]).';
	
	$select_amount='.$_SESSION["select_amount"].';
	$list_amount=array('.$_SESSION["list_amount"].');
	
	$database_set["truewallet"]["mul"]=1; //ตัวคูณเครดิตร

	$database_set["database_type"]='.$_SESSION["setdb_type"].';//ตั้งค่าชนิด ฐานข้อมูล 0=ไม่เชื่อมฐานข้อมูล พัฒนาเพิ่มเอง | 1=mysql | 2=mysqli | 3=mssql | 4=odbc | 5=sqlsrv
	$database_set["host"]="'.$_SESSION["db_server"].'";
	$database_set["user"]="'.$_SESSION["db_user"].'";
	$database_set["password"]="'.$_SESSION["db_pass"].'";
	$database_set["db_name"]="'.$_SESSION["setdb_db"].'";
	$database_set["user_table"]="'.$_SESSION["set_tb"].'";
	$database_set["user_field"]="'.$_SESSION["set_fd_user"].'";
	$database_set["point_field"]="'.$_SESSION["set_fd_point"].'";

			?>';
			@file_put_contents("config.php",$config_data);
			
			$_SESSION["alert_content"]="Setup เรียบร้อย หากต้องการแก้ไขค่าต่างๆ สามารถทำได้ที่ไฟล์ config.php หากต้องการตั้งค่าใหม่ให้ลบไฟล์ config.php";
			$_SESSION["alert_type"]="alert-success";
			header( "location:index.php" );
			die();
		}
		
		switch($_GET['setup']){
			case 2:
			?>
				<form method="post">
					<p>เบอร์ทรูวอเลท :  <input name="tmw_mobile" maxlength="10"> ใส่ตัวเลข 10 หลัก</p>
					<p>ชื่อบัญชีทรูวอเลท  :  <input name="tmw_name"></p>
					<p>Api secret key :  <input name="tmw_secret"> ได้จากหน้าตั้งค่า web hook บนทรูวอเลท</p>
					<p>con id :  <input name="tmw_conid"> ได้จากหน้า setting บน tmweasy กดเปิด Truewallet hook</p>
					<!--<p>
					<div><b>ช่องทางรับชำระ</b></div>
						<p>
						<div><label><input type="checkbox" name="type_ppay" value="true" checked> พร้อมเพย์   <input name="tmw_ppay"> ป้อนเลข E-wallet ของทรูวอเลท</label></div>
						
						</p>
					</p>-->
					<div><b>การกำหนดยอดชำระ</b></div>
					<p>
						<select name="select_amount">
							<option value="0">ลูกค้ากรอกยอดเอง</option>
							<option value="1">แบบฟิกยอด ตาม List ยอดชำระ</option>
						</select> 
						List ยอด :  <input name="list_amount" value="50,100,150,300,500,1000"> 
					</p>
					
					<p><input type="submit" value="Next"></p>
				</form>
				
			<?php
			break;
			case 3:
			?>
				<h2>ตั้งค่า ฐานข้อมูล เพื่ออัพเดท ยอดเครดิตร - พ้อย ให้ลูกค้าหลังเติม</h2>
				<form method="post">
					<p>
						<div><label><input type="radio" name="setdb" value="true" checked> ตั้งค่า ฐานข้อมูลต่อไป </label></div>
						<div><label><input type="radio" name="setdb" value="false" > ไม่ต้อง ฉันต้องการพัฒนาส่วนนี้เอง</label></div>
					</p>
					<p><input type="submit" value="Next"></p>
				</form>
				<p><a href="index.php?setup=2">ย้อนกลับ</a></p>
			<?php
			break;
			case 4:
			?>
				<h2>ตั้งค่า ฐานข้อมูล เพื่ออัพเดท ยอดเครดิตร - พ้อย ให้ลูกค้าหลังเติม</h2>
				<form method="post">
					<div><b>เลือกชนิดการเชื่อมต่อ ฐานข้อมูล</b></div>
					<p>
					<?php
						if(function_exists("mysql_connect")){
							echo '<div><label><input type="radio" name="setdb_type" value="1" > mysql *สำหรับ  Mysql , MariaDB Php รุ่นเก่า</label></div>';
						}
						if(function_exists("mysqli_connect")){
							echo '<div><label><input type="radio" name="setdb_type" value="2" > mysqli *สำหรับ  Mysql , MariaDB</label></div>';
						}
						if(function_exists("mssql_connect")){
							echo '<div><label><input type="radio" name="setdb_type" value="3" > mssql *สำหรับ  Microsoft SQL Server php รุ่นเก่า</label></div>';
						}
						if(function_exists("odbc_connect")){
							echo '<div><label><input type="radio" name="setdb_type" value="4" > odbc *สำหรับ  Microsoft SQL Server</label></div>';
						}
						if(function_exists("sqlsrv_connect")){
							echo '<div><label><input type="radio" name="setdb_type" value="5" > sqlsrv *สำหรับ  Microsoft SQL Server php v. 5-8</label></div>';
						}
					?>
						
					</p>

					<p><input type="submit" value="Next"></p>
				</form>
				<p><a href="index.php?setup=3">ย้อนกลับ</a></p>
			<?php
			break;
			case 5:
			?>
				<h2>ตั้งค่า ฐานข้อมูล เพื่ออัพเดท ยอดเครดิตร - พ้อย ให้ลูกค้าหลังเติม</h2>
				<form method="post">
					<p>Database Server : <input name="db_server"> เช่น Localhost , ip</p>
					<p>Database User : <input name="db_user"> เช่น root , sa</p>
					<p>Database Password : <input name="db_pass"></p>
					
					<p><input type="submit" value="Next"></p>
				</form>
				<p><a href="index.php?setup=4">ย้อนกลับ</a></p>
			<?php
			break;
			case 6:
			?>
				<h2>ตั้งค่า ฐานข้อมูล เพื่ออัพเดท ยอดเครดิตร - พ้อย ให้ลูกค้าหลังเติม</h2>
				<form method="post">
					<div><b>เลือกฐานข้อมูล ที่ต้องการ</b></div>
					<p>
					<?php
					foreach($_SESSION["dbname_array"] as $dbname){
						echo '<div><label><input type="radio" name="setdb_db" value="'.$dbname.'" > '.$dbname.'</label></div>';
					}
					?>
					</p>
					<p><input type="submit" value="Next"></p>
				</form>
				<p><a href="index.php?setup=5">ย้อนกลับ</a></p>
			<?php
			break;
			case 7:
			?>
				<h2>ตั้งค่า ฐานข้อมูล เพื่ออัพเดท ยอดเครดิตร - พ้อย ให้ลูกค้าหลังเติม</h2>
				<form method="post">
					<div><b>เลือกตารางข้อมูล ที่เก็บเครดิตรลูกค้า เช่นตาราง User</b></div>
					<p>
					<?php
					foreach($_SESSION["tablename_array"] as $tbname){
						echo '<div><label><input type="radio" name="set_tb" value="'.$tbname.'" > '.$tbname.'</label></div>';
					}
					?>
					</p>
					<p><input type="submit" value="Next"></p>
				</form>
				<p><a href="index.php?setup=6">ย้อนกลับ</a></p>
			<?php
			break;
			case 8:
			?>
				<h2>ตั้งค่า ฐานข้อมูล เพื่ออัพเดท ยอดเครดิตร - พ้อย ให้ลูกค้าหลังเติม</h2>
				<form method="post">
					<div><b>เลือกฟิวด์ ที่ใช้อ้างอิง ID ลูกค้า เช่น username uid email เป็นคอลั่มข้อมูลที่ใช้เทียบ Ref1</b></div>
					<p>
					<?php
					foreach($_SESSION["fdname_array"] as $fdname){
						echo '<div><label><input type="radio" name="set_fd_user" value="'.$fdname.'" > '.$fdname.'</label></div>';
					}
					?>
					</p>
					<p><input type="submit" value="Next"></p>
				</form>
				<p><a href="index.php?setup=7">ย้อนกลับ</a></p>
			<?php
			break;
			case 9:
			?>
				<h2>ตั้งค่า ฐานข้อมูล เพื่ออัพเดท ยอดเครดิตร - พ้อย ให้ลูกค้าหลังเติม</h2>
				<form method="post">
					<div><b>เลือกฟิวด์ ที่ต้องการให้อัพเดท เครดิตร  - พ้อย </b></div>
					<p>
					<?php
					foreach($_SESSION["fdname_array"] as $fdname){
						if($_SESSION['set_fd_user']!=$fdname){
							echo '<div><label><input type="radio" name="set_fd_point" value="'.$fdname.'" > '.$fdname.'</label></div>';
						}
					}
					?>
					</p>
					<p><input type="submit" value="Next"></p>
				</form>
				<p><a href="index.php?setup=8">ย้อนกลับ</a></p>
			<?php
			break;
			case 10:
			?>
				<h2>ตั้งค่า เรทพ้อย</h2>
				<form method="post">
				
					<p>
						<p>ตัวคูณพ้อย  <input type ="number" name="mul" value="1"></p>
					</p>
					
					<p><input type="submit" value="Next"></p>
				</form>
				<p><a href="index.php?setup=9">ย้อนกลับ</a></p>
			<?php
			break;
			default:
	?>
				<form method="post">
					<p>User Tmweasy : <input name="tmweasyuser"></p>
					<p>Password Tmweasy : <input name="tmweasypass"></p>
					<p><input type="submit" value="Next"></p>
				</form>
				
	<?php
		}
	}
	$doamin=$_SERVER["SERVER_NAME"];
	if($_SERVER["SERVER_NAME"]=="localhost"){
		$doamin="yourdomain";
	}
	$pa=parse_url($_SERVER["REQUEST_URI"], PHP_URL_PATH);
	$pa=preg_replace("/index.php/","",$pa);
	echo '
	<h3>URL Api Hook นำไปใส่ตั้งค่าแอปทรูวอเลท : <span>'.$_SERVER["REQUEST_SCHEME"].'://'.$doamin.$pa.'hook_action.php</span></h3>
</body>
</html>';
	die();
}

?>