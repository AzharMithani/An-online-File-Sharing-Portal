<?php
date_default_timezone_set("Asia/Kolkata"); 
$encrypted = $_GET['code'];               
parse_str(base64_decode($encrypted));     

if(isset($username)){ 
	if(isset($filename)){ 
		if(isset($expiration)){ 
			$finaldatetime = str_replace("/"," ", $expiration); 
			$timeinput = strtotime($finaldatetime); 
			$status = ($timeinput < time()) ? "Expired" : "Good"; 
			
			if($status == "Expired"){
				
				errorpage(
					"Expired Link",
					"Link is already Expired",
					"Request for new access link from the author to get the requested file."
				);
			}else{
				$filePath = $username.'/'.$filename;
				if(!empty($filename) && file_exists($filePath)){
					header("Content-Length: " . filesize($filePath));  
					header("Content-Encoding: none");
					header("Cache-Control: public");
					header("Content-Description: File Transfer");
					header("Content-Disposition: attachment; filename=$filename");
					header("Content-Type: application/stream");
					header("Content-Transfer-Encoding: binary");
					
					readfile($filePath); // Read the file
					exit;
				}else{
					error404();
				}
			}
		}else{
			error404();
		}
	}else{ 
		error404();
	}
}else{ 
	error404();
}

function error404(){
	errorpage(
		"We've got some trouble | 404 - Resource not found",
		"Resource not found <small>Error 404</small>",
		"The requested resource could not be found but may be available again in the future."
	);
}

function errorpage($title,$headerm,$message){
	echo 
	'
	<!DOCTYPE html>
	<html lang="en">
	<head>
		<meta charset="utf-8" />
		<meta http-equiv="X-UA-Compatible" content="IE=edge" />
		<meta name="viewport" content="width=device-width, initial-scale=1" /> 
		<title>'.$title.'</title>
		<style type="text/css">
			body,html{width:100%;height:100%;background-color:#21232a}
			body{
				margin:0;
				color:#fff;
				text-align:center;
				text-shadow:0 2px 4px rgba(0,0,0,.5);
				padding:0;min-height:100%;
				-webkit-box-shadow:inset 0 0 75pt rgba(0,0,0,.8);
				box-shadow:inset 0 0 75pt rgba(0,0,0,.8);
				display:table;
				font-family:"Open Sans",Arial,sans-serif
			}
			h1{font-family:inherit;font-weight:500;line-height:1.1;color:inherit;font-size:36px}
			h1 small{font-size:68%;font-weight:400;line-height:1;color:#777}
			.lead{color:silver;font-size:21px;line-height:1.4}
			.cover{display:table-cell;vertical-align:middle;padding:0 20px}
		</style>
	</head>
	<body>
		<div class="cover">
			<h1>'.$headerm.'</h1>
			<p class="lead">'.$message.'</p>
		</div>    
		</body>
	</html>
	';
}
?>