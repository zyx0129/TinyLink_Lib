<?php

   session_start();
   header("Content-type: text/html; charset=GB2312"); 
   include ("scripts.php");
   if($_SERVER["REQUEST_METHOD"] == "POST"){
	   $fct=$_POST['fct'];
	   $submitBy=$_POST['submitBy'];
	   $status=$_POST['status'];
	   $reason=mb_convert_encoding($_POST['reason'], "GB2312","UTF-8");
	   //echo $fct;
	   $servername = "localhost";
		$username = "root";
		$password = "";
		$dbname = "workflow";
		$conn = new mysqli($servername, $username, $password, $dbname);// 创建连接
		if ($conn->connect_error) { die(" [".date("Y h:i:s A")."] 数据库连接失败: " . $conn->connect_error);} // 检测连接
		else{
			$sql="UPDATE new_function_table SET status='".$status."',reason='".$reason."',checkedBy='".$_SESSION["username"]."' WHERE function='".$fct."' AND submitBy='".$submitBy."'";
			if($conn->query($sql)){
				echo "成功，审核结果已反馈给用户";
			}else{
				echo "Error:".$conn->error;
			}
		}
		$conn->close();
  }
	
		


?>