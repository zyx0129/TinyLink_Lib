<?php
   session_start();
   header("Content-type: text/html; charset=GB2312"); 
   include("scripts.php");
   include("FileUploader.php");
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
	$paths=explode(",",$_POST['paths']);
	$module=$_POST['module'];
	$fct=$_POST['fct'];
	$device=$_POST['device'];
	$fileUploader=new FileUploader($_FILES,$paths,$module,$fct,$device);
	$fileUploader->download();

	   //echo $path[0];
	   //echo strrpos("grove_button","/");
	   //echo substr("Hello world",6);
	   //echo substr("Hello world",0);
	   //echo substr("grove_button",0);
	  

	   //mkdir(UPLOADEDFILES.$_SESSION['username']."\\Grove_Button\\examples",0700,true);

	
	   
	   /*foreach($_FILES as $keys => $value)
   {
		echo "hhh\n";   
   }*/
   
   //echo $_POST["paths"][0];
   
   
   /*if(isset($_FILES['ufile']['name'])){
	   delete_file_info_json($_FILES['ufile']['name']);
       echo "Uploading: ".$_FILES['ufile']['name']."<br>";
 
       $tmpName = $_FILES['ufile']['tmp_name']; 
	   $mydir=UPLOADEDFILES.$_SESSION['username'];
	   
	   if(!is_dir($mydir)){
			mkdir($mydir);
	   }
	   $newName=$mydir."\\".$_FILES['ufile']['name'];
//	   $filename=iconv("ascii","gb2312",$filename);
//       $filen = mb_convert_encoding($filename, "GB2312", "UTF-8"); 
//	   echo $filen;
//       $newName = UPLOADEDFILES . $filename; 
	   //$filename=iconv("7","gb2312",$filename);
      
	   //$encode=mb_detect_encoding($newName, array("ASCII","UTF-8","GB2312","GBK","BIG5")); 
	   //echo $encode;
       if(!is_uploaded_file($tmpName)||!move_uploaded_file($tmpName, $newName)){
            echo "FAILED TO UPLOAD " . $_FILES['ufile']['name'] ."<br>Temporary Name: $tmpName <br>";
       } else {
		    //saveDocumentInfo($_FILES['ufile']);
			save_file_info_json($_FILES['ufile']);
			echo "File uploaded.  Thank you!";
			echo "<h3>Available Files</h3>";
            display_files();
	      
       } 
 
 
   } else {
     echo "You need to select a file.  Please try again.";
  }*/
   //include("bottom.txt");
?>