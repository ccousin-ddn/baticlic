<?php
error_reporting(E_ALL);
ini_set('display_errors', '1');

$path = realpath('');
$filecount_php = 0;
$filecount_js = 0;
$filecount_css = 0;
$lines = 0;
foreach (new RecursiveIteratorIterator(new RecursiveDirectoryIterator($path)) as $filename)
{
	$dir = pathinfo($filename,PATHINFO_DIRNAME );
	$file = pathinfo($filename,PATHINFO_BASENAME  );
	$ext = pathinfo($filename,PATHINFO_EXTENSION );
	if (!strstr($dir, 'mpdf57')) {
		
		switch ($ext){
			case "php" : 
				$filecount_php++; $lines += count(file($filename));
				echo "$file : ".count(file($filename))." lines<br>";
				break;
			case "js" : 
				$filecount_js++; $lines += count(file($filename));
				echo "$file : ".count(file($filename))." lines<br>";
				break;
			case "css" : 
				$filecount_css++; $lines += count(file($filename));
				echo "$file : ".count(file($filename))." lines<br>";
				break;
		}
	}
}
echo $path."<br>";

echo "Php : $filecount_php<br>JS : $filecount_js<br>Css : $filecount_css";

echo "<br>lignes : $lines";

?>