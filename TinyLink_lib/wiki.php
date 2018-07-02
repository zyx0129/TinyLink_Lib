<?php
    session_start();
	header("Content-type: text/html; charset=GB2312"); 
	if(!isset($_SESSION["username"])){
		header("Location: /TinyLink_lib/login.php");
	}
	include("top.txt");
	include ("scripts.php");
?>

<style type="text/css">
	table
	{
		border-collapse:collapse;
	}
	table, th, td
	{
		border: 1px solid #98bf21;
		padding:0px 5px 0px 5px;
	}
	table th{
		padding:5px 7px 5px 7px;
		background-color:#A7C942;
		color:#ffffff;
	}

	td.subclass{
		width:50px;
	}
	
</style>
 
	<!--p>You can add files to the system for review by an administrator.
	Click <b>Browse</b> to select the file you'd like to upload, 
	and then click <b>Upload</b>.</p-->
	 
	<!--form action="uploadfile_action.php" method="POST" enctype="multipart/form-data"-->
	
    <!--input type="file" name="ufile" \-->
	<table width="740px"  style='text-align:left'>
		<tr>
			<th>功能所在大类</th>
			<th class='subclass'>功能所在小类</th>
			<th>功能名称</th>
			<th>功能描述</th>
			<!--th>继承模板</th-->
			<!--th>模板介绍</th-->
		</tr>
		<?php 
			$servername = "localhost";
			$username = "root";
			$password = "";
			$dbname = "workflow";
			$mainclass="";
			$subclass="";
			$conn = new mysqli($servername, $username, $password, $dbname);// 创建连接
			if ($conn->connect_error) { die(" [".date("Y h:i:s A")."] 数据库连接失败: " . $conn->connect_error);} // 检测连接
			else {		
				 //$sql = "SELECT function_table.* FROM function_table,abstract_table where function_table.abstract=abstract_table.abstract ORDER BY weight";
				 $sql = "SELECT * FROM function_view ORDER BY id";
				 $result = $conn->query($sql);
				 if ($result->num_rows > 0) {
					while($row = $result->fetch_assoc()) {
						echo "<tr>";
						if($mainclass!=$row['mainclass']){
							$mainclass=$row['mainclass'];
							$sql="SELECT COUNT(*) FROM function_view WHERE mainclass='$mainclass'";
							$result1 = $conn->query($sql);
							$num=$result1->fetch_array();
							echo "<td rowspan='".$num[0]."'>".$mainclass."</td>";
						}
						if($subclass!=$row['subclass']){
							$subclass=$row['subclass'];
							$sql="SELECT COUNT(*) FROM function_view WHERE subclass='$subclass'";
							$result1 = $conn->query($sql);
							$num=$result1->fetch_array();
							echo "<td class='subclass' rowspan='".$num[0]."'>".$subclass."</br>".$row['subdes']."</td>";
						}
						echo "<td>".$row['fct']."</td>";
						echo "<td>".$row['description']."</td>";
						
						echo "</tr>";
					}
				}
			}
			$conn->close();
		 ?>
	</table>

<script type="text/javascript">

</script>


<?php
   include("bottom.txt");
?>