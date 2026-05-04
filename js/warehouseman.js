var duration = 300;
function createDateTime(){
	// Create two variable with the names of the months and days in an array
	var monthNames = [ "Janvier", "Février", "Mars", "Avril", "Mai", "Juin", "Juillet", "Août", "Septembre", "Octobre", "Novembre", "Décembre" ]; 
	var dayNames= ["Dimanche","Lundi","Mardi","Mercredi","Jeudi","Vendredi","Samedi"]

	// Create a newDate() object
	var newDate = new Date();
	// Extract the current date from Date object
	newDate.setDate(newDate.getDate());
	// Output the day, date, month and year    
	$('#Date').html(dayNames[newDate.getDay()] + " " + newDate.getDate() + ' ' + monthNames[newDate.getMonth()] + ' ' + newDate.getFullYear());

	setInterval( function() {
		// Create a newDate() object and extract the seconds of the current time on the visitor's
		var seconds = new Date().getSeconds();
		// Add a leading zero to seconds value
		$("#sec").html(( seconds < 10 ? "0" : "" ) + seconds);
		},1000);
		
	setInterval( function() {
		// Create a newDate() object and extract the minutes of the current time on the visitor's
		var minutes = new Date().getMinutes();
		// Add a leading zero to the minutes value
		$("#min").html(( minutes < 10 ? "0" : "" ) + minutes);
	    },1000);
		
	setInterval( function() {
		// Create a newDate() object and extract the hours of the current time on the visitor's
		var hours = new Date().getHours();
		// Add a leading zero to the hours value
		$("#hours").html(( hours < 10 ? "0" : "" ) + hours);
	    }, 1000);
}

function displaySave(){
	if($('#wh_mvt_from .active')[0] && $('#wh_mvt_to .active')[0] && $('#wh_mvt_article .check-active')[0]){
		$("#btn_save").removeAttr('disabled');
	}else{
		$("#btn_save").attr("disabled",true);
	}
}

