<?php
	echo '
		<div class="modal fade mt-5" id="session-timeout-dialog">
			<div class="modal-dialog">
				<div class="modal-content">
					<div class="modal-header bg-danger">
						<h4 class="modal-title">'.gettext("Votre session va bientôt expirer !").'</h4>
						<button class="close" aria-hidden="true" data-dismiss="modal" type="button" aria-label="Close">
							<i class="fas fa-times-circle"></i>
						</button>
					</div>
					<div class="modal-body">
						<p>'.gettext("Vous allez être déconnectés dans").' <span class="countdown-holder"></span> '.gettext("secondes").'</p>
						<div class="progress">
							<div class="progress-bar progress-bar-warning progress-bar-striped bg-danger progress-bar-animated countdown-bar active" role="progressbar" style="min-width: 15px; width: 100%;">
								
							</div>
						</div>
					</div>
					<div class="modal-footer">
						<button id="session-timeout-dialog-logout" type="button" class="btn btn-outline-warning btn-left">'.gettext("Se déconnecter").'</button>
						<button id="session-timeout-dialog-keepalive" type="button" class="btn btn-outline-success" data-dismiss="modal">'.gettext("Rester connecté").'</button>
					</div>
				</div>
			</div>
		</div>
	';
?>