
// *********** ART_ARTICLE
var art_article_domId;
function art_article_ext_showCard(idrecord){
}
function art_article_ext_iud(idName,idValue,action){}

// *********** ART_CATEGORY
var art_category_domId;
function art_category_ext_showCard(idrecord){
}
function art_category_ext_iud(idName,idValue,action){}

// *********** ART_SUBCATEGORY
var art_subcategory_domId;
function art_subcategory_ext_showCard(idrecord){
}
function art_subcategory_ext_iud(idName,idValue,action){}

// *********** CAS_CASE
var cas_case_domId;
function cas_case_ext_showCard(idrecord){
}
function cas_case_ext_iud(idName,idValue,action){}

// *********** CAS_TYPE
var cas_type_domId;
function cas_type_ext_showCard(idrecord){
}
function cas_type_ext_iud(idName,idValue,action){}

// *********** CLI_CLIENT
var cli_client_domId;
function cli_client_ext_showCard(idrecord){
}
function cli_client_ext_iud(idName,idValue,action){}

// *********** CLI_CONTACT
var cli_contact_domId;
function cli_contact_ext_showCard(idrecord){
}
function cli_contact_ext_iud(idName,idValue,action){}

// *********** CLI_LOGIN
var cli_login_domId;
function cli_login_ext_showCard(idrecord){
}
function cli_login_ext_iud(idName,idValue,action){}

// *********** CON_TYPE
var con_type_domId;
function con_type_ext_showCard(idrecord){
}
function con_type_ext_iud(idName,idValue,action){}

// *********** INV_INVOICE
var inv_invoice_domId;
var inv_sortable;
function inv_invoice_ext_showCard(idrecord){
	$("#inv_holdback_percent").focusout(function(){
		param.idrecord = $(this).data("idparent");
		param.holdback = $(this).val();
		param.tableName = "inv_invoice";
		param.action = "updateHoldback";
		tableAction(param);
	})
	/*
	inv_sortable = new Sortable(dragdrop, {
		animation: 150,
		ghostClass: 'list-group-item-info',
		onEnd: function (evt) {
			var elem = evt.item;  // dragged HTMLElement
			
			newData = [];
			table = document.getElementById("inv_line_table");
			tableBsData = $(table).data('bootstrap.table');
			tableBsOptions = $(table).data('bootstrap.table').options;
			
			$("#inv_line_table #dragdrop tr").each(function(){
				var newNdx = $(this).index();
				var oldNdx = $(this).data("index");
				row = $(table.tBodies[0].rows[newNdx])
				newData.push(tableBsOptions.data[row.data('index')]);
				row.data('index', newNdx).attr('data-index', newNdx);
			});
			
			tableBsOptions.data = tableBsOptions.data.slice(0, tableBsData.pageFrom - 1)
			.concat(newData)
			.concat(tableBsOptions.data.slice(tableBsData.pageTo));
			$(table).bootstrapTable('filterBy');
			
			param.tableName = "inv_line";
			param.idrecord = $(elem).closest("tr").data("uniqueid");
			param.action = "lineMove";
			param.dataSent = {idparent:idrecord, oldIndex:evt.oldIndex, newIndex:evt.newIndex};
			tableAction(param);
		},
	});
	*/
}
function inv_invoice_ext_iud(idName,idValue,action){}

// *********** INV_LINE
var inv_line_domId;
function inv_line_ext_showCard(idrecord){
}
function inv_line_ext_iud(idName,idValue,action){}

// *********** JOB_JOB
var job_job_domId;
function job_job_ext_showCard(idrecord){
	showListgroup("tas_task");
	$(".nav-profit a").click(function(){
		table_action({idrecord:idrecord, tablename:'job_job', dataSent:{tab:$(this).attr("href")}, action:'getProfit'});
	})
	$("#idsite").change(function(){
		if($("#job_surname").val().length < 1){
			$("#job_surname").val($(this).find(":selected").text().substr(0,30).toUpperCase());
		}
	})
}
function job_job_ext_iud(idName,idValue,action){}

// *********** MOV_MOVEMENT
var mov_movement_domId;
function mov_movement_ext_showCard(idrecord){
	$("#mov_movement_modal .modal-dialog").removeClass("modal-dialog-scrollable");
	controleMovementSelect();
}
function mov_movement_ext_iud(idName,idValue,action){}

// *********** QUO_COMMENT
var quo_comment_domId;
function quo_comment_ext_showCard(idrecord){
}
function quo_comment_ext_iud(idName,idValue,action){}

// *********** QUO_LINE
var quo_line_domId;
function quo_line_ext_showCard(idrecord){
}
function quo_line_ext_iud(idName,idValue,action){}

