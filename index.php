<!DOCTYPE html>
<?php 
	require_once "libraries/lib_include.php";
	//var_dump($_COOKIE, $_SESSION);
	$ver = "v2.1";
	$now = date("Ymdhms");
	
	// location ('out' : if no cookie, only normal login - 'office' : normal & worker - 'mobile' : only worker
	define ('LOCATION',$_COOKIE['location']??"out");

	$navbar 	= "index_navbar.php";
	$body 		= "index_body.php";
	$footer		= "index_footer.php";
	if(CONNECTED){
		switch($_SESSION['usr_type']){
			case 0 : // Worker
				// check if active or not
				$worker = new wor_worker();
				$worker->m_getById($_SESSION['iduser']);
				if($worker->values[0]["wor_state"] == 2){
					//header("location:index_logout.php");
					$body = "index_logout.php";
					$navbar = "offline.html";
				}else{
					$body = "index_body_worker.php";
					$navbar = "index_navbar_worker.php";	
				}
				break;
			case 1 : // Admin
				$body = "index_body.php";
				$navbar = "index_navbar.php";
				break;
			case 2 : // Forman
				$body = "index_body.php";
				$navbar = "index_navbar_forman.php";
				break;
			case 3 : // Warehouseman
				$body = "index_body_warehouseman.php";
				$navbar = "index_navbar_warehouseman.php";
				break;
			case 9 : // Client
				break;
		}
	}else{
		$body = "index_login.php";
	}
?>

<html lang="<?php echo LANG?>">
	
<?php
	include_once 'index_head.php';
?>
	<body>
<?php
	include_once $navbar;
	include_once $body;
	include_once $footer;
	include_once 'index_script.php';
?>
	</body>
</html>
