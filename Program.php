<?php

function processImage()
{
	$target_in_dir = "uploads/";
	$target_in_file = $target_in_dir . basename($_FILES["images"]["name"]);
	$uploadOk = 1;
	$target_out_dir="outputs/";
	$target_out_file=$target_out_dir . basename($_FILES["images"]["name"]);
	$imageFileType = pathinfo($target_in_file,PATHINFO_EXTENSION);
	// Check if image file is a actual image or fake image
	if(isset($_POST["submit"])) {

		$check = getimagesize($_FILES["images"]["tmp_name"]);
		if($check !== false) {
			//echo "File is an image - " . $check["mime"] . ".";
			$uploadOk = 1;
		} else {
			echo "File is not an image.";
			$uploadOk = 0;
		}
	}

	// Allow certain file formats
	/*if($imageFileType != "jpg" && $imageFileType != "png" && $imageFileType != "jpeg"
	&& $imageFileType != "gif" ) {
		echo "Sorry, only JPG, JPEG, PNG & GIF files are allowed.";
		$uploadOk = 0;
	}*/
	// Check if $uploadOk is set to 0 by an error
	if ($uploadOk == 0) {
		echo "Sorry, your file was not uploaded.";
	// if everything is ok, try to upload file
	} else {
		if (move_uploaded_file($_FILES["images"]["tmp_name"], $target_in_file)) {
			$type = $_POST["type"];
			$value;
			switch($type)
			{
				case(0):
				$type="Pennies";
				$value=0.01;
				break;
				case(1):
				$type="Nickels";
				$value=0.05;
				break;
				case(2):
				$type="Dimes";
				$value=0.10;
				break;
				case(3):
				$type="Quarters";
				$value=0.25;
				break;				
			}
			//echo "The file ". basename( $_FILES["images"]["name"]). " has been uploaded.</br>";
			$execute = "main "  . $target_in_file . " " . $target_out_file;
			//echo "main.exe</br>";
			//echo $execute . '</br>';
			exec($execute ,$output,$result);
			//echo $result . "</br>";
			if($result !=0 )
			{
				echo "Error Processing Image";
			}
			else {
				echo "There are $output[0] $type in the Picture</br>
				Total value: $" . $value*$output[0] . "</br>";
				echo "<img src=\"$target_out_file\" alt=\"Results\" style=\"vertical-align: middle;width:304px;height:228px;\"> </br>";

			}
		}
		else 
		{
			echo "Sorry, there was an error uploading your file.";
		}
	}
}

echo '<!DOCTYPE html>
<html lang="en">
  <head>
    <meta charset="utf-8">
    <meta http-equiv="X-UA-Compatible" content="IE=edge">
    <meta name="viewport" content="width=device-width, initial-scale=1">
    <!-- The above 3 meta tags *must* come first in the head; any other head content must come *after* these tags -->
    <meta name="description" content="">
    <meta name="author" content="">
    <!-- <link rel="icon" href="images/cents.ico"> -->

    <title>Change Counter</title>
      
    <link href="css/bootstrap.min.css" rel="stylesheet">
      <link href="css/cc-cover.css" rel="stylesheet">

  </head>

    <body>
	 <div class="navbar">
    <div class="container">
        <!-- Collapse button -->
        <button class="navbar-toggle" data-toggle="collapse" data-target=".navHeaderCollapse">
            <span class="text">Show Menu</span>
        </button>
        <!-- Navigation buttons -->
        <!-- Collapse content -->
        <div class="collapse navbar-collapse navHeaderCollapse">
                <ul class="nav navbar-nav navbar-left">					
                    <li class="active">
                    <li class="dropdown">
                        <a href="#" class="dropdown-toggle" data-toggle="dropdown">Menu<span class="caret"></span></a>
                        <ul class="dropdown-menu" role="menu">
                            <li><a href="upload-main.html">Go to Upload</a></li>
                            <li><a href="#">Go to Count Info</a></li>
                            <li><a href="#">Image Log</a></li>
                            <li><a href="currency-type.html">Currency Type</a></li>
                            <li><a href="about.html">About</a></li>
                        </ul>
                    </li>
                </ul>
                </div>
            </div>
            </div>
      <!-- Changer Counter Logo -->
      <div class="row">
          <p><a href="index.html"><img src="http://i.imgur.com/azo1zCc.png" style="width:250px;height:104px;top-padding:50px"/></a></p>
          </div> 
	<div class ="container" id="input">
	<div class="jumbotron" style="width:100%;height:100%;padding-top:3px">
		<section class="main-content" style="color:#0099ff;text-align:left;margin:20px">
		<h1> Results</h1>
		<p>';
		processImage();
echo '</p>
</section>
</div>
</div>
		</body>
		</html>';
	
?>