<?php
	echo "upload_max_filesize : ".ini_get('upload_max_filesize');
	echo "<br>post_max_size : ".ini_get('post_max_size');
	echo "<br>memory_limit : ".ini_get('memory_limit');
	phpinfo();
?>