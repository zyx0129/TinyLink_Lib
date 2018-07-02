<?php
   session_start();
   header("Content-type: text/html; charset=GB2312"); 
   include("scripts.php");
   include("FileUploader.php");
 
	$module=$_POST['module'];
	$fct=$_POST['fct'];
	$device=$_POST['device'];
	$abstract=getabstract($fct);
	$ifreuse=$_POST['ifreuse'];
	$reuseFileName=$_POST['reuseFileName'];
	$module_type=getModuleType($abstract);
	
		$fileName=$abstract."_New";
		$file_status="New";
		if($ifreuse==1){
			$fileName=$reuseFileName;
			$file_status="Reuse";
		}
		$h_File=$fileName.".h";
		$cpp_File=$fileName.".cpp";
		$workDir=UPLOADEDFILES."Template\\".$_SESSION['username']."\\".$file_status."\\".$device."\\".$module;  //生成模板的路径
		$example_dir=$workDir."\\examples";
		$test_dir=$example_dir."\\TL_".$fct;
		$config_dir=$workDir."\\TinyLink";
		$template_dir=UPLOADEDFILES."Template_Original\\".$device;
		$config_template_dir=UPLOADEDFILES."Template_Original\\TinyLink";
		$libraries_file=$config_dir."\\TL_Libraries.h";
		$deviceID_file=$config_dir."\\TL_Device_ID.h";
		$config_file=$config_dir."\\TL_Config.h";
		$test_file=$test_dir."\\TL_".$fct.".ino";

		$tinylink_lib=UPLOADEDFILES."TinyLink\\".$device."\\".$module_type;
		$abstract_file=$config_dir."\\".$abstract.".h";
		$pin=$fct."_Pin";
		$board_id="";
		$platform_id="";
		$object="TL_".$fct;
		switch($device){
			case "Arduino_Uno":
				$board_id="1002";
				$platform_id="1";
				break;
			default:
				break;
		}
		if(is_dir($example_dir)){  //测试程序目录重命名
			 $dh=opendir($example_dir);
			 while ($last_file=readdir($dh)) {
				 if($last_file!="." && $last_file!="..") {
					$fullpath=$example_dir."\\".$last_file;
					$dh2=opendir($fullpath);
					while ($lasttest_file=readdir($dh2)) {
						if($lasttest_file!="." && $lasttest_file!="..") {
							$testpath=$fullpath."\\".$lasttest_file;
							unlink($testpath);
						}
					}
					closedir($dh2);
					rmdir($fullpath);
					//rename($fullpath,$test_dir);
				 } 
			 }
			 rmdir($example_dir);
			 closedir($dh);
		}
		if(!is_dir($test_dir)){
			mkdir($test_dir,0700,true);
		}
		if(!is_dir($config_dir)){
			mkdir($config_dir,0700,true);
		}
		if($ifreuse){
			//$module_type=getModuleType($abstract);
			$reuseFile=$tinylink_lib."\\"."libraries\\".$abstract."\\".$fileName;
			copy($reuseFile."\\".$fileName.".h",$workDir."\\".$h_File);
			copy($reuseFile."\\".$fileName.".cpp",$workDir."\\".$cpp_File);
			//copy($reuseFile."\\".$fileName.".h",$workDir."\\".$cpp_File);
		}else{
			copy($template_dir."\\".$abstract."\\".$h_File,$workDir."\\".$h_File);
			copy($template_dir."\\".$abstract."\\".$cpp_File,$workDir."\\".$cpp_File);
		}
		copy($config_template_dir."\\TL_Libraries.h",$libraries_file);
		$str=file_get_contents($libraries_file);
		$str=str_replace("FUNCTION",strtoupper($fct),$str); 
		$str=str_replace("MODULE",strtoupper($module),$str);
		$str=str_replace("CLASS_NAME",$fileName,$str);
		$str=str_replace("OBJECT",$object,$str);
		$str=str_replace("PIN",strtoupper($pin),$str);
		file_put_contents($libraries_file,$str);
		
		copy($config_template_dir."\\TL_Config.h",$config_file);
		$str=file_get_contents($config_file);
		$str=str_replace("MODULE",strtoupper($module),$str);  
		$str=str_replace("BOARD_ID",$board_id,$str);  
		$str=str_replace("PLATFORM_ID",$platform_id,$str);  
		$str=str_replace("FUNCTION",strtoupper($fct),$str);
		$str=str_replace("PIN",strtoupper($pin),$str);
		file_put_contents($config_file,$str);
		
		copy($config_template_dir."\\TL_Device_ID.h",$deviceID_file);
		$str=file_get_contents($deviceID_file);
		$str=str_replace("MODULE",strtoupper($module),$str);
		$str=str_replace("DEVICE_ID","8888",$str);
		file_put_contents($deviceID_file,$str);

		copy($tinylink_lib."\\Abstract\\".$abstract.".h",$abstract_file);

		copy($template_dir."\\".$abstract."\\examples\\TL_Function\\TL_Function.ino",$test_file);
		$str=file_get_contents($test_file);
		$str=str_replace("Function",$fct,$str);
		file_put_contents($test_file,$str);
		
		//download("Template\\".$_SESSION['username']."\\".$file_status."\\".$device."\\".$fileName);
		echo "<a href='/TinyLink_lib/download_file.php?file=Template\\".$_SESSION['username']."\\".$file_status."\\".$device."\\".$module."'>模板生成成功，请点击下载</a>";
		
		
?>