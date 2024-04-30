<?php
ob_start();
session_start();
function alert_content($content,$type){
	switch($type){
		case "alert-danger":
			$type_alert="error";
		 break;
		case "alert-warning":
			$type_alert="warning";
		 break;
		case "alert-success":
			$type_alert="success";
			
		 break;
	}
	$content_box=preg_replace('/"/','\"',$content);
	$script_alert='<script type="text/javascript">
	function JSalert(){
	swal({   title: "System Message",   
    text: "'.$content_box.'",   
    type: "'.$type_alert.'",//error success warning   
    showCancelButton: 0,   
    confirmButtonText: "",   
    cancelButtonText: "ปิด",   
    closeOnConfirm: true,   
    closeOnCancel: true }, 
    function(isConfirm){   
        if (isConfirm) 
		{   
			false  
        } 
         });
}
JSalert();
</script>';
	echo $script_alert;
	$_SESSION['alert_content']="";
}
function my_ip(){
	if ($_SERVER['HTTP_CLIENT_IP']) { 
		$IP = $_SERVER['HTTP_CLIENT_IP'];
	} elseif (preg_match("[0-9]",$_SERVER["HTTP_X_FORWARDED_FOR"] )) { 
		$IP = $_SERVER["HTTP_X_FORWARDED_FOR"];
	} else { 
		$IP = $_SERVER["REMOTE_ADDR"];
	}
		return $IP;
}


function tf($content){
	if($content=="true"){
		return "true";
	}else{
		return "false";
	}
}

$config=@include("config.php");
$thismain=true;

include("src.php");
include("sys_setup.php");
$tran_id=false;
if($_GET['action']=="cancel"){
	$check_api=connect_api($url_api."?username=".$tmweasy['user']."&password=".$tmweasy['password']."&con_id=".$tmweasy["con_id"]."&method=cancel&id_pay=".$_SESSION['id_pay']);
	$_SESSION["id_pay"]="";
	header("Location: index.php");
}
if($_POST['ref1']&&$_POST['amount']){
	$check_api=connect_api($url_api."?username=".$tmweasy['user']."&password=".$tmweasy['password']."&con_id=".$tmweasy["con_id"]."&method=create_pay&ip=".my_ip()."&amount=".$_POST['amount']."&ref1=".$_POST['ref1']);
	$check_api=json_decode($check_api,true);
	if($check_api['status']!=1){
		$_SESSION["alert_content"]="Error : ".$check_api['msg'];
		$_SESSION["alert_type"]="alert-danger";
	}else{
		$_SESSION["id_pay"]=$check_api['id_pay'];
		header("Location: index.php");
	}
}else {
	$check_api=connect_api($url_api."?username=".$tmweasy['user']."&password=".$tmweasy['password']."&con_id=".$tmweasy["con_id"]);
	$check_api=json_decode($check_api,true);
	if($check_api['status']!=1){
		$_SESSION["alert_content"]="Error : ".$check_api['msg'];
		$_SESSION["alert_type"]="alert-danger";
	}
}

?>
<!DOCTYPE html>
<html lang="en" >
<head>
<meta charset="UTF-8">
 <meta name="viewport" content="width=device-width, initial-scale=1.0"> 
<title>เติมเงินด้วยทรูมันนี่วอเลท</title>
<script src="https://tmwallet.thaighost.net/alert_box/sweetalert.min.js"></script>
<link rel="stylesheet" href="https://tmwallet.thaighost.net/alert_box/sweetalert.css">
<script src="http://ajax.aspnetcdn.com/ajax/jQuery/jquery-1.9.1.min.js"></script>
<style>
@import url("https://fonts.googleapis.com/css2?family=Lato:wght@400;700&amp;display=swap");
* {
  box-sizing: border-box;
}

body {
  margin: 0;
  padding: 0;
  font-family: "Lato", sans-serif;
  font-size: 14px;
  color: #333333;
}

h1,
p {
  margin-top: 0;
}

h1 {
  margin-bottom: 10px;
}
h1 + p {
  color: #999999;
  margin-bottom: 30px;
}

p {
  margin-bottom: 20px;
}

a {
  color: #0086e4;
  text-decoration: none;
}

input[type=checkbox] {
  margin: 0;
  padding: 0;
  height: 17px;
}

