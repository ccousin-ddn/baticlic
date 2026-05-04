<header style="height:72px;">
	<nav class="navbar navbar-dark fixed-top p-0 justify-content-between" style="height:72px;">
		<div class="navbar-brand p-0 h-100">
		<a class=""><img src="images/logo.png" class="d-inline-block align-top"></a><a type="button" class="btn btn-secondary h-100 rounded-0" onclick="stock_action({action:'home'})" style="line-height:5rem; width:200px;">
			<i class="fas fa-home fa-3x"></i>
		</a>
		</div>
		<div class="clock bg-dark text-white text-center">
			<div id="Date" class="color1 text-nowrap"><?php echo ucwords(IntlDateFormatter::formatObject(new DateTime(), 'eeee dd MMMM yyyy', 'fr')); ?></div>
			<ul>
				<li id="hours">00</li>
			    <li>:</li>
			    <li id="min">00</li>
			    <li>:</li>
			    <li id="sec">00</li>
			</ul>
		</div>
		
		<a type="button" class="btn btn-danger rounded-0 h-100 py-2 px-4" href="index_logout.php" style="font-size:1.5rem;line-height: 2.5;">
			<i class="fal fa-unlock-alt mr-2"></i><span><?php echo gettext("Me déconnecter")?></span>
		</a>

	</nav>
</header>