<?php

?>
	<style type="text/css">
		html {height:100%;}
		body {font-size:1.5rem;height:100%;}
		.clock ul {margin:0 auto; padding:0px; list-style:none; text-align:center; }
		.clock ul li { display:inline; text-align:center; text-shadow:0 0 5px #00c6ff; }
		
		#div_result {background-color:#f7f7f7; min-height: calc(100% - 105px);}
		
		.btn-xl-square {
			padding: .5rem 1rem;
			font-size: 2rem;
			width:300px;
			height:300px;
			line-height: 1.5;
		}
		.list-group-item {padding: .75rem .5rem!important;}
		.list-group-item.disabled {background-color:lightgray;}
		
		#btn_save:disabled {background-color:var(--col_green); opacity:.4;}
		
		.list-group-item-action.active {background-color:var(--col_dark); color:white!important; border:0;}
		.list-group-item-action .inp-qty {width:50px;border: 0;background-color: #eee;padding: 0 .5rem;text-align: right;-moz-appearance: textfield;}
		.list-group-item-action .inp-qty::-webkit-outer-spin-button,
		.list-group-item-action .inp-qty::-webkit-inner-spin-button{-webkit-appearance: none;margin: 0;}
		
		.list-group-item-client-light {background-color:white; color:var(--col_dark);cursor:pointer;}
		.list-group-item-client-light .badge {background-color:var(--col_light); color:white;}
		.list-group-item-client-light:hover, .list-group-item-client-light.active, .card.active {background-color:var(--col_light); color:white!important;}
		.list-group-item-client-light:hover .badge, .list-group-item-client-light.active .badge {background-color:white; color:var(--col_light);}
		
		.brand-logo {height:25px;}
		.veh-plate {border:2px solid black; border-radius:6px; font-size:2rem; height:27px; width:135px;}
		.veh-plate img {height:25px;}

		#wh_mvt_from, #wh_mvt_to {max-height: calc(100vh - 340px); overflow-y: auto;font-size:1.2rem;}
		#wh_mvt_article {max-height: calc(100vh - 303px); overflow-y: auto;}
		#wh_mvt_article .list-group-item {cursor:pointer; font-size:1.2rem;}
		.check-active, .check-active:hover {background-color: var(--col_light); color:white;}
		.check-active .fa-square::before {content: "\f14a";}
		#wh_other_job .btn-block + .btn-block{margin-top:0; border-top:0;}
		
		#searchArticle, #searchJob {
	border-bottom: 1px solid #ccc;
	height: 50px;
	color: #ccc;
	background-color: #fff;
}
		#btnSearchArticle, #btnSearchJob {
	border: 1px solid transparent;
	padding-left: 20px;
	border-radius: unset;
}

	</style>
	
	<div id="div_result" class="container-fluid pt-3 pb-5">
		<div id="wh_menu" class="row">
			<div class="col-md text-center pt-5">
				<button type="button" class="btn btn-client btn-xl-square rounded-lg">
					<i class="fal fa-warehouse-alt fa-3x mb-3"></i><br>
					Magasins
				</button>
			</div>
			<div class="col-md text-center pt-5">
				<button type="button" class="btn btn-client-light btn-xl-square rounded-lg">
					<i class="fal fa-truck-moving fa-3x mb-3"></i><br>
					Réception commande
				</button>
			</div>
			<div class="col-md text-center pt-5">
				<button type="button" class="btn btn-blue btn-xl-square rounded-lg" onclick="stock_action({action:'addFuel'})">
					<i class="fal fa-gas-pump fa-3x mb-3"></i><br>
					Carburant
				</button>
			</div>
			<div class="col-md text-center pt-5">
				<button type="button" class="btn btn-red btn-xl-square rounded-lg" onclick="stock_action({action:'stockOut'})">
					<i class="fal fa-person-dolly fa-3x mb-3"></i><br>
					Sortie de stock
				</button>
			</div>
		</div>
		<div id="wh_mvt_out" class="row" style="display:none;">
		</div>
	</div>

	<script src="js/warehouseman.js"></script>
	<script>
		document.addEventListener("DOMContentLoaded", function(event) { 
			createDateTime();
		});
	</script>