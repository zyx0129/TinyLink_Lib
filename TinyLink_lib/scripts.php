<?php
header("Content-type: text/html; charset=gb2312"); 
function validate($allSubmitted)
{
    $message = "";

    $passwords = $allSubmitted["pword"];
    $firstPass = $passwords[0];
    $secondPass = $passwords[1];
    $username = $allSubmitted["name"];

    if ($firstPass != $secondPass) {
        $message = $message . "Passwords don't match<br />";
    }
    if (strlen($username) < 5 || strlen($username) > 50) {
        $message = $message . "Username must be \
            between 5 and 50 characters<br />";
    }

    if ($message == "") {
        $message = "OK";
    }

    return $message;

}

function checkPasswords($firstPass, $secondPass)
{

    if ($firstPass == $secondPass) {
        echo "<p>Passwords match. Thank you.</p>";
    } else {
        echo "<p>Passwords don't match. Please try again.</p>";
    }

}

 define("UPLOADEDFILES", "E:\TinyLink\毕设\文件上传\\");
 
 function saveDocumentInfo($fileInfo)
 {
	 $doc=new DOMDocument('1.0');
	 $xmlfile=UPLOADEDFILES."docinfo.xml";
	 if(is_file($xmlfile)){
		$doc->load($xmlfile);
		$root=$doc->getElementsByTagName("workflow")->item(0);
		//$statistics=$doc->getElementsByTagName("statistics")->item(0);
		//$total=$statistics->getAttribute("total");
		//$statistics->setAttribute("total",$total+1);
	 }else{
		
		 $root=$doc->createElement('workflow');
		 $doc->appendChild($root);
		 $statistics=$doc->createElement('statistics');
		 $statistics->setAttribute("total","1");
		 $statistics->setAttribute("approved","0");
		 $root->appendChild($statistics);
	 }
		 $filename=$fileInfo['name'];
		 $filesize=$fileInfo['size'];
		 $filetype=$fileInfo['type'];

		 $fileInfo=$doc->createElement('fileInfo');
		 $fileInfo->setAttribute("status", "pending");
		 $fileInfo->setAttribute("submittedBy", $_SESSION["username"]);
		 $root->appendChild($fileInfo);
		
		 $checkedBy=$doc->createElement('checkedBy');
		 $fileName=$doc->createElement('filename');
		 $fileNameText=$doc->createTextNode($filename);
		 $fileName->appendChild($fileNameText);
		 /*$loc=UPLOADEDFILES;
		 $encode=mb_detect_encoding( $loc , array("ASCII","UTF-8","GB2312","GBK","BIG5")); 
		 echo $encode;*/
		 $location = $doc->createElement("location");
		 $loc = mb_convert_encoding(UPLOADEDFILES.$_SESSION["username"]."\\", "UTF-8","GB2312"); 
		 //$encode=mb_detect_encoding( $loc , array("ASCII","UTF-8","GB2312","GBK","BIG5")); 
		 //echo $encode;
		 $locationText = $doc->createTextNode($loc);
		 $location->appendChild($locationText);
	 
		 $type = $doc->createElement("fileType");
		 $typeText = $doc->createTextNode($filetype);
		 $type->appendChild($typeText);
	 
		 $size = $doc->createElement("size");
		 $sizeText = $doc->createTextNode($filesize);
		 $size->appendChild($sizeText);
		 $fileInfo->appendChild($checkedBy);
		 $fileInfo->appendChild($fileName);
		 $fileInfo->appendChild($location);
		 $fileInfo->appendChild($type);
		 $fileInfo->appendChild($size);
		 
		 $statistics=$doc->getElementsByTagName("statistics")->item(0);
		 $total=$root->getElementsByTagName("fileInfo")->length;
		 $statistics->setAttribute("total",$total);

		 $doc->save($xmlfile);
 }
 
 function save_upload_info_json($filename)
 {
	 $jsonFile = UPLOADEDFILES."uploadinfo.json";
	 if (is_file($jsonFile)){
		$jsonText = file_get_contents($jsonFile);
		$workflow = json_decode($jsonText, true);
	 } else{
		$jsonText = '{"statistics": {"total": 0, "approved": 0}, "fileInfo":[]}';
		$workflow = json_decode($jsonText, true);
	 }
	 foreach($workflow["fileInfo"] as $key=>$thisFile){
		$name=$thisFile["fileName"];
		if($name==$filename&&$thisFile["submittedBy"]==$_SESSION["username"]){
			if($thisFile["status"]=="approved"){
				echo "您之前提交的".$filename."文件已被管理员审核通过，无法重复提交";
				return false;
			}else if($thisFile["status"]=="rejected"){
				$fileInfo["checkedBy"] = "";
				$thisFile["status"]=="pending";
				$jsonText = json_encode($workflow);
				file_put_contents($jsonFile, $jsonText);
				echo "文件更新成功，等待管理员审核";
				return true;
			}else{ 
				echo "文件更新成功，等待管理员审核";
				return true;
			}

			/*unset($workflow["fileInfo"][$key]);
			$jsonText = json_encode($workflow);
			file_put_contents($jsonFile, $jsonText);
			break;*/
		 }
	 }
	 //$filename=$file['name'];
	 //$filesize=$file['size'];
	 //$filetype=$file['type'];	 
     $fileInfo["submittedBy"] = $_SESSION["username"];
	 $status=getUserStatus();
     if($status=='ADMIN'){
		echo "文件上传成功";
		$fileInfo["checkedBy"] = $_SESSION["username"];
		$fileInfo["status"]="approved";
		$workflow["statistics"]["approved"]++;
	 }else{
		echo "文件上传成功，等待管理员审核";
		$fileInfo["checkedBy"] = "";
		$fileInfo["status"] = "pending";
	 }
     $fileInfo["fileName"] = mb_convert_encoding($filename, "UTF-8", "GB2312"); 
     $fileInfo["location"] = mb_convert_encoding(UPLOADEDFILES.$_SESSION["username"]."\\", "UTF-8","GB2312") ;
     //$fileInfo["fileType"] = $filetype;
     //$fileInfo["size"] = $filesize;
	 
    
	 array_push($workflow["fileInfo"], $fileInfo);
	 
	 $total =  count($workflow["fileInfo"]);
	 $workflow["statistics"]["total"] = $total;
     $jsonText = json_encode($workflow);
     file_put_contents($jsonFile, $jsonText);
	 return true;
 }

 /*function save_file_info_json($file)
 {
	 $jsonFile = UPLOADEDFILES."docinfo.json";
	 if (is_file($jsonFile)){
		$jsonText = file_get_contents($jsonFile);
		$workflow = json_decode($jsonText, true);
	 } else{
		$jsonText = '{"statistics": {"total": 0, "approved": 0}, "fileInfo":[]}';
		$workflow = json_decode($jsonText, true);
	 }
	 
	 $filename=$file['name'];
	 $filesize=$file['size'];
	 $filetype=$file['type'];	 
     $fileInfo["submittedBy"] = $_SESSION["username"];
	 $status=getUserStatus();
     if($status=='ADMIN'){
		$fileInfo["checkedBy"] = $_SESSION["username"];
		$fileInfo["status"]="approved";
		$workflow["statistics"]["approved"]++;
	 }else{
		$fileInfo["checkedBy"] = "";
		$fileInfo["status"] = "pending";
	 }
     $fileInfo["fileName"] = mb_convert_encoding($filename, "UTF-8", "GB2312"); 
     $fileInfo["location"] = mb_convert_encoding(UPLOADEDFILES.$_SESSION["username"]."\\", "UTF-8","GB2312") ;
     $fileInfo["fileType"] = $filetype;
     $fileInfo["size"] = $filesize;
	 
    
	 array_push($workflow["fileInfo"], $fileInfo);
	 
	 $total =  count($workflow["fileInfo"]);
	 $workflow["statistics"]["total"] = $total;
     $jsonText = json_encode($workflow);
     file_put_contents($jsonFile, $jsonText);
 }*/

 function delete_upload_info_json($name,$submitted){
	$jsonFile = UPLOADEDFILES."uploadinfo.json";
	 if (is_file($jsonFile)){
		$jsonText = file_get_contents($jsonFile);
		$workflow = json_decode($jsonText, true);
		foreach($workflow["fileInfo"] as $key=>$thisFile){
			$filename=$thisFile["fileName"];
			if($filename==$name&&$thisFile["submittedBy"]==$submitted){
				if($thisFile["status"]=="approved"){
					return false;
				}
				unset($workflow["fileInfo"][$key]);
				$jsonText = json_encode($workflow);
				file_put_contents($jsonFile, $jsonText);
				return true;
			}
		}
		/*for ($i = 0; $i < count($workflow["fileInfo"]); $i++){
			$thisFile = $workflow["fileInfo"][$i];
			$filename=$thisFile["fileName"];
			//$filename=mb_convert_encoding($thisFile["fileName"], "GB2312", "UTF-8"); 
			if($filename==$name&&$thisFile["submittedBy"]==$submitted){
				//echo "<script type='text/javascript'>alert('此文件已提交过，重复提交会覆盖原文件，是否继续提交？');</script>";
				//delete $workflow.fileInfo[$i];
				if($thisFile["status"]=="approved"){
					return false;
				}
				unset($workflow["fileInfo"][$i]);
				$jsonText = json_encode($workflow);
				file_put_contents($jsonFile, $jsonText);
				return true;
			}
		}*/
	 }
 }
 
 function display_pending_uploads()
 {
	  $servername = "localhost";
	 $username = "root";
	 $password = "";
	 $dbname = "workflow";
	 $error="";
	 
		 /*echo "共有文件:".$total;
		 echo "已审核通过库文件:".$approved;
		 echo "待审核文件".$pending;*/

		$name = $_SESSION["username"];
		$conn = new mysqli($servername, $username, $password, $dbname);// 创建连接
		if ($conn->connect_error) { die(" [".date("Y h:i:s A")."] 数据库连接失败: " . $conn->connect_error);} // 检测连接
		else{
			$sql="SELECT * from uploads_table WHERE status='pending'";
			$result = $conn->query($sql);
			if ($result->num_rows > 0) {
				 echo "<fieldset style='text-align: center;width:600px'>";
				 echo "<legend  style='font-size: 22px' >待审核文件 </legend>";
				 //echo "<form action='/TinyLink_lib/approved_action.php' method='POST'>";
				 echo "<table width=100% id='myUploadsTable' style='text-align: center;border-spacing:10px 5px'>";
				 echo "<tr><th>模块型号</th>";
				 echo "<th>模块功能</th>";
				 echo "<th>上传用户</th>";
				 echo "<th>审核结果</th></tr>";
				 while($row = $result->fetch_assoc()) {
					echo "<tr>";
					echo "<td><a href='/TinyLink_lib/download_file.php?file=".$row["submitBy"]."\\".$row['module']."'>".$row['module']."</a></td>";
					echo "<td>".$row['function']."</td>";
					echo "<td>".$row['submitBy']."</td>";
					echo "<td><select  onchange='checkSelect(event)'>
							<option value='Approve' >Approve</option> 
							<option value='Reject' >Reject</option> 
					      </select></td>";
					echo "<td><input type='text' placeholder='请输入不通过的原因' style='display:none'></td>";
					echo "<td><input type='button' value='确定' onclick='pass(event)' class='btn_uploads_pass'></td>";
					echo "</tr>";
				}
			echo "</table>";
			echo "</fieldset>";
			echo "<div>".$error."</div>";
			}
		}
		$conn->close();
 }

  function display_my_uploads()
 {
	 $servername = "localhost";
	 $username = "root";
	 $password = "";
	 $dbname = "workflow";
	 $error="";
	 
		 /*echo "共有文件:".$total;
		 echo "已审核通过库文件:".$approved;
		 echo "待审核文件".$pending;*/

		$name = $_SESSION["username"];
		$conn = new mysqli($servername, $username, $password, $dbname);// 创建连接
		if ($conn->connect_error) { die(" [".date("Y h:i:s A")."] 数据库连接失败: " . $conn->connect_error);} // 检测连接
		else{
			$sql="SELECT * from uploads_table WHERE submitBy='$name'";
			$result = $conn->query($sql);
			if ($result->num_rows > 0) {
				 echo "<fieldset style='text-align: center;width:500px'>";
				 echo "<legend  style='font-size: 22px' >我上传的文件 </legend>";
				 //echo "<form action='/TinyLink_lib/approved_action.php' method='POST'>";
				 echo "<table width=100% id='myUploadsTable' style='text-align: center;border-spacing:10px 5px'>";
				 echo "<tr><th>模块型号</th>";
				 echo "<th>模块功能</th>";
				 echo "<th>审核状态</th>";
				 echo "<th>审核人</th></tr>";
				 while($row = $result->fetch_assoc()) {
					if($row["checkedBy"]==''){
						$checkedBy="暂无";
					}else{
						$checkedBy=$row["checkedBy"];
					}
					echo "<tr>";
					echo "<td><a href='/TinyLink_lib/download_file.php?file=".$row["submitBy"]."\\".$row['module']."'>".$row['module']."</a></td>";
					echo "<td>".$row['function']."</td>";
					echo "<td>".$row['status']."</td>";
					echo "<td>".$checkedBy."</td>";
					echo "</tr>";
					if($row["status"]=="Reject"){
						$reason=$row["reason"];
						$error=$error."您提交的".$row['module']."模块审核未通过，原因：".$reason.",请修改后重新提交</br>";
					}
				}
			echo "</table>";
			echo "</fieldset>";
			echo "<div style='color:red'>".$error."</div>";
			}
		}
		$conn->close();
 }

 function display_pending_function()
 {		
	$servername = "localhost";
	$username = "root";
	$password = "";
	$dbname = "workflow";
	$name = $_SESSION["username"];
	$conn = new mysqli($servername, $username, $password, $dbname);// 创建连接
	if ($conn->connect_error) { die(" [".date("Y h:i:s A")."] 数据库连接失败: " . $conn->connect_error);} // 检测连接
    else{
		$sql="select * from new_function_table where status='pending'";
		$result=$conn->query($sql);
		if ($result->num_rows > 0) {
			echo "<fieldset style='text-align: center;width:600px'>";
			echo "<legend  style='font-size: 22px' >待审核功能 </legend>";
			 //echo "<form action='/TinyLink_lib/approved_action.php' method='POST'>";
			echo "<table style='text-align: center;border-spacing:10px 5px'>";
			echo "<tr><th>功能名称</th>";
			echo "<th>上传用户</th>";
			echo "<th>代表模块举例</th>";
			echo "<th>审核结果</th></tr>";
			while($row = $result->fetch_assoc()) {
				echo "<tr>";
				echo "<td onmouseover=\"wsug(event, '功能描述：".$row['description']."<br />应用场景：".$row['application']."')\" onmouseout=\"wsug(event, 0)\">".$row['function']."</td>";
				echo "<td>".$row['submitBy']."</td>";
				echo "<td><a href='".$row['link']."'>".$row['module']."</a></td>";
				echo "<td><select  onchange='checkSelect(event)'>
							<option value='Approve' >Approve</option> 
							<option value='Reject' >Reject</option> 
					      </select></td>";
				echo "<td><input type='text' placeholder='请输入不通过的原因' style='display:none'>";
				echo "<td><input type='button' value='确定' onclick='pass_function(event)' class='btn_function_pass'></td>";
				echo "</tr>";
			}
			echo "</table>";
			echo "</fieldset>";
		}
    }
	$conn->close();
 }

 function display_my_function()
 {		
	$servername = "localhost";
	$username = "root";
	$password = "";
	$dbname = "workflow";
	$name = $_SESSION["username"];
	$error="";
	$conn = new mysqli($servername, $username, $password, $dbname);// 创建连接
	if ($conn->connect_error) { die(" [".date("Y h:i:s A")."] 数据库连接失败: " . $conn->connect_error);} // 检测连接
    else{
		$sql="SELECT * from new_function_table WHERE submitBy='".$_SESSION["username"]."'";
		$result=$conn->query($sql);
		if ($result->num_rows > 0) {
			echo "<fieldset style='text-align: center;width:400px'>";
			echo "<legend  style='font-size: 22px' >我申请的新功能 </legend>";
			 //echo "<form action='/TinyLink_lib/approved_action.php' method='POST'>";
			echo "<table width=100% id='myUploadsTable' style='text-align: center;border-spacing:10px 5px'>";
			echo "<tr><th>功能名称</th>";
			echo "<th>审核状态</th>";
			echo "<th>审核人</th></tr>";
			//echo "<th>审核结果</th></tr>";
			while($row = $result->fetch_assoc()) {
				echo "<tr>";
				echo "<td onmouseover=\"wsug(event, '功能描述：".$row['description']."<br />应用场景：".$row['application']."')\" onmouseout=\"wsug(event, 0)\">".$row['function']."</td>";
				echo "<td>".$row['status']."</td>";
				if($row["checkedBy"]==''){
					$checkedBy="暂无";
				}else{
					$checkedBy=$row["checkedBy"];
				}
				echo "<td>".$checkedBy."</td>";
				if($row["status"]=="Reject"){
					//$reason=mb_convert_encoding($row["reason"], "GB2312", "UTF-8");
					$reason=$row["reason"];
					$error=$error."您申请的".$row['function']."功能审核未通过，原因：".$reason.",请修改后重新提交</br>";
				}
				echo "</tr>";
			}
			echo "</table>";
			echo "</fieldset>";
			echo "<div style='color:red'>".$error."</div>";
		}
    }
	$conn->close();
 }

 function display_tinylink_libs($filename)
{
	echo "<fieldset style='text-align: left'>";
	echo "<legend  style='font-size: 22px' >$filename </legend>";
	echo "<table width='100%'>";
	$dir=UPLOADEDFILES.$filename;
	if (is_dir($dir)){
		if ($dh = opendir($dir)){
			while (($file = readdir($dh)) !== false){
				if($file != "." && $file != ".."){
					$subdir=$dir."\\".$file;
					$dirname=$filename."\\".$file;
					if (is_dir($subdir)){
						echo "<tr><td><a href='/TinyLink_lib/loggedin/filetree.php?dir=".$dirname."'>".$file."</a></td></tr>";
					}else{
						echo  "<tr><td><a href='/TinyLink_lib/loggedin/display_contents.php?dir=".$dirname."'>".$file . "</a></td></tr>";
					}
				}
			}
			closedir($dh);
		}
	}
	echo "</table>";
	echo "</fieldset>";
}

