		<footer class="container-fluid">
			<div class="row justify-content-between px-3">
				<div class="text-left">
					soluWORK <?php echo $ver?> (<?php echo MYSQL_SERVER.' : '.MYSQL_DB?>)
				</div>
				<div class="text-center">
					<i class="fal fa-copyright"></i> 2019-<?php echo date("Y")?> <a href="https://www.solufile.be" target="_blank" rel="noopener nofollow">SOLUfile</a>
				</div>
				<div class="text-right" onclick="changeBG()" style="cursor:pointer;">
					<i class="fal fa-photo-video"></i><i class="fal fa-sync ml-2"></i>
				</div>
			</div>
		</footer>
		<div class="modal fade" tabindex="-1" role="dialog" id="xdebug-modal" aria-labelledby="x-debug" aria-hidden="true" style="z-index: 10000;">
			<div class="modal-dialog modal-xlg modal-dialog-centered">
				<div class="modal-content">
					<div class="modal-header">
						<h5 class="modal-title" id="">Debug</h5>
						<button type="button" class="close" data-dismiss="modal" aria-label="Close">
							<i class='fas fa-times-circle'></i>
						</button>
					</div>
					<div class="modal-body" id="xdebug">
					</div>
				</div>
			</div>
		</div>
		<div id="popup-delete" class="modal fade"></div>
		<div id="popup-msg" class="modal fade"></div>
		<div id="edit-img" class="img-full" style="display:none;z-index:10000;"></div>