// *********** QUO_QUOTATION
var quo_quotation_domId;
var quo_sortable;
function quo_quotation_ext_showCard(idrecord){
	$('#btn_other_actions').on('shown.bs.dropdown', function () {
		$('.custom-control-input').on('click', function (event) {
			$(this).closest('.dropdown-menu').toggleClass('show');
			$(this).closest('.dropup').toggleClass('show');
		});
	})
	//console.log(dragdrop);
	quo_sortable = new Sortable($("#quo_line_table tbody")[0], {
		animation: 150,
		ghostClass: 'list-group-item-info',
		onEnd: function (evt) {
			var elem = evt.item;  // dragged HTMLElement
			
			newData = [];
			table = document.getElementById("quo_line_table");
			tableBsData = $(table).data('bootstrap.table');
			tableBsOptions = $(table).data('bootstrap.table').options;
			$("#quo_line_table tbody tr").each(function(){
				var newNdx = $(this).index();
				var oldNdx = $(this).data("index");
				row = $(table.tBodies[0].rows[newNdx])
				newData.push(tableBsOptions.data[row.data('index')]);
				row.data('index', newNdx).attr('data-index', newNdx);
			});
			tableBsOptions.data = tableBsOptions.data.slice(0, tableBsData.pageFrom - 1)
			.concat(newData)
			.concat(tableBsOptions.data.slice(tableBsData.pageTo));
			$(table).bootstrapTable('filterBy');

			param.tableName = "quo_line";
			param.idrecord = $(elem).closest("tr").data("uniqueid");
			param.action = "lineMove";
			param.dataSent = {idparent:idrecord, oldIndex:evt.oldIndex, newIndex:evt.newIndex};
			tableAction(param);
		},
	});
	
	var $contextMenu = $("#contextMenu");

    $("#quo_line_table").on("contextmenu", "tr", function(e) {
    	var modal = $("#quo_quotation_modal  .modal-dialog").offset();
		var form = $("#quo_quotation_form").offset();
		var index = $(this).data("index");
		$(this).css("background-color","#00000013");
		$contextMenu.css({
			display: "block",
			left: e.pageX - modal.left + 10,
			top: e.pageY - form.top + 10
		});
		$("#insertBefore").off("click").on("click",function(){
			tableAction({tableName:'quo_line',index:index,way:'before',action:'insertLine'});
		})
		$("#insertAfter").off("click").on("click",function(){
			tableAction({tableName:'quo_line',index:index+1,way:'after',action:'insertLine'});
		})
		return false;
    });
	$('html').click(function() {
         $contextMenu.hide();
		 $("#quo_line_table tr").removeAttr('style');
    });
}
function quo_quotation_ext_iud(idName,idValue,action){}

// *********** SHO_SHOP
var sho_shop_domId;
function sho_shop_ext_showCard(idrecord){
}
function sho_shop_ext_iud(idName,idValue,action){}

// *********** SIT_SITE
var sit_site_domId;
function sit_site_ext_showCard(idrecord){
}
function sit_site_ext_iud(idName,idValue,action){}

// *********** STO_STOCK
var sto_stock_domId;
function sto_stock_ext_showCard(idrecord){
}
function sto_stock_ext_iud(idName,idValue,action){}

// *********** SUP_ARTICLE
var sup_article_domId;
function sup_article_ext_showCard(idrecord){
	$("#idarticle").change(function(){
		$("#art_unit").val($(this).find(":selected").data("udm"));
	})
}
function sup_article_ext_iud(idName,idValue,action){}

// *********** SUP_ORDER
var sup_order_domId;
function sup_order_ext_showCard(idrecord){
	$("#ord_tva_percent").focusout(function(){
		param.idrecord = $(this).data("idparent");
		param.vat = $(this).val();
		param.tableName = "sup_order";
		param.action = "getTotal";
		tableAction(param);
	})
	$('#btn_other_actions').on('shown.bs.dropdown', function () {
		$('.custom-control-input').on('click', function (event) {
			$(this).closest('.dropdown-menu').toggleClass('show');
			$(this).closest('.dropup').toggleClass('show');
		});
	})
}
function sup_order_ext_iud(idName,idValue,action){}

// *********** SUP_ORDER_LINE
var sup_order_line_domId;
function sup_order_line_ext_showCard(idrecord){
}
function sup_order_line_ext_iud(idName,idValue,action){}

// *********** SUP_RECEIPT
var sup_receipt_domId;
function sup_receipt_ext_showCard(idrecord){
	$("button.line-copy").click(function(){
		qty = $(this).closest("tr").children("td[data-name='lin_qty_remaining']").text();
		$(this).closest(".input-group").children("input").val(qty);
    });
}
function sup_receipt_ext_iud(idName,idValue,action){}