function download($file){
	$dir = UPLOADEDFILES.$file;
	$fileName= basename($file);
	$newName = $fileName.".zip"; //最终生成的文件名（含路径）
	//echo  $newName;
	$zip = new ZipArchive();//使用本类，linux需开启zlib，windows需取消php_zip.dll前的注释  
	if ($zip->open($newName, ZIPARCHIVE::CREATE)!==TRUE) {  
		exit('文件创建失败');
	}
	$zip->addEmptyDir($fileName); //这里注意：php只需传递一个文件夹名称路径即可 
	createZip($dir,$zip,$fileName);
	$zip->close();//关闭  
	
	if(!file_exists($newName)){  
	  exit("无法找到文件"); //即使创建，仍有可能失败。。。。  
	}  
	
	header("Cache-Control: public"); 
	header("Content-Description: File Transfer"); 
	header('Content-disposition: attachment; filename='.$newName); //文件名  
	header("Content-Type: application/zip"); //zip格式的  
	header("Content-Transfer-Encoding: binary"); //告诉浏览器，这是二进制文件  
	header('Content-Length: '. filesize($newName)); //告诉浏览器，文件大小  
	@readfile($newName);
	unlink($newName);
}

function createZip($dir,$zip,$newRelat = '')  
{  
	if ($dh = opendir($dir)){
		while(($file = readdir($dh)) != false)  
		{  
			if($file=="." || $file=="..")  
				continue;  
			  
			/*源目录路径(绝对路径)*/  
			$sourceTemp = $dir."\\".$file;  
			/*目标目录路径(相对路径)*/  
			$newTemp = $newRelat==''?$file:$newRelat.'\\'.$file;  
			//$newTemp=$newRelat.'\\'.$file;
			if(is_dir($sourceTemp))  
			{  
				//echo '创建'.$newTemp.'文件夹<br/>';  
				//echo $sourceTemp;
				$zip->addEmptyDir($newTemp);/*这里注意：php只需传递一个文件夹名称路径即可*/  
				createZip($sourceTemp,$zip,$newTemp);  
			}
			if(is_file($sourceTemp))  
			{  
				//echo $sourceTemp;
				//echo '创建'.$newTemp.'文件<br/>';  
				$zip->addFile($sourceTemp,$newTemp);  
			}  
		}
		closedir($dh);
	}
	
}  

 function display_contents($filename)
{
	echo $filename;
	$dir=UPLOADEDFILES.$filename;
	$fileContent=file_get_contents($dir);
	echo "<textarea id='txt' name='filecontent' readOnly='true' style='width:100%;height:200px'>$fileContent</textarea>";
	
}


 /*function display_files()
{

     $workflow = json_decode(file_get_contents(UPLOADEDFILES."docinfo.json"), true);
	 $total=$workflow["statistics"]["total"];
	 $approved=$workflow["statistics"]["approved"];
	 $pending=$total-$approved;
	 //echo "共有文件:".$total;
	 //echo "已审核通过库文件:".$approved;
	 //echo "待审核文件".$pending;
	 $status=getUserStatus();
     if($status=='ADMIN'){
		 echo "<fieldset style='text-align: center'>";
		 echo "<legend  style='font-size: 22px' >Pending </legend>";
		 echo "<form action='/TinyLink_lib/approved_action.php' method='POST'>";
		 echo "<table width='100%'>";
		 echo "<tr><th>File Name</th>";
		 echo "<th>Submitted By</th><th>Size</th>";
		 echo "<th>Approve</th></tr>";
		 for ($i = 0; $i < count($workflow["fileInfo"]); $i++){
			$thisFile = $workflow["fileInfo"][$i];
			$filename=mb_convert_encoding($thisFile["fileName"], "GB2312", "UTF-8"); 
			if($thisFile["status"]=='pending'){
				echo "<tr>";
				echo "<td><a href='/TinyLink_lib/loggedin/download_file.php?file=" .$thisFile["submittedBy"]."\\". $filename ."&filetype=" . $thisFile["fileType"] . "'>" .$filename . "</a></td>";
				echo "<td>".$thisFile["submittedBy"]."</td>";
				echo "<td>".$thisFile["size"]."</td>";
				echo "<td><input type='checkbox' name='toapprove[]' value='".$i."' checked='checked' /></td>";
				echo "</tr>";
			}
		}
		echo "</table>";
		echo "<input type='submit' value='Approve Checked Files' />";
        echo "</form>";
		echo "</fieldset>";
	 }
	 else if($status=='USER'){
		echo "<fieldset style='text-align: center'>";
		echo "<legend  style='font-size: 22px' >My Submitted </legend>";
		echo "<table width='100%'>";
		//$files = $workflow["fileInfo"];
	 
		echo "<tr><th>File Name</th>";
		echo "<th>Size</th>";
		echo "<th>Status</th>";
		echo "<th>Approved By</th></tr>";
 
		for ($i = 0; $i < count($workflow["fileInfo"]); $i++){
			$thisFile = $workflow["fileInfo"][$i];
			$filename=mb_convert_encoding($thisFile["fileName"], "GB2312", "UTF-8"); 
			if((isset($_SESSION["username"])&&$thisFile["submittedBy"]==$_SESSION["username"])){
				echo "<tr>";
				echo "<td><a href='/TinyLink_lib/loggedin/download_file.php?file=" .$thisFile["submittedBy"]."\\". $filename ."&filetype=" . $thisFile["fileType"] . "'>" . $filename . "</a></td>";
				echo "<td>".$thisFile["size"]."</td>";
				echo "<td>".$thisFile["status"]."</td>";
				echo "<td>".$thisFile["checkedBy"]."</td>";
				echo "</tr>";
			}
		 }
		 echo "</table>";
		 echo "</fieldset>";
	 }
     echo "<fieldset style='text-align: center'>";
	 echo "<legend  style='font-size: 22px' >TinyLink-Lib </legend>";
	 echo "<table width='100%'>";
     //$files = $workflow["fileInfo"];
	 
     echo "<tr><th>File Name</th>";
	 echo "<th>Size</th>";
     echo "<th>Approved By</th></tr>";
 
     for ($i = 0; $i < count($workflow["fileInfo"]); $i++){
        $thisFile = $workflow["fileInfo"][$i];
		$filename=mb_convert_encoding($thisFile["fileName"], "GB2312", "UTF-8"); 
		if($thisFile["checkedBy"]!=null){
			echo "<tr>";
			echo "<td><a href='/TinyLink_lib/loggedin/download_file.php?file=" . $filename ."&filetype=" . $thisFile["fileType"] . "'>" . $filename . "</a></td>";
			echo "<td>".$thisFile["size"]."</td>";
			echo "<td>".$thisFile["checkedBy"]."</td>";
			echo "</tr>";
		}
     }
	 echo "</table>";
	 echo "</fieldset>";
}*/

