<?php
/**
*** june 2019@SoluFile 
**/
	require_once (dirname(__FILE__)."/../models/m_usr_user.php");
	
	class view extends model {
		
		public function __construct()
		{
			parent::__construct();
			
			$this->columns = array(
				array("field"=>"iduser", "alter"=>"", "title"=>"id", "sortable"=>true, "searchable"=>false, "class"=>"text-nowrap"),
				array("field"=>"usr_lastname", "alter"=>"", "title"=>"Nom", "sortable"=>true, "searchable"=>true, "class"=>"text-nowrap"),
				array("field"=>"usr_firstname", "alter"=>"", "title"=>"Prénom", "sortable"=>true, "searchable"=>true, "class"=>"text-nowrap"),
				array("field"=>"usr_login", "alter"=>"", "title"=>"Login", "sortable"=>true, "searchable"=>true, "class"=>"text-nowrap"),
				array("field"=>"usr_email", "alter"=>"email", "title"=>"E-mail", "sortable"=>false, "searchable"=>false, "class"=>"text-nowrap"),
				array("field"=>"usr_type", "alter"=>"array", "title"=>"Type", "sortable"=>false, "searchable"=>false, "class"=>"text-nowrap"),
			);
		}
		
		public function v_createFullTable()
		{
			// *** Table object ***
			// info : new table(usr_user,level[,checkbox(true/false)])
			$table = new table("usr_user", $this->level, false);
			// *** create header ***
			$head = $table->createHead($this->picto, ngettext($this->label, $this->labels, $this->count), $this->count);
			// *** create columns title ***
			$thead = $table->createTableHead($this->columns);
			// *** create table lines ***
			$tbody = $table->createTableBody($this->key, $this->values, array());
			// *** create table ***
			$param = array("height"=>"auto", "show-columns"=>"false", "pagination"=>"false", "search"=>true, "classes"=>"table table-no-bordered table-hover table-light w-100", "striped"=>"true", "new-record"=>"true", "export-table"=>"false", "filter-table"=>"usr_type");//org_organization,usr_level
			$this->json['html'] = $table->createTable($head, $param, $thead, $tbody);
		}
		
		public function v_createTr()
		{
			// *** Table object ***
			// info : new table(inv_line,level[,checkbox(true/false)])
			$table = new table($this->table, $this->level, false);
			// *** create columns title ***
			$table->createTableHead($this->columns);
			$this->json['html'] = $table->createJsonTr($this->key, $this->values, array());
		}
		
		public function v_createCard()
		{
			// *** Input object ***
			$input = new input($this->values, $this->cryptfields);
			// info : input->create(type, label, name, mandatory(true/false)[, defaultValue[, divSize[, inputClass[, info(array)]]]])
			$inputs = $input->create("text", "Prénom", "usr_firstname", true, "", "col-md-6");
			$inputs .= $input->create("text", "Nom", "usr_lastname", true, "", "col-md-6");
			$inputs .= $input->create("text", "Login", "usr_login", true, "", "col-md-6");
			$inputs .= $input->create("text", "E-mail", "usr_email", false, "", "col-md-6");
			$inputs .= $input->create("radio", "Type", "usr_type", false,"1","col-md-6","",array("contents"=>$this->usr_type));
			$inputs .= $input->create("radio", "Niveau", "usr_level", false,"1","col-md-6","",array("contents"=>$this->usr_level));
			$inputs .= $input->create("checkbox", "Tableau de bord", "usr_dashboard", false,"","col-md-12","",array("contents"=>$this->usr_dashboard));
			$inputs .= $input->create("password", "Nouveau mot de passe", "usr_new_password", false, "", "col-md-6");
			
			// *** Div object ***
			$div = new div();
			// info : div->create(divClass, elem1[, elem2[, elem3[, ...]]])
			$divs = $div->create("row", $inputs);
			
			// *** Form object ***
			$form = new form();
			// info : form->create("normal/upload", formId, formClass, elem1[, elem2[, elem3[, ...]]])
			$forms = $form->create("normal", "usr_user_form", "", $divs);
			
			// *** Tab object ***
			// ex : $tab = new tab(1);
			// info : tab->create(label, tabClass, form)
			// ex : $tabs = $tab->create("Onglet1", "", $form1);
			// ex : $tabs = $tab->create("Onglet2", "", $form2);
			
			// *** Body variable ***
			// info : $body = $input[.$div][.$form][.tabs]
			$body = $forms;
			
			// *** Button object ***
			$button = new button("usr_user", $_POST["idrecord"]);
			
			$button1 = $button->create("save");
			$button2 = $button->create("close");
			$button3 = $button->create("delete", "btn-left");
			$buttons = $button1.$button2.$button3;
			
			// *** Page object ***
			if($this->values->usr_firstname != ""){$name = $this->values->usr_firstname." ".$this->values->usr_lastname;}
			$title = $name ?? gettext("Nouvel")." ".$this->label;
			$page = new page("modal", "usr_user_modal", $this->picto." ".$title, "noscroll modal-lg", $body, $buttons);
			$this->json['html'] = $page->create(array($this->values->insert_date??date("y-m-d"), $this->values->update_date??date("y-m-d")));
		}
		
		public function v_codeVerification()
		{
			$this->json['html'] = '
			<div class="input-group">
				<div class="input-group-prepend">
					<div class="input-group-text" id="btnGroupAddon"><i class="fal fa-key"></i></div>
				</div>
				<input onkeyup="userCheckEnter(3,event)" id="usr_code" name="usr_code" value="" placeholder="'.gettext("Code reçu par mail").'" type="text" class="form-control" placeholder="Input group example" aria-describedby="btnGroupAddon">
			</div>
			<div id="result_code" class="alert alert-danger collapse mt-3" role="alert"></div>
			<div class="form-group mt-3">
				<button type="button" id="btn-login" onclick="userPassword(\'verifCode\',event)" class="btn btn-login btn-block">'.gettext("Vérifier").'</button>
			</div>
			<div class="form-group mt-3">
				<div style="border-top: 1px solid grey; padding-top:15px; font-size:85%" >
					<h4 class="text-danger">'.gettext("Veuillez laisser cette fenêtre ouverte et y introduire le code que vous allez recevoir par mail.").'</h4>
					<h5>'.gettext("Si vous ne recevez rien dans les 5 minutes, veuillez vérifier dans vos spams.").'</h5>
				</div>
			</div>
			';
		}
		
		public function v_newPassword()
		{
			$this->json['html'] = '
			<form class="" id="usr_user_form">
				<div class="">
					<input type="hidden" id="usr_password_old" name="usr_password_old" value="none">
					<div class="input-group mb-1">
						<div class="input-group-prepend"><div class="input-group-text"><i class="fal fa-lock"></i></div></div>
						<input onkeyup="userCheckEnter(4,event)" id="usr_password_new" type="password" class="form-control" name="usr_password_new" placeholder="'.gettext("Nouveau mot de passe (min 6 car, min 1 chiffre)").'" autofocus>
					</div>
					<div class="input-group mb-3">
						<div class="input-group-prepend"><div class="input-group-text"><i class="fal fa-lock"></i></div></div>
						<input onkeyup="userCheckEnter(4,event)" id="usr_password_new_confirm" type="password" class="form-control" name="usr_password_new_confirm" placeholder="'.gettext("Confirmer le nouveau mot de passe").'">
					</div>
				</div>
			</form>
			<div id="result_password" class="alert alert-danger collapse mt-3 mb-3" role="alert">
				<span id="mess1">'.gettext("Le mot de passe doit contenir au moins 6 caractères").'</span>
				<span id="mess2">'.gettext("Le mot de passe doit contenir au moins 1 chiffre").'</span>
				<span id="mess3">'.gettext("Le mot de passe ne correspond pas à la confirmation").'</span>
				<span id="mess4">'.gettext("Le mot de passe doit contenir au moins 1 lettre").'</span>
			</div>
			<button class="btn btn-login btn-block" type="button" onclick="userPassword(\'saveNewPassword\',event)" autocomplete="off">
				<i class="fal fa-check mr-2"></i>
				'.gettext("Enregistrer").'
			</button>
			<p class="font-weight-light mt-3">
				'.gettext("Veuillez introduire votre nouveau mot de passe.").'<br>
				'.gettext("Et le confirmer dans la deuxième case.").'
			</p>
			';
		}
		
		public function __destruct()
		{
		}
	}