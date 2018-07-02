<?php
    session_start();
	header("Content-type: text/html; charset=GB2312"); 
	if(!isset($_SESSION["username"])){
		header("Location: /TinyLink_lib/login.php");
	}
	include("top.txt");
	include ("scripts.php");
	$status=getUserStatus();
    $jsonFile = UPLOADEDFILES."uploadinfo.json";
    if (is_file($jsonFile)){
		 $workflow = json_decode(file_get_contents($jsonFile), true);
    }
    if($status=="ADMIN"){
	   display_pending_uploads();
	   display_pending_function();
    }else if($status=="USER"){
?>
	<div id="my_uploads">
<?php
	   display_my_uploads();
	   display_my_function();
?>
	</div>
<?php
    }
?>
	

	<h3>生成开发模板</h3>
 
	<!--p>You can add files to the system for review by an administrator.
	Click <b>Browse</b> to select the file you'd like to upload, 
	and then click <b>Upload</b>.</p-->
	 
	<!--form action="uploadfile_action.php" method="POST" enctype="multipart/form-data"-->
	
    <!--input type="file" name="ufile" \-->
	<table width="%100" style="table-layout:fixed">
		<tr><td>传感器模块型号</td><td><input type="text" id="module"></td></tr>
		<tr>
			<td>传感器模块功能</td>
			<td>
				<input type="text" id="function" list="flist">
				<datalist id="flist" >
					<option value="-- 全 部 --">
				 <?php 
					$servername = "localhost";
					$username = "root";
					$password = "";
					$dbname = "workflow";
					$conn = new mysqli($servername, $username, $password, $dbname);// 创建连接
					if ($conn->connect_error) { die(" [".date("Y h:i:s A")."] 数据库连接失败: " . $conn->connect_error);} // 检测连接
					else {	//$num=1;
							//$conn->query("SET NAMES utf8");		
							$sql = "SELECT function FROM function_table";
							$result = $conn->query($sql);
							if ($result->num_rows > 0) {
								while($row = $result->fetch_assoc()) {
									echo "<option value='".$row['function']."'>";
									//echo "<option value='ni'>";
								}
								/*echo "<option value='swim'>";
								echo "<option value='swim'>";*/
							}
					}
					$conn->close();
				 ?>
				</datalist> 
			</td>
		</tr>
		<tr><td>开发平台</td>
			<td><select id="device" >
					<option value="Arduino_Uno" > Arduino_Uno</option> 
					<option value="LinkIT" > LinkIT </option> 
					<option value="RPI" > RPI </option> 
					<option value="BBB" > BBB </option> 
				</select></td></tr>
		<!--tr><td>传感器所在类别</td>
			<td><select id="type" >
					<option value="Single_Collections" >Single_Collections</option> 
					<option value="Multi_Collections" >Multi_Collections</option> 
					<option value="Detectors" >Detectors</option> 
					<option value="Deboucers" >Deboucers</option> 
					<option value="On_Offs" >On_Offs</option> 
					<option value="Single_Leds" >Single_Leds</option> 
					<option value="Led_Bars" >Led_Bars</option> 
					<option value="Digit_Displays" >Digit_Displays</option>  
					<option value="Char_Displays" >Char_Displays</option> 
					<option value="Picture_Displays" >Picture_Displays</option> 
					<option value="WiFi" >WiFi</option> 
					<option value="BlueTooth" >BlueTooth</option> 
					<option value="RFID" >RFID</option> 
					<option value="Others" >Others</option> 
				</select> </td></tr-->
		<tr id="reuses">
			<td>是否复用驱动文件</td>
			<td><input type="radio" name="reuse" value="yes" id="yes"><label for='yes'>是</label><input type="radio"  name="reuse"value="no" id="no"><label for='no'>否</label>&nbsp&nbsp&nbsp&nbsp&nbsp<span id="hide1" style="display:none">文件名<input type="text" id="reuseFile"></span></td>
		</tr>
		<!--tr id="hide"><td>复用文件文件名</td><td><input type="text" id="reuseFile"></td></tr-->
	</table>
	<input type="button" id="btn_generate" value="生成开发模板">
	<div id="input_error"  style="color:red"> </div>
	<h3>库文件上传</h3>
	<div><input type="file" id="files" name="multi_upload[]" id="multi_upload" multiple webkitdirectory /> <input type="button" id="btnUpload" value="上传" onclick="upload(event);" />	</div>
    
	<div id="info"> </div>

	<h3>新功能申请</h3>
		<table width="%100" style="table-layout:fixed">
			<tr><td>功能名称</td><td><input type="text" id="newfct"></td></tr>
			<tr><td>功能描述</td><td><input type="text" id="description"></td></tr>
			<tr><td>应用场景举例</td><td><input type="text" id="application"></td></tr>
			<tr><td>代表模块型号</td><td><input type="text" id="new_module"></td></tr>
			<tr><td>模块详情链接</td><td><input type="text" id="module_link"></td></tr>
		</table>
		<input type="button" id="btn_apply" value="提交" onclick="apply()"> <span id="apply_error" style="color:red"></span>
		<div id="info_2"> </div>
	<h3>TinyLink软件库</h3>

