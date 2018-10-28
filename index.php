<?php
//generate visitor counter file
$f = "visit.php";
if(!file_exists($f)){
	touch($f);
	$handle =  fopen($f, "w" ) ;
	fwrite($handle,0) ;
	fclose ($handle);
}
//generate .htaccess file for SEO friendly URL creation
if(!file_exists(".htaccess")){
	touch(".htaccess");
	$handle =  fopen(".htaccess", "w" );
	$texttowrite = 
	"
	#Turn Rewrite Engine On
	RewriteEngine on

	#L makes this the last rule that this specific condition will match

	#RewriteRule for decrypt.php?code=xxx to /file/xxx
	RewriteRule ^file/([0-9a-zA-Z=]+) decrypt.php?code=$1 [L]
	";
	fwrite($handle,$texttowrite) ;
	fclose ($handle);
}
// sets default timezone to Manila, Philippines
date_default_timezone_set("Asia/Manila"); 
if($_SERVER['REQUEST_METHOD'] == "POST"){
	$namevar = "Mr.Azhar";
	$filevar = basename($_FILES["uploaded_file"]["name"]); // get filename from uploaded file
	$numbervar = $_POST['intinput'];
	//increment time by adding input number in minutes
	$limit = date('Y-m-d h:i:s A',strtotime('+'.$numbervar.' minutes',strtotime(date("Y-m-d h:i:s A"))));
	//replace space to front slash to create one word string only
	$datetime_no_space = str_replace(" ","/",$limit);
	//string to be encrypted
	$mainstr = "username=".$namevar."&filename=".$filevar."&expiration=".$datetime_no_space;
	//encrypt final string
	$encrypted_txt = base64_encode($mainstr);
	
	//Creates a folder named MrNiemand03
	if(!file_exists("Mr.Azhar")){
		mkdir("Mr.Azhar", 0777, true);
	}
	
	//generate new link if upload successfull
	if(move_uploaded_file($_FILES["uploaded_file"]["tmp_name"], $namevar."/".$filevar)){
		$protocol = (isset($_SERVER['HTTPS'])) ? "https://" : "http://";
		$dl_link = $protocol ."192.168.1.2".dirname($_SERVER['PHP_SELF'])."/file/".$encrypted_txt;
	}else{
		$dl_link = $namevar."/".$filevar; //shows file if upload not successfull
	}
}
?>
<!DOCTYPE html>
<html>
    <head>
        <title>Expiring Download Link </title>
		<link rel="stylesheet" href="css/bootstrap.min.css">
		<link rel="stylesheet" href="css/style.css">
		<script src="js/navbarclock.js"></script>
    </head>
	<body onload="startTime()">
		<nav class="navbar-inverse" role="navigation">
			<a href="#" target="_blank">
				<h1> Azhar's Uploader</h1>
			</a>
			<div id="clockdate">
				<div class="clockdate-wrapper">
					<div id="clock"></div>
					<div id="date"><?php echo date('l, F j, Y'); ?></div>
				</div>
			</div>
			<div class="pagevisit">
				<div class="visitcount">
					<?php
					//displays how many visitor(s) visits the page
					$handle = fopen($f, "r");
					$counter = ( int ) fread ($handle,20) ;
					fclose ($handle) ;
					
					if(!isset($_POST['iploadfile'])){
						$counter++ ;
					}
					
					echo "This Page is Visited ".$counter." Times";
					$handle =  fopen($f, "w" ) ;
					fwrite($handle,$counter) ;
					fclose ($handle) ;
					?>
				</div>
			</div>
		</nav>
		<br><br><br><br><br><br>
		<center>
			<div style="text-align:left;width:400px;">
				<form method="post" enctype="multipart/form-data">
					<div style="border: 3px groove cornflowerblue;border-radius: 10px;padding:20px;">
						<div class = "form-group">
							<label>File to Upload</label>
							<input type="file" name="uploaded_file" class="form-control" required>
						</div>
						<div class = "form-group">
							<label>Expiry Date and Time (in minutes)</label>
							<input type="number" name="intinput" class="form-control" required>
						</div>
						<div class = "form-group">
							<input type="submit" class="btn btn-primary btn-block" value="Upload" name="iploadfile">
						</div>
					</div>
				</form>
			</div>
		</center>
		<br>
		<?php
		//show generated encrypted download link
		if($_SERVER['REQUEST_METHOD'] == 'POST'){
			echo 
			'
			<div class="jumbotron" style="padding:10px">
				<div style="text-align:center;color:red;">
					Remember, you need download <q style="color:green;">'.$filevar.'</q> before 
					<strong>'.$limit.'</strong> else you will lose access to the file<br>
					<small>Expiry Date and Time is in format (year-month-day hour:minute:second)</small>
				</div>
				<div class="container">
					<strong>Download Link:</strong><br>
					<input type="text" id="dllink" value="'.$dl_link.'" class="form-control" style="margin-bottom:5px" readonly>
					<button onclick="CopyToClipboard()" class="btn btn-primary">Copy Link to Clipboard</button>
					<button onclick="OpenInNewTab()" class="btn btn-success">Open in New Tab</button>
				</div>
			</div>
			';
		}
		?>
	</body>	
	<script>
	function CopyToClipboard() {
	  var copyText = document.getElementById("dllink");
	  copyText.select();
	  document.execCommand("Copy");
	  
	  alert("Link Copied to Clipboard");
	}

	function OpenInNewTab(){
		var newtab = window.open(document.getElementById('dllink').value);
		newtab.focus();
	}
	</script>
</html>