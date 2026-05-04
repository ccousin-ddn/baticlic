<?php
	require_once (dirname(__FILE__)."/libraries/lib_include.php");
	if(isset($_SESSION["iduser"])){
	    echo '
			<div class="modal-dialog no-scroll" role="document">
				<div class="modal-content">
					<div class="modal-header">
						<h4 class="modal-title" id="modaltitle"><i class="fal fa-random mr-2"></i>'.gettext("Changer mon mot de passe").'</h4>
						<button class="close" aria-hidden="true" data-dismiss="modal" type="button" aria-label="Close">
							<i class="fas fa-times-circle"></i>
						</button>
					</div>
					<div class="modal-body">
						<form class="" id="usr_user_form">
							<div class="form-normal">
								<div class="input-group my-3">
									<div class="input-group-prepend"><div class="input-group-text"><i class="fal fa-lock-alt"></i></div></div>
									<input onkeyup="userCheckEnter(5,event)" id="usr_password_old" type="password" class="form-control" name="usr_password_old" placeholder="'.gettext("Ancien mot de passe").'">
								</div>

								<div class="input-group mb-1">
									<div class="input-group-prepend"><div class="input-group-text"><i class="fal fa-key"></i></div></div>
									<input onkeyup="userCheckEnter(5,event)" id="usr_password_new" type="password" class="form-control" name="usr_password_new" placeholder="'.gettext("Nouveau mot de passe (min 6 car, min 1 chiffre)").'">
								</div>
								
								<div class="input-group mb-3">
									<div class="input-group-prepend"><div class="input-group-text"><i class="fal fa-key"></i></div></div>
									<input onkeyup="userCheckEnter(5,event)" id="usr_password_new_confirm" type="password" class="form-control" name="usr_password_new_confirm" placeholder="'.gettext("Confirmer le nouveau mot de passe").'">
								</div>
							</div>
						</form>
						<div id="result_password" class="alert alert-danger collapse mt-2" role="alert">
							<span id="mess1">'.gettext("Le mot de passe doit contenir au moins 6 caractères").'</span>
							<span id="mess2">'.gettext("Le mot de passe doit contenir au moins 1 chiffre").'</span>
							<span id="mess3">'.gettext("Le mot de passe ne correspond pas à la confirmation").'</span>
							<span id="mess4">'.gettext("Le mot de passe doit contenir au moins 1 lettre").'</span>
						</div>

						<button class="btn btn-outline-client float-right mt-3" type="button" onclick="userPassword(\'saveNewPassword\',event)" autocomplete="off">
							<i class="fal fa-check"></i>
							'.gettext("Enregistrer").'
						</button>
					</div>
				</div>
			</div>
	    ';
	}