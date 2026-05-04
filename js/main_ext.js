function table_action(param,callback) { //table_action({tablename:, dataSent:, action:})
	var table = param.tablename;
	var idrecord = param.idrecord || 0;
	var dataSent = param.dataSent || {};
	var action = param.action;
	switch(action){
		case "resetFilter" : 
			action = "weekTable";
			//dataSent['value'] = "";
			break;
		case "weekTable" : 
		case "weekCard" :
			$('.bootstrap-table .btn-group .selectpicker').each(function(){
				name = $(this).attr("id");
				dataSent[name] = $(this).val();
			});
			break;
		case "showWeek" :
			scroll = 0;
			break;
		case "pla_changeWeek" : 
			dataSent = {way:dataSent, year:$("#weekInfo").attr("data-year"), week:$("#weekInfo").attr("data-week")};
			scroll = $("#weeks").scrollTop();
			action = 'showWeek';
			break;
		case "refreshBreakdownPie" : 
			dataSent = {dateFrom:$("#dateFrom").val(), dateTo:$("#dateTo").val()};
			action = 'getBreakdown';
			break;
		case "addOneWeekEverywhere" :
			var btn = $("#btn_updateWeek");
			btn.button('loading');
			break;
		case "deleteJobPicture" :
			action = 'delete';
			break;
		case "createFromQuotation" :
			var btn = $(".btn-save");
			btn.button('loading');
			//dataSent = {idjob:$("#idjob").val(), tas_name:$("#quo_title").val(), tas_duration:$("#quo_amount").val()};
			break;
		case "analyzeBPU" :
			var btn = $("#btn_analyze_bpu");
			btn.button('loading');
			dataSent = {lib_bpu_file:$("#lib_bpu_file").val()};
			$("#lib_library_modal .modal-dialog").removeClass("modal-lg").addClass("modal-xlg");
			$("#btn_import_bpu").removeAttr("Disabled");
			break;
		case "analyzeBPUContract" :
			var btn = $("#btn_analyze_bpu");
			btn.button('loading');
			dataSent = {con_bpu_file:$("#con_bpu_file").val()};
			$("#con_contract_modal .modal-dialog").removeClass("modal-lg").addClass("modal-xlg");
			$("#btn_import_bpu").removeAttr("Disabled");
			break;
		case "sup_analyzeXLS" :
			var btn = $("#btn_analyze_xls");
			btn.button('loading');
			dataSent = {sup_rate_file:$("#sup_rate_file").val()};
			$("#sup_supplier_modal .modal-dialog").removeClass("modal-lg").addClass("modal-xlg");
			$("#btn_import_xls").removeAttr("Disabled");
			action = "analyzeXLS";
			break;
		case "importBPU" :
			var options = $('#set_fields select option:selected');
			var tot = 0;
			var values = $.map(options ,function(option) {
				tot += parseInt(option.value)||0;
			});
			if(tot >= 10){
				dataSent = {lib_bpu_file:$("#lib_bpu_file").val(), selects:$("#set_fields select").serialize()};
				var btn = $("#btn_import_bpu");
				btn.button('loading');
			}else{
				return;
			}
			break;
		case "importBPUContract" :
			var options = $('#set_fields select option:selected');
			var tot = 0;
			var values = $.map(options ,function(option) {
				tot += parseInt(option.value)||0;
			});
			if(tot >= 10){
				dataSent = {con_bpu_file:$("#con_bpu_file").val(), selects:$("#set_fields select").serialize()};
				var btn = $("#btn_import_bpu");
				btn.button('loading');
			}else{
				return;
			}
			break;
		case "sup_importXLS" :
			var options = $('#set_fields select option:selected');
			var tot = 0;
			var values = $.map(options ,function(option) {
				tot += parseInt(option.value)||0;
			});
			if(tot >= 10){
				dataSent = {sup_rate_file:$("#sup_rate_file").val(), selects:$("#set_fields select").serialize()};
				var btn = $("#btn_import_xls");
				btn.button('loading');
			}else{
				return;
			}
			action = "importXLS";
			break;
		case "saveBPU" :
		case "saveXLS" :
			break;
		case "analyzeDPGF" :
			var btn = $("#btn_analyze_dpgf");
			btn.button('loading');
			dataSent = {quo_dpgf_file:$("#quo_dpgf_file").val()};
			$("#quo_quotation_modal .modal-dialog").removeClass("modal-lg").addClass("modal-xlg");
			$("#btn_import_dpgf").removeAttr("Disabled");
			break;
		case "importDPGF" :
			var options = $('#set_fields select option:selected');
			var tot = 0;
			var values = $.map(options ,function(option) {
				tot += parseInt(option.value)||0;
			});
			if(tot >= 10){
				dataSent = {quo_dpgf_file:$("#quo_dpgf_file").val(), selects:$("#set_fields select").serialize()};
				var btn = $("#btn_import_dpgf");
				btn.button('loading');
			}else{
				return;
			}
			break;
		case "saveDPGF" :
			break;
		case "inv_getTotals" :
			action = "getTotals";
			dataSent = {idinvoice:$("#inv_line_table").data("id-parent")};
			break;
		case "quo_importToInvoice" :
			action = "importToInvoice";
			dataSent = {idinvoice:$("#inv_line_table").data("id-parent"),title:param.dataSent};
			break;
	}
	callRouter(table, idrecord, action, dataSent, function(result){
	if(result){
		switch(result.code){
			case 1 : 
				$('body').prepend(result.html);
				$("#"+table+"_modal").modal({backdrop:'static',keyboard:false, show:true});
				construct_input("#"+table+"_form");
				$("#"+table+"_modal").on('shown.bs.modal', function (e) {
					$(".form-control").first().focus();
				});
				$("#"+table+"_modal").on('hidden.bs.modal', function (e) {
					$(this).remove();
				});
				break;
			case "weekCreated" :
				infoContent.html(result.html);
			    $("#wor_attendance_table").bootstrapTable({
			    	height : $("#wor_attendance_table").data("height") == "auto" ? getHeight() : $("#wor_attendance_table").data("height"),
				    iconsPrefix : 'fal',
					//iconSize : 'lg',
					locale : "fr-FR",
					toolbar : "#table-toolbar",
					buttonsClass : "full-height",
					tableName : 'wor_attendance',
					undefinedText: '',
					showExport: true,
					//showColumns: false,
					searchTimeOut: 500,
					selection : dataSent,
					onClickRow: function(row, tr){
						table_action({tablename:'wor_attendance',idrecord:row.id,action:'weekCard'});
					}
				});
				break;
			case "weekCardCreated" : 
					$('body').prepend(result.html);
					$("#wor_attendance_modal").modal({backdrop:'static',keyboard:false, show:true});
					construct_input("#wor_attendance_form");
					$("#wor_attendance_modal").on('hidden.bs.modal', function (e) {
						$(this).remove();
					});
					break;
			case "pictureSaved" :
				$("[data-idworker="+jq(idrecord,'')+"] img, #wor_picture").attr("src",result.src);
				$("[data-idworker="+jq(idrecord,'')+"]").attr("data-wor_picture",result.src);
				webcam("off");
				$("#usr_password").focus();
				break;
			case "pictureJobSaved" :
				break;
			case "pictureJobDeleted" :
				$(".nav-job-picture .badge").text(parseInt($(".nav-job-picture .badge").text())-1);
				$(jq(idrecord,"#picture_")).fadeOut(800,function(){
					$(this).remove();
				})
				break;
			case "showProfit" :
				$(dataSent.tab).html(result.html);
				break;
			case "movFromShop" :
				$('body').append(result.html);
				construct_input("#mov_movement_form");
				$("#mov_movement_modal").css("z-index","1060").modal({backdrop:'static',keyboard:false, show:true});
				$(".modal-backdrop.fade.show:eq(-1)").css("z-index","1050");
				$("#mov_movement_modal .modal-dialog").removeClass("modal-dialog-scrollable");
				$("#mov_movement_modal").on('shown.bs.modal', function (e) {
					controleMovementSelect();
				});
				$("#mov_movement_modal").on('hidden.bs.modal', function (e) {
			    	if($('.modal.fade.in').length){$('body').addClass('modal-open');}
					$("#mov_movement_modal").remove();
			    });
				break;
			case "mov_idplace" :
				$("#mov_idplace").html(result.html).selectpicker('refresh').selectpicker('toggle');
				$("#mov_idplace").on("change", function(){
					var source = $(this).val();
					switch(table){
						case "sho_shop" : 
							table_action({tablename:"sto_stock", action:"filterChild", dataSent:{numList:"mvt",codeReturn:"mov_idarticle",conds:["idshop = "+source]}});
							break;
						case "sup_order" : 
							table_action({tablename:"sup_order_line", action:"filterChild", dataSent:{numList:"mvt",codeReturn:"mov_idarticle",conds:["idsuporder = "+source,"idsuparticle IS NOT NULL"]}});
							break;
						case "veh_vehicle" : 
							if($("#mov_type").val() == 1){ // Entrée
								table_action({tablename:"lnk_vehicle_article", action:"filterChild", dataSent:{numList:"mvt",codeReturn:"mov_idarticle",conds:["idvehicle = "+$("#mov_idplace").val()]}});
							}
							if($("#mov_type").val() == 2){ // Sortie
								table_action({tablename:"sto_stock", action:"filterChild", dataSent:{numList:"mvt",codeReturn:"mov_idarticle",conds:["idshop = "+$("#idshop").val()]}});
							}
							break;
						case "job_job" :
							table_action({tablename:"sto_stock", action:"filterChild", dataSent:{numList:"mvt",codeReturn:"mov_idarticle",conds:["idshop = "+$("#idshop").val()]}});
							break;
						case "wor_worker" :
							table_action({tablename:"sto_stock", action:"filterChild", dataSent:{numList:"mvt",codeReturn:"mov_idarticle",conds:["idshop = "+$("#idshop").val()]}});
							break;
					}
				})
				break;
			case "mov_idarticle" :
				$("#idarticle").html(result.html).selectpicker('refresh').selectpicker('toggle');
				break;
			case "timeCardCreated" : 
					$('body').prepend(result.html);
					$("#wor_attendance_modal").modal({backdrop:'static',keyboard:false, show:true});
					construct_input("#wor_attendance_form");
					$("#wor_attendance_modal").on('hidden.bs.modal', function (e) {
						$(this).remove();
					});
					break;
			// Planning
			case "pla_weekCreated" :
				infoContent.html(result.html);
				$("#planning .day").on("click", function(e){
					table_action({tablename:'pla_planning', idrecord:0, dataSent:{date:$(this).data("date"), idworker:$(this).data("idworker")}, action:'showCard'})
				})
				$("#planning .day").on("click",".btn", function(e){
					e.stopPropagation();
					showCard('pla_planning',$(this).data("idplanning"));
				})
				$("#weeks").scrollTop(scroll);
				break;
			// Chart
			case "chartCreated" :
				$("#chart_ca").html(result.html);
				//$("#ca_tot_month").html(result.tot_month);
				$("#year").html(result.year);
				$("#tot_ca").html(result.tot_ca);
				$("#tot_net").html(result.tot_net);
				var colors = ['#fff', '#6FB342'];
				var d = new Date();
				var x = d.getMonth()+1;
				var chBar = document.getElementById("chBar");
				var ca = new Array(12);
				for(var i in result.data) {
					ca[i] = result.data[i].tot_inv;
				}
				var net = new Array(12);
				for(var i in result.data) {
					net[i] = result.data[i].total;
				}
				var chartData = {
					labels: result.labels,
					datasets: [
					{
						label: result.label[0],
						data: ca,
						backgroundColor: colors[0]
					},
					{
						label: result.label[1],
						data: net,
						backgroundColor: colors[1]
					}]
				};
				Chart.defaults.color = '#fff';
				Chart.defaults.font = {size: 14,family: 'Nunito Sans'};
				if (chBar) {
					caChart = new Chart(chBar, {
					type: 'bar',
					data: chartData,
					options: {
						responsive: true,
						maintainAspectRatio: false,
						scales: {
							x: {
								barPercentage: 0.9,
								categoryPercentage: 0.9
							},
							y: {
								ticks: {
									beginAtZero: true,
									callback: function(value, index, values) {
										return currencyFormat(value,0);
									}
								}
							}
						},
						plugins: {
							legend: {
								display: false,
							},
							tooltip: {
								callbacks: {
									label: function(tooltipItem) {
										return currencyFormat(tooltipItem.parsed.y,2);
									}
								}
							},
						},
						animation: {
							duration: 1000,
						},
						onClick: function(event,data){
							table_action({idrecord:0,tablename:'cfg_config',dataSent:{year:$("#year").text(), month:data[0]["index"]+1},action:'getPies'});
						}
					}
					});
				}
				// change year
				$(".ca_year").off("click").on("click", function(){
					caChart.destroy();
					table_action({idrecord:0,tablename:'cfg_config',dataSent:$(this).data("year"),action:'getChart'});
				})
				break;
			case "piesCreated" :
				$("#popup-chart #modaltitle span").html(result.modal_title);
				$("#popup-chart").modal({backdrop:'static',keyboard:false, show:true});
				$('#popup-chart').on('hidden.bs.modal', function (e) {
					$("#pie_invoice").html("");
					$("#pie_biller").html("");
				})
				
				$('#popup-chart').on('shown.bs.modal', function (e) {
					$("#pie_invoice").html(result.html_inv);
					$("#pie_biller").html(result.html_bil);
					
					var chBar_inv = document.getElementById("chPie_inv");
					var chBar_bil = document.getElementById("chPie_bil");
					
					Chart.defaults.color = '#000';
					Chart.defaults.font = {size: 14,family: 'Nunito Sans'};
					
					var cli = new Array();
					var acc = new Array();
					var tot_inv = new Array();
					var tot_bil = new Array();
					
					for(var i in result.data_inv) {
						cli[i] = result.data_inv[i].cli_name;
						tot_inv[i] = result.data_inv[i].total;
					};
					var chartData_inv = {labels: cli, datasets: [{data: tot_inv, backgroundColor: result.col_inv}]};
					
					for(var i in result.data_bil) {
						acc[i] = result.data_bil[i].acc_name;
						tot_bil[i] = result.data_bil[i].total;
					};
					var chartData_bil = {labels: acc,datasets: [{data: tot_bil, backgroundColor: result.col_bil}]};
					
					if(chBar_inv){
						invChart = new Chart(chBar_inv,{
							type: 'doughnut',
							data: chartData_inv,
							options:{
								animation: {duration: 1000,},
								plugins: {
									title: {
										display: true, 
										text: result.label_inv, 
										font: {
											size: 18,
											}
										},
									legend: {
										display: true,
										labels: {
											font: {
												size: 14,
											}
										}
									},
									tooltip: {
										callbacks: {
											label: function(tooltipItem) {
												return ' '+tooltipItem.label+' : '+currencyFormat(tooltipItem.parsed,2);
											}
										}
									}
								},
								
							}
						});
					}
					if(chBar_bil){
						invChart = new Chart(chBar_bil,{
							type: 'doughnut',
							data: chartData_bil,
							options:{
								animation: {duration: 1000,},
								plugins: {
									title: {
										display: true, 
										text: result.label_bil, 
										font: {
											size: 18,
											}
										},
									legend: {
										display: true,
										labels: {
											font: {
												size: 14,
											}
										}
									},
									tooltip: {
										callbacks: {
											label: function(tooltipItem) {
												return ' '+tooltipItem.label+' : '+currencyFormat(tooltipItem.parsed,2);
											}
										}
									}
								},
							}
						});
					}
				})
				break;
			case "piesBreakdownCreated" :
				infoContent.html(result.html);
				
				$('.datetimepicker-input').datetimepicker({
			    	locale: "fr",
					useCurrent: false,
					buttons: {
			            showToday: true,
			            showClear: false,
			            showClose: true
			        },
			    });
				
				var chBar_bil1 = document.getElementById("chPie_bil1");
				var chBar_bil2 = document.getElementById("chPie_bil2");
				var chBar_bil3 = document.getElementById("chPie_bil3");
				
				Chart.defaults.color = '#000';
				Chart.defaults.font = {size: 14,family: 'Nunito Sans'};
				
				var acc1 = new Array();var acc2 = new Array();var acc3 = new Array();
				var tot1 = new Array();var tot2 = new Array();var tot3 = new Array();
				
				for(var i in result.data_bil1) {
					acc1[i] = result.data_bil1[i].acc_name;
					tot1[i] = result.data_bil1[i].total;
				};
				for(var i in result.data_bil2) {
					acc2[i] = result.data_bil2[i].acc_name;
					tot2[i] = result.data_bil2[i].total;
				};
				for(var i in result.data_bil3) {
					acc3[i] = result.data_bil3[i].acc_name;
					tot3[i] = result.data_bil3[i].total;
				};
				
				var chartData_bil1 = {labels: acc1,datasets: [{data: tot1, backgroundColor: result.col_bil1}]};
				var chartData_bil2 = {labels: acc2,datasets: [{data: tot2, backgroundColor: result.col_bil2}]};
				var chartData_bil3 = {labels: acc3,datasets: [{data: tot3, backgroundColor: result.col_bil3}]};
				
				if(chBar_bil1){
					bil1Chart = new Chart(chBar_bil1,{
						type: 'doughnut',
						data: chartData_bil1,
						options:{
							animation: {duration: 1000,},
							plugins: {
								title: {
									display: true, 
									text: result.label_bil1, 
									font: {
										size: 18,
										}
									},
								legend: {
									display: true,
									labels: {
										font: {
											size: 14,
										}
									}
								},
								tooltip: {
									callbacks: {
										label: function(tooltipItem) {
											return ' '+tooltipItem.label+' : '+currencyFormat(tooltipItem.parsed,2);
										}
									}
								}
							},
						}
					});
				}
				if(chBar_bil2){
					bil2Chart = new Chart(chBar_bil2,{
						type: 'doughnut',
						data: chartData_bil2,
						options:{
							animation: {duration: 1000,},
							plugins: {
								title: {
									display: true, 
									text: result.label_bil2, 
									font: {
										size: 18,
										}
									},
								legend: {
									display: true,
									labels: {
										font: {
											size: 14,
										}
									}
								},
								tooltip: {
									callbacks: {
										label: function(tooltipItem) {
											return ' '+tooltipItem.label+' : '+currencyFormat(tooltipItem.parsed,2);
										}
									}
								}
							},
						}
					});
				}
				if(chBar_bil3){
					bil3Chart = new Chart(chBar_bil3,{
						type: 'doughnut',
						data: chartData_bil3,
						options:{
							animation: {duration: 1000,},
							plugins: {
								title: {
									display: true, 
									text: result.label_bil3, 
									font: {
										size: 18,
										}
									},
								legend: {
									display: true,
									labels: {
										font: {
											size: 14,
										}
									}
								},
								tooltip: {
									callbacks: {
										label: function(tooltipItem) {
											return ' '+tooltipItem.label+' : '+currencyFormat(tooltipItem.parsed,2);
										}
									}
								}
							},
						}
					});
				}
				break;
			case "weekAdded" :
				btn.text('').append("<i class='fa fa-thumbs-o-up'></i> "+result.info);
				setTimeout(function(){btn.button('reset');$("#btn_updateWeek span").html(result.html);}, 800);
				break;
			case "taskCreated" :
				btn.text('').append("<i class='fa fa-thumbs-o-up'></i> "+result.info);
				setTimeout(function(){btn.button('reset');}, 800);
				//setTimeout(function(){btn.button('reset');$("#btn_tas_task span").html(result.html);}, 800);
				break;
			case "dashboardUpdated" :
			case "no-result" :
				break;
			case "bpuSaved" :
				$("#lib_bpu_name").val(dataSent.fileName);
				$("#dz-image").append("<span>"+dataSent.fileName+"</span>");
				$(".dz-preview").toggleClass("d-none");
				$("#dz-image").toggleClass("d-flex flex-column justify-content-center align-items-center d-none");
				$("#btn_analyze_bpu").removeAttr("Disabled");
				break;
			case "bpuAnalyzed" :
				btn.text('').append("<i class='fa fa-thumbs-o-up'></i> "+result.info);
				setTimeout(function(){btn.button('reset');}, 800);
				$(".table-bpu").html(result.html);
				construct_input(".table-bpu");
				break;
			case "bpuImported" :
				btn.text('').append("<i class='fa fa-thumbs-o-up'></i> "+result.info);
				setTimeout(function(){btn.button('reset');$(".table-bpu").html("");btn.attr("Disabled",true);}, 2000);
				break;
			case "bpuContractSaved" :
				$("#con_bpu_name").val(dataSent.fileName);
				$("#dz-image").append("<span>"+dataSent.fileName+"</span>");
				$(".dz-preview").toggleClass("d-none");
				$("#dz-image").toggleClass("d-flex flex-column justify-content-center align-items-center d-none");
				$("#btn_analyze_bpu").removeAttr("Disabled");
				break;
			case "bpuContractAnalyzed" :
				btn.text('').append("<i class='fa fa-thumbs-o-up'></i> "+result.info);
				setTimeout(function(){btn.button('reset');}, 800);
				$(".table-result").empty();
				$(".table-bpu").html(result.html);
				construct_input(".table-bpu");
				break;
			case "bpuContractImported" :
				btn.text('').append("<i class='fa fa-thumbs-o-up'></i> "+result.info);
				setTimeout(
					function(){btn.button('reset');
					$(".table-bpu").html("");
					$(".table-result").html(result.html);
					construct_input(".table-result");
					btn.attr("Disabled",true);
				}, 1000);
				break;
			case "dpgfSaved" :
				$("#quo_dpgf_name").val(dataSent.fileName);
				$("#dz-image").append("<span>"+dataSent.fileName+"</span>");
				$(".dz-preview").toggleClass("d-none");
				$("#dz-image").toggleClass("d-flex flex-column justify-content-center align-items-center d-none");
				$("#btn_analyze_dpgf").removeAttr("Disabled");
				break;
			case "dpgfAnalyzed" :
				btn.text('').append("<i class='fa fa-thumbs-o-up'></i> "+result.info);
				setTimeout(function(){btn.button('reset');}, 800);
				$(".table-dpgf").html(result.html);
				construct_input(".table-dpgf");
				break;
			case "dpgfImported" :
				btn.text('').append("<i class='fa fa-thumbs-o-up'></i> "+result.info);
				setTimeout(function(){
					btn.button('reset');
					$(".table-dpgf").html("");
					btn.attr("Disabled",true);
					$('#quo_quotation_modal').modal('hide');
					showCard('quo_quotation',idrecord);
				}, 500);
				break;
				
			case "sup_xlsSaved" :
				$("#dz-image").append("<span>"+dataSent.fileName+"</span>");
				$(".dz-preview").toggleClass("d-none");
				$("#dz-image").toggleClass("d-flex flex-column justify-content-center align-items-center d-none");
				$("#btn_analyze_xls").removeAttr("Disabled");
				break;
			case "sup_xlsAnalyzed" :
				btn.text('').append("<i class='fa fa-thumbs-o-up'></i> "+result.info);
				setTimeout(function(){btn.button('reset');}, 800);
				$(".table-result").empty();
				$(".table-xls").html(result.html);
				construct_input(".table-xls");
				break;
			case "sup_xlsImported" :
				btn.text('').append("<i class='fa fa-thumbs-o-up'></i> "+result.info);
				setTimeout(
					function(){btn.button('reset');
					$(".table-xls").html("");
					$(".table-result").html(result.html);
					construct_input(".table-result");
					btn.attr("Disabled",true);
				}, 1000);
				break;
			case "art_xlsImported" :
				$("#popup-msg").modal("hide");
				showTable('art_article','resetFilter','showCard')
				/*
				btn.text('').append("<i class='fa fa-thumbs-o-up'></i> "+result.info);
				setTimeout(
					function(){btn.button('reset');
					$(".table-xls").html("");
					$(".table-result").html(result.html);
					construct_input(".table-result");
					btn.attr("Disabled",true);
				}, 1000);
				*/
				break;
			case "inv_totals" :
				for (let key in result.allvat) {
					value = result.allvat[key];
					$("#"+key).val(value);
				}
				$("#inv_tot_articles").val(result.ht);
				$("#inv_tot_previous").val(result.pre);
				$("#inv_tot_situation").val(result.sit);
				$("#inv_tot_retention").val(result.ret);
				$("#inv_tot_ecotax").val(result.eco);
				$("#inv_tot_ht").val(result.tht);
				$("#inv_tot_vat").val(result.vat);
				$("#inv_tot_ttc").val(result.ttc);
				$("#inv_tot_to_pay").val(result.net);
				break;
			case "importedToInvoice" :
				table_action({tablename:'inv_invoice', action:'inv_getTotals'}, function(){
					iud('inv_invoice',dataSent.idinvoice,'update','')
					$('#inv_invoice_modal').modal('hide');
					$('#inv_invoice_modal').on('hidden.bs.modal', function (e) {
						showCard('inv_invoice',dataSent.idinvoice,'');
					})
				});
				break;
			default : 
				showError(result);
				break;
			}
		}else{
			showError(result);
		}
		if ($.isFunction(callback)){
		    callback.call();
		}else{
			if(callback){
				window[callback]();
			}
		}
	});
}

