<?php
//dump($_COOKIE, LOCATION);
	if(isset($_COOKIE['login'])){
		$login = $_COOKIE['login'];
		$class = "has-success";
	}else{
		$login = "";
		$class = "";
	}
	switch(LOCATION){
		case 'mobile' :
			$wor_worker = new wor_worker();
			$wor_worker->c_selectLogin();
			$class_workers = "";
			$class_user = "d-none";	
			break;
		case 'office' :
			$wor_worker = new wor_worker();
			$wor_worker->c_selectLogin();
			if(empty($login)){
				$class_workers = "";
				$class_user = "d-none";	
			}else{
				$class_workers = "d-none";
				$class_user = "absolute-center";
			}
			break;
		case 'out' :
			$class_workers = "d-none";
			$class_user = "absolute-center";
			break;
	}

?>
<style type="text/css">
.bg-login {
	background-image: url("images/bg0.jpg");
	min-height: calc(100vh - var(--nav_height));
	background-attachment: fixed;
	background-position: center;
	background-repeat: no-repeat;
	background-size: cover;
}
#loginbox {
	min-height: calc(100% - 260px);
	display: grid;
	align-content: start;
	grid-gap: 2rem 1.5rem;
	grid-template-columns: repeat(auto-fill,minmax(11rem,1fr));
	padding: 2rem 1rem 2rem 1rem;
}
#loginbox .box {min-height:200px; width:100%; font-weight:bold; cursor:pointer;background-color: var(--col_xxxlight);}
#loginbox .box:hover {background-color:var(--color1)!important;}
#loginbox .box img {height:100%; object-fit: cover;padding: .8rem;border: 0;width:100%;}
#loginbox .box:hover img {opacity:.8;}
.alert-danger {font-size:1rem;}

#modal_password .modal-dialog-centered { margin: 1.75rem auto; }

#wor_picture {height:12rem; object-fit: cover;border: 0;}
</style>

<div id="full-content" class="bg-login">

	<div id="login-workers" class="<?php echo $class_workers?>">
		<div id="loginbox">
			<?php echo $wor_worker->json['html']??""?>
		</div><!-- /.loginbox -->
		
		<div class="modal" id="modal_password" tabindex="-1" role="dialog" aria-hidden="true">
			<div class="modal-dialog modal-dialog-centered" role="document" style="min-width: 350px; max-width: max-content;">
				<div class="modal-content bg_light">
					<div class="modal-header detail">
						<span class="font-weight-bold" id="wor_name"></span>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<span aria-hidden="true">&times;</span>
						</button>
					</div>
					<div class="modal-body">
						<form autocomplete="on">
							<input type="hidden" name="idworker" id="idworker" value="">
							<div id="div_picture" class="d-flex flex-row justify-content-between align-items-start mb-3">
								<img id="wor_picture" src="">
								<button type="button" class="btn btn-secondary d-none d-md-block" onclick="webcam('on')"><i class="fal fa-camera-alt fa-2x"></i></button>
							</div>
							
							<div id="div_embed" class="d-none flex-row justify-content-between align-items-start mb-3">
								<video id="webcam" autoplay playsinline height="240" width="320"></video>
								<button id="btn_take_picure" type="button" class="btn btn-success d-none ml-3"><i class="fal fa-camera fa-2x"></i></button>
								<canvas id="canvas" class="d-none"></canvas>
							</div>

						   	<div class="form-group">
								<label for="usr_login">Login</label>
								<input type="text" readonly class="form-control form-control-lg usr-login" id="usr_login" name="usr_login" value="">
							</div>
							<div class="form-group">
								<label for="usr_password">Mot de passe</label>
								<input type="password" class="form-control form-control-lg usr-password" id="usr_password" name="usr_password" placeholder="<?php echo gettext("Mot de passe")?>" autofocus>
							</div>
							<hr>
							<button id="btn-login"  type="button" onclick="userLogin(event)" class="btn btn-save btn-block btn-lg"><?php echo gettext("Connexion")?></button>
							<div id='result_login' class="alert alert-danger collapse mt-2" role="alert"></div>
						</form>
					</div>
				</div>
			</div>
		</div>
	</div>

	<div id ="login_user" class="<?php echo $class_user?>" style="min-width: 350px;">
		<div class="card shadow bg_light">
			<div class="card-body p-4">
				<form autocomplete="on">
					<div class="form-group">
						<div class="input-group <?php echo $class?>">
							<div class="input-group-prepend">
								<div class="input-group-text" onclick="$('#login_user #usr_login').val('laurent.anezo')"><i class="fal fa-user"></i></div>
							</div>
							<input id="usr_login" type="text" class="form-control usr-login" name="usr_login" value="<?php echo $login ?>" placeholder="<?php echo gettext("Login ou email")?>">
						</div>
					</div>
					<div class="form-group">
						<div class="input-group">
							<div class="input-group-prepend">
								<div class="input-group-text"><i class="fal fa-lock"></i></div>
							</div>
							<input id="usr_password" type="password" class="form-control usr-password" name="usr_password" placeholder="<?php echo gettext("Mot de passe")?>" autofocus>
						</div>
					</div>
					
					<div class="custom-control custom-checkbox mb-3">
						<input type="checkbox" class="custom-control-input" id="rememberMe" name="rememberMe" value="1" checked>
						<label class="custom-control-label" for="rememberMe"><?php echo gettext("Se souvenir de moi")?></label>
					</div>
					<button id="btn-login"  type="button" onclick="userLogin(event)" class="btn btn-outline-client-light btn-block"><?php echo gettext("Connexion")?></button>
					<div id='result_login' class="alert alert-danger collapse mt-2 p-1" role="alert"></div>
				</form>
			</div>
		</div>
	</div>

