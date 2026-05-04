<?php
	include_once "libraries/lib_include.php";
	if((isset($_SESSION['iduser']))&&(isset($_POST['table']))){
	    $idrecord = $_POST["idrecord"];
	    $table = $_POST["table"];
	    
	    echo "
	        <div class='modal-dialog modal-delete no-scroll pt-5'>
	            <div class='modal-content'>
	                <div class='modal-header bg-red'>
						<h4 class='modal-title'><i class='fal fa-info-circle'></i> ".gettext("Confirmation")."</h4>
	                    <button type='button' class='close' onclick='$(\"#popup-delete\").modal(\"hide\")' aria-hidden='true'><i class='fal fa-times-circle'></i></button>
	                </div>
	                <div class='modal-body'>
	                    <p>".gettext("Voulez-vous réellement supprimer cette fiche ?")."</p>
	                </div>
	                <div class='modal-footer p-3 bg-white'>
	                    <button type='button' class='btn btn-delete btn-left' data-dismiss='modal' onclick='iud(\"$table\",\"$idrecord\",\"delete\",\"showCard\")'><i class='fal fa-check mr-1'></i> ".gettext("Oui")."<span class='d-none d-sm-inline'>".gettext(", je supprime")."</span></button>
	                	<button type='button' class='btn btn-outline-secondary' onclick='$(\"#popup-delete\").modal(\"hide\")'><i class='fal fa-times mr-1'></i> ".gettext("Non")."<span class='d-none d-sm-inline'>".gettext(", c'est une erreur")."</span></button>
	                </div>
	            </div>
	        </div>
	    ";
	}