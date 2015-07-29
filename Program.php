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
				echo "<img src=\"$target_out_file\" alt=\"Results\"> </br>";

			}
		}
		else 
		{
			echo "Sorry, there was an error uploading your file.";
		}
	}
}

echo '<!DOCTYPE html>
<html>
    <head>
        <meta charset="utf-8">
        <title>Change Counter</title>
        <link rel="stylesheet" href="css/base.css" type="text/css" media="screen">
    </head>

    <body>
	<div class ="container" id="input">
		<section class="main-content">
		<h1> Results</h1>
		<p>';
		processImage();
echo '</p>
</section>
		</body>
		</html>';
	
?>