function controleMovementSelect(){
	var table;
	// Qd type change d'option
	$("#mov_type").on("change", function(){
		var source = $(this).val();
		switch(source){
			case "1" : // Entrée
				$("#mov_movement_form .idsource div label").text("Source");
				$("#mov_place option[value=3]").attr("disabled",true);
				$("#mov_place option[value=2]").removeAttr("disabled");
				$("#mov_place").selectpicker('refresh').selectpicker('toggle');
				break;
			case "2" : // Sortie
				$("#mov_movement_form .idsource div label").text("Destination");
				$("#mov_place option[value=2]").attr("disabled",true);
				$("#mov_place option[value=3]").removeAttr("disabled");
				$("#mov_place").selectpicker('refresh').selectpicker('toggle');
				break;
		}
	})
	
	// Qd source change d'option
	$("#mov_place").on("change", function(){
		$("#idarticle").html("").selectpicker('refresh');
		var source = $(this).val();
		switch(source){
			case "1" : // Autre Magasin
				table = "sho_shop";
				$("#mov_movement_form .idplace").show(function(){
					$("div label",this).text("Magasin");
				});
				table_action({tablename:table, action:"filterChild", dataSent:{numList:1,codeReturn:"mov_idplace",conds:""}});
				break;
			case "2" : // Commande
				table = "sup_order";
				$("#mov_movement_form .idplace").show(function(){
					$("div label",this).text("Commande");
				});
				table_action({tablename:table, action:"filterChild", dataSent:{numList:2,codeReturn:"mov_idplace",conds:["ord_status > 3"]}});
				break;
			case "3" : // Job
				table = "job_job";
				$("#mov_movement_form .idplace").show(function(){
					$("div label",this).text("Job");
				});
				table_action({tablename:table, action:"filterChild", dataSent:{numList:3,codeReturn:"mov_idplace",conds:""}});
				break;
			case "4" : // Vehicle
				table = "veh_vehicle";
				$("#mov_movement_form .idplace").show(function(){
					$("div label",this).text("Véhicule");
				});
				table_action({tablename:table, action:"filterChild", dataSent:{numList:2,codeReturn:"mov_idplace",conds:""}});
				break;
			case "5" : // Manuel
				table = "art_article";
				$("#mov_movement_form .idplace").hide();
				var idshop = $("#idshop").val();
				if($("#mov_type").val() == 1){ // Entrée
					table_action({tablename:"art_article", action:"filterChild", dataSent:{numList:1,codeReturn:"mov_idarticle",conds:""}});
				}
				if($("#mov_type").val() == 2){ // Sortie
					table_action({tablename:"sto_stock", action:"filterChild", dataSent:{numList:"mvt",codeReturn:"mov_idarticle",conds:["idshop = "+idshop]}});
				}
				break;
			case "15" : // Worker
				table = "wor_worker";
				$("#mov_movement_form .idplace").show(function(){
					$("div label",this).text("Compagnon");
				});
				table_action({tablename:table, action:"filterChild", dataSent:{numList:5,codeReturn:"mov_idplace",conds:""}});
				break;
		}
	})
}

