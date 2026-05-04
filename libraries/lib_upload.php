<?php
	require_once (dirname(__FILE__).'/lib_include.php');
	//var_dump($_POST);
	// status
	$status = false;
	// info
	$info = gettext("Téléchargement réussi !");
    // maxSize
    $max_size = $_POST['maxSize'] * 1024 * 1024; // max file size (Mb)
    // path upload
    $path = '../'.UPLOAD_PATH;
	// resize
    $resizes = isset($_POST['resize']) ? explode(",", $_POST['resize']) : array();
	// slug name
	$slugName = "";
	// real extension
	$realExt = "";
	// preview extension
	$previewExt = "";
    // type

	function autoRotateImage($image) {
		$orientation = $image->getImageOrientation();
		switch($orientation) {
			case imagick::ORIENTATION_BOTTOMRIGHT: // 3
				$image->rotateimage("#000", 180); // rotate 180 degrees
				break;
			case imagick::ORIENTATION_RIGHTTOP: // 6
				$image->rotateimage("#000", 90); // rotate 90 degrees CW
				break;
			case imagick::ORIENTATION_LEFTBOTTOM: // 8
				$image->rotateimage("#000", -90); // rotate 90 degrees CCW
				break;
		}
		$image->setImageOrientation(imagick::ORIENTATION_TOPLEFT);
	}
	/*
	FILTER_POINT took: 0.334532976151 seconds
	FILTER_BOX took: 0.777871131897 seconds
	FILTER_TRIANGLE took: 1.3695909977 seconds
	FILTER_HERMITE took: 1.35866093636 seconds
	FILTER_HANNING took: 4.88722896576 seconds
	FILTER_HAMMING took: 4.88665103912 seconds
	FILTER_BLACKMAN took: 4.89026689529 seconds
	FILTER_GAUSSIAN took: 1.93553304672 seconds
	FILTER_QUADRATIC took: 1.93322920799 seconds
	FILTER_CUBIC took: 2.58396601677 seconds
	FILTER_CATROM took: 2.58508896828 seconds
	FILTER_MITCHELL took: 2.58368492126 seconds
	FILTER_LANCZOS took: 3.74232912064 seconds
	FILTER_BESSEL took: 4.03305602074 seconds
	FILTER_SINC took: 4.90098690987 seconds
	*/
	function resize($resize, $path, $file, $newPath, $newName=null){
		$im = new imagick($path.$file);
		autoRotateImage($im); 
		$im->setImageFormat('jpeg');
		$resize_type = substr($resize,0,1);
		switch($resize_type){
			case "h" : 	$new_height = substr($resize,1);
						//$ratio = $height/$new_height;
						//$new_width = ceil($width/$ratio);
						$im->resizeImage(0,$new_height,Imagick::FILTER_GAUSSIAN,1.5);
						break;
			case "w" : 	$new_width = substr($resize,1);
						//$ratio = $width/$new_width;
						//$new_height = ceil($height/$ratio);
						$im->resizeImage($new_width,0,Imagick::FILTER_GAUSSIAN,1.5);
						break;
			case "s" : 	$new_size = substr($resize,1);
						$im->cropThumbnailImage($new_size, $new_size);
						break;
			case "c" :	$newValue = explode("x", substr($resize,1));
						$im->resizeImage($newValue[0],$newValue[1],Imagick::FILTER_GAUSSIAN,1,true);
						//$im->scaleImage($newValue[0], $newValue[1], true);
						break;
		}
		$newFile = $newPath.($newName??$file);
		$im->writeImage($newFile);
		return $newFile;
	}

	try{
	    // Undefined | Multiple Files | $_FILES Corruption Attack
	    // If this request falls under any of them, treat it invalid.
	    if(!isset($_FILES['upload_file']['error']) || is_array($_FILES['upload_file']['error'])){
	    	$info = gettext("Paramètres non valides !");
	        throw new RuntimeException($info);
	    }
	    switch($_FILES['upload_file']['error']) {
	        case UPLOAD_ERR_OK:
	            break;
	        case UPLOAD_ERR_NO_FILE:
				$info = gettext("Aucun fichier envoyé !");
	            throw new RuntimeException($info);
	        case UPLOAD_ERR_INI_SIZE:
				$info = gettext("Fichier trop volumineux, max : ").ini_get("upload_max_filesize");
				throw new RuntimeException($info);
	        case UPLOAD_ERR_FORM_SIZE:
				$info = gettext("Fichier trop volumineux, max : ").ini_get("upload_max_filesize");
	            throw new RuntimeException($info);
	        default:
				$info = gettext("Erreur inconnue !");
	            throw new RuntimeException($info);
	    }
		$fileSize = $_FILES['upload_file']['size'];
		$realName = pathinfo($_FILES['upload_file']['name'], PATHINFO_FILENAME);
		$finfo = new finfo(FILEINFO_MIME_TYPE);
		$fileExt = $finfo->file($_FILES['upload_file']['tmp_name']);
		$mime = explode("/",mime_content_type($_FILES['upload_file']['tmp_name']));

	    // Check filesize
	    if($fileSize > $max_size){
	    	$info = sprintf(gettext("Fichier trop lourd : %s Mb (maximum %s Mb)"),round($fileSize/1024/1024,2),$_POST['maxSize']);
	        throw new RuntimeException($info);
	    }

	    // Check MIME Type
		$img_exts = array('jpeg'=>'image/jpeg', 'jpg'=>'image/jpg', 'png'=>'image/png', 'gif'=>'image/gif', 'webp'=>'image/webp');
		$pdf_exts = array('pdf'=>'application/pdf');
		$doc_exts = array('doc'=>'application/msword', 'docx'=>'application/vnd.openxmlformats-officedocument.wordprocessingml.document', 'ppt'=>'application/vnd.ms-powerpoint', 'pptx'=>'application/vnd.openxmlformats-officedocument.presentationml.presentation', 'xls'=>'application/vnd.ms-excel', 'xlsx'=>'application/vnd.openxmlformats-officedocument.spreadsheetml.sheet', 'txt'=>'text/plain'); 
		$vid_exts = array('mov'=>'video/mov', 'mp3'=>'video/mp3', 'mp4'=>'video/mp4', 'mpeg'=>'video/mpeg');
		switch($_POST['type']){
	        case 'img' :    $valid_exts = $img_exts;
	                        break;
			case 'pdf' :	$valid_exts = $pdf_exts;
	                        break;
			case 'img&pdf' :$valid_exts = array_merge($img_exts, $pdf_exts);
	                        break;
			case 'img&mov' :$valid_exts = array_merge($img_exts, $vid_exts);
	                        break;
	        case 'doc' :    $valid_exts = $doc_exts;
	                        break;
	        case 'all' :    $valid_exts = array_merge($img_exts, $pdf_exts, $doc_exts, $vid_exts);
	                        break;
	        default :       $valid_exts = array();
	                        break;
	    }
	    $finfo = new finfo(FILEINFO_MIME_TYPE);
	    if(false === $realExt = array_search($fileExt,$valid_exts,true)){
	    	$info = gettext("Format de fichier pas supporté !");
	        throw new RuntimeException($info);
	    }

	    // Rename
		$slugName = md5(uniqid($realName,true).$fileSize);
		$newName = $slugName.'.'.$realExt;
		
		// Upload file
		if(!move_uploaded_file($_FILES['upload_file']['tmp_name'], $path.$newName)){
			$info = gettext("Impossible d'écrire dans le répertoire de destination !");
	        throw new RuntimeException($info);
	    }

		// File processing
		switch($mime[0]){ //0=>image, 1=>png / 0=>application, 1=>pdf
			case "image" :
				foreach ($resizes as $sizes) {
					$size = explode(":",trim($sizes));
					// Create directory if not exists
					if (!file_exists($path.$size[1])) {
						if(!mkdir($path.$size[1], 0777, true)) {
							$info = gettext("Impossible de créer le répertoire de destination !");
					        throw new RuntimeException($info);
						}
					}
					// webp to jpg (imagick webp not in OVH)
					if($realExt == "webp"){
						$image = imagecreatefromwebp($path.$newName);
						imagejpeg($image, $path.$slugName.'.jpg', 100);
						unlink($path.$newName);
						$newName = $slugName.'.jpg';
						$realExt = "jpg";
						imagedestroy($image);
					}
					
			        resize($size[0], $path, $newName, $path.$size[1]);
	            }
				if(count($resizes) > 0){
					// Delete tmp image
					//if($directory == ""){
						unlink($path.$slugName.".".$realExt);
					//}
					$size = explode(":",trim($resizes[0]));
					$previewDir = $size[1];
				}
				$previewExt = $realExt; //$realExt="jpg"?"webp":$realExt;
				break;
			case "application" :
			case "video" :
				switch($mime[1]){
					case "pdf" :
						$size = explode(":",$resizes[0]);
						resize($size[0], $path, $newName.'[0]', $path.$size[1], $slugName.".png");
						$previewExt = "png";
						$previewName = $size[0].$slugName;
						break;
					case "mp4" :
						$previewExt = "mp4";
						$previewName = $slugName;
						break;
					default :
						$previewExt = $fileExt;
						$previewName = $slugName;
				}
				break;
        }
		
	    header('Content-type: application/json');
		// echo out json encoded status
		echo json_encode(array('status' => true, 'info' => $info, 'real_name' => $realName??"", 'real_ext' => $realExt??"", 'preview_ext' => $previewExt??"", 'slug_name' => $slugName??"", 'mimetype'=>$mime));

	}catch(RuntimeException $e){
	    echo json_encode(array('status' => false, 'info' => $e->getMessage()));
	}