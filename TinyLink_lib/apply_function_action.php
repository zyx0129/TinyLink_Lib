<?php
   date_default_timezone_set('prc');
   session_start();
   
   header("Content-type: text/html; charset=GB2312"); 
   include("scripts.php");
   include("FileUploader.php");
 
	$newfct=$_POST['newfct'];
	$description=mb_convert_encoding($_POST['description'], "GB2312","UTF-8");
	$application=mb_convert_encoding($_POST['application'], "GB2312","UTF-8");
	//$description=$_POST['description'];
	//$application=$_POST['application'];
	$new_module=$_POST['new_module'];
	$module_link=$_POST['module_link'];
	$submitBy=$_SESSION["username"];
	$apply_time=date('y-m-d H:i:m',time());
	$status="pending";
	$user=getUserStatus();
	if($user=='ADMIN'){
		$status="approved";
		$checkedBy=$_SESSION["username"];
	}else{
		$status="pending";
		$checkedBy="";
	}


	$servername = "localhost";
	$username = "root";
	$password = "";
	$dbname = "workflow";
	$conn = new mysqli($servername, $username, $password, $dbname);// 创建连接
	if ($conn->connect_error) { die(" [".date("Y h:i:s A")."] 数据库连接失败: " . $conn->connect_error);} // 检测连接
    else{
		$sql="SELECT * FROM new_function_table WHERE function='$newfct' AND submitBy='$submitBy'";
		$result = $conn->query($sql);
			if ($result->num_rows > 0) {
				$sql="UPDATE new_function_table SET status='$status',checkedBy='$checkedBy',reason='',description='$description',application='$application',module='$new_module' WHERE function='$newfct' AND submitBy='$submitBy'";
				if($conn->query($sql)){
					if($user=='ADMIN'){
						echo "新功能更新成功";
					}else{
						echo "新功能更新成功，等待管理员审核";
					}
				}else{
					echo "Error:".$conn->error;
				}
			}else{
				$sql="INSERT INTO new_function_table VALUES ('$newfct','$description','$application','$apply_time','$new_module','$module_link','$submitBy','$status','','')";
				if($conn->query($sql)){
					if($user=='ADMIN'){
						echo "新功能上传成功";
					}else{
						echo "申请已提交，等待管理员审核";
					}
				}else{
					echo "Error:".$conn->error;
				}
			}
    }
	$conn->close();
	echo "#";
	display_my_uploads();
	display_my_function();
?>