function updateSubTotal(table) {
	var count = false;
	var tot = 0;
	tr = $(table+" tbody tr").each(function(index,element){
		type = $("td[data-name='lin_unit']",element).text();
		if(type.toLowerCase() == "titre" || type.toLowerCase() == "option"){count = true;}
		if(type.toLowerCase() == "total"){
			$("td[data-name='lin_total'] strong",element).text(tot.toFixed(3));
			tot = 0;
			count = false;
		}else{
			tot_line = $("td[data-name='lin_total']",element).text()||0;
			if(count == true){tot += parseFloat(tot_line);}	
		}
	})
}

function changeLineVat(tableName, idvatName){ // quo_line, quo_idvat
	var idvat = $("#"+idvatName).val();
	var vatValue = $("#"+idvatName+" option:selected").text();
	$("#"+tableName+"_table tbody tr").each(function(index){
		if($("td[data-name='vat_percent']", this).text() != ""){
			$("#"+tableName+"_table").bootstrapTable('updateCell', {
				index: index,
				field: 'vat_percent',
				value: vatValue,
				reinit: false
			})
			$("td[data-name='vat_percent']", this).text(vatValue);
		}
	})
	tableAction({tableName:tableName, idrecord:$("#"+tableName+"_table").data("idParent"), dataSent:{idvat:idvat}, action:"updateVAT"});
}