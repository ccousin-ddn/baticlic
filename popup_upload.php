<?php
	include_once "libraries/lib_include.php";
	
	$input = new input(array(), array());
	$inputs = $input->create("uploadDrop", gettext("Fichier XLS"), "art_article_file", false, "", "col-12", "", array("table"=>"art_article","type"=>"doc","file"=>"excel","maxSize"=>"8","resize"=>"","realName"=>"newFile"));
	$div = new div();
	$div1 = $div->create("row", $inputs);
	$form = new form();
	$form1 = $form->create("normal", "art_article_form", "", $div1);
	
	echo "
        <div class='modal-dialog no-scroll' role='document'>
            <div class='modal-content'>
                <div class='modal-header'>
					<h4 class='modal-title'><i class='fal fa-upload'></i> ".gettext("Importation articles")."</h4>
                    <button type='button' class='close' onclick='$(\"#popup-msg\").modal(\"hide\")' aria-hidden='true'><i class='fal fa-times-circle'></i></button>
                </div>
                <div class='modal-body' id='upload_form'>
                    ".$form1."
                </div>
                <div class='modal-footer p-3 bg-white'>

                </div>
            </div>
        </div>
    ";