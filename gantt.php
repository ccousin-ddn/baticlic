<?php 
	include_once "libraries/lib_include.php";
	$_SESSION["usr_level"] = 2;
?>
<!DOCTYPE html>
<html lang="fr">
	<head>
		<meta charset="utf-8">
		<meta http-equiv="X-UA-Compatible" content="IE=edge">
		<meta name="viewport" content="width=device-width, initial-scale=1">
		<meta name="description" content="RamosConstructions - Gantt">
		<meta name="author" content="Solufile">
		<meta http-equiv="Cache-control" content="private, max-age=2592000">
		<link rel="shortcut icon" href="favicon.ico">
		<title>RC Constructions - Gantt</title>
		<link rel="stylesheet" href="css/bootstrap.min.css">
		<link rel="stylesheet" href="css/font-awesome.min.css">
		<link rel="stylesheet" href="css/frappe-gantt.css">
		<link rel="stylesheet" href="css/style_gen.css">
		
	</head>
	<body onload="<?php echo "getTask()" ?>" class="d-flex flex-column">
		
		<header style="position:fixed; width:100%; background:white; z-index:1; height:60px;">
			<nav class="navbar navbar-expand-lg fixed-top p-0 bg-white" style="height:60px;">
				<a class="navbar-brand p-0 h-100" href="#">
					<img src="images/logo.png" class="d-inline-block mr-2" alt="">
					Planning
				</a>
				<span class="navbar-text font-weight-bold">
					<?php echo ucfirst(strftime('%B %Y')); ?>
				</span>
				<span class="navbar-text">
					Dernière mise à jour le <span id="day"></span> à <span id="hour"></span>
				</span>
			</nav>
		</header>
		
		<div class="flex-grow-1" id="gantt">
		</div>
		<footer class="container-fluid footer small">
			<div class="row justify-content-center">
			<small>
				RC v0.1 | 2019 <i class="far fa-copyright"></i> <a href="http://" target="_blank">RC Constructions</a> | <i class="fas fa-code"></i> <a href="https://www.solufile.be" target="_blank">Solufile</a>
			</small>
			</div><!-- .row -->
		</footer>
		<script src="js/jquery.min.js"></script>
		<script src="js/snap.svg-min.js"></script>
		<script src="js/frappe-gantt.js"></script>
		<script>
		var tasks;
		function getToday(separator){
			var today = new Date();
			var dd = today.getDate();
			var mm = today.getMonth()+1;
			var yy = today.getFullYear();
			if(dd<10){dd='0'+dd;}
			if(mm<10){mm='0'+mm;}
			var date = dd+separator+mm+separator+yy;
			return date;
		}
		function getHour(separator){
			var now = new Date();
			var hh = now.getHours();
			var mm = now.getMinutes();
			var ss = now.getSeconds();
			if(hh<10){hh='0'+hh;}
			if(mm<10){mm='0'+mm;}
			if(ss<10){ss='0'+ss;}
			//var hour = hh+separator+mm+separator+ss;
			var hour = hh+separator+mm;
			return hour;
		}
		function getTask(){
			$.ajax({
				type:"POST",
				url: "controllers/c_router.php",
				dataType: 'json',
				data:{table:'tas_task', idrecord:-1, action:"gantt", dataSent:''},
				success: function (result, status){
					//console.log(result.html);
					tasks = JSON.parse(result.html);
					var gantt = new Gantt("#gantt", tasks, {
						header_height: 40,
						//column_width: 30,
						//step: 48,
						//view_modes: ['Quarter Day', 'Half Day', 'Day', 'Week', 'Month'],
						bar_height: 22,
						bar_corner_radius: 3,
						arrow_curve: 5,
						padding: 18,
						view_mode: 'Half Day', // ligne 1402 frappe-gantt.js
						date_format: 'DD-MM-YYYY',
						language: 'fr',
						custom_popup_html: null
					});
					// time
					var hour = getHour(":");
					var date = getToday("-");
					$("#day").text(date);
					$("#hour").text(hour);
				},
				error: function(js_result, js_status, error) {
					console.log("erreur");
					console.log(js_result, js_status, error);
					$("#gantt").html(js_result.responseText);
					//$("#gantt").html(JSON.parse(js_result));
				}
			});
			setTimeout(getTask, 1000*60*15); //1000*60 = 1 minute
		}
		</script>
	</body>
</html>