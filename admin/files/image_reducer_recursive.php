<?
	// require("lib_image.php");
	// Compress image
	function compressImage($source, $destination, $quality) {
		$maxlen = 1280;
		$resize = false;
		$info = getimagesize($source);
		$width = $info["width"] ? $info["width"] : $info[0];
		$height = $info["height"] ? $info["height"] : $info[1];
		
		if ($width > $maxlen || $height > $maxlen) {
			$resize = true;
			echo " [".$width."x".$height."]";
			if ($width > $height) {
				$height = round($height * $maxlen / $width);
				$width = $maxlen;
			} else {
				$width = round($width * $maxlen / $height);
				$height = $maxlen;
			}
			echo " -> [".$width."x".$height."]";
		}
		
		if ($info['mime'] == 'image/jpeg') {
			$image = imagecreatefromjpeg($source);
			// if ($resize) { $image = imagescale($image , $width, $height); echo " jpg"; }
			imagejpeg($image, $destination, $quality);
			return true;
		} elseif ($info['mime'] == 'image/gif') {
			$image = imagecreatefromgif($source);
			if ($resize) { $image = imagescale($image , $width, $height); echo " gif"; }
			// imagegif($image, $destination, $quality);
			return true;
		} elseif ($info['mime'] == 'image/png') {
			$image = imagecreatefrompng($source);
			if ($resize) { $image = imagescale($image , $width, $height); echo " png"; }
			// imagepng($image, $destination, $quality);
			return true;
		}
		echo $info['mime']." is not valid.";
		return false;
	}
	
	function load_image($filename, $type) {
		$new = 'new34.jpeg';
		if( $type == IMAGETYPE_JPEG ) {
			$image = imagecreatefromjpeg($filename);
			echo "here is jpeg output:";
			imagejpeg($image, $new);
		} elseif( $type == IMAGETYPE_PNG ) {
			$image = imagecreatefrompng($filename);
			echo "here is png output:";
			imagepng($image,$new);
		} elseif( $type == IMAGETYPE_GIF ) {
			$image = imagecreatefromgif($filename);
			echo "here is gif output:";
			imagejpeg($image, $new);
		}
		return $new;
	}

	function show_files($start) {
		$contents = scandir($start);
		array_splice($contents, 0,2);
		echo "<ul>";
		foreach ( $contents as $item ) {
			if ( is_dir("$start/$item") && (substr($item, 0,1) != '.') ) {
				echo "<li>$item</li>";
				show_files("$start/$item");
			} else {
				echo "<li>$item</li>";
			}
		}
		echo "</ul>";
	}
	
	function make_tiny($start) {
		$total = 0;
		
		// Filesize Threshold
		$maxsize = 400;
		
		// Valid extension
		$valid_ext = array('png','jpeg','jpg');
	
		$contents = scandir($start);
		array_splice($contents, 0,2);
		
		echo "<div>Total: <span id='total'></span></div>";
		echo "<ul>";
		foreach ( $contents as $item ) {
			if ( is_dir("$start/$item") && (substr($item, 0,1) != '.') ) {
				echo "<li>$item</li>";
				make_tiny("$start/$item");
			} else {
				$filename = "$start/$item";
				$location = $filename;
				
				// file extension
				$file_extension = pathinfo($location, PATHINFO_EXTENSION);
				$file_extension = strtolower($file_extension);
				
				// Check extension
				if (in_array($file_extension, $valid_ext)) {
					$ksize = filesize($filename) / 1024;
					// if (file_exists($filename) == true) {
					if ($ksize > $maxsize) {
						echo "<li>";
						echo $filename." (".number_format(round($ksize))."k)";

						compressImage($filename, $location, 50);
						$ksize = filesize($filename) / 1024;
						echo " = <span style='color: red;'>".number_format(round($ksize))."k</span>";
						
						echo "</li>";
						
						$total++;
					}
					// compressImage($filename, $location, 40);
					// }
				}
			}
		}
		echo "</ul>";
		echo "<script> document.getElementById('total').innerHTML = '".$total."' </script>";
	}
?>
<?
	$valid_dirs = array('banners', 'gallery', 'stores', 'tags');
	$start = isset($_GET["start"]) ? $_GET["start"] : "";
	
	if (in_array($start, $valid_dirs)) {
		make_tiny($start);
	} else {
		echo "start path is invalid: <br />";
		print_r($valid_dirs);
	}
?>