<?php
	display_tinylink_libs("TinyLink");
?>
	
		

<script type="text/javascript">
	function checkSelect(e){
		var ev = e || window.event;  
        var target = ev.target || ev.srcElement;  
		var hide = target.parentNode.nextSibling.firstChild;
		if(target.value=="Reject"){
			hide.style.display='block';
		}else{
			hide.style.display='none';
		}
	}

	function pass(e){
		var ev = e || window.event;  
        var target = ev.target || ev.srcElement; 
		
		var tr = target.parentNode.parentNode;
		var table=tr.parentNode;
		var module=tr.childNodes[0].innerText;
		var submitBy=tr.childNodes[2].innerText;
		var status=tr.childNodes[3].firstChild.value;
		var reason=tr.childNodes[4].firstChild.value;
		/*alert (module);
		alert (submitBy);
		alert (status);
		alert (reason);*/

		var xmlhttp;
		if (window.XMLHttpRequest)
	   {	// code for IE7+, Firefox, Chrome, Opera, Safari
			xmlhttp=new XMLHttpRequest();
	   }else
	   {	// code for IE6, IE5
			xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
	   }
	   var data = new FormData();  
	   xmlhttp.onreadystatechange = function(ev){  
			if (xmlhttp.readyState==4){  
				alert(xmlhttp.responseText);
				table.removeChild(tr);
                //document.getElementById("info").innerText=xmlhttp.responseText;  
            }  
       };  
	   data.append('module', module);  
	   data.append('submitBy', submitBy);  
       data.append('status',status);
	   data.append('reason',reason);
	   xmlhttp.open("POST","/TinyLink_lib/checkfile_action.php", true);  
       xmlhttp.send(data); 
	}

	function pass_function(e){
		var ev = e || window.event;  
        var target = ev.target || ev.srcElement; 
		
		var tr = target.parentNode.parentNode;
		var table=tr.parentNode;
		var fct=tr.childNodes[0].innerText;
		var submitBy=tr.childNodes[1].innerText;
		var status=tr.childNodes[3].firstChild.value;
		var reason=tr.childNodes[4].firstChild.value;
		/*alert (status);
		alert (reason);
		alert (fct);
		alert (submitBy);*/
			
		var xmlhttp;
		if (window.XMLHttpRequest)
	   {	// code for IE7+, Firefox, Chrome, Opera, Safari
			xmlhttp=new XMLHttpRequest();
	   }else
	   {	// code for IE6, IE5
			xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
	   }
	   var data = new FormData();  
	   xmlhttp.onreadystatechange = function(ev){  
			if (xmlhttp.readyState==4){  
				alert(xmlhttp.responseText);
				table.removeChild(tr);
                //document.getElementById("info").innerText=xmlhttp.responseText;  
            }  
       };  
	   data.append('fct', fct);  
	   data.append('submitBy', submitBy);  
       data.append('status',status);
	   data.append('reason',reason);
	   xmlhttp.open("POST","/TinyLink_lib/checkfunction_action.php", true);  
       xmlhttp.send(data);  
	}
	
	document.getElementById("yes").onclick=function(){
		
		var hide1=document.getElementById("hide1")
		//var hide2=document.getElementById("hide2")
		hide1.style.display="inline";
		//hide2.style.display="none";
	}

	document.getElementById("no").onclick=function(){
		var hide1=document.getElementById("hide1")
		//var hide2=document.getElementById("hide2")
		//hide2.style.display="inline";
		hide1.style.display="none";
	}

	/*document.getElementById("files").onchange=function(){
		for(i in this.files){
			//alert(this.files[i].webkitRelativePath);
			//alert(typeof this.files[i]);
			//var path="<php? echo rtrim(this.files[i].webkitRelativePath,"\\") ?>"
			//alert (path);
			//alert(i);
		}
		alert("hello");	
	}*/
	document.getElementById("btn_generate").onclick=function(e){
		var ev = e || window.event;  
        var target = ev.target || ev.srcElement; 
		
		var xmlhttp;
		if (window.XMLHttpRequest)
	   {	// code for IE7+, Firefox, Chrome, Opera, Safari
			xmlhttp=new XMLHttpRequest();
	   }else
	   {	// code for IE6, IE5
			xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
	   }
	   var data = new FormData();
	   var input_error_div=document.getElementById("input_error");
	   input_error_div.innerText="";
       
	   xmlhttp.onreadystatechange = function(ev){  
            if (xmlhttp.readyState==4){  
				
				str=xmlhttp.responseText;
				input_error_div.innerHTML=str;
            }  
        };
		
		
		var module=document.getElementById("module").value;
		var fct=document.getElementById("function").value;
		var device=document.getElementById("device").value;
		
		//var type=document.getElementById("type").value;
		var ifreuse=-1;
		var reuseFileName="";

		var reuses=document.getElementById("reuses").getElementsByTagName("input");
		//alert(reuses.length);
		if(reuses[0].checked) ifreuse=1;
		if(reuses[1].checked) ifreuse=0;
		/*for(var i=0;i<reuses.length;i++){
			if(reuses[i].checked) {ifreuse= reuses[i].value;}
		}*/
		if(ifreuse){
			reuseFileName=document.getElementById("reuseFile").value;
			//alert(reuseFileName);
		}
		if(module==""||fct==""||device==""||ifreuse==-1||(ifreuse==1&&reuseFileName=="")){
			input_error_div.innerText="信息填写不全";
			return false;
		}
		if(module.length>20){
			input_error_div.innerText="模块型号不能大于20个字符";
			return false;
		}

		var reg=/^[A-Z]\w+$/
		if(!reg.test(module)){
			input_error_div.innerText="模块型号命名不符合规范";
			return false;
		}
		
        data.append('module',module);
		data.append('fct',fct);
		data.append('device',device);
		//data.append('type',type);
		data.append('ifreuse',ifreuse);
		data.append('reuseFileName',reuseFileName);
        // Open and send HHTP requests to upload.php  
        xmlhttp.open("POST","/TinyLink_lib/generate_template_action.php", true);  
        xmlhttp.send(data);

	}
	
	function upload(e){	
		var ev = e || window.event;  
        var target = ev.target || ev.srcElement; 
		
		var xmlhttp;
		if (window.XMLHttpRequest)
	   {	// code for IE7+, Firefox, Chrome, Opera, Safari
			xmlhttp=new XMLHttpRequest();
	   }else
	   {	// code for IE6, IE5
			xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
	   }
	   var data = new FormData();
	   var input_error_div=document.getElementById("input_error");
	   input_error_div.innerText="";
       
	   xmlhttp.onreadystatechange = function(ev){  
            if (xmlhttp.readyState==4){  
				//alert("hello");
				str=xmlhttp.responseText.split("#");
				//alert(str.length);
				if(str.length==2){
					//alert(str[1]);
					document.getElementById("my_uploads").innerHTML=str[1];
				}
				document.getElementById("info").innerHTML=str[0];  
            }  
        };  
		
		
		var module=document.getElementById("module").value;
		var fct=document.getElementById("function").value;
		var device=document.getElementById("device").value;
		
		//var type=document.getElementById("type").value;
		var ifreuse=-1;
		var reuseFileName="";

		var reuses=document.getElementById("reuses").getElementsByTagName("input");
		//alert(reuses.length);
		if(reuses[0].checked) ifreuse=1;
		if(reuses[1].checked) ifreuse=0;
		/*for(var i=0;i<reuses.length;i++){
			if(reuses[i].checked) {ifreuse= reuses[i].value;}
		}*/
		if(ifreuse){
			reuseFileName=document.getElementById("reuseFile").value;
			//alert(reuseFileName);
		}
		if(module==""||fct==""||device==""||ifreuse==-1||(ifreuse==1&&reuseFileName=="")){
			input_error_div.innerText="信息填写不全";
			return false;
		}
		if(module.length>20){
			input_error_div.innerText="模块型号不能大于20个字符";
			return false;
		}

		var reg=/^[A-Z]\w+$/
		if(!reg.test(module)){
			input_error_div.innerText="模块型号命名不符合规范";
			return false;
		}
		
			//alert("点击的是button");
			var files=document.getElementById("files").files;
			var paths = new Array(); 
			for (var i in files){  
				//alert(typeof files[i]);
				if (typeof files[i] != 'object'){  
					continue;  
				} 
				//alert(files[i].webkitRelativePath);
				// Append the current file path to the paths variable (delimited by tripple hash signs - ###)  
				paths.push(files[i].webkitRelativePath);  
				//paths += files[i].webkitRelativePath+"###";  
				// Append current file to our FormData with the index of i  
				data.append(i, files[i]);  
			}
			data.append('paths', paths);  
		
        data.append('module',module);
		data.append('fct',fct);
		data.append('device',device);
		//data.append('type',type);
		data.append('ifreuse',ifreuse);
		data.append('reuseFileName',reuseFileName);
        // Open and send HHTP requests to upload.php  
        xmlhttp.open("POST","/TinyLink_lib/uploadfile_action.php", true);  
        xmlhttp.send(data);
	}

	function apply(){	
		var apply_error=document.getElementById("apply_error");
		apply_error.innerText="";
		if (window.XMLHttpRequest)
	   {	// code for IE7+, Firefox, Chrome, Opera, Safari
			xmlhttp=new XMLHttpRequest();
	   }else
	   {	// code for IE6, IE5
			xmlhttp=new ActiveXObject("Microsoft.XMLHTTP");
	   }
	   var data = new FormData();
       
	   xmlhttp.onreadystatechange = function(ev){  
            if (xmlhttp.readyState==4){  
				//alert("hello");
				//apply_error.innerText=xmlhttp.responseText;
				str=xmlhttp.responseText.split("#");
				if(str.length==2){
					document.getElementById("my_uploads").innerHTML=str[1];
				}
				document.getElementById("info_2").innerHTML=str[0];  
            }  
        };  
		
		
		var newfct=document.getElementById("newfct").value;
		var description =document.getElementById("description").value;
		var application=document.getElementById("application").value;
		var new_module=document.getElementById("new_module").value;
		var module_link=document.getElementById("module_link").value;
		
		
		var error="";

		var strRegex = '^((https|http|ftp|rtsp|mms)?://)'
		+ '?(([0-9a-z_!~*\'().&=+$%-]+: )?[0-9a-z_!~*\'().&=+$%-]+@)?' //ftp的user@ 
		+ '(([0-9]{1,3}.){3}[0-9]{1,3}' // IP形式的URL- 199.194.52.184 
		+ '|' // 允许IP和DOMAIN（域名） 
		+ '([0-9a-z_!~*\'()-]+.)*' // 域名- www. 
		+ '([0-9a-z][0-9a-z-]{0,61})?[0-9a-z].' // 二级域名 
		+ '[a-z]{2,6})' // first level domain- .com or .museum 
		+ '(:[0-9]{1,4})?' // 端口- :80 
		+ '((/?)|' // a slash isn't required if there is no file name 
		+ '(/[0-9a-z_!~*\'().;?:@&=+$,%#-]+)+/?)$'; 
		var re=new RegExp(strRegex); 
		//re.test() 
		if (!re.test(module_link)) { 
			error="链接格式不正确，请填写正确的url链接";
		} 

		if(error!=""){
			apply_error.innerText=error;
			return false;
		}
		
		
		data.append('newfct', newfct);  
        data.append('description',description);
		data.append('application',application);
		data.append('new_module',new_module);
		data.append('module_link',module_link);
        // Open and send HHTP requests to upload.php  
        xmlhttp.open("POST","/TinyLink_lib/apply_function_action.php", true);  
        xmlhttp.send(data);
	}

