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
			echo "�ύ�ļ����ײ�Ŀ¼��ӦΪģ����";
			return -1;  
		}
		foreach($this->uploads as $key =>$currentFile){
			$pathTree=explode("/",$this->paths[$key]); 
			$temp = explode(".", $currentFile["name"]);
			if(count($temp)!=2){
				echo "�ļ������ܰ���\".\"";
				return -2;  
			}
			$extension = end($temp);     // ��ȡ�ļ���׺��
			$filename=$temp[0];
			if(count($pathTree)==2){     //�ڶ���Ŀ¼�µ�ӦΪ���ļ�
				if($filename!=$this->libFileName) {
					echo "�����ļ���������";
					return -3; //�����ļ�������
				}
				if($extension=="h"){
					
				}else if($extension=="cpp"){
				
				}else{
					echo "�Ƿ����ļ���׺���ڶ���Ŀ¼��ֻ�ܰ��������ļ�";
					return -6; //�ļ����ʹ���
				}
 			}else if(count($pathTree)==4){
				if($pathTree[1]=="examples"){
					if($pathTree[2]!=$this->objectName) {
						echo "ʾ�������ļ�����������";
						return -7;
					}
					if($filename!=$this->objectName) {
						echo "ʾ�������ļ���������";
						return -8;
					}
					if($extension!="ino") {
						echo "ʾ�������ļ���׺����";
						return -9;
					}
				}
			}else{
				echo "Ŀ¼�ṹ����";
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
				$mydir=substr($mydir,0,strrpos($mydir,"\\"));    //�����ļ���Ŀ¼
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
					echo "�ϴ�ʧ��";
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