function getUserStatus()
{
	$servername = "localhost";
	$username = "root";
	$password = "";
	$dbname = "workflow";
	$name = $_SESSION["username"];
	$conn = new mysqli($servername, $username, $password, $dbname);// 创建连接
	if ($conn->connect_error) { die(" [".date("Y h:i:s A")."] 数据库连接失败: " . $conn->connect_error);} // 检测连接
    else{
		$sql="select status from users where username='$name'";
		$result=$conn->query($sql);
		$row =$result->fetch_assoc();
		$status = $row["status"];
    }
	$conn->close();
	return $status;
}

function getModuleType($abstract)
{
	$servername = "localhost";
	$username = "root";
	$password = "";
	$dbname = "workflow";
	$conn = new mysqli($servername, $username, $password, $dbname);// 创建连接
	if ($conn->connect_error) { die(" [".date("Y h:i:s A")."] 数据库连接失败: " . $conn->connect_error);} // 检测连接
    else{
		$sql="select mainclass from abstract_table where abstract='$abstract'";
		$result=$conn->query($sql);
		$row =$result->fetch_assoc();
		$module_type = $row["mainclass"];
    }
	$conn->close();
	return $module_type;
}
function getabstract($fct){
	$servername = "localhost";
	$username = "root";
	$password = "";
	$dbname = "workflow";
	$conn = new mysqli($servername, $username, $password, $dbname);// 创建连接
	if ($conn->connect_error) { die(" [".date("Y h:i:s A")."] 数据库连接失败: " . $conn->connect_error);} // 检测连接
    else{
		$sql="select abstract from function_table where function='$fct'";
		$result=$conn->query($sql);
		$row =$result->fetch_assoc();
		$module_type = $row["abstract"];
    }
	$conn->close();
	return $module_type;
}


