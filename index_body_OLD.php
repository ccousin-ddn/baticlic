<?php
	include_once "libraries/lib_include.php";
	
	$cfg = new cfg_config();
	$stats = $cfg->c_dashboard();
	
	$job = new job_job();
	$job->c_dashboard();
	
	$quo = new quo_quotation();
	$quo->c_dashboard();
	
	$bil = new bil_biller();
	$bil->c_dashboard();
	
	$inv = new inv_invoice();
	$inv->c_dashboard();
	
	$years = '';
	for($i = 2019; $i <= (date("m")<10 ? date("Y") : date("Y")+1); $i++){
		$years .= '<a class="dropdown-item text-right ca_year'.($i == date("Y")?" active":"").'" data-year="'.$i.'">'.$i.'</a>';
	}
	
	// toast
	$control = new veh_control();
	$control->c_checkNext();
	
	$maintenance = new veh_maintenance();
	$maintenance->c_checkNext();
	
	$medical = new wor_medical();
	$medical->c_checkNext();
?>
	<div id="full-content" class="container-fluid bg pb-3">
		<div id="toasts" style="position: absolute; top: 70px; right: 10px; z-index:10;">
			<?php echo $control->json['html']; ?>
			<?php echo $maintenance->json['html']; ?>
			<?php echo $medical->json['html']; ?>
		</div>
		<div class="page-header text-white text-center p-3">
			<h3 class="m-0"><i class="fal fa-tachometer-alt mr-2"></i>Tableau de bord</h3>
		</div>
		<div class="dashboard">
			<div class="row">
				<div class="col-lg-4 col-md-6">
					<?php if ($_SESSION['usr_level'] > 1): ?>
					<div class="card mb-3" data-table="bil_biller" data-action="showCard">
						<div class="card-header">
							<h4><i class="fad fa-shopping-cart fa-fw mr-2"></i>Fournisseurs à payer<span class="ml-3 badge badge-light"><small><?php echo $bil->count ?></small></span></h4>
							<div class="menu">
								<div class="dropdown">
									<div class="dropdown-toggle" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
										<i class="fal fa-ellipsis-v"></i>
									</div>
									<div class="dropdown-menu dropdown-menu-right">
										<a class="dropdown-item" onclick="sortList('ul_biller', 'li', 'name', 'up')"><i class="fal fa-sort-alpha-down"></i></a>
										<a class="dropdown-item" onclick="sortList('ul_biller', 'li', 'name', 'down')"><i class="fal fa-sort-alpha-up-alt"></i></a>
										<a class="dropdown-item" onclick="sortList('ul_biller', 'li', 'days', 'up')"><i class="fal fa-sort-numeric-down"></i></a>
										<a class="dropdown-item" onclick="sortList('ul_biller', 'li', 'days', 'down')"><i class="fal fa-sort-numeric-up-alt"></i></a>
									</div>
								</div>
							</div>
						</div>
						<?php echo $bil->json['html']?>
						<div class="card-footer d-flex">
							<span>Total</span>
							<span class="flex-grow-1 text-right" id="bil_total"><?php echo $bil->json['tot']?></span>
						</div>
					</div>
					<?php endif; ?>
					<div class="card mb-3" data-table="job_job" data-action="showCard">
						<div class="card-header">
							<h4><i class="fad fa-business-time fa-fw mr-2"></i>Jobs sans devis<span class="ml-3 badge badge-light"><small><?php echo $job->count ?></small></span></h4>
						</div>
						<?php echo $job->json['html']?>
					</div>
					<?php if ($_SESSION['iduser'] == 8): ?>
					<div class="card mb-3" data-table="quo_quotation" data-action="showCard">
						<div class="card-header">
							<h4><i class="fad fa-calculator fa-fw mr-2"></i>Devis pas envoyés<span class="ml-3 badge badge-light"><small><?php echo $quo->count ?></small></span></h4>
						</div>
						<?php echo $quo->json['html']?>
					</div>
					<?php endif; ?>
				</div>
				
				<div class="col-lg-4 col-md-6">
					<?php if ($_SESSION['usr_level'] > 1 || $_SESSION['iduser'] == 8): ?>
					<div class="card mb-3" data-table="inv_invoice" data-action="showCard">
						<div class="card-header">
							<h4><i class="fad fa-file-invoice fa-fw mr-2"></i>Factures en retard<span class="ml-3 badge badge-light"><small><?php echo $inv->count ?></small></span></h4>
							<div class="menu">
								<div class="dropdown">
									<div class="dropdown-toggle" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
										<i class="fal fa-ellipsis-v"></i>
									</div>
									<div class="dropdown-menu dropdown-menu-right">
										<a class="dropdown-item" onclick="sortList('ul_invoice', 'li', 'name', 'up')"><i class="fal fa-sort-alpha-down"></i></a>
										<a class="dropdown-item" onclick="sortList('ul_invoice', 'li', 'name', 'down')"><i class="fal fa-sort-alpha-up-alt"></i></a>
										<a class="dropdown-item" onclick="sortList('ul_invoice', 'li', 'days', 'up')"><i class="fal fa-sort-numeric-down"></i></a>
										<a class="dropdown-item" onclick="sortList('ul_invoice', 'li', 'days', 'down')"><i class="fal fa-sort-numeric-up-alt"></i></a>
									</div>
								</div>
							</div>
						</div>
						<?php echo $inv->json['html']?>
						<div class="card-footer d-flex">
							<span>Total</span>
							<span class="flex-grow-1 text-right" id="inv_total"><?php echo $inv->json['tot']?></span>
						</div>
					</div>
					<?php endif; ?>
					<?php if ($_SESSION['iduser'] != 8): ?>
					<div class="card mb-3" data-table="quo_quotation" data-action="showCard">
						<div class="card-header">
							<h4><i class="fad fa-calculator fa-fw mr-2"></i>Devis pas envoyés<span class="ml-3 badge badge-light"><small><?php echo $quo->count ?></small></span></h4>
						</div>
						<?php echo $quo->json['html']?>
					</div>
					<?php endif; ?>
				</div>
				
				<div class="col-lg-4 col-md-6">
					<div class="card mb-3">
						<div class="card-header">
							<h4><i class="fad fa-analytics fa-fw mr-2"></i>Statistiques</h4>
						</div>
						<div class="card-body d-flex align-items-end flex-column text-right">
							<h5>Jobs en cours<span class="ml-2 badge badge-light"><?php echo $stats->tot_job ?></span></h5>
							<h5>Tâches en cours <span class="ml-2 badge badge-light"><?php echo $stats->tot_tas ?></span></h5>
							<h5>Devis envoyés<span class="ml-2 badge badge-light"><?php echo $stats->tot_quo ?></span></h5>
							<h5>Commandes en cours<span class="ml-2 badge badge-light"><?php echo $stats->tot_ord ?></span></h5>
							<h5>Compagnons actifs<span class="ml-2 badge badge-light"><?php echo $stats->tot_wor ?></span></h5>
						</div>
					</div>
					<?php if ($_SESSION['usr_level'] > 1): ?>
					<div class="card mb-3" id="netChart">
						<div class="card-header text-white pb-0">
							<h6>
								<?php echo gettext("CA")?> : <span id="tot_ca" class="ml-2"></span>
								<span class="color1 ml-3"><?php echo gettext("NET")?> : <span id="tot_net" class="ml-2"></span></span>
							</h6>
							<h6 class="text-green"><?php echo gettext("CAP")?> : <span class="ml-2"><?php echo alterData("euro",$stats->tot_cap) ?></span></h6>
							<div class="menu">
								<div class="dropdown">
									<div class="dropdown-toggle" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
										<span id="year"><?php echo date("Y")?></span>
									</div>
									<div class="dropdown-menu dropdown-menu-right">
										<?php echo $years?>
									</div>
								</div>
							</div>
						</div>
						<div class="card-body py-0" id="chart_ca">
							
						</div>
					</div>
					<?php endif; ?>
				</div>
			</div>
		</div>
		<div id="popup-chart" class="modal fade">
			<div class="modal-dialog modal-xl modal-dialog-scrollable h-100" role="document">
				<div class="modal-content">
					<div class="modal-header">
						<h4 class="modal-title" id="modaltitle"><i class="fal fa-chart-pie mr-2"></i><span></span></h4>
						<button class="close" aria-hidden="true" data-dismiss="modal" type="button" aria-label="Close">
							<i class="fal fa-times-circle"></i>
						</button>
					</div>
					<div class="modal-body row">
						<div class="col-6" id="pie_invoice"></div>
						<div class="col-6" id="pie_biller"></div>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-outline-secondary" onclick="$('#popup-chart').modal('hide')"><i class="fal fa-times mr-md-2"></i><span class="d-none d-md-inline">Fermer</span></button>
					</div>
				</div>
			</div>
		</div>
	</div>