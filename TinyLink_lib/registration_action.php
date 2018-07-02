<?php
	
   include_once("top.txt");
   require_once("scripts.php");
?>
<p>You entered:</p>
<?php
	$servername = "localhost";
	$username = "root";
	$password = "";
	$dbname = "workflow";
	foreach ($_POST as $key => $value) {
		echo "<p>" . $key . " = " . $value . "</p>";
	}

$passwords = $_POST["pword"];
echo "First password = " . $passwords[0];
echo "<br />";
echo "Second password = " . $passwords[1];

if (validate($_POST) == "OK") {
	$conn = new mysqli($servername, $username, $password, $dbname);// 创建连接
	if ($conn->connect_error) { die(" [".date("Y h:i:s A")."] 数据库连接失败: " . $conn->connect_error);} // 检测连接
    else{
		$name = $_POST["name"];
		$sql="select * from users where username ='$name'";
		$result=$conn->query($sql);
		if ($result->num_rows==0) {
			$name = $_POST["name"];
			$email = $_POST["email"];
			$pword = $passwords[0];
			$sql="insert into users (username, email, password) values ('"."$name',"."'$email',"."'$pword')";
			$conn->query($sql);

			echo "<p>Thank you for registering!</p>";
		 } else {
			echo "<p>There is already a user with that name: </p>";
		 }
    }
	$conn->close();
} else {
    echo "<p>There was a problem with your registration:</p>";
    echo validate($_POST);
    echo "<p>Please try again.</p>";
}

?>

<?php

   include("bottom.txt");

?>