</div>
<script>
	document.addEventListener("DOMContentLoaded", function(event) {
		$("#modal_password").on('shown.bs.modal', function (e){
			var div = $(e.relatedTarget);
			$("#idworker").val(div.data('idworker'));
			$("#usr_login").val(div.data('wor_login'));
			$("#wor_name").text(div.data('wor_name'));
			$("#wor_picture").attr("src",div.attr('data-wor_picture'));
			$("#result_login").removeClass('show');
			$("#usr_password").val("").focus();
		})
		$(".usr-login").on("keyup",function(){
			userCheckEnter(1,event);
		})
		$(".usr-password").on("keyup",function(){
			userCheckEnter(1,event);
		})
		$("#usr_email").on("keyup",function(){
			userCheckEnter(2,event);
		})
	});
	function toggleLogin(){
		//console.log($("#login_user").length);
		//setCookie('login', '', '-1');
		$("#login-workers").toggleClass("d-none");
		$("#login_user").toggleClass("d-none absolute-center");
		/*
		if($("#login_user").hasClass("d-none")){
			setCookie("location", "office");
		}else{
			setCookie("location", "admin");
		}
		*/
	}
	function webcam(action){
		switch(action){
			case "on" : 
				$("#div_picture, #div_embed").toggleClass("d-flex d-none");
				const webcamElement = document.getElementById('webcam');
				const canvasElement = document.getElementById('canvas');
				const webcam = new Webcam(webcamElement, 'enviroment', canvasElement);
				webcam.start()
				    .then(result =>{
						$("#btn_take_picure").removeClass("d-none").one("click", function(){
							var picture = webcam.snap();
							webcam.stop();
							$("#canvas").removeClass("d-none");
							$("#webcam").addClass("d-none");
							$("#btn_take_picure").addClass("d-none");
							table_action({tablename:"wor_worker", idrecord:$("#idworker").val(), dataSent:{wor_picture:canvas.toDataURL('image/jpeg')}, action:"savePicture"});
						});
				    })
				    .catch(err => {
				        console.log(err);
				});
				break;
			case "off" :
				$("#div_picture, #div_embed").toggleClass("d-flex d-none");
				$("#canvas").addClass("d-none");
				$("#webcam").removeClass("d-none");
				$("#btn_take_picure").removeClass("d-none");
				break;
		}
	}
</script>