.wrapper {
	
  min-height: 100vh;
  display: grid;
  place-items: center;
  background: #FF8000;
 background: linear-gradient(225deg, #FF4F4F 20%, #EC0000 50%,  #FF8040 30%);
}

.container {
  width: 100%;
  height: auto;
  max-width: 500px;
  min-width: 320px;
  background-color: white;
  margin-top:15px;
  margin-bottom:15px;
}

.flex-space-between {
  display: flex;
  justify-content: space-between;
}

.flex-align-center {
  display: flex;
  justify-content: center;
  gap: 5px;
}

button {
  cursor: pointer;
  background-color: #0086e4;
  color: white;
  border: none;
  font-weight: bold;
  text-transform: uppercase;
  border-radius: 6px;
  letter-spacing: 1px;
  width: 100%;
  height: 40px;
  transition: 300ms background-color ease-in-out;
}
button:hover {
  background-color: #18a0ff;
}

.input-group {
  margin-bottom: 20px;
  position: relative;
}
.input-group__label {
  display: block;
  position: absolute;
  top: 0;
  line-height: 40px;
  color: #aaa;
  left: 5px;
  padding: 0 5px;
  transition: line-height 200ms ease-in-out, font-size 200ms ease-in-out, top 200ms ease-in-out;
  pointer-events: none;
}
.input-group__input {
  width: 100%;
  height: 50px;
  border: 1px solid #dddddd;
  border-radius: 3px;
  padding: 0 10px;
  font-size: 18px;
}
.input-group__input:not(:-moz-placeholder-shown) + label {
  background-color: white;
  line-height: 10px;
  opacity: 1;
  font-size: 20px;
  top: -5px;
}
.input-group__input:not(:-ms-input-placeholder) + label {
  background-color: white;
  line-height: 10px;
  opacity: 1;
  font-size: 20px;
  top: -5px;
}
.input-group__input:not(:placeholder-shown) + label, .input-group__input:focus + label {
  background-color: white;
  line-height: 10px;
  opacity: 1;
  font-size: 15px;
  top: -5px;
}
.input-group__input:focus {
  outline: none;
  border: 1px solid #0086e4;
}
.input-group__input:focus + label {
  color: #0086e4;
}

.tabs {
  display: flex;
  flex-flow: row wrap;
}
.tabs__text {
  flex: 1;
  margin: 0;
  cursor: pointer;
  padding: 20px 30px;
  font-size: 1.2em;
  opacity: 0.5;
  background-color: #eeeeee;
  border-top: 3px solid #eeeeee;
  transition: border-top 300ms ease-out;
  transform-origin: top;
  text-transform: uppercase;
  font-weight: bold;
  text-align: center;
}
.tabs__content {
  display: none;
  flex: 1 1 100%;
  order: 99;
  padding: 5px 30px 30px 30px;
}
.tabs__button {
  visibility: hidden;
  height: 0;
  margin: 0;
  position: absolute;
}
.tabs__button:checked + .tabs__text {
  color: #0086e4;
  opacity: 1;
  background-color: white;
  border-top: 3px solid #0086e4;
}
.tabs__button:checked + .tabs__text + .tabs__content {
  display: block;
}
</style>

  <script>
  window.console = window.console || function(t) {};
</script>

  
  
  <script>
  if (document.location.search.match(/type=embed/gi)) {
    window.parent.postMessage("resize", "*");
  }
</script>
<script>
		
		function time_display(id_tag,time_s){
			min=pad(Math.floor(time_s/60),2,0);
			sec=pad(Math.abs((min*60) - time_s),2,0);
			if(time_s<=0){
				document.getElementById(id_tag).innerHTML="หมดเวลาโอนเงิน";
				document.getElementById("pay1").innerHTML="";
				document.getElementById("pay2").innerHTML="";
				document.getElementById("pay3").innerHTML="-";
				document.getElementById("pay4").innerHTML="-";
				document.getElementById("pay5").innerHTML="-";
			
			}else{
				document.getElementById(id_tag).innerHTML=min+" : "+sec;
			}
			
		}
		
		function time_down(){
			sec_start=sec_start-1;
			time_display("time_count_down",sec_start);
			if(sec_start>0){
				setTimeout(time_down, 1000);
			}
			
			
		}
		function pad(n, width , fill) {
			n = n + '';
			return n.length >= width ? n : new Array(width - n.length + 1).join(fill) + n;
		}
		
	</script>

</head>
<body >
<div class="wrapper">
<?php
if($_SESSION['alert_content']){
	alert_content($_SESSION["alert_content"],$_SESSION["alert_type"]);
}
if($_GET["action"]=="success"){
	$_SESSION['id_pay']="";
	?>
	 <div class="container" style="border-radius: 25px;
    border: 2px solid #FF8040;
    padding: 20px; 
   ">
		<div align="center"><img src="check_green.png" width="30%"></div>
		<h2 class="title" align="center">ทำรายการสำเร็จแล้ว</h2>
		<h3 class="title" align="center">ยอดเงิน <?=$_SESSION["amount"]?> ฿ ได้รับ <?=$_SESSION["amount"]*$database_set["truewallet"]["mul"]?> Credit</h3>
		<p class="label" align="center">ตรวจสอบเครดิตรของคุณ หากพบปัญหากรุณาติดต่อ Admin ขอบคุณครับ</p>
		<p class="label" align="center">[ ! ปิดหน้านี้ได้เลยครับ ]</p>
	</div>
 <?php
}else if($_SESSION['id_pay']){
	$check_api=connect_api($url_api."?username=".$tmweasy['user']."&password=".$tmweasy['password']."&con_id=".$tmweasy["con_id"]."&method=detail_pay&id_pay=".$_SESSION['id_pay']."&promptpay_id=".$truewallet["promptpay_ewall_id"]."&tmw_mobile=".$truewallet["mobile"]);
	$check_api=json_decode($check_api,true);
	if($check_api['status']!=1){
		$_SESSION['id_pay']="";
		$_SESSION["alert_content"]="Error : ".$check_api['msg'];
		$_SESSION["alert_type"]="alert-danger";
		header("Location: index.php");
	}
	$_SESSION['amount']=floor($check_api['amount_check']/100);
	?>
	

        <div class="container" style="border-radius: 25px;
    border: 2px solid #FF8040;
    padding: 20px; 
   ">
            <div class="tabs">
				<?php
				if($paytype["truewallet"]){
				?>
                <!-- truewallet -->
                <input type="radio" class="tabs__button" name="tmwform" id="p2p" checked />
                <label class="tabs__text" for="p2p">
				<img height="20" src="data:image/png;base64,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">
				<br>
				ทรูวอเลท</label>
                <div class="tabs__content">
                    
					<div align="center" id="pay1"><img width="98%" src="data:image/png;base64,<?=$check_api['qr_tmw_image_base64']?>"></div>
                </div>
				
				<?php
				}
				if($paytype["promptpay"]){
					if(!$paytype["truewallet"]){
						$check_form1="checked";
					}
				?>
				
                <!-- truemoney -->
                <input type="radio" class="tabs__button" name="tmwform" id="ppay" <?=$check_form1?>/>
                <label class="tabs__text" for="ppay">
				<img height="20" src=" data:image/png;base64,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">
				<br>
				พร้อมเพย์
				</label>
                <div class="tabs__content">
                    <div align="center" id="pay2"><img width="98%" src="data:image/png;base64,<?=$check_api['qr_image_base64']?>"></div>
                </div>
				<?php
				}
				?>
            </div>
			
				<div style="margin-left:8px;margin-right:8px">
					<h1 align="center" style="color:red" id="time_count_down"><b>--</b></h1>
					<div style="color:red">* โอนยอดให้ตรงกับจำนวนที่ปรากฏเท่านั้น !</div>
					<div style="color:red">* โอน - สแกน โดยแอปทรูวอเลทเท่านั้น !</div>
						 <h1 id="pay5"><b>ยอดที่ต้องชำระ : <i style="color:green"><?=$check_api['amount_check']/100?> ฿</i></b></h1>
						
						<h3>รายละเอียด โอนทรูวอเลท มาที่เบอร์ </h3>
						 <h1 id="pay3"><img height="20" src="data:image/png;base64,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">
				           <b><i><?=substr($truewallet["mobile"],0,3)." ".substr($truewallet["mobile"],3,3)." ".substr($truewallet["mobile"],6)?></i></b></h1>
						 <h2 id="pay4">ชื่อบัญชี : <?=$truewallet["name"]?></h2>
						 <hr>
						<h2>Ref1/ID : <?=$check_api['ref1']?></h2>
						
						 <br>
						<p><button onclick="window.location.href='?action=cancel'" style=" background-color: gray" >ยกเลิก - เริ่มโอนใหม่</button></p>
				</div>
			<br>
			<script>
				var sec_start=<?=$check_api['time_out']?>;
				setTimeout(time_down,0); 
				

				setInterval(function()
				{ 
					$.ajax({
					  url:"ajax_paid.php?id_pay=<?=$_SESSION['id_pay']?>",
					  datatype:"html",
					  success:function(data)
					  {
						 if(data=="ok"){
							 window.location="?action=success";
						 }
						  //do something with response data
					  }
					});
				}, 3000);
			</script>
		
    </div>
	<?php
	}else{//หน้าแรก
	?>
	<div class="container" style="border-radius: 25px;
    border: 2px solid #FF8040;
    padding: 20px; 
   ">
            <div class="tabs">
				
                <!-- truewallet -->
                <input type="radio" class="tabs__button" name="tmwform" id="p2p" checked />
                <label class="tabs__text" for="p2p">
				<img height="20" src=" data:image/png;base64,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">
				   
				<br>
				ชำระด้วย ทรูมันนี่ วอเลท สแกน QR</label>
                <div class="tabs__content">
                   
					
                    <form class="form" method="post">
                        <div class="input-group">
                            <input class="input-group__input" type="text" placeholder="" name="ref1" id="ref1"  value="<?=@$_GET["ref1"]?>" required />
                            <label class="input-group__label" for="ref1">Ref1/user/id</label>
                        </div>
						<div class="input-group">
						<?php
						if($select_amount==1){
							foreach($list_amount as $value){
								$list_select.="<option value='$value'>$value</option>";
							}
						?>
							 <select name="amount" class="input-group__input" >
							 <?=$list_select?>
							 </select>
							
						<?php
						}else{
						?>
						 
                            <input class="input-group__input" type="number" name="amount" placeholder="" id="amount"  required />
                           
                       
						<?php
						}
						?>
							<label class="input-group__label" for="amount" >จำนวนเงิน</label>
						 </div>
                        <button type="submit">เริ่มชำระเงิน</button>
                    </form>
                </div>
			</div>
		</div>
	<?php
	}
	?> 
	</div>
</body>
</html>
<!-- Script By tmweasy.com ศูนย์รวม Api เติมเงิน-->