function stock_action(param) {
	var table,idrecord,dataSent;
	var action = param.action;
	var div = $("#div_result");
	switch(action){
		case "home" :
			$("#wh_mvt_out").fadeOut(function(){
				$("#wh_menu").fadeIn();
			});
			return;
		case "goBack" :
			return;
		case "stockOut" :
			table = "sto_stock";
			action = "showMvtOut";
			break;
		case "addFuel" :
			table = "veh_vehicle";
			break;
		case "saveFull" :
			table = "veh_full";
			action = "insertFromWarehouse";
			idrecord = "";
			dataSent = param.dataSent;
			break;
		case "selectShopByPlace" :
			div = $("#"+param.parent);
			switch(param.place){
				case 1 :
					table = "sho_shop";
					action = "stockGetByType";
					dataSent = {sho_type:"1,2,3"};
					break;
				case 4 :
					table = "veh_vehicle";
					action = "stockGetAll";
					break;
				case 14 :
					table = "sho_shop";
					action = "stockGetByType";
					dataSent = {sho_type:"3,4"};
					break;
				case 3 :
					table = "job_job";
					action = "stockGetAllBy";
					dataSent = {idvehicle:$("#wh_mvt_from .list-group-item.active").data("id")};
					break;
				case 15 :
					table = "wor_worker";
					action = "stockGetAll";
					break;
			}
			break;
		case "selectAllByShop" :
			table = "sto_stock";
			action = "showAllByShop";
			idrecord = param.idrecord;
			dataSent = {place:param.place};
			break;
		case "selectJobByVehicle" :
			table = "job_job";
			action = "showAllByVehicle";
			idrecord = param.idrecord;
			dataSent = {};
			break;
		case "createMovement" :
			table = "mov_movement";
			action = "insertFromWarehouse";
			idrecord = "";
			dataSent = param.dataSent;
			break;
		case "stockGetAllClients" :
			table = "job_job";
			idrecord = "";
			break;
		case "stockGetAllJobs" :
			table = "job_job";
			idrecord = param.idrecord;
			break;
		
	}
	callRouter(table, idrecord, action, dataSent, function(result){
	if(result){
		switch(result.code){
			case "homePage" :
				if(div.html() != ""){
					div.fadeOut(duration, function(){
						$(this).html(result.html).fadeIn(duration);
					});
				}else{
					div.html(result.html)
				}
				break;
			case "shopsCreated" :
				$("#wh_menu").fadeOut(duration, function(){
					$("#wh_mvt_out").html(result.html).fadeIn(duration);
					
					// search
					$("#btnSearchArticle").on("keyup", function() {
						var value = $(this).val().toLowerCase();
						if(value.length > 1){
							$("#btn_erase").fadeIn();
							console.log(value)
							$("#wh_mvt_article li").filter(function(index) {
								if($(this).text().toLowerCase().indexOf(value) > -1){
									$(this).closest("li").addClass("d-flex").removeClass("d-none");
								}else{
									$(this).closest("li").addClass("d-none").removeClass("d-flex");
								}
							});
						}else{
							$("#btn_erase").fadeOut();
							$('#wh_mvt_article li').toggleClass('d-none d-flex',true)
						}
					});
					
					// btn_save
					$("#btn_save").on("click", function(){
						$(this).button('loading');
						
						sourceType = $("#wh_mvt_source a.active").data("place");
						idSource = $("#wh_mvt_from a.active").data("id");
						destinationType = $("#wh_mvt_destination a.active").data("place");
						idDestination = $("#wh_mvt_to a.active").data("id");
						
						article = new Array();
						$("#wh_mvt_article .wh-article.check-active").each(function(e){
							article.push([$(this).data("idarticle"),$(".inp-qty",this).val()]);
						});

						stock_action({action:"createMovement", dataSent:{sourceType:sourceType, idSource:idSource, destinationType:destinationType, idDestination:idDestination, article:article}});
						$("#btnSearchArticle").val("");
					});
		
					$(".select-shop-type .list-group-item").on("click", function(){
						if($(this).parent().data("parent") == "wh_mvt_from"){
							$("#wh_mvt_article").html("");
							
							$("#wh_mvt_destination .list-group-item-action").show()
							switch($(this).data("place")){
								case 1 : 
									$("#wh_mvt_destination [data-place=15]").hide();
									break; // shop
								case 4 : 
									$("#wh_mvt_destination [data-place=14]").hide();
									$("#wh_mvt_destination [data-place=15]").hide();
									break; // vehicle
								case 14 : 
									$("#wh_mvt_destination [data-place=4]").hide();
									$("#wh_mvt_destination [data-place=3]").hide();
									$("#wh_mvt_to").empty();
									break; // equipment
							}
						}
						$(this).siblings().removeClass("active");
						$(this).addClass("active");
						stock_action({action:"selectShopByPlace", place:$(this).data("place"), parent:$(this).parent().data("parent")});
					})
					$("#wh_mvt_from").on("click", ".list-group-item", function(){
						$(this).siblings().removeClass("active");
						$(this).addClass("active");
						
						placeSelected = $("ul[data-parent='"+$(this).parent().attr("id")+"']").children(".active").data("place");
						stock_action({action:"selectAllByShop", place:placeSelected, idrecord:$(this).data("id")});
					})
					$("#wh_mvt_to").on("click", ".list-group-item", function(){
						$(this).siblings().removeClass("active");
						$(this).addClass("active");
						displaySave();
					})
				});
				break;
			case "showShops" :
			case "showWorkers" :
				div.fadeOut(duration, function(){
					$(this).html(result.html).fadeIn(duration);
				});
				break;
			case "showAllClients" :
				$("#wh_other_job").html(result.html).fadeIn(duration, function(){
					// search
					$("#btnSearchJob").on("keyup", function() {
						var value = $(this).val().toLowerCase();
						if(value.length > 2){
							$("#btn_erase").fadeIn();
							$("#wh_other_job button").filter(function() {
								if($(this).text().toLowerCase().indexOf(value) > -1){
									$(this).closest("button").removeClass("d-none");
								}else{
									$(this).closest("button").addClass("d-none");
								}
							});
						}else{
							$("#btn_erase").fadeOut();
							$('#wh_other_job button').removeClass('d-none',true)
						}
					});
				});
				
				break;
			case "showAllJobs" :
				$("#wh_other_job").html(result.html);
				break;
			case "showArticles" :
				$("#wh_mvt_article").html(result.html).fadeIn(duration, function(){
					$(".list-group-item", this).on("click", function(){
						$(this).toggleClass('check-active');
						displaySave();
					})
					$(".list-group-item .inp-qty", this).on("click", function(e){
						e.stopPropagation();
						$(this).focus().select();
					})
				})
				// if vehicle => show job by vehicle
				/*
				if(dataSent.type == 2){
					stock_action({action:"selectJobByVehicle", idrecord:idrecord});
				}
				*/
				break;
			case "movementsInserted" :
				$("#btn_save").text('').append("<i class='fal fa-thumbs-o-up'></i> "+result.info);
				$("#wh_mvt_article .wh-article").removeClass('check-active');
				$("#wh_mvt_source a.active").trigger("click");
				$("#wh_mvt_destination a.active").trigger("click");
				setTimeout(function(){
					$("#btn_save").button('reset');
					displaySave();
				}, 500);
				break;
			case "showAddFuel" :
				$("#wh_menu").fadeOut(duration, function(){
					$("#wh_mvt_out").html(result.html).fadeIn(duration);
					$("#vehicles").on("click", ".card", function(){
						$(this).siblings().removeClass("active");
						$(this).addClass("active");
						if($('#fuel .list-group-item.active')[0]){
							$("#veh_full_form fieldset").removeAttr('disabled');
						}else{
							$("#veh_full_form fieldset").attr("disabled",true);
						}
					})
					$("#fuel").on("click", ".list-group-item", function(){
						$(this).siblings().removeClass("active");
						$(this).addClass("active");
						if($('#vehicles .card.active')[0]){
							$("#veh_full_form fieldset").removeAttr('disabled');
						}else{
							$("#vehicles fieldset").attr("disabled",true);
						}
					})
					// btn_save
					$("#btn_save_full").on("click", function(){
						$(this).button('loading');
						
						idVehicle = $("#vehicles a.active").data("id");
						idShop = $("#fuel a.active").data("idshop");
						idArticle = $("#fuel a.active").data("idarticle");
						mileage = $("#ful_mileage").val();
						liter = $("#ful_liter").val();
						remark = $("#ful_remark").val();
						
						stock_action({action:"saveFull", dataSent:{idVehicle:idVehicle, idShop:idShop, idArticle:idArticle, ful_mileage:mileage, ful_liter:liter, ful_remark:remark}});
					});
				});
				break;
			case "fullInserted" :
				$("#btn_save_full").text('').append("<i class='fal fa-thumbs-o-up'></i> "+result.info);
				setTimeout(function(){
					$("#vehicles .card.active").removeClass("active");
					$("#fuel .list-group-item.active").removeClass("active");
					$("#veh_full_form fieldset").attr("disabled",true);
					$("#ful_mileage").val("");
					$("#ful_liter").val("");
					$("#btn_save_full").button('reset');
				}, 500);
				break;
			default : 
				showError(result);
				break;
			}
		setTimeout(function(){
			displaySave();
		}, 500);
		}else{
			showError(result);
		}
	});
}