// *********** SUP_SUPPLIER
var sup_supplier_domId;
function sup_supplier_ext_showCard(idrecord){
}
function sup_supplier_ext_iud(idName,idValue,action){}

// *********** TAS_TASK
var tas_task_domId;
function tas_task_ext_showCard(idrecord){
}
function tas_task_ext_lineEdited(idrecord){
	$("#tas_duration_days, #tas_men_needed").change(function(){
		var days = parseFloat($("#tas_duration_days").val());
		var men = parseInt($("#tas_men_needed").val());
		var hours = 8;
		
		// end date
		/*
		$("#tas_date_end").val(addDaysToDate($("#tas_date_begin").val(),"d-m-Y",days));
		if($("#tas_deadline").val() == ""){
			$("#tas_deadline").val($("#tas_date_end").val());
		}
		*/
		// duration			
		var duration = days * men * hours;
		newDuration = duration.toFixed(2);
		//newDuration = newDuration.replace(".50", ":30");
		//newDuration = newDuration.replace(".00", ":00");
		$("#tas_duration").val(newDuration);
	})
}
function tas_task_ext_iud(idName,idValue,action){}

// *********** TAS_TYPE
var tas_type_domId;
function tas_type_ext_showCard(idrecord){
}
function tas_type_ext_iud(idName,idValue,action){}

// *********** TIM_TIMESHEET
var tim_timesheet_domId;
function tim_timesheet_ext_showCard(idrecord){
	$("#tim_hour_end").on("blur", function(){
		var begin = $("#tim_hour_begin").val();
		var end = $("#tim_hour_end").val();
		console.log(begin,end)
		$("#tim_duration").val(calculateDuration(begin,end));
	})
}
function tim_timesheet_ext_iud(idName,idValue,action){}

// *********** WOR_CATEGORY
var wor_category_domId;
function wor_category_ext_showCard(idrecord){
}
function wor_category_ext_iud(idName,idValue,action){}

// *********** lib_library
var lib_library_domId;
function lib_library_ext_showCard(idrecord){
}
function lib_library_ext_iud(idName,idValue,action){}

// *********** WOR_SUBCATEGORY
var wor_subcategory_domId;
function wor_subcategory_ext_showCard(idrecord){
}
function wor_subcategory_ext_iud(idName,idValue,action){}

// *********** WOR_TEAM
var wor_team_domId;
function wor_team_ext_showCard(idrecord){
}
function wor_team_ext_iud(idName,idValue,action){}

// *********** lib_work
var lib_work_domId;
function lib_work_ext_showCard(idrecord){
}
function lib_work_ext_iud(idName,idValue,action){}

// *********** wor_worker
var wor_worker_domId;
function wor_worker_ext_showCard(idrecord){
}
function wor_worker_ext_iud(idName,idValue,action){}

// *********** SIT_TYPE
var sit_type_domId;
function sit_type_ext_showCard(idrecord){
}
function sit_type_ext_iud(idName,idValue,action){}

// *********** QUO_LINE_COMMENT
var quo_line_comment_domId;
function quo_line_comment_ext_showCard(idrecord){
}
function quo_line_comment_ext_iud(idName,idValue,action){}

// *********** WOR_ATTENDANCE
var wor_attendance_domId;
function wor_attendance_ext_showCard(idrecord){
	$('input[type=radio][name=att_driver]').change(function() {
		if(this.value == 1){
			$("#att_travel_time_going").removeAttr("disabled");
			$("#att_travel_time_coming").removeAttr("disabled");
		}else{
			$("#att_travel_time_going").attr("disabled", true);
			$("#att_travel_time_coming").attr("disabled", true);
		}
	});
}
function wor_attendance_ext_iud(idName,idValue,action){}

// *********** BIL_ACCOUNT_LEV1
var bil_account_lev1_domId;
function bil_account_lev1_ext_showCard(idrecord){
}
function bil_account_lev1_ext_iud(idName,idValue,action){}

// *********** BIL_ACCOUNT_LEV2
var bil_account_lev2_domId;
function bil_account_lev2_ext_showCard(idrecord){
}
function bil_account_lev2_ext_iud(idName,idValue,action){}

// *********** BIL_ACCOUNT_LEV3
var bil_account_lev3_domId;
function bil_account_lev3_ext_showCard(idrecord){
}
function bil_account_lev3_ext_iud(idName,idValue,action){}

// *********** BIL_BILLER
var bil_biller_domId;
function bil_biller_ext_showCard(idrecord){
	$("#bil_total, #bil_expenses, #bil_ecotax").change(function(){
		var tot = (parseFloat($("#bil_total").val())||0) - (parseFloat($("#bil_expenses").val())||0) - (parseFloat($("#bil_ecotax").val())||0);
		$("#bil_amount").val(tot);
	})
}
function bil_biller_ext_iud(idName,idValue,action){}

