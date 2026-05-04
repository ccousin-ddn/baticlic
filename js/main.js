var infoContent = $("#full-content");
var action;
var param = {};
var bg = getCookie('bg') ?? 0;
// *********** Utiles
history.pushState({ page: 1 }, "", " ");
window.onhashchange = function (event) {
	window.location.hash = "";
};
function getHeight() {return $(window).height() - 115;} // hauteur de la fenêtre - 150px (= hauteur avant tableau)
function scrollTo(source,target){
	if(target.length) {
		$(source).stop().animate({scrollTop: target.offset().top - $(source).offset().top}, 100);
    }
}
function jq(myid,ext){
    return ext + myid.replace( /(:|\.|\[|\]|,|=)/g, "\\$1" );
}
function modalRemove(modal){
	$(modal).remove();
	$(".modal-backdrop.fade.show").eq(0).remove();
}
function printElement(elem) {
	var style = $("#div-body style").html();
	var bodyClass = $("body").attr("class");
	var bodyStyle = $("body").attr("style");
	
	$(".modal-backdrop").removeClass("show");
	$("section").hide();
	$("body").removeClass().addClass("print");
	$(".modal-dialog").removeClass("modal-dialog-scrollable");

	window.print();
	
	$("body").removeClass("print").attr("class",bodyClass).attr("style",bodyStyle);
	$("section").show();
	$(".modal-dialog").addClass("modal-dialog-scrollable");
	$(".modal-backdrop").addClass("show");
	
}

// cookie
function setCookie(key, value, duration) {
	// duration in days
	duration = 1000 * 60 * 60 * 24 * duration;
	var date = new Date();
	date.setTime(date.getTime() + duration);
	if(duration > 0){
		document.cookie = key + '=' + value + ';expires=' + date.toGMTString() + ';path=/lpdn;secure=true;samesite=strict';
	}else{
		document.cookie = key + '=' + value + ';path=/lpdn;secure=true;samesite=strict';
	}
}
function getCookie(key) {
	var keyValue = document.cookie.match('(^|;) ?' + key + '=([^;]*)(;|$)');
	
	return keyValue ? keyValue[2] : null;
}
function eraseCookie(key) {
	var keyValue = getCookie(key);
	setCookie(key, keyValue, '-1');
}
// dashboard
function dashBoard(){
	$("#full-content").load("index_body.php", function(){
		$(this).children(':first').unwrap();
		infoContent = $("#full-content");
		dashBoard_action();
	});
}
function dashBoard_action(){
	$("#full-content").css("background-image", "url('images/bg"+bg+".jpg')");
	
	$(".dashboard .card .card-body ul").on("click","li.list-group-item",function(event){
		var tableName = $(this.offsetParent).data("table");
		var action = $(this.offsetParent).data("action");
		var idtab = $(this.offsetParent).data("tab");
		var idrecord = $(this).data("idrecord");
		if(event.target.nodeName == "I" || event.target.nodeName == "BUTTON"){
			if(event.target.nodeName == "I"){
				var btn = $(event.target).parent();
			}else{
				var btn = $(event.target);
			}
			action = btn.data("action");
			var table = tableName.split("_");
			switch(action){
				case "add" :
					btn.children("i").toggleClass("fa-square fa-check-square");
					var formatter = new Intl.NumberFormat("de-DE", {
						style: "currency",
						currency: "EUR"
					});
					total = parseFloat($("#"+table[0]+"_total").text().replace(".","").replace(",",".").replace(" €",""))||0;
					amount = parseFloat(btn.data("amount"))||0;
					if(btn.hasClass("active")){
						btn.removeClass("active");
						total -= amount;
					}else{
						btn.addClass("active");
						total += amount;
					}
					$("#"+table[0]+"_total").text(formatter.format(total));
					break;
				case "paid" :
					table_action({tablename:tableName, idrecord:idrecord, action:'paid'});
					$(this).fadeOut(800, function(){
						$(this).remove();
					});
					break;
			}
		}else{
			if(action == "showCard"){
				if(typeof idtab == "undefined"){
					showCard(tableName,idrecord);
				}else{
					showTabCard(tableName,idrecord,idtab);
				}
			}else{
				window[tableName+"_action"](idrecord, {}, action);
				switch(tableName){
					case "" :
						$(this).hide(function(){
							$(this).remove();
						})
						break;
				}
			}
		}
	})
	if($("#netChart").length){
		today = new Date();
		month = today.getMonth();
		if(month < 9){
			year = today.getFullYear();
		}else{
			year = today.getFullYear()+1;
		}
		table_action({idrecord:0,tablename:'cfg_config',dataSent:year,action:'getChart'});
	}
}
function changeBG(){
	bg++;
	if(bg >= 4){bg=0;}
	$("#full-content").css("background-image", "url('images/bg"+bg+".jpg')");
	setCookie('bg', bg);
	//document.body.style.backgroundImage = "url('images/bg"+bg+".jpg')";
}

/*** Bootstrap button loading ***/
(function($) {
    $.fn.button = function(action) {
        if (action === 'loading' && this.data('loading-text')) {
            this.data('original-text', this.html()).html(this.data('loading-text')).prop('disabled', true);
        }
        if (action === 'reset' && this.data('original-text')) {
            this.html(this.data('original-text')).prop('disabled', false);
        }
    };
}(jQuery));
function generateRandomColor() {
    var letters = '0123456789ABCDEF';
    var color = '#';
    for (var i = 0; i < 6; i++) {
        color += letters[Math.floor(Math.random() * 16)];
    }
    return color;
}
function calculSum(elm){
	var sum = 0;
	$(elm).each(function() {
	    var value = $(this).text();
	    if(!isNaN(value) && value.length != 0) {
	        sum += parseFloat(value);
	    }
	});
	return sum.toFixed(2);
}
function currencyFormat(num,dec) {
	num = parseFloat(num);
    return (
        num
            .toFixed(dec)
            .replace('.', ',') // replace decimal point character with ,
            .replace(/(\d)(?=(\d{3})+(?!\d))/g, '$1.') + ' €'
    ) // use . as a separator
}
function getToday(separator){
	var today = new Date();
	var dd = today.getDate();
	var mm = today.getMonth()+1;
	var yy = today.getFullYear();
	if(dd<10){
	    dd='0'+dd;
	} 
	if(mm<10){
	    mm='0'+mm;
	} 
	var today = dd+separator+mm+separator+yy;
	return today;
}
function addDaysToDate(date,format,days){
	var separator = format.charAt(1);
    var elem = format.split(separator);
    var parts = date.split(separator);
    var nd = elem.findIndex(part => part === "d");
    var nm = elem.findIndex(part => part === "m");
    var ny = elem.findIndex(part => part === "Y");
    var dateFormated = new Date(parts[ny], parts[nm] - 1, parts[nd]);
    dateFormated.setDate(parseInt(parts[nd]) + parseInt(days));
    var dd = dateFormated.getDate();
	var mm = dateFormated.getMonth()+1;
	var yy = dateFormated.getFullYear();
	if(dd<10){
	    dd='0'+dd;
	} 
	if(mm<10){
	    mm='0'+mm;
	} 
	var newDate = dd+separator+mm+separator+yy;
    
    return newDate;

}
function padnum(n){return n<10 ? '0'+n : n}
function calculateDuration(begin,end){
	var hour=0;
    var minute=0;
    var splitBegin= begin.split(':');
    var splitEnd= end.split(':');
	minBegin = (parseInt(splitBegin[0])*60) + parseInt(splitBegin[1]);
	minEnd = (parseInt(splitEnd[0])*60) + parseInt(splitEnd[1]);
	minutes = minEnd-minBegin;
	
	hour = padnum(Math.floor(minutes/60));
	minute = padnum(minutes % 60);
	
	return hour+':'+minute+':00';
}
function sortList(idlist, children, data, order){
	if(order == "up"){
		$("#"+idlist).html(
			$("#"+idlist).children(children).sort(function (a, b) {
				return $(a).data(data).toString().localeCompare(
					$(b).data(data), 
					undefined, 
					{'numeric': true}
				);
			}) // End Sort
		); // End HTML 
	}else{
		$("#"+idlist).html(
			$("#"+idlist).children(children).sort(function (a, b) {
				return $(b).data(data).toString().localeCompare(
					$(a).data(data).toString(), 
					undefined, 
					{'numeric': true}
				);
			}) // End Sort
		); // End HTML 
	}
}
/*** Bootstrap table sort column ***/
function sortDate(a, b) {
	dateA = new Date(a.substring(6,10),a.substring(3,5),a.substring(0,2));
	dateB = new Date(b.substring(6,10),b.substring(3,5),b.substring(0,2));
	if (dateA > dateB) return 1;
	if (dateA < dateB) return -1;
	return 0;
}
function sortEuro(a, b) {
	a = a || "0";
	b = b || "0";
	euroA = a.replace(".","");
	euroA = parseFloat(euroA.replace(",","."));
	euroB = b.replace(".","");
	euroB = parseFloat(euroB.replace(",","."));
	if (euroA > euroB) return 1;
	if (euroA < euroB) return -1;
	return 0;
}
function sortDuration(a, b) {
	a = a.replace('.', '');
	b = b.replace('.', '');
	durA = Number.parseInt(a.substring(0, a.length-3));
	durB = Number.parseInt(b.substring(0, b.length-3));
	if (durA > durB) return 1;
	if (durA < durB) return -1;
	return 0;
}
/*** ***/
function callRouter(table, idrecord, action, dataSent, callback){
	$.ajax({
		type:"POST",
		url: "controllers/c_router.php",
		dataType: 'json',
		data:{table:table, idrecord:idrecord, action:action, dataSent:dataSent},
		success: callback,
		error: function(js_result, js_status, error) {
			showError(js_result);
			$("#waiting").hide();
		}
	});
}
function showError(result){
	if(typeof result.code !== 'undefined'){
		$("#xdebug").html("Error "+result.code+"<br>"+result.html+"<br>File : "+result.file+":"+result.line+"<br>From : "+result.from+"<br>Trace : "+result.trace);
	}else{
		//infoContent.html(result.responseText);
		if(typeof result.responseText !== 'undefined'){
			$("#xdebug").html("<pre>"+result.responseText+"</pre>");
		}else{
			$("#xdebug").html("<pre>"+result+"</pre>");
		}
	}
	$("#xdebug-modal").on('hidden.bs.modal', function (e) {
		$("#xdebug").empty();
	});
	$('#xdebug-modal').modal({
		backdrop: false,
    	show: true
	});
}

