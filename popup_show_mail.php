<?php
	include_once "libraries/lib_include.php";
	if(isset($_SESSION["iduser"])){
		$pdf = $_POST['file'];
		
		$table = $_POST['table'];
		$mailto = $_POST['mail'];
		$title = $_POST['subject']??substr($_POST['file'],0,-4);
		
		// *** config ***
		$cfg_config = new cfg_config();
		$config = $cfg_config->c_getInfos();
		
		$message = $config['cfg_template_'.$_POST['table']];
		// Signature
		$message .= '&#13;&#13;'.COMPANY_NAME.' - '.$config["cfg_contact_".substr($_POST['table'],4,3)];

		//print_r($_POST['info']);
	    echo '
			<div class="modal-dialog modal-mail modal-lg" role="document">
				<div class="modal-content">
					<div class="modal-header">
						<h4 class="modal-title" id="modaltitle">
							<i class="fal fa-envelope"></i> 
							'.gettext("Nouveau mail").'
						</h4>
						<button class="close" aria-hidden="true" data-dismiss="modal" type="button" aria-label="Close">
							<i class="fal fa-times-circle"></i>
						</button>
					</div>
					<div class="modal-body bg-light table-card">
						<form id="usr_user_form">
							<div class="form-group row form-normal mb-2">
								<label for="from" class="col-sm-2 col-form-label">'.gettext("De :").'</label>
								<div class="col-sm-10">
									<input type="text" class="form-control" id="from" name="from" value="'.$config['cfg_mail'].'">
								</div>
							</div>
							<div class="form-group row form-normal mb-2">
								<label for="to" class="col-sm-2 col-form-label">'.gettext("A :").'</label>
								<div class="col-sm-10">
									<input type="text" class="form-control" id="to" name="to" value="'.$mailto.'">
								</div>
							</div>
							<div class="form-group row form-normal mb-2">
								<label for="bcc" class="col-sm-2 col-form-label">'.gettext("Bcc :").'</label>
								<div class="col-sm-10">
									<input type="text" class="form-control" id="bcc" name="bcc" value="'.$config['cfg_cci'].'">
								</div>
							</div>
							<div class="form-group row form-normal mb-2">
								<label for="subject" class="col-sm-2 col-form-label">'.gettext("Objet :").'</label>
								<div class="col-sm-10">
									<input type="text" class="form-control" id="subject" name="subject" value="Les peintures du nord - '.$title.'">
								</div>
							</div>
							<div class="form-group row form-normal mb-2">
								<label for="message" class="col-sm-2 col-form-label">'.gettext("Message :").'</label>
								<div class="col-sm-10">
									<textarea class="form-control" id="message" name="message" rows="8">'.$message.'</textarea>
								</div>
							</div>
		';
		if($pdf != ""){
			echo '
							<div class="form-group row form-normal">
								<div class="input-group col-sm-12">
									<div class="input-group-prepend" style="width:132px;">
										<button style="width:100%" class="btn btn-secondary" type="button" onclick="window.open(\'pdf_tmp/'.$pdf.'\',\'_blank\');"><i class="fal fa-file-pdf"></i></button>
									</div>
									<input type="text" class="form-control" name="attach" value="'.$pdf.'" readonly>
								</div>
							</div>
			';
		}
		echo '
						</form>
					</div>
					<div class="modal-footer">
						<button id="btn_send_mail" type="button" class="btn btn-outline-client" onclick="cardAction(\''.$table.'\', 0, \'sendMail\', \'send_mail\')" autocomplete="off" data-loading-text="<i class=\'fal fa-spinner fa-pulse fa-fw mr-2\'></i>'.gettext("En cours...").'">
							<i class="fal fa-paper-plane mr-2"></i>
							'.gettext("Envoyer").'
						</button>
					</div>
				</div>
			</div>
	    ';
	}
?>