<?php
   session_start();
   require_once("scripts.php");
?>
<?php
	$servername = "localhost";
	$username = "root";
	$password = "";
	$dbname = "workflow";

	$name=$_POST["username"];
	$pwd=$_POST["password"];
	include_once("top.txt");
	$conn=new mysqli($servername, $username, $password, $dbname);// 创建连接
	if ($conn->connect_error) { die(" [".date("Y h:i:s A")."] 数据库连接失败: " . $conn->connect_error);} // 检测连接
    else{
		$sql="select * from users where username='$name' and password='$pwd'";
		$result=$conn->query($sql);
		if($result->num_rows>0){
			$row = $result->fetch_assoc();
			$_SESSION["username"] =$row['username'];
			$_SESSION["email"] = $row["email"];
			echo "You are logged in.  Thank you!";
		}
		else{
			echo "There is no user account with that username and password.";
		}
	}
	$conn->close();
?>

<?php

   include("bottom.txt");

?>