$('.dropdown-menu a.dropdown-toggle').on('click', function(e) {
    if (!$(this).next().hasClass('show')) {
        $(this).parents('.dropdown-menu').first().find('.show').removeClass("show");
    }
    var $subMenu = $(this).next(".dropdown-menu");
    $subMenu.toggleClass('show');

    $(this).parents('li.nav-item.dropdown.show').on('hidden.bs.dropdown', function(e) {
        $('.dropdown-submenu .show').removeClass("show");
    });

    return false;
});

// *** init search in navbar
$("#navbar-menu .div-search").each(function(){
	var tableName = $(this).data("table");
	// input
	checkEnter($("#"+tableName+"_search"), function(){
		showTable(tableName,'search','showCard');
		$("#navbar-menu .nav-item.show .dropdown-item .dropdown-toggle").dropdown('hide');
		$("#navbar-menu .nav-item.show > .dropdown-toggle").dropdown('hide');
	})
	// button
	$(".fa-search", this).click(function(){
		showTable(tableName,'search','showCard');
	})
})
$("#help-diagram").click(function(){
	infoContent.load("tree.html");
});

function checkEnter(elem, callback){
	$(elem).keydown(function (e){
	    if(e.keyCode == 13){
	    	e.preventDefault();
	        if ($.isFunction(callback)){
			    callback.call();
			}
	    }
	});
}