// *********** BIL_BREAKDOWN
var bil_breakdown_domId;
function bil_breakdown_ext_showCard(idrecord){
}
function bil_breakdown_ext_iud(idName,idValue,action){}

// *********** BIL_TYPE_PAYMENT
var bil_type_payment_domId;
function bil_type_payment_ext_showCard(idrecord){
}
function bil_type_payment_ext_iud(idName,idValue,action){}

// *********** ARE_AREA
var are_area_domId;
function are_area_ext_showCard(idrecord){
}
function are_area_ext_iud(idName,idValue,action){}

// *********** VEH_VEHICLE
var veh_vehicle_domId;
function veh_vehicle_ext_showCard(idrecord){
}
function veh_vehicle_ext_iud(idName,idValue,action){}

// *********** VEH_BRAND
var veh_brand_domId;
function veh_brand_ext_showCard(idrecord){
}
function veh_brand_ext_iud(idName,idValue,action){}

// *********** PLA_PLANNING
var pla_planning_domId;
function pla_planning_ext_showCard(idrecord){
}
function pla_planning_ext_iud(idName,idValue,action){}


// *********** TIM_WEEKLY
var tim_weekly_domId;
function tim_weekly_ext_showCard(idrecord){
}
function tim_weekly_ext_iud(idName,idValue,action){}

// *********** TRA_TRAINING
var tra_training_domId;
function tra_training_ext_showCard(idrecord){
}
function tra_training_ext_iud(idName,idValue,action){}

// *********** WOR_MEDICAL
var wor_medical_domId;
function wor_medical_ext_showCard(idrecord){
}
function wor_medical_ext_iud(idName,idValue,action){}

// *********** WOR_TRAINING
var wor_training_domId;
function wor_training_ext_showCard(idrecord){
}
function wor_training_ext_iud(idName,idValue,action){}

// *********** JOB_PICTURE
var job_picture_domId;
function job_picture_ext_showCard(idrecord){
}
function job_picture_ext_iud(idName,idValue,action){}

// *********** VEH_CONTROL
var veh_control_domId;
function veh_control_ext_showCard(idrecord){
}
function veh_control_ext_iud(idName,idValue,action){}

// *********** VEH_FULL
var veh_full_domId;
function veh_full_ext_showCard(idrecord){
}
function veh_full_ext_iud(idName,idValue,action){}

// *********** VEH_MAINTENANCE
var veh_maintenance_domId;
function veh_maintenance_ext_showCard(idrecord){
}
function veh_maintenance_ext_iud(idName,idValue,action){}

// *********** VEH_TIRE
var veh_tire_domId;
function veh_tire_ext_showCard(idrecord){
}
function veh_tire_ext_iud(idName,idValue,action){}

// *********** MET_METIER
var met_metier_domId;
function met_metier_ext_showCard(idrecord){
}
function met_metier_ext_iud(idName,idValue,action){}

// *********** VEH_INSURANCE
var veh_insurance_domId;
function veh_insurance_ext_showCard(idrecord){
}
function veh_insurance_ext_iud(idName,idValue,action){}

// *********** AGE_AGENCY
var age_agency_domId;
function age_agency_ext_showCard(idrecord){
}
function age_agency_ext_iud(idName,idValue,action){}

// *********** CON_CONTRACT
var con_contract_domId;
function con_contract_ext_showCard(idrecord){
}
function con_contract_ext_iud(idName,idValue,action){}

// *********** INV_LINE_ECOTAX
var inv_line_ecotax_domId;
function inv_line_ecotax_ext_showCard(idrecord){
}
function inv_line_ecotax_ext_iud(idName,idValue,action){}

// *********** INV_LINE_PREVIOUS
var inv_line_previous_domId;
function inv_line_previous_ext_showCard(idrecord){
}
function inv_line_previous_ext_iud(idName,idValue,action){}

// *********** SUP_AGENCY
var sup_agency_domId;
function sup_agency_ext_showCard(idrecord){
}
function sup_agency_ext_iud(idName,idValue,action){}

// *********** INV_LINE_RETENTION
var inv_line_retention_domId;
function inv_line_retention_ext_showCard(idrecord){
}
function inv_line_retention_ext_iud(idName,idValue,action){}

// *********** RET_RETENTION
var ret_retention_domId;
function ret_retention_ext_showCard(idrecord){
}
function ret_retention_ext_iud(idName,idValue,action){}

// *********** WOR_ARTICLE
var wor_article_domId;
function wor_article_ext_showCard(idrecord){
}
function wor_article_ext_iud(idName,idValue,action){}