/*function checkFile($fileNumber,$status,$reason){
 
    $workflow = json_decode(file_get_contents(UPLOADEDFILES."uploadinfo.json"), true);
	//$workflow["statistics"]["approved"]++;
    $workflow["fileInfo"][$fileNumber]["checkedBy"] = $_SESSION["username"];
    $workflow["fileInfo"][$fileNumber]["status"] = $status;
	if($status="Reject"){
		 $workflow["fileInfo"][$fileNumber]["reason"] = mb_convert_encoding($reason, "UTF-8","GB2312");
	}
    $jsonText = json_encode($workflow);
    file_put_contents(UPLOADEDFILES . "uploadinfo.json", $jsonText);
 
}*/



/*function checkUploads($uploads)
{
	$allowedExts = array("h", "cpp", "txt");    
	$allowedPlat=array("Arduino","LintIT","RPI","BBB");

	foreach($uploads as $key =>$currentFile){
		$temp = explode(".", $currentFile["name"]);
		if(count($temp)>2) return -1; 
		$extension = end($temp);     // 获取文件后缀名
		$filename=$temp[0];
		if(!in_array($extension, $allowedExts)) return -2;   //非法后缀
		$nameParts=explode("_",$filename);
		if(count($nameParts)!=3) return -3;
		$platName=end($nameParts);
		if(!in_array($platName,$allowedPlat)) return -4;   
		//if()
	}
}*/
?>