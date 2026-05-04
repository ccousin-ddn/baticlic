<?php
	
?>
<header style="height:60px;">
	<div class="progressbar">
		<div id="waiting" class="indeterminate" style="display: none;"></div>
	</div>
	<nav class="navbar navbar-top navbar-light navbar-expand-lg fixed-top p-0 bg-white" style="height:60px;">
		<a class="navbar-brand p-0 h-100 m-0" href="" target="_blank">
		    <img src="images/logo.png" alt="">
		</a>
		<?php if (!CONNECTED): ?>
			<?php if (LOCATION == "office"): ?>
		<span class="navbar-text" style="position: absolute; right: 20px; cursor:pointer;" onclick="toggleLogin();">
			<h3><i class="fal fa-sign-in-alt mr-2"></i>Connexion</h3>
		</span>
			<?php endif; ?>
		<?php else: ?>
		<button class="navbar-toggler mr-3" type="button" data-toggle="collapse" data-target="#navbar-menu" aria-controls="navbar-menu" aria-expanded="false" aria-label="Toggle navigation">
			<span class="navbar-toggler-icon"></span>
		</button>
		
		<div class="btn-toolbar align-items-center px-2 d-xl-block d-none" role="toolbar" aria-label="">
			<div class="btn-group mr-1" role="group" aria-label="First group">
				<button type="button" class="btn btn-outline-client" onclick="showTable('con_contract','resetFilter','showCard')"><i class="fal fa-file-signature fa-fw mr-2"></i><?php echo gettext("Marchés")?></button>
				<button type="button" class="btn btn-outline-client" onclick="showTable('job_job','resetFilter','showCard')"><i class="fal fa-digging fa-fw mr-2"></i><?php echo gettext("Chantiers")?></button>
				<button type="button" class="btn btn-outline-client" onclick="table_action({tablename:'pla_planning', dataSent:{year:<?php echo date("Y")?>, week:<?php echo date("W")?>, way:'now'}, action:'showWeek'})"><i class="fal fa-calendar-week fa-fw mr-2"></i><?php echo gettext("Planning")?></button>
				<button type="button" class="btn btn-outline-client" onclick="showTable('tim_timesheet','resetFilter','showCard')"><i class="fal fa-clock fa-fw mr-2"></i><?php echo gettext("Emplois du temps")?></button>
				<button type="button" class="btn btn-outline-client" onclick="showTable('wor_attendance','resetFilter','showCard')"><i class="fal fa-stopwatch fa-fw mr-2"></i><?php echo gettext("Pointages quotidiens")?></button>
				<button type="button" class="btn btn-outline-client" onclick="table_action({tablename:'wor_attendance',action:'resetFilter'})"><i class="fal fa-calendar-week fa-fw mr-2"></i><?php echo gettext("Pointages hebdomadaires")?></button>
			</div>
		</div>

		<div class="collapse navbar-collapse justify-content-lg-end py-lg-0 py-3 bg-white" id="navbar-menu">
			<ul class="navbar-nav flex-grow-1 justify-content-lg-end align-items-lg-center px-3 px-lg-0 h-100">
				<?php if ($_SESSION['usr_level'] > 0): ?>
				<li class="nav-item"><a class="nav-link" onclick="dashBoard()"><i class="fal fa-tachometer-alt"></i></a></li>
				
				<li class="nav-item dropdown">
					<a class="nav-link dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><span>Achats</span></a>
					<ul class="dropdown-menu dropdown-menu-right">
						<a class="dropdown-item" onclick="showTable('sup_supplier','resetFilter','showCard')"><i class="fal fa-warehouse-alt fa-fw mr-2"></i>Fournisseurs</a>
						<a class="dropdown-item" onclick="showTable('wor_order','resetFilter','showCard')"><i class="fal fa-comment-alt-edit fa-fw mr-2"></i>Demandes job</a>
						<a class="dropdown-item" onclick="showTable('sup_order','resetFilter','showCard')"><i class="fal fa-shopping-cart fa-fw mr-2"></i>Commandes</a>
						<a class="dropdown-item" onclick="showTable('sup_receipt','resetFilter','showCard')"><i class="fal fa-clipboard-check fa-fw mr-2"></i>Réceptions</a>
					</ul>
				</li>
				
				<li class="nav-item dropdown">
					<a class="nav-link dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><span>Stocks</span></a>
					<ul class="dropdown-menu dropdown-menu-right">
						<a class="dropdown-item" onclick="showTable('sho_shop','resetFilter','showCard');"><i class="fal fa-warehouse-alt mr-2 fa-fw"></i><span><?php echo gettext("Magasins")?></span></a>
						<a class="dropdown-item" onclick="showTable('mov_movement','resetFilter','showCard');"><i class="fal fa-exchange-alt mr-2 fa-fw"></i><span><?php echo gettext("Mouvements")?></span></a>
						<?php if ($_SESSION['usr_level'] > 1): ?>
						<div class="dropdown-divider"></div>
						<li class="dropdown-submenu">
							<a class="dropdown-item dropdown-toggle" role="button" aria-haspopup="true" aria-expanded="false"><span><i class="fal fa-tools fa-fw mr-2"></i>Articles</span></a>
							<ul class="dropdown-menu">
								<a class="dropdown-item div-search" data-table="art_article">
									<i class="fal fa-search mr-2"></i><input id="art_article_search" class="dropdown_search" type="search" placeholder="<?php echo gettext("Rechercher")?>" aria-label="Search">
								</a>
								<a class="dropdown-item" onclick="showCard('art_article','0')">
									<i class="fal fa-plus mr-3"></i><?php echo gettext("Nouvel article")?>
								</a>
								<a class="dropdown-item" onclick="showTable('art_article','resetFilter','showCard')">
									<i class="fal fa-table mr-3"></i><?php echo gettext("Tableau")?>
								</a>
							</ul>
						</li>
						<a class="dropdown-item" onclick="showTable('art_category','resetFilter','showCard')">
							<i class="fal fa-list fa-fw mr-2"></i>Catégories
						</a>
						<a class="dropdown-item" onclick="showTable('art_subcategory','resetFilter','showCard')">
							<i class="fal fa-list-ul fa-fw mr-2"></i>Sous-catégories
						</a>
						<?php endif; ?>
					</ul>
				</li>
				
				<?php endif; ?>
				
				<li class="nav-item dropdown">
					<a class="nav-link dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
						<i class="fal fa-user"></i>
						<span><?php echo $_SESSION['usr_firstname']?></span>
					</a>
					<ul class="dropdown-menu dropdown-menu-right">
						<a class="dropdown-item" onclick="userPassword('showChangingPassword',event)">
							<i class="fal fa-key mr-2 fa-fw"></i><span><?php echo gettext("Changer mon mot de passe")?></span>
						</a>
						
						<div class="dropdown-divider"></div>
						<a class="dropdown-item" href="index_logout.php">
							<i class="fal fa-sign-out-alt mr-2 fa-fw"></i><span><?php echo gettext("Me déconnecter")?></span>
						</a>
					</ul>
				</li>
				
			</ul>
		</div>
		<?php endif; ?>
	</nav>
</header>