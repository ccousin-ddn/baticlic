<?php
	require_once "libraries/lib_include.php";
	
	// MYSQL_SERVER, MYSQL_USER, MYSQL_PASSWORD, MYSQL_DB, MYSQL_PORT
	
	echo "Votre base est en cours de sauvegarde.......<br>";
	$file = "upload/".MYSQL_DB."_".date("Ymd").".sql";
	$cmd = "mysqldump --host=".MYSQL_SERVER." --user=".MYSQL_USER." --port=".MYSQL_PORT." --password=".MYSQL_PASSWORD." ".MYSQL_DB." > ".$file;
	echo $cmd."<br>";
	echo "<pre>";
	$line = system($cmd." 2>&1",$result);
	echo "</pre>";
	echo $line."<br>";
	echo $result."<br>DB sauvegardée. Envoi sur le FTP.....";
	
	// Send file to sftp
	$sftp = new \FtpClient\FtpClient();
	$sftp->connect("soluweb.solufile.be", true, 7722);
	$sftp->login("sftp_backup", "sftp_backup");
	$sftp->putAll($file, "/rc/");
	
	//$sftp = new sftp("soluweb.solufile.be",7722);
	//$sftp->login("sftp_backup", "sftp_backup");
	//$sftp->uploadFile($file, "/rc/");
	echo $result."<br>Upload OK";
	
	exit;
	
	// connect to FTP server
	$ftp_server = "ftp.example.com";
	$ftp_conn = ftp_connect($ftp_server) or die("Could not connect to $ftp_server");

	//login to FTP server
	$login = ftp_login($ftp_conn, $ftp_username, $ftp_userpass);

	$file = "localfile.txt";

	// upload file
	if (ftp_put($ftp_conn, "serverfile.txt", $file, FTP_ASCII))
		{
		echo "Successfully uploaded $file.";
		}
	else
		{
		echo "Error uploading $file.";
		}

	// close connection
	ftp_close($ftp_conn);