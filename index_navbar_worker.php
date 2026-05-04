<header>
	<nav class="navbar navbar-dark fixed-top p-0" style="z-index:100;height:60px;">
		<a class="navbar-brand p-0 h-100">
		    <img src="images/logo.png" class="d-inline-block align-top" alt="">
		</a>
		
		<div class="d-flex flex-row h-100">
			<div class="text-white h-100 align-items-center mr-4">
				<i class="fal fa-user mr-2 d-none d-sm-inline-block"></i>
				<small><?php echo $_SESSION['usr_firstname']?></small>
			</div>
			<a type="button" class="btn btn-danger h-100 py-2 px-4" href="index_logout.php" style="font-size:1.5rem;">
				<i class="fal fa-unlock-alt mr-2"></i><span class="d-none d-md-inline-block"><?php echo gettext("Me déconnecter")?></span>
			</a>
		</div>
		<!--
		<div class="justify-content-end text-light mr-2 h-100" id="navbar-menu">
			<a class="dropdown-toggle d-flex align-items-center h-100" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
				<i class="fal fa-user mr-2"></i>
				<span><?php echo $_SESSION['usr_firstname']?></span>
			</a>
			<div class="dropdown-menu dropdown-menu-right mr-2">

				<a class="dropdown-item" onclick="userPassword('showChangingPassword',event)">
					<i class="fal fa-lock mr-2"></i><span><?php echo gettext("Changer mon mot de passe")?></span>
				</a>
				
				<div class="dropdown-divider"></div>

				<a class="dropdown-item" href="index_logout.php" style="padding:2rem; font-size:1.5rem">
					<i class="fal fa-unlock-alt mr-2"></i><span><?php echo gettext("Me déconnecter")?></span>
				</a>
			</div>
		</div>
		-->
	</nav>
</header>