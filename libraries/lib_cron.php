<?php
 // cron task
 // 0 0 * * * php /var/www/vhosts/domain.com/httpdocs/libraries/lib_cron.php
 	//get session storage path
	//$dir = dirname($_SERVER['DOCUMENT_ROOT']).DIRECTORY_SEPARATOR."sessions";
	$dir = "/homez.27/ramosconcy/sessions";
	echo "dir = ".$dir;
	//get all session files
	$sessFiles = preg_grep("/^sess_/", scandir($dir));
	
	//get all session file by looping the files.
	foreach ($sessFiles as $key => $value) {
		$sess_file = $dir.DIRECTORY_SEPARATOR.$value;
		$size = filesize($sess_file);
		//if($size < 50 ){
			echo $sess_file."<br>";
			fclose($sess_file);
			unlink($sess_file);
		//}
	}