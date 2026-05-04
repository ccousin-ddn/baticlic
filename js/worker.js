var duration = 500;
var backAction = "";
var idclient = "";
var idsite = "";

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
//worker_action({action:\'startTimer\'})
function worker_action(param) {
	var table,idrecord,dataSent;
	var action = param.action;
	var div = $("#div_result");
	switch(action){
		case "goBack" : 
			worker_action({action:backAction});
			return;
		case "showHomePage" :
			table = "wor_worker";
			$("#btn_back").addClass("disabled");
			break;
		case "showTimesheetHome" :
			table = "wor_attendance";
			$("#btn_back").removeClass("disabled");
			$("#to_timesheet").button('loading');
			break;
		case "selectVehicle" : // vehicle
			table = "wor_attendance";
			break;
		case "selectMaterial" : // material
			dataSent = {idvehicle:param.idvehicle,att_driver:param.att_driver};
			table = "wor_attendance";
			break;
		case "startTimer" :
			table = "wor_attendance";
			var article = [];
			$("#material .check-active").each(function(){
				article.push($(this).data("idarticle"));
			})
			dataSent = {idvehicle:param.idvehicle,att_driver:param.att_driver,idarticle:article};
			//$("#att_start").button('loading');
			break;
		case "stopTimer" :
			table = "wor_attendance";
			idrecord = param.idrecord;
			$("#att_stop").button('loading');
			break;
		case "editTimesheet" :
			table = "tas_task";
			idrecord = param.idrecord;
			$("#btn_back").removeClass("disabled");
			$("#timesheet_edit").button('loading');
			break;
		case "saveAttTravelTime" :
			table = "wor_attendance";
			idrecord = param.idrecord;
			if($("#att_travel_time_going").val() == "" || $("#att_travel_time_coming").val() == ""){
				$("#alert_att_travel_time").show();
				return false;
			}
			dataSent = $("#att_travel_time_form").serialize();
			break;
		case "taskSelectSite" :
			table = "tas_task";
			idrecord = param.idrecord??idclient;
			idclient = idrecord;
			break;	
		case "taskSelectTask" :
			table = "tas_task";
			idrecord = param.idrecord??idsite;
			idsite = idrecord;
			break;	
		case "createTimesheet" :
			table = "tas_task";
			idrecord = param.idtask;
			if($("#tim_duration_"+param.idtask).val() == ""){
				$("#alert_"+param.idtask).show();
				return false;
			}
			dataSent = $("#div_task_"+param.idtask+" form").serialize();
			break;
		case "emptyTimeDuration" :
			$("#tim_duration_"+param.idtask).val("");
			return;
		case "emptyAttTravelTime" :
			$("#att_travel_time_going").val("");
			$("#att_travel_time_coming").val("");
			return;
		case "showMaterial" :
			table = "sto_stock";
			$("#btn_back").removeClass("disabled");
			$("#to_shop").button('loading');
			break;
		case "showPlanning" :
			table = "pla_planning";
			$("#btn_back").removeClass("disabled");
			$("#to_planning").button('loading');
			break;
		case "showWeekCard" :
			table = "wor_attendance";
			action = "weekCardWorker";
			idrecord = param.idrecord;
			dataSent = {idyear:param.year, idweek:param.week};
			break;
		case "showSignature" :
			$("#signature-pad").fadeIn(function(){
				$("#btn_showSignature").css('visibility', 'hidden');;
				enableSignature();
				$("#btn_saveSignature").css('visibility', 'visible');;
			})
			return;
		case "saveSignature" :
			if($("#signature-pad").length){
				if(!signaturePad.isEmpty()){
					btn_save = $("#btn_saveSignature");
					btn_save.button('loading');
					table = "tim_weekly";
					action = "insert";
					idrecord = param.idrecord;
					dataSent = {tim_week:param.tim_week, signature:signaturePad.toDataURL()};
					//$("#quo_signature").val(signaturePad.toDataURL());
				}else{
					return;
				}
			}
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
			case "timesheetHome" :
				div.fadeOut(duration, function(){
					$(this).html(result.html).fadeIn(duration);
				});
				backAction = "showHomePage";
				break;
			case "vehicleSelection" :
				div.fadeOut(duration, function(){
					$(this).html(result.html).fadeIn(duration);
				});
				backAction = "showTimesheetHome";
				break;
			case "materialSelection" :
				div.fadeOut(duration, function(){
					$(this).html(result.html).fadeIn(duration);
				});
				backAction = "showTimesheetHome";
				break;
			case "timerStarted" :
				$("#att_start").button('reset');
				$("#att_start").html(result.html).prop("disabled",true);
				break;
			case "timerStopped" :
				$("#att_stop").button('reset');
				$("#att_stop").html(result.html).prop("disabled",true);
				break;
			case "timesheetEdited" :
				div.fadeOut(duration, function(){
					$(this).html(result.html).fadeIn(duration, function(){
						/*
						$('#edit_timesheet .datetimepicker-input').datetimepicker({
							format: 'HH:mm',
							useCurrent: false,
							stepping: 5,
							defaultDate: moment({hour: 0, minute: 10}),
							readonly: true,
							ignoreReadonly: false,
						}).val('');
						*/
						$('#edit_timesheet .clockpicker').clockpicker({
							placement: 'top',
							align: 'left',
							autoclose: true,
						}).val('');	
					});
					$("#att_stop").removeAttr("disabled");
				});
				backAction = "showTimesheetHome";
				break;
			case "showSelectSite" :
				div.fadeOut(duration, function(){
					$(this).html(result.html).fadeIn(duration);
				});
				//$("#edit_timesheet").html(result.html);
				backAction = "editTimesheet";
				break;
			case "showSelectTask" :
				div.fadeOut(duration, function(){
					$(this).html(result.html).fadeIn(duration, function(){
						$('#edit_timesheet .clockpicker').clockpicker({
							placement: 'top',
							align: 'left',
							autoclose: true,
						}).val('');	
					});
				});
				//$("#edit_timesheet").html(result.html);
				backAction = "taskSelectSite";
				
				break;
			case "timesheetCreated" :
				$("#div_task_"+param.idtask).fadeOut(duration, function(){
					$(this).remove();
				})
				break;
			case "travelTimeInserted" :
				$("#att_travel_time_div").fadeOut(duration, function(){
					$(this).remove();
				})
				break;
			case "material" :
				div.fadeOut(duration, function(){
					$(this).html(result.html).fadeIn(duration);
				});
				backAction = "showHomePage";
				break;
			case "planning" :
				div.fadeOut(duration, function(){
					$(this).html(result.html).fadeIn(duration);
					$('.dropzone').each(function(ndx){
						createDropzone(this);
					})
				});
				backAction = "showHomePage";
				break;
			case "weekCardWorkerCreated" :
				div.fadeOut(duration, function(){
					$(this).html(result.html).fadeIn(duration);
				});
				backAction = "showTimesheetHome";
				break;
			case "signatureInserted" :
				btn_save.text('').append("<i class='fa fa-thumbs-o-up'></i> "+result.info);
				setTimeout(function(){
					btn_save.hide();
					$("#signature-pad").removeClass("absolute-center shadow-lg");
					$("#btn_print").show();
				}, 500);
				break;
			default : 
				showError(result);
				break;
			}
		}else{
			showError(result);
		}
	});
}

function createDropzone(form){
	let myDropzone = new Dropzone(form,{
			url: "libraries/lib_upload.php",
			paramName: "upload_file", // The name that will be used to transfer the file
			maxFilesize: 10, // MB
			parallelUploads: 1,
			createImageThumbnails: false,
			disablePreviews: true,
			uploadprogress: function(file, progress, bytesSent) {
				$(".dz-message",form).html(progress+"%");
			}
		});
		myDropzone.on("sending", function(file, xhr, formData) {
			$(".dz-message",form).html('<i class="fal fa-spinner fa-pulse fa-2x"></i>');
			formData.append("maxSize", 8);
			formData.append("type", "img");
			formData.append("resize", "w400:small/,:large/");
		});
		myDropzone.on("complete", function(file) {
			response = JSON.parse(file.xhr.response);
			if(response.status){
				dataSent = {
					doc_real_name : response.real_name+"."+response.real_ext,
					doc_slug_name : response.slug_name+"."+response.real_ext
				}
				table_action({tablename:"job_picture", idrecord:$(form).data("idjob"), dataSent:dataSent, action:"savePicture"});
				
				setTimeout(function(){
					$(".dz-message",form).html('<i class="fal fa-camera fa-2x"></i>');
				}, 1000);
			}
		});
}