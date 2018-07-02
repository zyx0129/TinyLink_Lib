<?php

//include_once("scripts.txt");

class FileUploader
{
	public $uploads;
	public $paths; 
	public $deviceModule;
	public $deviceFunction;
	public $deviceName;
	
	public $libFileName;
	public $objectName;
	public $folder;

	function __construct($_uploads,$_paths,$_deviceModule,$_deviceFunction,$_deviceName)
    {
        $this->uploads=$_uploads;
		$this->paths=$_paths;
		$this->deviceModule=$_deviceModule;
		$this->deviceFunction=$_deviceFunction;
		$this->deviceName=$_deviceName;
		$this->libFileName=$_deviceModule."_".$_deviceFunction."_".$_deviceName;
		$this->objectName="TL_".$this->deviceFunction;
		$temp=explode("/",$this->paths[0]);
		$this->folder=$temp[0];
    }

	private function checkUploads()
	{
		if($this->folder!=$this->deviceModule){
			echo "提交文件夹首层目录名应为模块名";
			return -1;  
		}
		foreach($this->uploads as $key =>$currentFile){
			$pathTree=explode("/",$this->paths[$key]); 
			$temp = explode(".", $currentFile["name"]);
			if(count($temp)!=2){
				echo "文件名不能包含\".\"";
				return -2;  
			}
			$extension = end($temp);     // 获取文件后缀名
			$filename=$temp[0];
			if(count($pathTree)==2){     //第二层目录下的应为库文件
				if($filename!=$this->libFileName) {
					echo "驱动文件命名错误";
					return -3; //驱动文件名错误
				}
				if($extension=="h"){
					
				}else if($extension=="cpp"){
				
				}else{
					echo "非法的文件后缀，第二层目录下只能包含驱动文件";
					return -6; //文件类型错误
				}
 			}else if(count($pathTree)==4){
				if($pathTree[1]=="examples"){
					if($pathTree[2]!=$this->objectName) {
						echo "示例程序文件夹命名错误";
						return -7;
					}
					if($filename!=$this->objectName) {
						echo "示例程序文件命名错误";
						return -8;
					}
					if($extension!="ino") {
						echo "示例程序文件后缀错误";
						return -9;
					}
				}
			}else{
				echo "目录结构错误";
				return -10;
			}
		}
		return 1;
	}	
	
	function download()
	{	
		if($this->checkUploads()==1){
			//$okNum=0;
			//$fileNum=sizeof($this->paths);
			foreach ($this->paths as $key => $currentPath){
				$pathTree=explode("/",$currentPath);
				if($pathTree[0])
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
				$fileName=$this->uploads[$key]['name'];
				$tmpName =$this->uploads[$key]['tmp_name']; 
				$newName=$mydir."\\".$fileName;
				//echo $fileName."\n";
				//echo $tmpName."\n";
				move_uploaded_file($tmpName, $newName);
			}
			save_upload_info_json($this->folder);
			echo ",";
			display_my_uploads();
			/*	if(move_uploaded_file($tmpName, $newName)){
					//$okNum++;
				}else{
					echo "上传失败";
					break;
				}
		   }
		   if($okNum==$fileNum){
			  
			   //delete_upload_info_json($this->folder);
			   save_upload_info_json($this->folder);
		   }*/
		}
	}
}
 

?>