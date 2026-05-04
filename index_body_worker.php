<?php

?>
	<link rel="stylesheet" type="text/css" href="css/bootstrap-clockpicker.css">
	<link rel="stylesheet" type="text/css" href="css/worker.css?ver=<?php echo filemtime('css/worker.css')?>">
	<div class="clock bg-dark text-white w-100 text-center fixed-top" style="top:56px; z-index:10;">
		<div id="Date" class="color1 text-nowrap"><?php echo ucfirst(IntlDateFormatter::formatObject(new DateTime("now", new DateTimeZone('Europe/Paris')), 'eeee dd MMMM yyyy', 'fr'));?></div>
		<ul>
			<li id="hours">00</li>
		    <li>:</li>
		    <li id="min">00</li>
		    <li>:</li>
		    <li id="sec">00</li>
		</ul>
	</div>
	<div class="d-flex btn-nav fixed-top" style="top:128px; z-index:20;">
		
		<a class="p-2 flex-fill btn-secondary text-center text-white btn" id="btn_home" onclick="worker_action({action:'showHomePage'})"><i class="fas fa-home"></i></a>
		<a class="p-2 flex-fill btn-secondary text-center text-white btn disabled" id="btn_back" onclick="worker_action({action:'goBack'})" style="border-left:1px solid white;"><i class="fas fa-arrow-alt-circle-left"></i></a>
	</div>
	
	<div class="content-home" style="padding-bottom:40px;" id="div_result"></div>
	
	<script src="js/worker.js?ver=<?php echo filemtime('js/worker.js');?>"></script>
	<script>
		document.addEventListener("DOMContentLoaded", function(event) { 
			createDateTime();
			worker_action({action:'showHomePage'});
		});
	</script>