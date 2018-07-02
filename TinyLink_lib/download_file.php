<?php

   //include ("../WFDocument.php");
	include ("scripts.php");
 
    //$filetype = $_GET['filetype'];
    $file = $_GET['file'];
	download($file);
    //$filepath = UPLOADEDFILES.$file;
	

   //获取文件列表
   /*function list_dir($dir){
	  $result = array();
	  if (is_dir($dir)){
			$file_dir = scandir($dir);
			foreach($file_dir as $file){
				if ($file == '.' || $file == '..'){
				continue;
				}else if (is_dir($dir.$file)){
					$result = array_merge($result, list_dir($dir.$file.'/'));
				}else{
					array_push($result, $dir.$file);
				}
			}
	   }
	   return $result;
	}
	 
	//获取列表 
	$datalist=list_dir($filepath);*/
	//$filename = "./bak.zip"; //最终生成的文件名（含路径）
	//$filename = strstr($file,"\\").".zip"; //最终生成的文件名（含路径） 
	//createZip($filepath,$filename);
	/*if(!file_exists($filename)){  
	//重新生成文件  
	  $zip = new ZipArchive();//使用本类，linux需开启zlib，windows需取消php_zip.dll前的注释  
	  if ($zip->open($filename, ZIPARCHIVE::CREATE)!==TRUE) {  
		exit('文件创建失败');
	  }  
	  foreach( $datalist as $val){
		//echo $val."</br>";
		if(file_exists($val)){  
		  $zip->addFile( $val, basename($val));//第二个参数是放在压缩包中的文件名称，如果文件可能会有重复，就需要注意一下  
		}  
	  }  
	  $zip->close();//关闭  
	}  
	if(!file_exists($filename)){  
	  exit("无法找到文件"); //即使创建，仍有可能失败。。。。  
	}  */
	

   //$wfdocument = new WFDocument($filename, $filetype);
   //$wfdocument->download();

?>