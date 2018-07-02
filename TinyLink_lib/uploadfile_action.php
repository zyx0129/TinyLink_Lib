<?php
   session_start();
   header("Content-type: text/html; charset=GB2312"); 
   include("scripts.php");
   //include("FileUploader.php");
   /*
   include("top.txt");
   include("scripts.php");*/
   
   /*foreach($_FILES['multi_upload']['name'] as $current){
		echo $current;
   }*/
   //echo "hello world";
   //$path = explode(',',I('post.paths'));//把传到后台的paths变成数组  
  // echo $path[0];
   //echo $_FILES['name'];
   //echo "hhhh";
   //foreach($_FILES as $keys => $value){
	   //checkUploads($_FILE);
	//$clickType=$_POST['clickType'];
	$module=$_POST['module'];
	$fct=$_POST['fct'];
	$device=$_POST['device'];
	$abstract=getabstract($fct);
	$ifreuse=$_POST['ifreuse'];
	$reuseFileName=$_POST['reuseFileName'];
	$submitBy=$_SESSION["username"];

		$paths=explode(",",$_POST['paths']);
		/*$fileUploader=new FileUploader($_FILES,$paths,$module,$fct,$device);
		$fileUploader->download();*/
	

	
		$fileName=$abstract."_New";
		$file_status="New";
		if($ifreuse==1){
			$fileName=$reuseFileName;
			$file_status="Reuse";
		}
		$h_File=$fileName.".h";
		$cpp_File=$fileName.".cpp";
		$config_dir=$module."/TinyLink";
		$h_path=$module."/".$h_File;
		$cpp_path=$module."/".$cpp_File;
		$divice_id_path=$config_dir."/"."TL_Device_ID.h";
		$config_path=$config_dir."/"."TL_Config.h";
		$libs_path=$config_dir."/"."TL_Libraries.h";
		$example_path=$module."/examples/TL_".$fct."/TL_".$fct.".ino";;

		$file_exist=array(0,0,0,0,0,0);

		foreach($_FILES as $key =>$currentFile){
			$pathTree=explode("/",$paths[$key]); 
			if($paths[$key]==$h_path){
				$file_exist[0]=1;
			}else if($paths[$key]==$cpp_path){
				$file_exist[1]=1;
			}else if($paths[$key]==$divice_id_path){
				$file_exist[2]=1;
			}else if($paths[$key]==$config_path){
				$file_exist[3]=1;
			}else if($paths[$key]==$libs_path){
				$file_exist[4]=1;
			}else if($paths[$key]==$example_path){
				$file_exist[5]=1;
			}else{
				
			}
		}
		foreach($file_exist as $key=>$value){
			if($value==0){
				echo "上传错误，请勿修改开发模板目录结构及文件名".$key;
				return -1;  
			}
		}
		
		foreach ($paths as $key => $currentPath){
				$pathTree=explode("/",$currentPath);
				$mydir=UPLOADEDFILES.$_SESSION['username'];
				foreach ($pathTree as $key1 => $currentCatalog){
					$mydir=$mydir."\\".$currentCatalog;
				}
				$mydir=substr($mydir,0,strrpos($mydir,"\\"));    //返回文件的目录
				//echo $mydir."\n";
				if(!is_dir($mydir)){
					mkdir($mydir,0700,true);
				}
				//echo sizeof($pathTree)."\n";
				$current_fileName=$_FILES[$key]['name'];
				$tmpName =$_FILES[$key]['tmp_name']; 
				$newName=$mydir."\\".$current_fileName;
				
				move_uploaded_file($tmpName, $newName);
			}

		//echo "文件上传成功，等待管理员审核";
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
			$sql="SELECT * FROM uploads_table WHERE module='$module' AND submitBy='$submitBy'";
			$result = $conn->query($sql);
			if ($result->num_rows > 0) {
				$sql="UPDATE uploads_table SET status='$status',checkedBy='$checkedBy',reason='' WHERE module='$module' AND submitBy='$submitBy'";
				if($conn->query($sql)){
					if($user=='ADMIN'){
						echo "文件更新成功";
					}else{
						echo "文件更新成功，等待管理员审核";
					}
				}else{
					echo "Error:".$conn->error;
				}
			}else{
				$sql="INSERT INTO uploads_table VALUES ('$module','$fct','$submitBy','$checkedBy','$status','$fileName','$device','')";
				if($conn->query($sql)){
					if($user=='ADMIN'){
						echo "文件上传成功";
					}else{
						echo "文件上传成功，等待管理员审核";
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