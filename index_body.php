<?php
	include_once "libraries/lib_include.php";
	
	$usr = new usr_user();
	$usr->c_dashboard();
	$dashboards = $usr->json['info'];
	
	/*** 0 ***/
	if(in_array(0,$dashboards)){
		$control = new veh_control();
		$control->c_checkNext();
		
		$maintenance = new veh_maintenance();
		$maintenance->c_checkNext();
		
		$medical = new wor_medical();
		$medical->c_checkNext();
		
		$toast = '
		<div id="toasts" style="position: absolute; top: 70px; right: 10px; z-index:10;">
			'.$control->json['html'].'
			'.$maintenance->json['html'].'
			'.$medical->json['html'].'
		</div>
		';
	}
	
	/*** 1 ***/
	if(in_array(1,$dashboards)){
		$bil = new bil_biller();
		$bil->c_dashboard();
		
		$biller = '
		<div class="card full" data-table="bil_biller" data-action="showCard">
			<div class="card-header">
				<h4><i class="fad fa-warehouse-alt fa-fw mr-2"></i>Fournisseurs à payer<span class="ml-3 badge badge-light"><small>'.$bil->count.'</small></span></h4>
				<div class="menu">
					<div class="dropdown">
						<div class="dropdown-toggle" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
							<i class="fal fa-ellipsis-v"></i>
						</div>
						<div class="dropdown-menu dropdown-menu-right">
							<a class="dropdown-item" onclick="sortList(\'ul_biller\', \'li\', \'name\', \'up\')"><i class="fal fa-sort-alpha-down"></i></a>
							<a class="dropdown-item" onclick="sortList(\'ul_biller\', \'li\', \'name\', \'down\')"><i class="fal fa-sort-alpha-up-alt"></i></a>
							<a class="dropdown-item" onclick="sortList(\'ul_biller\', \'li\', \'days\', \'up\')"><i class="fal fa-sort-numeric-down"></i></a>
							<a class="dropdown-item" onclick="sortList(\'ul_biller\', \'li\', \'days\', \'down\')"><i class="fal fa-sort-numeric-up-alt"></i></a>
						</div>
					</div>
				</div>
			</div>
			'.$bil->json['html'].'
			<div class="card-footer d-flex">
				<span>Total<small id="bil_total" class="pl-3"></small></span>
				<span class="flex-grow-1 text-right">'.$bil->json['tot'].'</span>
			</div>
		</div>
		';
	}
	
	/*** 2 ***/
	if(in_array(2,$dashboards)){
		$inv = new inv_invoice();
		$inv->c_dashboard();
		
		$invoice = '
		<div class="card full" data-table="inv_invoice" data-action="showCard">
			<div class="card-header">
				<h4><i class="fad fa-file-invoice fa-fw mr-2"></i>Factures en retard<span class="ml-3 badge badge-light"><small>'.$inv->count.'</small></span></h4>
				<div class="menu">
					<div class="dropdown">
						<div class="dropdown-toggle" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
							<i class="fal fa-ellipsis-v"></i>
						</div>
						<div class="dropdown-menu dropdown-menu-right">
							<a class="dropdown-item" onclick="sortList(\'ul_invoice\', \'li\', \'name\', \'up\')"><i class="fal fa-sort-alpha-down"></i></a>
							<a class="dropdown-item" onclick="sortList(\'ul_invoice\', \'li\', \'name\', \'down\')"><i class="fal fa-sort-alpha-up-alt"></i></a>
							<a class="dropdown-item" onclick="sortList(\'ul_invoice\', \'li\', \'days\', \'up\')"><i class="fal fa-sort-numeric-down"></i></a>
							<a class="dropdown-item" onclick="sortList(\'ul_invoice\', \'li\', \'days\', \'down\')"><i class="fal fa-sort-numeric-up-alt"></i></a>
						</div>
					</div>
				</div>
			</div>
			'.$inv->json['html'].'
			<div class="card-footer d-flex">
				<span>Total<small id="inv_total" class="pl-3"></small></span>
				<span class="flex-grow-1 text-right">'.$inv->json['tot'].'</span>
			</div>
		</div>
		';
	}
	
	/*** 3 ***/
	if(in_array(3,$dashboards)){
		$job = new job_job();
		$job->c_dashboard();
		
		$jobs = '
		<div class="card" data-table="job_job" data-action="showCard">
			<div class="card-header">
				<h4><i class="fad fa-business-time fa-fw mr-2"></i>Chantiers sans devis<span class="ml-3 badge badge-light"><small>'.$job->count.'</small></span></h4>
			</div>
			'.$job->json['html'].'
		</div>
		';
	}
	
	/*** 4 ***/
	if(in_array(4,$dashboards)){
		$quo = new quo_quotation();
		$quo->c_dashboard();
		
		$quotation = '
		<div class="card" data-table="quo_quotation" data-action="showCard">
			<div class="card-header">
				<h4><i class="fad fa-calculator fa-fw mr-2"></i>Devis pas envoyés<span class="ml-3 badge badge-light"><small>'.$quo->count.'</small></span></h4>
			</div>
			'.$quo->json['html'].'
		</div>
		';
	}
	
	/*** 5 ***/
	if(in_array(5,$dashboards)){
		$cfg = new cfg_config();
		$stats = $cfg->c_dashboard();
		
		$stat = '
		<div class="card">
			<div class="card-header">
				<h4><i class="fad fa-analytics fa-fw mr-2"></i>Statistiques</h4>
			</div>
			<div class="card-body d-flex align-items-end flex-column text-right">
				<h5>Jobs en cours<span class="ml-2 badge badge-light">'.$stats->tot_job.'</span></h5>
				<h5>Tâches en cours <span class="ml-2 badge badge-light">'.$stats->tot_tas.'</span></h5>
				<h5>Devis envoyés<span class="ml-2 badge badge-light">'.$stats->tot_quo.'</span></h5>
				<h5>Commandes en cours<span class="ml-2 badge badge-light">'.$stats->tot_ord.'</span></h5>
				<h5>Compagnons actifs<span class="ml-2 badge badge-light">'.$stats->tot_wor.'</span></h5>
			</div>
		</div>
		';
	}
	
	/*** 6 ***/
	if(in_array(6,$dashboards)){
		$cfg = new cfg_config();
		$stats = $cfg->c_dashboard();
		$years = '';
		for($i = 2023; $i <= (date("m")<10 ? date("Y") : date("Y")+1); $i++){
			$years .= '<a class="dropdown-item text-right ca_year'.($i == date("Y")?" active":"").'" data-year="'.$i.'">'.$i.'</a>';
		}
		$chart = '
		<div class="card" id="netChart">
			<div class="card-header text-white pb-0">
				<h6>
					CA : <span id="tot_ca" class="ml-2"></span>
					<span class="color1 ml-3">NET : <span id="tot_net" class="ml-2"></span></span>
				</h6>
				<h6 class="text-green">CAP : <span class="ml-2">'.alterData("euro",$stats->tot_cap).'</span></h6>
				<div class="menu">
					<div class="dropdown">
						<div class="dropdown-toggle" role="button" id="dropdownMenuLink" data-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
							<span id="year">'.date("Y").'</span>
						</div>
						<div class="dropdown-menu dropdown-menu-right">
							'.$years.'
						</div>
					</div>
				</div>
			</div>
			<div class="card-body py-0" id="chart_ca">
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
						<div class="col-xl-6" id="pie_invoice"></div>
						<div class="col-xl-6" id="pie_biller"></div>
					</div>
					<div class="modal-footer">
						<button type="button" class="btn btn-outline-secondary" onclick="$(\'#popup-chart\').modal(\'hide\')"><i class="fal fa-angle-left mr-md-2"></i><span class="d-none d-md-inline">Annuler</span></button>
					</div>
				</div>
			</div>
		</div>
		';
	}
	
	/*** 7 ***/
	if(in_array(7,$dashboards)){
		$sto = new sto_stock();
		$sto->c_dashboard();
		
		$stock = '
		<div class="card" data-table="sho_shop" data-action="showCard">
			<div class="card-header">
				<h4><i class="fad fa-empty-set fa-fw mr-2"></i>Rupture de stock<span class="ml-3 badge badge-light"><small>'.$sto->count.'</small></span></h4>
			</div>
			'.$sto->json['html'].'
		</div>
		';
	}
	
	/*** 8 ***/
	if(in_array(8,$dashboards)){
		$ord = new sup_order();
		$ord->c_dashboard();
		
		$order = '
		<div class="card" data-table="sup_order" data-action="showCard">
			<div class="card-header">
				<h4><i class="fad fa-shopping-cart fa-fw mr-2"></i>Commandes envoyées<span class="ml-3 badge badge-light"><small>'.$ord->count.'</small></span></h4>
			</div>
			'.$ord->json['html'].'
		</div>
		';
	}
?>
	<div id="full-content" class="container-fluid bg pb-3">
		<div class="page-header text-white text-center p-3">
			<h3 class="m-0"><i class="fal fa-tachometer-alt mr-2"></i>Tableau de bord</h3>
		</div>
		<div class="dashboard">
			<?php echo $toast??""; ?>
			<?php echo $biller??""; ?>
			<?php echo $invoice??""; ?>
			<?php echo $jobs??""; ?>
			<?php echo $quotation??""; ?>
			<?php echo $stock??""; ?>
			<?php echo $order??""; ?>
			<?php echo $stat??""; ?>
			<?php echo $chart??""; ?>
		</div>
	</div>
<script>
	document.addEventListener('DOMContentLoaded', function() {
		dashBoard_action();
		$('.toast').toast("show");
	})
</script>