// *********** User
function userCheckEnter(type,e){
  var keycode;
  if (window.event) keycode = window.event.keyCode;
  else if (e) keycode = e.which;
  else return true;
  if (keycode == 13){
  	e.preventDefault();
  	switch(type){
  		case 1 : e.target = e.target.activeElement; userLogin(e); return false;
		case 2 : userPassword("forgotPassword",e); return false;
		case 3 : userPassword("verifCode",e); return false;
		case 4 : userPassword("saveNewPassword",e); return false;
		case 5 : userPassword("saveNewPassword",e); return false;
  	}
  }else
     return true;
}
function userLogin(e){
	var form = e.target.form??e.target.activeElement.form;
	var usr_login = $("#usr_login",form);
	var usr_password = $("#usr_password",form);
	var btn_login = $("#btn-login",form);
	var result_login = $("#result_login",form);
	
    var login = usr_login.val();
	var password = usr_password.val();
	
	if(login == ""){
		usr_login.parent().removeClass("has-success").addClass("has-error");
		usr_login.focus();
		return false;
	}else{
		usr_login.parent().removeClass("has-error").addClass("has-success");
	}

	if(password == ""){
		usr_password.parent().removeClass("has-success").addClass("has-error");
		usr_password.focus();
		return false;
	}else{
		usr_password.parent().removeClass("has-error").addClass("has-success");
	}
	if($("#rememberMe",form).is(':checked')){
		remember = 1;
	}else{
		remember = 0;
	}
	callRouter('usr_user', -1, 'login', {usr_login:login, usr_password:password, remember:remember}, function(result){
		if(result){
			switch(result.code){
				case 0 : window.location.href = window.location.href.split('?')[0];
					break;
				case 1 : 
					result_login.html(result.info).addClass('show');
					usr_login.parent().removeClass("has-success").addClass("has-error");
					usr_login.focus();
					break;
				case 2 : 
					result_login.html(result.info).addClass('show');
					usr_password.parent().removeClass("has-success").addClass("has-error");
					usr_password.val("").focus();
					break;
				case 3 : 
					result_login.html(result.info).addClass('show');
					usr_password.parent().removeClass("has-success").addClass("has-error");
					usr_password.val("").attr("disabled","true");
					btn_login.attr("disabled","true");
					var idInt = setInterval(function(){
						var sec = parseInt($("#timer").text());
						if(sec > 0){
							sec --;
						}else{
							result_login.html(result.info).removeClass('show');
							usr_password.removeAttr("disabled");
							btn_login.removeAttr("disabled");
							clearInterval(idInt);
						}
						$("#timer").text(sec);
					}, 1000);
					break;
				case 4 : 
					result_login.html(result.html).toggleClass("alert-danger alert-light").addClass('show');
					btn_login.remove();
					break;
				default : 
					result_login.html(result.info).addClass('show');
					usr_login.parent().removeClass("has-success").addClass("has-error");
					break;
			}
		}
	});
}
function userPassword(action,e){
	var usr_info
	switch(action){
		case "forgotPassword" :
			if($("#usr_email").val() == ""){
				$("#usr_email").parent().addClass("has-error");
				return false;
			}else{
				dataSent = {usr_email:$("#usr_email").val()};
			}
			break;
		case "verifCode" :
			dataSent = {usr_code:$("#usr_code").val()};
			if(dataSent == ""){
				$("#usr_code").parent().addClass("has-error");
				return false;
			}
			break;
		case "showChangingPassword" :
			$("#popup-info").remove();
			$('body').prepend('<div id="popup-info" class="modal fade"></div>');
			open_popup("popup_change_password.php", {}, "popup-info");
			return true;
			//break;
		case "saveNewPassword" :
			if($("#usr_password_old").val() == ""){
				$("#usr_password_old").parent().addClass("has-error");
				$("#usr_password_old").focus();
				return false;
			}
            // Vérification validité nouveau mot de passe
            var pwd = $("#usr_password_new").val();
			$("#result_password > span").hide();
            if(pwd.length < 6){
            	$("#mess1").show();
                $("#result_password").addClass('show');
                $("#usr_password_new").parent().addClass("has-error");
                $("#usr_password_new").focus();
                return false;
            }
            if(!pwd.match(/\d+/)){
            	$("#mess2").show();
                $("#result_password").addClass('show');
                $("#usr_password_new").parent().addClass("has-error");
                $("#usr_password_new").focus();
                return false;
            }
			if(!pwd.match(/[a-zA-Z]+/)){
            	$("#mess4").show();
                $("#result_password").addClass('show');
                $("#usr_password_new").parent().addClass("has-error");
                $("#usr_password_new").focus();
                return false;
            }
            // Correspondance mots de passe
            if($("#usr_password_new").val() != $("#usr_password_new_confirm").val()){
            	$("#mess3").show();
                $("#result_password").addClass('show');
                $("#usr_password_new_confirm").parent().addClass("has-error");
                $("#usr_password_new_confirm").focus();
				return false;
            }
			dataSent = {old_password:$("#usr_password_old").val(), new_password:pwd};
            break;
	}
	
	callRouter('usr_user', -1, action, dataSent, function(result){
		if(result){
			switch(result.code){
				case 0 : 
		            $("#result_password").html(result.info).addClass('show');
					$("#usr_password_old").focus();
					return false;
				case "saved" :
					$("#popup-info .modal-footer").html("");
					$("#popup-info .modal-body").html(result.info);
					setTimeout(function(){$("#popup-info").modal("hide");}, 1500);
					break;
				case "codeSent" :
					$("#passwordform").html(result.html);
					break;
				case "codeCorrect" :
					$("#passwordform").html(result.html);
					break;
				case "codeError" :
					$("#result_code").html(result.info).addClass('show');
					break;
				case "savedAfterCode" :
					$("#passwordform .btn").text(result.info);
					setTimeout(function(){$('#passwordbox').hide(); $('#loginbox').show();}, 1500);
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
function userSignup(){
	$("#waiting").show();
	if(!verif_input("signupbox")){
		$("#waiting").hide();
		return;
	}
	dataSent = $("#signupbox_modal form[id^='signupbox_form'] :not(.external) > :input").serialize(); 
	$.ajax({
		type:"POST",
		url: "controllers/c_usr_user.php",
		data:{action:'signUp', data:dataSent},
		success: function (result, status){
			$('#loginform #usr_login').val($('#signupbox_form input[name="usr_email"]').val());
			$("#signupbox_form").html(result);
			$("#waiting").hide();
		},
		error: function(result, status, error) {
			console.log(result.responseText);
		},
		complete: function(xhr,status){
		}
	});	
}


function enableSignature(){
	// Signature Pad
	if($("#signature-pad").length){
		$.getScript("js/signature_pad.min.js")
		.done(function (script, textStatus) {
			var wrapper = document.getElementById("signature-pad");
			var undoButton = wrapper.querySelector("[data-action=undo]");
			var savePNGButton = wrapper.querySelector("[data-action=save-png]");
			var canvas = wrapper.querySelector("canvas");
			signaturePad = new SignaturePad(canvas, {
				minWidth: 0.5,
				maxWidth: 2,
				throttle: 8,
				minDistance: 1,
				//backgroundColor: 'rgb(255, 255, 255)'
			});
			var ratio =  Math.max(window.devicePixelRatio || 1, 1);
			canvas.width = canvas.offsetWidth * ratio;
			canvas.height = canvas.offsetHeight * ratio;
			canvas.getContext("2d").scale(ratio, ratio);
			signaturePad.clear();
		})
		.fail(function (jqxhr, settings, ex) {
			alert("Could not load Signature Pad script: " + jqxhr);
		});
	}
}

//https://github.com/hhurz/tableExport.jquery.plugin
function exportExcel(db_name){
	var table_name = db_name+"_table";
	var file_name = db_name.substring(4);
	$("#"+table_name).tableExport({
		consoleLog: false,
		csvEnclosure: '"',
		csvSeparator: ',',
		csvUseBOM: true,
		displayTableName: false,
		escape: false,
		excelstyles: [],
		fileName: "LPDN-"+file_name,
		htmlContent: false,
		ignoreColumn: [],
		ignoreRow: [],
		jsonScope: 'all',
		numbers: {
			html: {decimalMark: '.',thousandsSeparator: ','},
		    output: {decimalMark: '.',thousandsSeparator: ','}
		},
		onCellData: null,
		onCellHtmlData: null,
		onMsoNumberFormat: null,
		outputMode: 'file',
		tbodySelector: 'tr',
		tfootSelector: 'tr',
		theadSelector: 'tr',
		tableName: 'myTableName',
		type: 'xlsx',
		worksheetName: file_name
	});
}

// *********** Upload
function upload_dropzone(form) {
	if (!$(form).hasClass("dz-clickable")){
		switch($(".dz-message", form).data("type")){
			case "img" : type = "image/*"; break;
			case "img&pdf" : type = "image/*,application/pdf"; break;
			case "doc" : type = "application/*"; break;
			case "all" : type = "file"; break;
		}
		let myDropzone = new Dropzone(form,{
			url: "libraries/lib_upload.php",
			paramName: "upload_file", // The name that will be used to transfer the file
			maxFilesize: 120, // MB
			parallelUploads: 20,
			uploadMultiple: false,
			acceptedFiles: type,
			thumbnailWidth: '100%',
			thumbnailHeight: 350,
			thumbnailMethod: "contain",
			dictInvalidFileType: "Format de fichier pas supporté !",
			dictFileTooBig: "Fichier trop lourd : {{filesize}} Mb (Maximum {{maxFilesize}}Mb)",
		});
		myDropzone.on("processing", function(file) {
			if($(form).hasClass("dz-input")){
				if($(".dz-preview", form).length > 1){
					$(".dz-preview", form)[0].remove();
				}
			}
		});
		myDropzone.on("sending", function(file, xhr, formData) {
			formData.append("maxSize", $(".dz-message", form).data("maxSize"));
			formData.append("type", $(".dz-message", form).data("type"));
			formData.append("resize", $(".dz-message", form).data("resize"));
		});
		myDropzone.on("success", function(file) {
			response = JSON.parse(file.xhr.response);
			if(response.status){
				// dz-input for lib_bs_input upload
				if($(form).hasClass("dz-input")){
					$("input.dz-newName", form).val(response.slug_name+"."+response.real_ext);
					//LIBRARY BPU
					if($("input.dz-newName", form).attr("id") == "lib_bpu_file"){
						table_action({tablename:"lib_library", idrecord:$("#idlibrary").val(), dataSent:{fileName:response.real_name, slug:response.slug_name+"."+response.real_ext}, action:"saveBPU"})
					}
					//CONTRACT BPU
					if($("input.dz-newName", form).attr("id") == "con_bpu_file"){
						table_action({tablename:"con_contract", idrecord:$("#idcontract").val(), dataSent:{fileName:response.real_name, slug:response.slug_name+"."+response.real_ext}, action:"saveBPU"})
					}
					//DPGF
					if($("input.dz-newName", form).attr("id") == "quo_dpgf_file"){
						table_action({tablename:"quo_quotation", idrecord:$("#idquotation").val(), dataSent:{fileName:response.real_name, slug:response.slug_name+"."+response.real_ext}, action:"saveDPGF"})
					}
					//XLS
					if($("input.dz-newName", form).attr("id") == "sup_rate_file"){
						table_action({tablename:"sup_supplier", idrecord:$("#idsupplier").val(), dataSent:{fileName:response.real_name, slug:response.slug_name+"."+response.real_ext}, action:"saveXLS"})
					}
					//ARTICLE
					if($("input.dz-newName", form).attr("id") == "art_article_file"){
						table_action({tablename:"art_article", idrecord:0, dataSent:{fileName:response.slug_name+"."+response.real_ext}, action:"importXLS"})
					}
				}else{
					dataSent = {
						mimetype : response.mimetype,
						doc_from : $("#docFrom", form).val(), 
						idfrom : $("#idFrom", form).val(),
						resize : $(".dz-message", form).data("resize"),
						doc_real_name : response.real_name+"."+response.real_ext,
						doc_slug_name : response.slug_name+"."+response.real_ext,
						formId : form
					}
					setTimeout(function(){
						upload_action(0, "insert", dataSent, function(){
							myDropzone.removeFile(file);
							$(form).removeClass("dz-started");
						});
					}, 100);
				}
			}else{
				//$(".dz-preview", form).toggleClass("dz-success dz-error");
				$(file.previewElement).toggleClass("dz-success dz-error");
				$(".dz-error-message span", form).text(response.info);
				$(".dz-error-mark", form).on("click", function(){
					$(this).parent().remove();
					$(form).removeClass("dz-started");
				});
				
			}
		});
		myDropzone.on("error", function(file) {
			$(".dz-error-mark", form).on("click", function(){
				$(this).parent().remove();
				$(form).removeClass("dz-started");
			});
		});
		// update info
		$(form).on("change","input,textarea",function(event){
			upload_action($(this).data("iddocument"), "updateInfo", {doc_info:$(this).val()});
		})
		// zoom
		$(form).on("click", "div.doc-thumbnail div.zoomable", function(){
			if(!$(this).hasClass("zoom")){
				var that = this;
				var img = $(this).attr("href");
				$("img", this).attr("src",img);
				$(this).addClass("img-full zoom");
				$(".btn-close-zoom", this).removeClass("d-none").on("click touchend", function(e){
					e.stopPropagation();
					$(".btn-close-zoom", that).addClass("d-none");
					$(that).removeClass("img-full zoom zoom-active");
					$("img", that).css("transform","");
					$(that).replaceWith($(that).clone());
				});
				zoom({zoom: "zoom"},{scrollDisable: false});
			}
		})
		
		//PDF
		$(form).on("click", "div.doc-thumbnail div.zoomPdf", function(){
			if(!$(this).hasClass("img-full")){
				var that = this;
				$(this).addClass("img-full");
				$("img",this).hide();
				$("embed",this).show();
				$(".btn-close-zoom", this).removeClass("d-none").on("click touchend", function(e){
					e.stopPropagation();
					$(".btn-close-zoom", that).addClass("d-none");
					$("embed",that).hide();
					$("img",that).show();
					$(that).removeClass("img-full");
				});
			}
		})
	}
}
function upload_action(idrecord, action, dataSent, callback){
	switch(action){
		case "insert" : 
			var $div_upload = dataSent['formId'];
			delete dataSent['formId'];
			break;
		case "delete" : 
		 break;
	}
	callRouter("doc_document", idrecord, action, dataSent, function(result){
		if(result){
			switch(result.code){
				case "inserted" : 
					tab = $($div_upload).closest(".tab-pane").attr("id");
					nav = $(".nav-item a[aria-controls="+tab+"]");
					$(".dz-message", $div_upload).before(result.html);
					$(".badge",nav).text(parseInt($(".badge",nav).text())+1);
					break;
				case "deleted" :
					$(jq(idrecord,".upload_")).fadeOut(800,function(){
						tab = $(this).closest(".tab-pane").attr("id");
						nav = $(".nav-item a[aria-controls="+tab+"]");
						$(this).remove();
						$(".badge",nav).text(parseInt($(".badge",nav).text())-1);
					})
					break;
				case "updated" :
					break;
				default :
					showError(result);
			}
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
function deleteImage(input, tablename, dataSent){
	$("#waiting").show();
	var idrecord = 0;
	var action = "deleteImg";
	switch(tablename){
		case "quo_quotation" :
			idrecord = $("#idquotation").val();
			action = "deleteDPGF";
			break;
		case "con_contract" :
			idrecord = $("#idcontract").val();
			action = "deleteBPU";
			break;
	}
	callRouter(tablename, idrecord, action, dataSent, function(result){
		if(result){
			switch(result.code){
				case "dpgfDeleted" :
					$("#quo_dpgf_name").val("");
					$("#btn_analyze_dpgf").attr("Disabled",true);
					$("#btn_import_dpgf").attr("Disabled",true);
					$("#dz-message").removeClass("d-none");
					$("#dz-image").toggleClass("d-flex d-none");
				case "bpuDeleted" :
					$("#con_bpu_name").val("");
					$("#btn_analyze_bpu").attr("Disabled",true);
					$("#btn_import_bpu").attr("Disabled",true);
					$("#dz-message").removeClass("d-none");
					$("#dz-image").empty();
					$(".table-result").empty();
				case "imageDeleted" : 
					$("#"+input).val("");
					if(input == "lib_bpu_file"){
						$("#lib_bpu_name").val("");
						$("#btn_analyze_bpu").attr("Disabled",true);
						$("#btn_import_bpu").attr("Disabled",true);
					}
					if(input == "con_bpu_file"){
						$("#con_bpu_name").val("");
						$("#btn_analyze_bpu").attr("Disabled",true);
						$("#btn_import_bpu").attr("Disabled",true);
					}
					$("#dz-message").removeClass("d-none");
					$("#dz-image").toggleClass("d-flex d-none");
					break;
				default : 
					showError(result);
					break;
			}
		}else{
			showError(result);
		}
		$("#waiting").hide();
	});
}

// *********** Open popup
function open_popup(file, post, popup){
    $("#"+popup).replaceWith($("#"+popup).clone());
	$.post(file, post, function(result){
        $("#"+popup).html(result).modal({backdrop:'static',keyboard:false, show:true});
		if(popup == "popup-mail" || popup == "popup-msg"){
			$(".modal-backdrop.fade.show").eq(1).css("z-index","1050");
			$("#"+popup).on('hidden.bs.modal', function (e) {
                $('body').addClass('modal-open');
            });
		}else if(popup != "popup-delete"){
            $("#"+popup).on('hide.bs.modal', function (e) {
				$(this).remove();
				$('body').addClass('modal-open');
            });
        }else{
        	$(".modal-backdrop.fade.show").eq(1).css("z-index","1050");
        }
		switch(file){
			case "popup_upload.php" :
				construct_input('#upload_form');
				break;
		}
	});
}
// *********** input
function construct_input(div) {
// table
	$(div+" .bootstrap-table:not(.bt-done)").bootstrapTable({
    	//height : $(this).data("height") == "auto" ? $("body").height()-300 : $(this).data("height"),
		//height : 500,
		locale : "fr-FR",
		//toolbar : "#table-toolbar",
		//buttonsClass : "outline-secondary hidden-xs",
		//tableName : tableName,
		//showExport: false,
		//showColumns: false,
		searchTimeOut: 500,
		undefinedText: '',
	});
	$(div+" .bootstrap-table").addClass("bt-done");
	//$(div+" .bootstrap-table").bootstrapTable( 'resetView', {height: $("body").height()-350} );
// Popover
	$(div+' [data-toggle="popover"]').popover();
/* Full img upload */
	$(div+' .upload_preview').click(function(){
		$(this).toggleClass("img-full");
	})
/* Tooltip */
	//$(div+' [data-toggle="tooltip"]').tooltip();
/* number input */
	$(div+' :input[type="number"]').on('input',function(){
		object = $(this)[0];
		if (object.value.length > object.max.length){
			object.value = object.value.slice(0, object.max.length)
		}
	})
// https://getdatepicker.com/5-4
	$(div+' .datetimepicker-input:not(.clockpicker)').datetimepicker({
    	locale: "fr",
		useCurrent: false,
		buttons: {
            showToday: true,
            showClear: false,
            showClose: true
        },
		//widgetPositioning: {vertical:'bottom',horizontal: 'right'},
		//debug: true,
       });
// https://getdatepicker.com/5-4
	$(div+' .datetimepicker-input.clockpicker').datetimepicker({
		format: 'HH:mm',
		useCurrent: false,
		stepping: 5,
		//defaultDate: moment({hour: 0, minute:15}),
	});
	
// Color https://farbelous.io/bootstrap-colorpicker/v3/	
	$(div+' .color-picker').colorpicker({
		format: 'rgba',
		useAlpha: true,
		fallbackColor: 'rgba(128, 128, 128, 1)',
		 /*options...*/ 
	});
// Select http://silviomoreto.github.io/bootstrap-select/
	$.fn.selectpicker.Constructor.BootstrapVersion = '4';
	if( /Android|webOS|iPhone|iPad|iPod|BlackBerry/i.test(navigator.userAgent) ) {
		$.fn.selectpicker.Constructor.DEFAULTS.mobile = true;
	    $(div+' .selectpicker').selectpicker({
	    	mobile : true,
			iconBase : "fal",
			tickIcon : "fa-check",
			style : "",
		});
	}else{
		$(div+' .selectpicker').selectpicker({
			iconBase : "fal",
			tickIcon : "fa-check",
			style : "",
		});		
	}
	
	// SearchDB
	$(div+' .selectpicker[data-live-search-db]').one('shown.bs.select', function (e) {
		if($(this).data("table")){
	    	tableName = $(this).data("table");
			elem = $(this);
			minSize = $(this).data("minSize");
			
			$(".bs-searchbox .form-control").keyup(function(e){
				var code = e.keyCode ? e.keyCode : e.which;
			    switch(code){
			        case 38: e.preventDefault(); return true;
			        case 40: e.preventDefault(); return true;
			        default:
						var dataSent = $(this).val();
						if(dataSent.length > 2){
							callRouter(tableName, -1, "selectSearch", dataSent, function(result){
								if(result){
									switch(result.code){
										case "results" :
											elem.html(result.html).selectpicker('refresh');//.selectpicker('toggle');
											break;
										case "noResult" :
											//$(".bootstrap-select.show .no-results").html(result.html);
											break;
										default : 
											showError(result);
											break;
									}
								}else{
									showError(result);
								}
							});
						}else{
							$(".bootstrap-select.show .no-results").text(minSize);
							elem.html("").selectpicker('refresh');
						}
						break;
				}
			});
		}
	});
	// FilterChild (selectPicker)
	$(div+' .selectpicker[data-filter-child]').on('changed.bs.select', function(e){
		var childTableNames = $(this).data("filter-child");
		var idrecord = $(this).val();
		var dataSent = {};
		childTableNames = childTableNames.split(',');
		childTableNames.forEach(function(childTableName, ndx) {
		    selectFilterChild(idrecord, childTableName, ndx, dataSent);
		});
	});
	// no-results click new
	$(div+' .selectpicker').on('shown.bs.select', function(e){
		var position = $(this).offset();
		var posX = position.top;
		var modalH = $(".modal-dialog-scrollable").height();
		var footerHeight = $(".modal-footer").outerHeight(true);
		//console.log(modalH, posX, footerHeight);
		$(".bootstrap-select.show .inner.show").css("max-height",Math.max(modalH-posX-footerHeight-95, 70)+"px");
		$("body").off("click").on("click",".no-results", function(elm){
			tableName = $("span",this).data("table");
			idparent = $("span",this).data("id");
			value = $("span",this).text();
			selectNewRecord(tableName,idparent,value,elm);
		})
	});
/* Unlock */
	$(div+" .btn-unlock").click(function(event){
		event.preventDefault();
    	$(this).parent().prev("input").prop("disabled", false).focus();
	})
/* action */
	$(div+" .btn-action").click(function(event){
		event.preventDefault();
		param = $(this).data("param");
		$("#"+param.column).val(param.iduser);
    	$(this).parent().prev("input").val(param.usr_name);
	})
/* contentEditable */
	$('.modal').on('focus', '[contenteditable=true]',function() {
		requestAnimationFrame(function(){document.execCommand('selectAll', false, null)});
    });
/* upload document */
	$(div+'.dropzone').each(function(ndx){
		upload_dropzone(this);
	})
/* upload input */
	$(div+' .dropzone').each(function(ndx){
		upload_dropzone(this);
	})
};

function selectFilterChild(idrecord, childTableName, index, dataSent, callback){
	switch(childTableName){
		case "job_job" :
			dataSent['idclient'] = $("#idclient").val();
			break;
	}

	callRouter(childTableName, idrecord, "filterChild", dataSent, function(result){
		if(result){
			switch(result.code){
				case 0 : 
					$("#"+result.idchild).html("").selectpicker('refresh');
					break;
				case 1 : 
					if(index==0){
						$("#"+result.idchild).html(result.html).selectpicker('refresh').selectpicker('toggle');
					}else{
						$("#"+result.idchild).html(result.html).selectpicker('refresh');
					}
					break;
				default :
					showError(result);
			}
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

function selectNewRecord(tableName, idparent, value, e, callback){
	var modal = $('.modal.fade.show').last().attr('id');
	switch(tableName){
		case "art_subcategory" :
			otherId = $("#idartcategory").val();
			break;
		case "sit_site" :
		case "lib_library" :
			otherId = $("#idclient").val();
			break;
		case "sup_article" :
			otherId = $("#idsupplier").val();
			break;
		case "lib_work" :
			otherId = $("#idclient").val();
			break;
		case "wor_subcategory" :
			otherId = $("#idworcategory").val();
			break;
		case "con_contract" :
			otherId = $("#idclient").val();
			break;
		default : otherId = 0;
	}
	callRouter(tableName, idparent, "newFrom", {parentModal:modal, newValue:value.replace(/['"]+/g, ''), otherId:otherId}, function(result){
		if(result){
			switch(result.code){
				case 0 : 
					$("#"+result.idchild).html("").selectpicker('refresh');
					break;
				case 1 : 
					var newModal = '#'+tableName+'_modal';
					$('body').append(result.html);
					$(newModal).modal({backdrop:'static',keyboard:false, show:true}).css("z-index","1100");
					$(".modal-backdrop.fade.show:eq(1)").css("z-index","1050");
					construct_input(newModal);
					$(newModal).attr("data-parent-modal",result.parentModal);
					$(newModal).attr("data-field-name",result.fieldName);
					
					if (typeof window[tableName+"_ext_showCard"] == 'function'){
						window[tableName+"_ext_showCard"](0);
					}
					$(newModal).on('hidden.bs.modal', function (e) {
						$(this).remove();
						$('body').addClass('modal-open');
					});
					break;
				default :
					showError(result);
			}
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

function verif_input(tableName) {
    var status = true;
    $("#"+tableName+"_form .form-group.mandatory").each(function(index){
		elem = $(':input.form-control:first', this);
        elemType = elem.get(0).tagName;
        switch(elemType){
            case "INPUT" :
            case "TEXTAREA" :
                if(elem.val() == ""){
                    $(this).addClass("has-error in");
                    status = false;
                }else{
                    $(this).removeClass("has-error");
                }
                break;
            case "SELECT" :
                if(elem.val().length > 0){
                    $(this).removeClass("has-error");
                }else{
                    $(this).addClass("has-error in");
                    status = false;
                }
                break;
            case "DIV" :
                checked = elem.children('input:checked').length;
                if(checked == 0){
                    $(this).addClass("has-error in");
                    status = false;
                }else{
                    $(this).removeClass("has-error");
                }
                break;
            }
    });
    if(status){
        //$("#"+tableName+"_modal #alert-empty").addClass('hidden');
		$(".alert.alert-danger").first().addClass('hidden');
    }else{
        //$("#"+tableName+"_modal #alert-empty").fadeIn(20).fadeOut(3000);
		$(".alert.alert-danger").first().fadeIn(20).fadeOut(3000);
    }
    return status;
}

function tabButton(tableName){
	// Seulement si tab
	if($("#"+tableName+"_modal .nav-tabs").length){
		$('#'+tableName+'_modal .nav-tabs li a[data-toggle="tab"]').on('shown.bs.tab', function (e) {
			iddiv = $(this).attr('href');
			construct_input(iddiv+" form");
			if($(e.target).parent().hasClass("other")){
				$("#"+tableName+"_modal .modal-footer .btn-mod").hide();
			}else{
				$("#"+tableName+"_modal .modal-footer .btn-mod").show();
			}
		})
	}
}

// ************ BS Table
// http://bootstrap-table.wenzhixin.net.cn/documentation/
function showTable(tableName,action,js,callback){
	$("#waiting").show();
	$("#navbar-menu").collapse('hide');

	var filters = {};
	switch(action){
		case "fullTable" :
			$('.bootstrap-table .btn-group .show-tick .selectpicker').each(function(){
				name = $(this).attr("id");
				filters[name] = $(this).val();
			});
			break;
		case "resetFilter" : 
			sessionStorage.removeItem('search');
			action = "fullTable";
			filters['value'] = "";
			break;
		case "search" : 
			filters['value'] = $("#"+tableName+"_search").val();
			$("#"+tableName+"_search").val("");
			break;
	}

	callRouter(tableName, -1, action, filters, function(result){
		if(result){
			switch(result.code){
				case 0 : 
					showError(result);
					$("#waiting").hide();
					break;
				case 1 : 
					infoContent.html(result.html);
				    $("#"+tableName+"_table").bootstrapTable({
				    	height : $("#"+tableName+"_table").data("height") == "auto" ? getHeight() : $("#"+tableName+"_table").data("height"),
					    iconsPrefix : 'fal',
						//iconSize : 'lg',
						locale : "fr-FR",
						toolbar : "#table-toolbar",
						buttonsClass : "full-height",
						tableName : tableName,
						undefinedText: '',
						searchTimeOut: 500,
						selection : filters,
						onClickRow: function(row, tr){
							if(js!="none"){
								window[js](tableName,row.id);
							}
						}
					});
					//$(".bootstrap-table .btn-group").removeClass("columns columns-right ");
					$(window).resize(function () {
				        $("#"+tableName+"_table").bootstrapTable('resetView', {
				           height: getHeight()
				        });
				    });
					$("#waiting").hide();
					break;
				default :
					showError(result);
					$("#waiting").hide();
			}
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

window.tableEvents = {
	'click .line-show': function (e, value, row, index) {
		param.idrecord = row.id;
		param.tableName = $(e.target.closest(".bootstrap-table")).data("tableName");
		// find parent modal
		parent = $(e.target.closest(".modal.fade.show"));
		parent.on('hidden.bs.modal', function(){
			showCard(param.tableName,param.idrecord);
		});
		parent.modal("hide");
    },
	'click .line-edit': function (e, value, row, index) {
		param.idrecord = row.id;
		param.tableName = $(e.target.closest(".bootstrap-table")).data("tableName");
		param.idParent = $(e.target.closest(".bootstrap-table")).data("idParent");
		switch(param.tableName){
			case "bil_breakdown" :
			case "sup_order_line" :
			case "quo_line_comment" :
			case "inv_line_ecotax" :
			case "inv_line_previous" :
			case "inv_line_retention" :
			case "sto_stock" : 
			case "veh_insurance" :
			case "pay_payment" :
			case "wor_article" :
			case "inv_tracking" :
			case "quo_line" : param.action = "editLineTable"; break; //quo_sortable.option("disabled", true); break;
			case "inv_line" : param.action = "editLineTable"; break; //inv_sortable.option("disabled", true); break;
			default : param.action = "editCardTable"; break;
		}
		tableAction(param);
    },
    'click .line-delete': function (e, value, row, index) {
    	param.idrecord = row.id;
		param.tableName = $(e.target.closest(".bootstrap-table")).data("tableName");
		iud(param.tableName,param.idrecord,'delete','');
    },
	'click .line-save': function (e, value, row, index) {
    	param.idrecord = row.id;
		param.tableName = $(e.target.closest(".bootstrap-table")).data("tableName");
		param.idParent = $(e.target.closest(".bootstrap-table")).data("idParent");
		param.action = "update";
		tableAction(param);
    },
	'click .line-undo': function (e, value, row, index) {
    	param.idrecord = row.id;
		param.tableName = $(e.target.closest(".bootstrap-table")).data("tableName");
		param.action = "undo";
		tableAction(param);
    },
	'click .line-change': function (e, value, row, index) {
    	param.idrecord = row.id;
		param.tableName = $(e.target.closest(".bootstrap-table")).data("tableName");
		$('#'+param.tableName+'_table tr[data-uniqueid="'+param.idrecord+'"] td:first-child .form-group').toggleClass("d-none");
    },
	'click .line-pdflight': function (e, value, row, index) {
    	param.idrecord = row.id;
		param.tableName = $(e.target.closest(".bootstrap-table")).data("tableName");
		param.action = "pdf";
		param.type = "pdflight";
		cardAction(param.tableName, param.idrecord, param.action, param.type)
    }
}

function tableAction(param,callback){
	var dataSent = "";
	switch(param.action){
		case "saveCardTable" : 
			if(!verif_input(param.tableName)){$("#waiting").hide();return;}
			dataSent = $('#'+param.tableName+'_table #'+param.tableName+'_form').serialize(); break;
		case "update" : 
			dataSent = $('#'+param.tableName+'_table tr[data-uniqueid="'+jq(param.idrecord,'')+'"]').find("select, textarea, input").serialize() + "&idparent="+param.idParent; 
			if(param.tableName == "sup_order_line"){
				dataSent += "&lin_ecotax="+$('#idsuparticle :selected').data("supart_ecotax");
			}
			break;
		case "editLineTable" : 
		case "newLine" :
			if(param.tableName == "quo_line"){
				dataSent = {idclient:$("#idclient").val(),idlibrary:$("#idlibrary").val(),idvat:$("#quo_idvat").val(),idjob:$("#idjob").val()};
				quo_sortable.option("disabled", true);
			}
			if(param.tableName == "quo_line_comment"){
				dataSent = {idclient:$("#idclient").val()};
			}
			if(param.tableName == "inv_line"){
				dataSent = {idclient:$("#idclient").val(),idvat:$("#inv_idvat").val()};
				//inv_sortable.option("disabled", true);
			}
			if(param.tableName == "inv_line_ecotax"){
				dataSent = {idclient:$("#idclient").val()};
			}
			if(param.tableName == "inv_line_previous"){
				dataSent = {idclient:$("#idclient").val(), idsite:$("#idsite").val(), idjob:$("#idjob").val()};
			}
			if(param.tableName == "sup_order_line"){
				dataSent = {idsupplier:$("#idsupplier").val()};
			}
			if(param.tableName == "pay_payment"){
				dataSent = {total:$("#inv_tot_to_pay").val()};
			}
			if(param.tableName == "bil_breakdown"){
				if($("#idsupplier").val() == ""){
					textError = $("#alert-empty").text();
					$("#alert-empty").text("Veuillez d'abord choisir un fournisseur");
					$(".alert.alert-danger").first().fadeIn(20).fadeOut(3000, function(){
						$("#alert-empty").text(textError);
					});
					return;
				}else{
					dataSent = {idsupplier:$("#idsupplier").val()};
				}
			}
			break;
		case "getTotal" :
			dataSent = param.vat;
			break;
		case "updateWithActualIndex" :
			var btn_save = $("#quo_quotation_modal .modal-footer .btn-save");
			btn_save.button('loading');
		case "lineMove" :
		case "updateVAT" :
			dataSent = param.dataSent;
			break;
		case "insertLine" :
			if(param.tableName == "quo_line"){
				dataSent = {idclient:$("#idclient").val(),idlibrary:$("#idlibrary").val(),idquotation:$("#quo_line_table").data("idParent"),way:param.way,oldIndex:param.index};
				quo_sortable.option("disabled", true);
			}
			break;
	}
	callRouter(param.tableName, param.idrecord, param.action, dataSent, function(result){
		if(result){
			switch(result.code){
				case 0 : 
					showError(result);
					break;
				case "newLineInserted" : 
					$('#'+param.tableName+'_table').bootstrapTable('append', JSON.parse(result.html));
					construct_input('#'+param.tableName+'_table');
					switch(param.tableName){
						case "bil_breakdown" : 
							$('#'+param.tableName+'_table tr[data-uniqueid="'+result.idparent+'"] #idsuporder').on('changed.bs.select', function (e, clickedIndex, isSelected, previousValue) {
								var option = $(e.target.selectedOptions);
								$('#'+param.tableName+'_table tr[data-uniqueid="'+result.idparent+'"] #bre_amount').val(option.data('subtext'));
							});
							break;
						case "quo_line" : 
							$('#'+param.tableName+'_table tr[data-uniqueid="'+result.idparent+'"] #idwork').on('changed.bs.select', function (e, clickedIndex, isSelected, previousValue) {
								var option = $(e.target.selectedOptions);
								$('#'+param.tableName+'_table tr[data-uniqueid="'+result.idparent+'"] #lin_unit').val(option.data('wor_unit'));
								$('#'+param.tableName+'_table tr[data-uniqueid="'+result.idparent+'"] #lin_pu_formula').val(option.data('wor_rate'));
								$('#'+param.tableName+'_table tr[data-uniqueid="'+result.idparent+'"] #lin_qty_formula').focus().select();
							});
							break;
						case "sup_order_line" : 
							$('#'+param.tableName+'_table tr[data-uniqueid="'+result.idparent+'"] #idsuparticle').on('changed.bs.select', function (e, clickedIndex, isSelected, previousValue) {
								var option = $(e.target.selectedOptions);
								$('#'+param.tableName+'_table tr[data-uniqueid="'+result.idparent+'"] #lin_QdM').val(option.data('supart_unit_qty'));
								$('#'+param.tableName+'_table tr[data-uniqueid="'+result.idparent+'"] #lin_UdM').val(option.data('art_unit'));
								$('#'+param.tableName+'_table tr[data-uniqueid="'+result.idparent+'"] #lin_UdC').val(option.data('supart_unit'));
								$('#'+param.tableName+'_table tr[data-uniqueid="'+result.idparent+'"] #lin_pu_UdM').val(option.data('supart_price'));
								$('#'+param.tableName+'_table tr[data-uniqueid="'+result.idparent+'"] #lin_QdC').focus().select();
							});
							break;
						case "inv_line_previous" : 
							$('#'+param.tableName+'_table tr[data-uniqueid="'+result.idparent+'"] #idprevinvoice').on('changed.bs.select', function (e, clickedIndex, isSelected, previousValue) {
								var option = $(e.target.selectedOptions);
								$('#'+param.tableName+'_table tr[data-uniqueid="'+result.idparent+'"] #lin_total').val(option.data('inv_tot_situation'));
								$('#'+param.tableName+'_table tr[data-uniqueid="'+result.idparent+'"] #lin_description').val(option.data('subtext'));
							});
							break;
						case "inv_line_retention" : 
							$('#'+param.tableName+'_table tr[data-uniqueid="'+result.idparent+'"] #ret_percent').on('change', function(){
								var tot = $("#inv_tot_articles").val() * $(this).val() / 100;
								$('#'+param.tableName+'_table tr[data-uniqueid="'+result.idparent+'"] #ret_total').val(tot.toFixed(2));
							});
							break;
					}
					break;
				case "newCardCreated" : 
					if($('#'+param.tableName+'_table .no-records-found').length){
						$('#'+param.tableName+'_table .no-records-found').replaceWith(result.html);
					}else{
						$('#'+param.tableName+'_table > tbody').prepend(result.html);
					}
					construct_input('#'+param.tableName+'_form');
					$('#'+param.tableName+'_table .tr-detail .td-collapse').slideDown(700);
					break;
				case "newCardCanceled" :
					$('#'+param.tableName+'_table .tr-detail .td-collapse').slideUp(700,function(){
						$('#'+param.tableName+'_table .tr-detail').remove();
					});
					break;
				case "cardEdited" :
					if(result.idparent != 0){
						$('#'+param.tableName+'_table').bootstrapTable('prepend', {id:result.idparent});
						param.idrecord = result.idparent;
					}
					$('#'+param.tableName+'_table tr[data-uniqueid="'+jq(param.idrecord,'')+'"]').html(result.html);
					construct_input('#'+param.tableName+'_form');
					$('#'+param.tableName+'_table .tr-detail .td-collapse').slideDown(700);
					break;
				case "cardUpdated" : // or canceled
					$('#'+param.tableName+'_table .tr-detail .td-collapse').slideUp(700,function(){
						$('#'+param.tableName+'_table').bootstrapTable('updateRow', {
			                //id: idrecord,
							index: $('#'+param.tableName+'_table tr[data-uniqueid="'+param.idrecord+'"]').data("index"),
			                row: JSON.parse(result.html),
			            });
					});
					break;
				case "cardInserted" :
					$('#'+param.tableName+'_table .tr-detail .td-collapse').slideUp(700,function(){
						$('#'+param.tableName+'_table').bootstrapTable('prepend', JSON.parse(result.html));
					});
					break;
				case "lineEdited" :
					$('#'+param.tableName+'_table').bootstrapTable('updateByUniqueId', {
		                id: param.idrecord,
		                row: JSON.parse(result.html),
						replace: false
		            });
					construct_input('#'+param.tableName+'_table');
					switch(param.tableName){
						case "bil_breakdown" : 
							$('#'+param.tableName+'_table tr[data-uniqueid="'+param.idrecord+'"] #idsuporder').on('changed.bs.select', function (e, clickedIndex, isSelected, previousValue) {
								var option = $(e.target.selectedOptions);
								$('#'+param.tableName+'_table tr[data-uniqueid="'+param.idrecord+'"] #bre_amount').val(option.data('subtext'));
							});
							break;
						case "quo_line" : 
							$('#'+param.tableName+'_table tr[data-uniqueid="'+param.idrecord+'"] #idwork').on('changed.bs.select', function (e, clickedIndex, isSelected, previousValue) {
								var option = $(e.target.selectedOptions);
								$('#'+param.tableName+'_table tr[data-uniqueid="'+param.idrecord+'"] #lin_unit').val(option.data('wor_unit'));
								$('#'+param.tableName+'_table tr[data-uniqueid="'+param.idrecord+'"] #lin_pu_formula').val(option.data('wor_rate'));
								$('#'+param.tableName+'_table tr[data-uniqueid="'+param.idrecord+'"] #lin_qty_formula').focus().select();
							});
							break;
						case "sup_order_line" : 
							$('#'+param.tableName+'_table tr[data-uniqueid="'+param.idrecord+'"] #idsuparticle').on('changed.bs.select', function (e, clickedIndex, isSelected, previousValue) {
								var option = $(e.target.selectedOptions);
								$('#'+param.tableName+'_table tr[data-uniqueid="'+param.idrecord+'"] #lin_unit').val(option.data('supart_unit'));
								$('#'+param.tableName+'_table tr[data-uniqueid="'+param.idrecord+'"] #lin_pu').val(option.data('supart_price'));
								$('#'+param.tableName+'_table tr[data-uniqueid="'+param.idrecord+'"] #lin_quantity').focus().select();
							});
							break;
						case "inv_line_previous" : 
							$('#'+param.tableName+'_table tr[data-uniqueid="'+param.idrecord+'"] #idprevinvoice').on('changed.bs.select', function (e, clickedIndex, isSelected, previousValue) {
								var option = $(e.target.selectedOptions);
								$('#'+param.tableName+'_table tr[data-uniqueid="'+param.idrecord+'"] #lin_total').val(option.data('inv_tot_situation'));
								$('#'+param.tableName+'_table tr[data-uniqueid="'+param.idrecord+'"] #lin_description').val(option.data('subtext'));
							});
							break;
						case "inv_line_retention" : 
							$('#'+param.tableName+'_table tr[data-uniqueid="'+param.idrecord+'"] #ret_percent').on('change', function(){
								var tot = $("#inv_tot_ttc").val() * $(this).val() / 100;
								$('#'+param.tableName+'_table tr[data-uniqueid="'+param.idrecord+'"] #ret_total').val(tot.toFixed(2));
							});
							break;
					}
					break;
				case "lineUpdated" :
					if(result.html.length > 0){
						$('#'+param.tableName+'_table').bootstrapTable('updateByUniqueId', {
			                id: param.idrecord,
			                row: JSON.parse(result.html),
							replace: false
			            });
					}
					switch(param.tableName){
						case "bil_breakdown" :
							$(".bre_tot input").val(calculSum("#bil_breakdown_table tr td.bre_amount"));
							break;
						case "quo_line" :
							// update sub-total
							updateSubTotal("#quo_line_table");
							// update total
							$("#quo_amount").val(result.ht);
							$("#quo_tot_amount").val(result.ttc);
							$("#quo_tot_step").val(result.step);
							for (let key in result.vat) {
								value = result.vat[key];
								$("#"+key).val(value);
							}
							quo_sortable.option("disabled", false);
							break;
						case "inv_line" : 
							// update sub-total
							updateSubTotal("#inv_line_table");
							// update totals
							table_action({tablename:'inv_invoice', action:'inv_getTotals'});
							break;
						case "inv_line_ecotax" :
						case "inv_line_previous" :
						case "inv_line_retention" :
							// update totals
							table_action({tablename:'inv_invoice', action:'inv_getTotals'});
							break;
						case "sup_order_line" : 
							$("#ord_tva_percent").trigger("focusout");
							break;
					}
					
					break;
				case "lineInserted" : 
					$('#'+param.tableName+'_table').bootstrapTable('insertRow', {
		                index: param.index,
		                row: JSON.parse(result.html)
		            })
					construct_input('#'+param.tableName+'_table');
					switch(param.tableName){
						case "quo_line" : 
							$('#'+param.tableName+'_table tr[data-uniqueid="'+result.idparent+'"] #idwork').on('changed.bs.select', function (e, clickedIndex, isSelected, previousValue) {
								var option = $(e.target.selectedOptions);
								$('#'+param.tableName+'_table tr[data-uniqueid="'+result.idparent+'"] #lin_unit').val(option.data('wor_unit'));
								$('#'+param.tableName+'_table tr[data-uniqueid="'+result.idparent+'"] #lin_pu_formula').val(option.data('wor_rate'));
								$('#'+param.tableName+'_table tr[data-uniqueid="'+result.idparent+'"] #lin_qty_formula').focus().select();
							});
							break;
					}
					break;
				case "setTotal" :
					$(".tot-amount").val(result.amount);
					$(".tot-ecotax").val(result.ecotax);
					$(".tot-ht").val(result.ht);
					$(".tot-ttc").val(result.ttc);
					$(".tot-step").val(result.step);
					break;
				case "lineMoved" : 
					switch(param.tableName){
						case "quo_line" :
							// update sub-total
							updateSubTotal("#quo_line_table");
							break;
					}
					break;
				case "quoActualIndexUpdated" :
					btn_save.text('').append("<i class='fal fa-thumbs-up'></i> "+result.info);
					setTimeout(function(){
						$('#quo_quotation_modal').modal('hide');
						showCard('quo_quotation',param.idrecord);
					}, 500);
					break;
				default :
					showError(result);
			}
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

// *********** BS Card
function showCard(tableName,idrecord,callback){
	$("#waiting").show();
	var search = $("#table-result .search-input").val()??"";
	if(search.length > 0){
		sessionStorage.setItem('search', search);
	}else{
		sessionStorage.removeItem('search');
	}
	callRouter(tableName, idrecord, "showCard", "", function(result){
		if(result){
			switch(result.code){
				case 0 : 
					showError(result);
					break;
				case 1 : 
					$('body').prepend(result.html);
					$("#"+tableName+"_modal").modal({backdrop:'static',keyboard:false, show:true});
					construct_input("#"+tableName+"_form");
					$("#"+tableName+"_modal").on('shown.bs.modal', function (e) {
						// main_gen
						if (typeof window[tableName+"_ext_showCard"] == 'function'){
							window[tableName+"_ext_showCard"](idrecord);
						}
						$("#"+tableName+"_modal .form-control").first().focus();
						tabButton(tableName);
					});
					$("#"+tableName+"_modal").on('hidden.bs.modal', function (e) {
						$(this).remove();
					});
					break;
				default :
					showError(result);
			}
		}
		$("#waiting").hide();
		if ($.isFunction(callback)){
		    callback.call();
		}else{
			if(callback){
				window[callback]();
			}
		}
	});
}

function showTabCard(tableName,idrecord,idtab){
	showCard(tableName,idrecord,function(){
		$(idtab+" a").trigger("click");
	})
}

function cardAction(tableName, idrecord, action, dataSent){
	var btn = $("#btn_"+dataSent);
	btn.button('loading');
	switch (action){
		case "pdf" :
			$.ajax({
				url: "libraries/lib_empty_pdf.php",
				type: "GET",
				data : "",
				success: function() {
					window.open("controllers/c_router.php?table="+tableName+"&idrecord="+idrecord+"&action="+action+"&dataSent="+dataSent, '_blank');
					btn.button('reset');
				}
			});
			break;
		case "mail" :
			var data = {table:tableName,idrecord:idrecord,action:action,dataSent:dataSent};
			$.ajax({
				url: "controllers/c_router.php",
				type: "POST",
				data : data,
				success: function(result) {
					$("#"+tableName+"_modal").modal("hide");
					$("#popup-mail").remove();
					$('body').append('<div id="popup-mail" class="modal fade" style="top:25px; z-index:1060"></div>');
					open_popup("popup_show_mail.php", jQuery.param(data)+"&mail="+encodeURI(result.info)+"&file="+result.html+"&subject="+result.subject, "popup-mail");
					setTimeout(function(){btn.button('reset');}, 1000);
				},
				error: function(result, status, error) {
					showError(result);
				}
			});
			break;
		case "sendMail" :
				dataSent = $("#popup-mail :input").serialize();
				$.ajax({
					url: "controllers/c_router.php",
					type:"POST",
					data : {table:tableName,idrecord:idrecord,action:action,dataSent:dataSent},
					success: function (result, status){
						$("#btn_send_mail").text('').append("<i class='fal fa-thumbs-up'></i> "+result.info);
						setTimeout(function(){$("#popup-mail").modal("hide");}, 800);
					},
					error: function(result, status, error) {
						showError(result);
					}
				});
			break;
		case "duplicate" :
		case "createFrom" :
		case "createFullFrom" :
		case "createNext" :
		case "createPercentFrom" : dataSent={idsituation:$("#quo_inv_situation").val()};
				$.ajax({
					url: "controllers/c_router.php",
					type:"POST",
					data : {table:tableName,idrecord:idrecord,action:action,dataSent:dataSent},
					success: function (result, status){
						btn.text('').append("<i class='fal fa-thumbs-up'></i> "+result.info);
						setTimeout(function(){
							modalRemove("#"+tableName+"_modal");
							if(dataSent == "inv_invoice_full"){dataSent = "inv_invoice";}
							if(dataSent == "inv_invoice_percent"){dataSent = "inv_invoice";}
							if(action == "createPercentFrom"){dataSent = "inv_invoice";}
							showCard(dataSent,result.idparent);
						}, 800);
					},
					error: function(result, status, error) {
						showError(result);
					}
				});
			break;
		case "updateVariables" :
				$.ajax({
					url: "controllers/c_router.php",
					type:"POST",
					data : {table:tableName,idrecord:idrecord,action:action,dataSent:$("#quo_variables").serialize()},
					success: function (result, status){
						btn.text('').append("<i class='far fa-thumbs-up'></i>");
						// update lines
						$("#quo_line_table").bootstrapTable('destroy').replaceWith(result.html);
						$("#quo_line_table").bootstrapTable({
					    	height : $(this).data("height") == "auto" ? $("body").height()-300 : $(this).data("height"),
							locale : "fr-FR",
							showExport: false,
							showColumns: false,
							undefinedText: '',
						});
						
						// update subtotal
						updateSubTotal("#quo_line_table");
						// update total
						$("#quo_amount").val(result.ht);
						$("#quo_tot_amount").val(result.ttc);
						$("#quo_tot_step").val(result.step);
						for (let key in result.vat) {
							value = result.vat[key];
							$("#"+key).val(value);
						}
							
						setTimeout(function(){
							btn.button('reset');
						}, 800);
					},
					error: function(result, status, error) {
						showError(result);
					}
				});
			break;
	}
}

// ************ BS List-group + Sortable
// https://github.com/SortableJS/Sortable
function showListgroup(tableName){
	new Sortable(eval(tableName+"_list"), {
		animation: 150,
		handle: '.handle',
		ghostClass: 'list-group-item-info',
		onChoose: function (evt) {
			$("#"+tableName+"_list .collapse.show").collapse("hide");
			$("#"+tableName+"_list .list-add-line").addClass("disabled");
		},
		onEnd: function (evt) {
			var elem = evt.item;  // dragged HTMLElement
			dataSent = {idparent:$(elem).parent().data("idparent"), oldIndex:evt.oldIndex, newIndex:evt.newIndex};
			listgroupAction(tableName,$(elem).data("id"), dataSent, "lineMove");
			$("#"+tableName+"_list .list-add-line").removeClass("disabled");
		},
	});
	$("#"+tableName+"_list").on("click", "a.list-group-item-action", function(){
		var idrecord = $(this).data("id");
		if($(jq(idrecord,"#")).length){
			$(jq(idrecord,"#")).collapse("hide", function(){
				$(this).remove();
			})
		}else{
			listgroupAction(tableName,idrecord,"","editCardLine");
		}
	})
}

function listgroupAction(tableName,idrecord,dataSent,action,callback){
	switch(action){
		case "saveCardLine" : 
			if(!verif_input(tableName)){
				return;
			}else{
				dataSent = $('#'+tableName+'_list #'+tableName+'_form').serialize(); 
			}
			break;
		case "deleteCardLine" : action = "delete"; break;
	}
	$("#"+tableName+"_list .collapse.show").collapse("hide");
	callRouter(tableName, idrecord, action, dataSent, function(result){
		if(result){
			switch(result.code){
				case 0 : 
					showError(result);
					break;
				case "newLineCreated" :
					$("#"+tableName+"_list .list-add-line").before(result.html);
					construct_input('#'+tableName+'_form');
					$(jq(result.idchild,"#")).on('shown.bs.collapse', function(){
						scrollTo("#job_job_modal div.modal-body", $("#"+tableName+"_list a[data-id='"+result.idchild+"']"));
				    });
					$(jq(result.idchild,"#")).on('hidden.bs.collapse', function(){
				        $(this).remove();
				    });
					$(jq(result.idchild,"#")).collapse("show");
					break;
				case "lineCreated" :
					$("#"+tableName+"_list a[data-id='"+idrecord+"']").replaceWith(result.html);
					$(jq(idrecord,"#")).collapse("hide");
					break;
				case "lineEdited" :
					$("#"+tableName+"_list a[data-id='"+idrecord+"']").after(result.html);
					construct_input('#'+tableName+'_form');
					$(jq(idrecord,"#")).collapse("show");
					$(jq(idrecord,"#")).on('shown.bs.collapse', function(){
						scrollTo("#job_job_modal div.modal-body", $("#"+tableName+"_list a[data-id='"+idrecord+"']"));
				    });
					$(jq(idrecord,"#")).on('hidden.bs.collapse', function(){
				        $(this).remove();
				    });
					// main_gen
					if (typeof window[tableName+"_ext_lineEdited"] == 'function'){
						window[tableName+"_ext_lineEdited"](idrecord);
					}
					break;
				case "lineUpdated" :
					$("#"+tableName+"_list a[data-id='"+idrecord+"'] div.row").html(result.html);
					$(jq(idrecord,"#")).collapse("hide");
					break;
				case "lineMoved" : 
					break;
				case "deleted" :
					$("#"+tableName+"_list a[data-id='"+idrecord+"']").hide(500, function(){
						$(this).remove();
					})
					break;
				default :
					showError(result);
			}
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

// *********** IUD
function iud(tableName,idrecord,action,js,callback){
	$("#waiting").show();
	var formId = "."+tableName+"_form";
	var modalId = "#"+tableName+"_modal";
	var btn_save = $("#"+tableName+"_modal .modal-footer .btn-save");
	
	// Check input mandatory
	if(action != "delete"){if(!verif_input(tableName)){$("#waiting").hide();return;}}
	
	btn_save.button('loading');
	var dataSent = $(formId).serialize();
	
	// Other action if needed
	switch(tableName){
		case "sup_receipt" :
			data = {};
			$("#sup_order_line_table tbody tr").each(function(){
				idline = $(this).data("uniqueid");
				valline = $(this).find("input").val()||0;
				data[idline] = valline;
			})
			dataSent += "&lines="+encodeURIComponent(JSON.stringify(data));
			break;
		case "quo_quotation" :
		case "sup_order" :
			dataSent = $(formId).serialize()+"&"+$(".modal-footer input").serialize();
			break;
		case "quo_line" :
		case "inv_line" :
		case "inv_line_ecotax" :
			dataSent = {"idparent":$("#"+tableName+"_table").data("idParent")};
			break;
		case "sit_site" :
			dataSent += "&city="+$("#sit_idcity option:selected").text();
			break;
	}
	
	callRouter(tableName, idrecord, action, dataSent, function(result){
		if(result){
			var line = $('#'+tableName+'_table tr[data-uniqueid="'+idrecord+'"]');
			switch(result.code){
				case 0 : 
					showError(result);
					break;
				case "inserted" :
					if(js!="none"){
						if($("#"+tableName+"_table").length){
							showTable(tableName,"fullTable",js);
						}
					}
					break;
				case "insertedFrom" :
					var $modalChild = $("#"+tableName+"_modal");
					var modalParent = $modalChild.data("parentModal");
					var fieldName = $modalChild.data("fieldName");
					var idparent = idrecord;
					var value = result.idparent;
					
					switch(modalParent){
						case "sup_order_modal" : 
							if(tableName != "sup_supplier"){
								var text = $("#sup_article_form #supart_ref").val()+" "+$("#sup_article_form #supart_description").val();
								var UdM = $("#sup_article_form #art_unit").val();
								var UdC = $("#sup_article_form #supart_unit").val();
								var QdM = $("#sup_article_form #supart_unit_qty").val();
								var rate = $("#sup_article_form #supart_price").val();
								var eco = $("#sup_article_form #supart_ecotax").val();
								
								$("#"+modalParent+" #"+idparent).append("<option value='"+value+"'>"+text+"</option>").selectpicker('refresh').selectpicker('val',value);
								
								$("#sup_order_modal #lin_QdM").val(QdM);
								$("#sup_order_modal #lin_UdM").val(UdM);
								$("#sup_order_modal #lin_UdC").val(UdC);
								$("#sup_order_modal #lin_pu_UdM").val(rate);
							}else{
								var text = $("#"+tableName+"_form #"+fieldName).val();
								$("#"+modalParent+" #"+idparent).append("<option value='"+value+"'>"+text+"</option>").selectpicker('refresh').selectpicker('val',value);
							}
							break;
						default :
							var text = $("#"+tableName+"_form #"+fieldName).val();
							$("#"+modalParent+" #"+idparent).append("<option value='"+value+"'>"+text+"</option>").selectpicker('refresh').selectpicker('val',value);
					}
					
					$modalChild.modal("hide");
					break;
				case "updated" : 
					if(line.length){
						$('#'+tableName+'_table').bootstrapTable('updateByUniqueId', {
			                id: idrecord,
			                row: JSON.parse(result.html),
							//replace: "true"
			            });
					}else{
						$('#'+tableName+'_table').bootstrapTable('prepend', JSON.parse(result.html));
						$("#table-result .count-numbers").text(parseInt($("#table-result .count-numbers").text())+1);
						$('#'+tableName+'_table').bootstrapTable('scrollTo', 'top')
					}
					// update td class
					if(typeof(result.tdClass) !== "undefined"){
						$('#'+tableName+'_table tr[data-uniqueid="'+idrecord+'"] td:first').attr('class', function(i, c){
						    if(c.indexOf("td-") == 0){
								$(this).removeClass(c).addClass(result.tdClass);
							}else{
								$(this).addClass(result.tdClass);
							}
						});
					}
					break;
				case "updated_notable" :
					switch(tableName){
						case "pla_planning" :
							table_action({tablename:'pla_planning', dataSent:'now', action:'pla_changeWeek'});
							break;
					}
					break;
				case "deleted" :
					line.fadeOut(800,function(){
						$(this).remove();
						$('#'+tableName+'_table').bootstrapTable('removeByUniqueId', idrecord);
						switch(tableName){
							case "bil_breakdown" :
								$(".bre_tot input").val(calculSum("#bil_breakdown_table tr td.bre_amount"));
								break;
							case "quo_line" : 
								// update sub-total
								updateSubTotal("#quo_line_table");
								// update total
								$("#quo_amount").val(result.ht);
								$("#quo_tot_amount").val(result.ttc);
								$("#quo_tot_step").val(result.step);
								for (let key in result.vat) {
									value = result.vat[key];
									$("#"+key).val(value);
								}
								break;
							case "sup_order_line" : 
								$("#ord_tva_percent").trigger("focusout");
								break;
							case "inv_line" : 
								// update sub-total
								updateSubTotal("#inv_line_table");
								// update totals
								table_action({tablename:'inv_invoice', action:'inv_getTotals'});
								break;
							case "inv_line_ecotax" :
							case "inv_line_previous" :
							case "inv_line_retention" :
								table_action({tablename:'inv_invoice', action:'inv_getTotals'});
								break;
						}
					})
					$("#table-result .count-numbers").text(parseInt($("#table-result .count-numbers").text())-1);
					break;
				default :
					showError(result);
			}
		}
		if(result.status && result.code !== "error"){
			btn_save.text('').append("<i class='fal fa-thumbs-up'></i> "+result.info);
			if(js!="none"){
				setTimeout(
					function(){
						$(modalId)
						.on('hidden.bs.modal', function(){
							// other action if needed	
						})
						//.modal("hide");
						btn_save.button('reset')
					}, 1000);
			}else{
				btn_save.button('reset');
			}
		}else{
			btn_save.button('reset');
		}
		$("#waiting").hide();
		if ($.isFunction(callback)){
		    callback.call();
		}else{
			if(callback){
				window[callback]();
			}
		}
	});
}