function wsug(e, str){ // 鼠标跟随提示功能
 var oThis = arguments.callee;
 if(!str) {
  oThis.sug.style.visibility = 'hidden';
  document.onmousemove = null;
  return;
 }  
 if(!oThis.sug){
  var div = document.createElement('div'), css = 'top:0; left:0; position:absolute; z-index:100; visibility:hidden';
   div.style.cssText = css;
   div.setAttribute('style',css);
  var sug = document.createElement('div'), css= 'font:normal 12px/16px "宋体"; white-space:nowrap; color:#666; padding:3px; position:absolute; left:0; top:0; z-index:10; background:#f9fdfd; border:1px solid #0aa';
   sug.style.cssText = css;
   sug.setAttribute('style',css);
  var dr = document.createElement('div'), css = 'position:absolute; top:3px; left:3px; background:#333; filter:alpha(opacity=50); opacity:0.5; z-index:9';
   dr.style.cssText = css;
   dr.setAttribute('style',css);
  var ifr = document.createElement('iframe'), css='position:absolute; left:0; top:0; z-index:8; filter:alpha(opacity=0); opacity:0';
   ifr.style.cssText = css;
   ifr.setAttribute('style',css);
  div.appendChild(ifr);
  div.appendChild(dr);
  div.appendChild(sug);
  div.sug = sug;
  document.body.appendChild(div);
  oThis.sug = div;
  oThis.dr = dr;
  oThis.ifr = ifr;
  div = dr = ifr = sug = null;
 }
 var ee = e || window.event, obj = oThis.sug, dr = oThis.dr, ifr = oThis.ifr;
 obj.sug.innerHTML = str;
   
 var w = obj.sug.offsetWidth, h = obj.sug.offsetHeight, dw = document.documentElement.clientWidth||document.body.clientWidth; dh = document.documentElement.clientHeight || document.body.clientHeight;
 var st = document.documentElement.scrollTop || document.body.scrollTop, sl = document.documentElement.scrollLeft || document.body.scrollLeft;
 var left = e.clientX +sl +17 + w < dw + sl && e.clientX + sl + 15 || e.clientX +sl-8 - w, top = e.clientY + st + 17;
 obj.style.left = left+ 10 + 'px';
 obj.style.top = top + 10 + 'px';
 dr.style.width = w + 'px';
 dr.style.height = h + 'px';
 ifr.style.width = w + 3 + 'px';
 ifr.style.height = h + 3 + 'px';
 obj.style.visibility = 'visible';
 document.onmousemove = function(e){
  var ee = e || window.event, st = document.documentElement.scrollTop || document.body.scrollTop, sl = document.documentElement.scrollLeft || document.body.scrollLeft;
  var left = e.clientX +sl +17 + w < dw + sl && e.clientX + sl + 15 || e.clientX +sl-8 - w, top = e.clientY + st +17 + h < dh + st && e.clientY + st + 17 || e.clientY + st - 5 - h;
  obj.style.left = left + 'px';
  obj.style.top = top + 'px';
 }
}


</script>


<?php
   include("bottom.txt");
?>