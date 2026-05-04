<?php
	
?>
<header style="height:60px;">
	<div class="progressbar">
		<div id="waiting" class="indeterminate" style="display: none;"></div>
	</div>
	<nav class="navbar navbar-top navbar-light navbar-expand-lg fixed-top p-0 bg-white" style="height:60px;">
		<a class="navbar-brand py-0 px-2 h-100 m-0" href="" target="_blank">
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
				<!--<button type="button" class="btn btn-outline-client" onclick="showTable('quo_quotation','resetFilter','showCard')"><i class="fal fa-calculator fa-fw mr-2"></i><?php echo gettext("Devis")?></button>-->
				<button type="button" class="btn btn-outline-client" onclick="showTable('sup_order','resetFilter','showCard')"><i class="fal fa-shopping-cart fa-fw mr-2"></i><?php echo gettext("Commandes")?></button>
			</div>
			<?php if ($_SESSION['usr_level'] > 0): ?>
			<div class="btn-group d-none d-xxl" role="group" aria-label="Second group">
				<!--<button type="button" class="btn btn-outline-client" onclick="showTable('tim_timesheet','resetFilter','showCard')"><i class="fal fa-clock fa-fw mr-2"></i><?php echo gettext("Emplois du temps")?></button>-->
				<button type="button" class="btn btn-outline-client" onclick="showTable('wor_attendance','resetFilter','showCard')"><i class="fal fa-stopwatch fa-fw mr-2"></i><?php echo gettext("Pointages")?></button>
			</div>
			<?php endif; ?>
		</div>

		<div class="collapse navbar-collapse justify-content-lg-end py-lg-0 py-3 bg-white" id="navbar-menu">
			<ul class="navbar-nav flex-grow-1 justify-content-lg-end align-items-lg-center px-3 px-lg-0 h-100">
				<?php if ($_SESSION['usr_level'] >= 0): ?>
				<li class="nav-item"><a class="nav-link" onclick="dashBoard()"><i class="fal fa-tachometer-alt"></i></a></li>
				
				<li class="nav-item dropdown">
					<a class="nav-link dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><span>Ventes</span></a>
					<ul class="dropdown-menu dropdown-menu-right">
						<li class="dropdown-submenu">
							<a class="dropdown-item dropdown-toggle" role="button" aria-haspopup="true" aria-expanded="false"><span><i class="fal fa-address-card fa-fw mr-2"></i>Clients</span></a>
							<ul class="dropdown-menu">
								<a class="dropdown-item div-search" data-table="cli_client">
									<i class="fal fa-search mr-2"></i><input id="cli_client_search" class="dropdown_search" type="search" placeholder="<?php echo gettext("Rechercher")?>" aria-label="Search">
								</a>
								<a class="dropdown-item" onclick="showCard('cli_client','0')">
									<i class="fal fa-plus mr-3"></i><?php echo gettext("Nouveau client")?>
								</a>
								<a class="dropdown-item" onclick="showTable('cli_client','resetFilter','showCard')">
									<i class="fal fa-table mr-3"></i><?php echo gettext("Tableau")?>
								</a>
							</ul>
						</li>
						<li class="dropdown-submenu">
							<a class="dropdown-item dropdown-toggle" role="button" aria-haspopup="true" aria-expanded="false"><span><i class="fal fa-file-signature fa-fw mr-2"></i>Marchés</span></a>
							<ul class="dropdown-menu">
								<a class="dropdown-item div-search" data-table="con_contract">
									<i class="fal fa-search mr-2"></i><input id="con_contract_search" class="dropdown_search" type="search" placeholder="<?php echo gettext("Rechercher")?>" aria-label="Search">
								</a>
								<a class="dropdown-item" onclick="showCard('con_contract','0')">
									<i class="fal fa-plus mr-3"></i><?php echo gettext("Nouveau marché")?>
								</a>
								<a class="dropdown-item" onclick="showTable('con_contract','resetFilter','showCard')">
									<i class="fal fa-table mr-3"></i><?php echo gettext("Tableau")?>
								</a>
							</ul>
						</li>
						<li class="dropdown-submenu">
							<a class="dropdown-item dropdown-toggle" role="button" aria-haspopup="true" aria-expanded="false"><span><i class="fal fa-map-marker-alt fa-fw mr-2"></i>Sites</span></a>
							<ul class="dropdown-menu">
								<a class="dropdown-item div-search" data-table="sit_site">
									<i class="fal fa-search mr-2"></i><input id="sit_site_search" class="dropdown_search" type="search" placeholder="<?php echo gettext("Rechercher")?>" aria-label="Search">
								</a>
								<a class="dropdown-item" onclick="showCard('sit_site','0')">
									<i class="fal fa-plus mr-3"></i><?php echo gettext("Nouveau Site")?>
								</a>
								<a class="dropdown-item" onclick="showTable('sit_site','resetFilter','showCard')">
									<i class="fal fa-table mr-3"></i><?php echo gettext("Tableau")?>
								</a>
							</ul>
						</li>
						<li class="dropdown-submenu">
							<a class="dropdown-item dropdown-toggle" role="button" aria-haspopup="true" aria-expanded="false"><span><i class="fal fa-digging fa-fw mr-2"></i>Chantiers</span></a>
							<ul class="dropdown-menu">
								<a class="dropdown-item div-search" data-table="job_job">
									<i class="fal fa-search mr-2"></i><input id="job_job_search" class="dropdown_search" type="search" placeholder="<?php echo gettext("Rechercher")?>" aria-label="Search">
								</a>
								<a class="dropdown-item" onclick="showCard('job_job','0')">
									<i class="fal fa-plus mr-3"></i><?php echo gettext("Nouveau chantier")?>
								</a>
								<a class="dropdown-item" onclick="showTable('job_job','resetFilter','showCard')">
									<i class="fal fa-table mr-3"></i><?php echo gettext("Tableau")?>
								</a>
							</ul>
						</li>
						<div class="dropdown-divider"></div>
						<li class="dropdown-submenu">
							<a class="dropdown-item dropdown-toggle" role="button" aria-haspopup="true" aria-expanded="false"><span><i class="fal fa-calculator fa-fw mr-2"></i>Devis</span></a>
							<ul class="dropdown-menu">
								<a class="dropdown-item div-search" data-table="quo_quotation">
									<i class="fal fa-search mr-2"></i><input id="quo_quotation_search" class="dropdown_search" type="search" placeholder="<?php echo gettext("Rechercher")?>" aria-label="Search">
								</a>
								<a class="dropdown-item" onclick="showCard('quo_quotation','0')">
									<i class="fal fa-plus mr-3"></i><?php echo gettext("Nouveau devis")?>
								</a>
								<a class="dropdown-item" onclick="showTable('quo_quotation','resetFilter','showCard')">
									<i class="fal fa-table mr-3"></i><?php echo gettext("Tableau")?>
								</a>
							</ul>
						</li>
						<li class="dropdown-submenu">
							<a class="dropdown-item dropdown-toggle" role="button" aria-haspopup="true" aria-expanded="false"><span><i class="fal fa-file-invoice fa-fw mr-2"></i>Factures</span></a>
							<ul class="dropdown-menu">
								<a class="dropdown-item div-search" data-table="inv_invoice">
									<i class="fal fa-search mr-2"></i><input id="inv_invoice_search" class="dropdown_search" type="search" placeholder="<?php echo gettext("Rechercher")?>" aria-label="Search">
								</a>
								<a class="dropdown-item" onclick="showCard('inv_invoice','0')">
									<i class="fal fa-plus mr-3"></i><?php echo gettext("Nouvelle facture")?>
								</a>
								<a class="dropdown-item" onclick="showTable('inv_invoice','resetFilter','showCard')">
									<i class="fal fa-table mr-3"></i><?php echo gettext("Tableau")?>
								</a>
							</ul>
						</li>
						<div class="dropdown-divider"></div>
						<a class="dropdown-item" onclick="showTable('quo_comment','resetFilter','showCard')"><i class="fal fa-comment fa-fw mr-2"></i><?php echo gettext("Commentaires devis")?></a>
						<div class="dropdown-divider"></div>
						<a class="dropdown-item" onclick="showTable('mem_memo','resetFilter','showCard')"><i class="fal fa-edit fa-fw mr-2"></i><?php echo gettext("Mémo")?></a>
					</ul>
				</li>
				
				<li class="nav-item dropdown">
					<a class="nav-link dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><span>Tarifications</span></a>
					<ul class="dropdown-menu dropdown-menu-right">
						<li class="dropdown-submenu">
							<a class="dropdown-item dropdown-toggle" role="button" aria-haspopup="true" aria-expanded="false"><span><i class="fal fa-th-list fa-fw mr-2"></i>Bibliothèque</span></a>
							<ul class="dropdown-menu">
								<a class="dropdown-item div-search" data-table="lib_library">
									<i class="fal fa-search mr-2"></i><input id="lib_library_search" class="dropdown_search" type="search" placeholder="<?php echo gettext("Rechercher")?>" aria-label="Search">
								</a>
								<a class="dropdown-item" onclick="showCard('lib_library','0')">
									<i class="fal fa-plus mr-3"></i><?php echo gettext("Nouvelle bibliothèque")?>
								</a>
								<a class="dropdown-item" onclick="showTable('lib_library','resetFilter','showCard')">
									<i class="fal fa-table mr-3"></i><?php echo gettext("Tableau")?>
								</a>
							</ul>
						</li>
						<li class="dropdown-submenu">
							<a class="dropdown-item dropdown-toggle" role="button" aria-haspopup="true" aria-expanded="false"><span><i class="fal fa-toolbox fa-fw mr-2"></i>Prestations</span></a>
							<ul class="dropdown-menu">
								<a class="dropdown-item div-search" data-table="lib_work">
									<i class="fal fa-search mr-2"></i><input id="lib_work_search" class="dropdown_search" type="search" placeholder="<?php echo gettext("Rechercher")?>" aria-label="Search">
								</a>
								<a class="dropdown-item" onclick="showCard('lib_work','0')">
									<i class="fal fa-plus mr-3"></i><?php echo gettext("Nouvelle prestation")?>
								</a>
								<a class="dropdown-item" onclick="showTable('lib_work','resetFilter','showCard')">
									<i class="fal fa-table mr-3"></i><?php echo gettext("Tableau")?>
								</a>
							</ul>
						</li>
						<div class="dropdown-divider"></div>
						<a class="dropdown-item" onclick="showTable('wor_category','resetFilter','showCard')">
							<i class="fal fa-list fa-fw mr-2"></i><?php echo gettext("Catégories")?>
						</a>
						<a class="dropdown-item" onclick="showTable('wor_subcategory','resetFilter','showCard')">
							<i class="fal fa-list-ul fa-fw mr-2"></i><?php echo gettext("Sous-catégories")?>
						</a>
					</ul>
				</li>
				
				<li class="nav-item dropdown">
					<a class="nav-link dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><span>Achats</span></a>
					<ul class="dropdown-menu dropdown-menu-right">
						<li class="dropdown-submenu">
							<a class="dropdown-item dropdown-toggle" role="button" aria-haspopup="true" aria-expanded="false"><span><i class="fal fa-warehouse-alt fa-fw mr-2"></i>Fournisseurs</span></a>
							<ul class="dropdown-menu">
								<a class="dropdown-item div-search" data-table="sup_supplier">
									<i class="fal fa-search mr-2"></i><input id="sup_supplier_search" class="dropdown_search" type="search" placeholder="<?php echo gettext("Rechercher")?>" aria-label="Search">
								</a>
								<a class="dropdown-item" onclick="showCard('sup_supplier','0')">
									<i class="fal fa-plus mr-3"></i><?php echo gettext("Nouveau fournisseur")?>
								</a>
								<a class="dropdown-item" onclick="showTable('sup_supplier','resetFilter','showCard')">
									<i class="fal fa-table mr-3"></i><?php echo gettext("Tableau")?>
								</a>
							</ul>
						</li>
						<li class="dropdown-submenu">
							<a class="dropdown-item dropdown-toggle" role="button" aria-haspopup="true" aria-expanded="false"><span><i class="fal fa-shopping-cart fa-fw mr-2"></i>Commandes</span></a>
							<ul class="dropdown-menu">
								<a class="dropdown-item div-search" data-table="sup_order">
									<i class="fal fa-search mr-2"></i><input id="sup_order_search" class="dropdown_search" type="search" placeholder="<?php echo gettext("Rechercher")?>" aria-label="Search">
								</a>
								<a class="dropdown-item" onclick="showCard('sup_order','0')">
									<i class="fal fa-plus mr-3"></i><?php echo gettext("Nouvelle commande")?>
								</a>
								<a class="dropdown-item" onclick="showTable('sup_order','resetFilter','showCard')">
									<i class="fal fa-table mr-3"></i><?php echo gettext("Tableau")?>
								</a>
							</ul>
						</li>
						<li class="dropdown-submenu">
							<a class="dropdown-item dropdown-toggle" role="button" aria-haspopup="true" aria-expanded="false"><span><i class="fal fa-clipboard-check fa-fw mr-2"></i>Réceptions</span></a>
							<ul class="dropdown-menu">
								<a class="dropdown-item div-search" data-table="sup_receipt">
									<i class="fal fa-search mr-2"></i><input id="sup_receipt_search" class="dropdown_search" type="search" placeholder="<?php echo gettext("Rechercher")?>" aria-label="Search">
								</a>
								<a class="dropdown-item" onclick="showCard('sup_receipt','0')">
									<i class="fal fa-plus mr-3"></i><?php echo gettext("Nouvelle réception")?>
								</a>
								<a class="dropdown-item" onclick="showTable('sup_receipt','resetFilter','showCard')">
									<i class="fal fa-table mr-3"></i><?php echo gettext("Tableau")?>
								</a>
							</ul>
						</li>
						<li class="dropdown-submenu">
							<a class="dropdown-item dropdown-toggle" role="button" aria-haspopup="true" aria-expanded="false"><span><i class="fal fa-receipt fa-fw mr-2"></i>Frais généraux</span></a>
							<ul class="dropdown-menu">
								<a class="dropdown-item div-search" data-table="bil_biller">
									<i class="fal fa-search mr-2"></i><input id="bil_biller_search" class="dropdown_search" type="search" placeholder="<?php echo gettext("Rechercher")?>" aria-label="Search">
								</a>
								<a class="dropdown-item" onclick="showCard('bil_biller','0')">
									<i class="fal fa-plus mr-3"></i><?php echo gettext("Nouvelle facture")?>
								</a>
								<a class="dropdown-item" onclick="showTable('bil_biller','resetFilter','showCard')">
									<i class="fal fa-table mr-3"></i><?php echo gettext("Tableau")?>
								</a>
								<?php if ($_SESSION['usr_level'] > 1): ?>
								<div class="dropdown-divider"></div>
								<a class="dropdown-item" onclick="table_action({idrecord:0,tablename:'cfg_config',dataSent:{dateFrom:'<?php echo date('01-m-Y')?>',dateTo:'<?php echo date('d-m-Y')?>'},action:'getBreakdown'})">
									<i class="fal fa-chart-pie mr-3"></i><?php echo gettext("Ventilation")?>
								</a>
								<?php endif; ?>
							</ul>
						</li>
						<div class="dropdown-divider"></div>
						<li class="dropdown-submenu">
							<a class="dropdown-item dropdown-toggle" role="button" aria-haspopup="true" aria-expanded="false"><span><i class="fal fa-tag fa-fw mr-2"></i>Tarifs fournisseur</span></a>
							<ul class="dropdown-menu">
								<a class="dropdown-item div-search" data-table="sup_article">
									<i class="fal fa-search mr-2"></i><input id="sup_article_search" class="dropdown_search" type="search" placeholder="<?php echo gettext("Rechercher")?>" aria-label="Search">
								</a>
								<a class="dropdown-item" onclick="showCard('sup_article','0')">
									<i class="fal fa-plus mr-3"></i><?php echo gettext("Nouveau Tarif")?>
								</a>
								<a class="dropdown-item" onclick="showTable('sup_article','resetFilter','showCard')">
									<i class="fal fa-table mr-3"></i><?php echo gettext("Tableau")?>
								</a>
							</ul>
						</li>
					</ul>
				</li>
				
				<li class="nav-item dropdown">
					<a class="nav-link dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><span>Stocks</span></a>
					<ul class="dropdown-menu dropdown-menu-right">
						<a class="dropdown-item" onclick="showTable('sho_shop','resetFilter','showCard');"><i class="fal fa-warehouse-alt mr-2 fa-fw"></i><span><?php echo gettext("Magasins")?></span></a>
						<a class="dropdown-item" onclick="showTable('mov_movement','resetFilter','showCard');"><i class="fal fa-exchange-alt mr-2 fa-fw"></i><span><?php echo gettext("Mouvements")?></span></a>
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
								<div class="dropdown-divider"></div>
								<a class="dropdown-item" onclick="open_popup('popup_upload.php', {table:'art_article', action:'importArtXls'}, 'popup-msg')">
									<i class="fal fa-upload mr-3"></i><?php echo gettext("Importer xls")?>
								</a>
							</ul>
						</li>
						<a class="dropdown-item" onclick="showTable('art_category','resetFilter','showCard')">
							<i class="fal fa-list fa-fw mr-2"></i>Catégories
						</a>
						<a class="dropdown-item" onclick="showTable('art_subcategory','resetFilter','showCard')">
							<i class="fal fa-list-ul fa-fw mr-2"></i>Sous-catégories
						</a>
					</ul>
				</li>
				
				<li class="nav-item dropdown">
					<a class="nav-link dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false"><span>Ressources</span></a>
					<ul class="dropdown-menu dropdown-menu-right">
						<a class="dropdown-item d-xl-block d-none" onclick="table_action({tablename:'pla_planning', dataSent:{year:<?php echo date("Y")?>, week:<?php echo date("W")?>, way:'now'}, action:'showWeek'})"><i class="fal fa-calendar-week fa-fw mr-2"></i><span><?php echo gettext("Planning hebdomadaire")?></span></a>
						<div class="dropdown-divider d-xl-block d-none"></div>
				<?php if ($_SESSION['usr_level'] > 1): ?>
						<li class="dropdown-submenu">
							<a class="dropdown-item dropdown-toggle" role="button" aria-haspopup="true" aria-expanded="false"><span><i class="fal fa-user-hard-hat fa-fw mr-2"></i>Compagnons</span></a>
							<ul class="dropdown-menu">
								<a class="dropdown-item div-search" data-table="wor_worker">
									<i class="fal fa-search mr-2"></i><input id="wor_worker_search" class="dropdown_search" type="search" placeholder="<?php echo gettext("Rechercher")?>" aria-label="Search">
								</a>
								<a class="dropdown-item" onclick="showCard('wor_worker','0')">
									<i class="fal fa-plus mr-3"></i><?php echo gettext("Nouveau compagnon")?>
								</a>
								<a class="dropdown-item" onclick="showTable('wor_worker','resetFilter','showCard')">
									<i class="fal fa-table mr-3"></i><?php echo gettext("Tableau")?>
								</a>
							</ul>
						</li>
						<a class="dropdown-item d-xl-block d-none" onclick="showTable('age_agency','resetFilter','showCard')"><i class="fal fa-house-user fa-fw mr-2"></i><span><?php echo gettext("Agences Intérim")?></span></a>
						<a class="dropdown-item d-xl-block d-none" onclick="showTable('wor_medical','resetFilter','showCard')"><i class="fal fa-user-md fa-fw mr-2"></i><span><?php echo gettext("Visites médicales")?></span></a>
						<a class="dropdown-item d-xl-block d-none" onclick="showTable('wor_training','resetFilter','showCard')"><i class="fal fa-user-graduate fa-fw mr-2"></i><span><?php echo gettext("Parcours Formation")?></span></a>   
				<?php endif; ?>
						<div class="dropdown-divider"></div>
						<li class="dropdown-submenu">
							<a class="dropdown-item dropdown-toggle" role="button" aria-haspopup="true" aria-expanded="false"><span><i class="fal fa-clock fa-fw mr-2"></i>Emplois du temps</span></a>
							<ul class="dropdown-menu">
								<a class="dropdown-item div-search" data-table="tim_timesheet">
									<i class="fal fa-search mr-2"></i><input id="tim_timesheet_search" class="dropdown_search" type="search" placeholder="<?php echo gettext("Rechercher")?>" aria-label="Search">
								</a>
								<a class="dropdown-item" onclick="showCard('tim_timesheet','0')">
									<i class="fal fa-plus mr-3"></i><?php echo gettext("Nouvel emploi du temps")?>
								</a>
								<a class="dropdown-item" onclick="showTable('tim_timesheet','resetFilter','showCard')">
									<i class="fal fa-table mr-3"></i><?php echo gettext("Tableau")?>
								</a>
							</ul>
						</li>
						<li class="dropdown-submenu">
							<a class="dropdown-item dropdown-toggle" role="button" aria-haspopup="true" aria-expanded="false"><span><i class="fal fa-stopwatch fa-fw mr-2"></i>Pointages</span></a>
							<ul class="dropdown-menu">
								<a class="dropdown-item div-search d-xl-block d-none" data-table="wor_attendance">
									<i class="fal fa-search mr-2"></i><input id="wor_attendance_search" class="dropdown_search" type="search" placeholder="<?php echo gettext("Rechercher")?>" aria-label="Search">
								</a>
								<a class="dropdown-item d-xl-block d-none" onclick="showCard('wor_attendance','0')">
									<i class="fal fa-plus mr-3"></i><?php echo gettext("Nouveau pointage")?>
								</a>
								<a class="dropdown-item" onclick="showTable('wor_attendance','resetFilter','showCard')">
									<i class="fal fa-table mr-3"></i><?php echo gettext("Quotidiens")?>
								</a>
								<a class="dropdown-item" onclick="table_action({tablename:'wor_attendance',action:'resetFilter'})">
									<i class="fal fa-calendar-week mr-3"></i><?php echo gettext("Hebdomadaires")?>
								</a>
							</ul>
						</li>
						<div class="dropdown-divider d-xl-block d-none"></div>
						<li class="dropdown-submenu">
							<a class="dropdown-item dropdown-toggle" role="button" aria-haspopup="true" aria-expanded="false"><span><i class="fal fa-car-bus fa-fw mr-2"></i><?php echo gettext("Flotte")?></span></a>
							<ul class="dropdown-menu">
								<a class="dropdown-item" onclick="showTable('veh_vehicle','resetFilter','showCard')">
									<i class="fal fa-truck fa-fw mr-3"></i><?php echo gettext("Véhicules")?>
								</a>
								<div class="dropdown-divider d-xl-block d-none"></div>
								<a class="dropdown-item" onclick="showTable('veh_full','resetFilter','showCard')">
									<i class="fal fa-gas-pump fa-fw mr-3"></i><?php echo gettext("Carburants")?>
								</a>
								<a class="dropdown-item" onclick="showTable('veh_maintenance','resetFilter','showCard')">
									<i class="fal fa-oil-can fa-fw mr-3"></i><?php echo gettext("Entretiens")?>
								</a>
								<a class="dropdown-item" onclick="showTable('veh_control','resetFilter','showCard')">
									<i class="fal fa-ballot-check fa-fw mr-3"></i><?php echo gettext("Contrôles")?>
								</a>
								<!--
								<a class="dropdown-item" onclick="showTable('veh_tire','resetFilter','showCard')">
									<i class="fal fa-tire-flat fa-fw mr-3"></i><?php echo gettext("Pneus")?>
								</a>
								-->
							</ul>
						</li>
					</ul>
				</li>
				<?php if ($_SESSION['usr_level'] > 0): ?>
				<li class="nav-item dropdown">
					<a class="nav-link dropdown-toggle" data-toggle="dropdown" role="button" aria-haspopup="true" aria-expanded="false">
						<i class="fal fa-cogs mr-2"></i><span><?php echo gettext("Configuration")?></span>
					</a>
					<ul class="dropdown-menu dropdown-menu-right">
						<a class="dropdown-item d-xl-block d-none" onclick="showTable('met_metier','resetFilter','showCard')"><i class="fal fa-paint-roller fa-fw mr-2"></i><span><?php echo gettext("Métiers")?></span></a>
						<a class="dropdown-item d-xl-block d-none" onclick="showTable('cli_type','resetFilter','showCard')"><i class="fal fa-tasks fa-fw mr-2"></i><span><?php echo gettext("Types client")?></span></a>
						<a class="dropdown-item d-xl-block d-none" onclick="showTable('cas_type','resetFilter','showCard')"><i class="fal fa-tasks fa-fw mr-2"></i><span><?php echo gettext("Types marché")?></span></a>
						<a class="dropdown-item d-xl-block d-none" onclick="showTable('con_type','resetFilter','showCard')"><i class="fal fa-id-card fa-fw mr-2"></i><span><?php echo gettext("Fonctions contact")?></span></a>
						<a class="dropdown-item d-xl-block d-none" onclick="showTable('abs_type','resetFilter','showCard')"><i class="fal fa-tasks fa-fw mr-2"></i><span><?php echo gettext("Types absence")?></span></a>
						<div class="dropdown-divider d-xl-block d-none"></div>
						<a class="dropdown-item d-xl-block d-none" onclick="showTable('bil_account_lev1','resetFilter','showCard')"><i class="fal fa-list-alt fa-fw mr-2"></i><span><?php echo gettext("Charges niv.1")?></span></a>
						<a class="dropdown-item d-xl-block d-none" onclick="showTable('bil_account_lev2','resetFilter','showCard')"><i class="fal fa-list fa-fw mr-2"></i><span><?php echo gettext("Charges niv.2")?></span></a>
						<a class="dropdown-item d-xl-block d-none" onclick="showTable('bil_account_lev3','resetFilter','showCard')"><i class="fal fa-list-ul fa-fw mr-2"></i><span><?php echo gettext("Charges niv.3")?></span></a>
						<div class="dropdown-divider d-xl-block d-none"></div>
						<a class="dropdown-item d-xl-block d-none" onclick="showTable('ret_retention','resetFilter','showCard');"><i class="fal fa-envelope-open-dollar mr-2 fa-fw"></i><span><?php echo gettext("Retenues")?></span></a>
						<a class="dropdown-item d-xl-block d-none" onclick="showTable('bil_type_payment','resetFilter','showCard')"><i class="fal fa-tasks fa-fw mr-2"></i><span><?php echo gettext("Types paiement")?></span></a>
						<a class="dropdown-item d-xl-block d-none" onclick="showTable('vat_vat','resetFilter','showCard');"><i class="fal fa-percent mr-2 fa-fw"></i><span><?php echo gettext("TVA")?></span></a>
						<?php if ($_SESSION['usr_level'] > 1): ?>
						<div class="dropdown-divider d-xl-block d-none"></div>
						<a class="dropdown-item d-xl-block d-none" onclick="showTable('tra_training','resetFilter','showCard')"><i class="fal fa-users-class fa-fw mr-2"></i><span><?php echo gettext("Formations")?></span></a>
						<div class="dropdown-divider d-xl-block d-none"></div>
						<a class="dropdown-item d-xl-block d-none" onclick="showTable('are_area','resetFilter','showCard')"><i class="fal fa-dot-circle fa-fw mr-2"></i><span><?php echo gettext("Zones")?></span></a>
						<a class="dropdown-item d-xl-block d-none" onclick="showTable('veh_brand','resetFilter','showCard')"><i class="fal fa-copyright fa-fw mr-2"></i><span><?php echo gettext("Marques véhicule")?></span></a>
						<div class="dropdown-divider d-xl-block d-none"></div>
						<a class="dropdown-item" onclick="showCard('cfg_config',0);"><i class="fal fa-address-card fa-fw mr-2"></i><span><?php echo gettext("Infos générales")?></span></a>
						<div class="dropdown-divider"></div>
						<a class="dropdown-item" onclick="showTable('usr_user','resetFilter','showCard')"><i class="fal fa-users-cog fa-fw mr-2"></i><span><?php echo gettext("Utilisateurs")?></span></a>
						<?php endif; ?>
					</ul>
				</li>
				<?php endif; ?>
				
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