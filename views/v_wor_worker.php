<?php
/**
*** Juillet 2019@SoluFile 
**/
	require_once (dirname(__FILE__)."/../models/m_wor_worker.php");
	
	class wor_worker_view extends wor_worker_model {
		
		public function __construct()
		{
			parent::__construct();
			
			$this->columns = array(
				array("field"=>"wor_picture", "alter"=>"photo", "title"=>html("Photo"), "sortable"=>false, "searchable"=>false, "class"=>"p-0", "halign"=>"center", "align"=>"left", "width"=>100),
				array("field"=>"wor_name", "alter"=>"", "title"=>html("Dénomination"), "sortable"=>true, "searchable"=>true, "class"=>"text-nowrap", "halign"=>"left", "align"=>"left"),
				array("field"=>"wor_login", "alter"=>"", "title"=>html("Login"), "sortable"=>false, "searchable"=>false, "class"=>"text-nowrap", "halign"=>"left", "align"=>"left"),
				array("field"=>"wor_type", "alter"=>"array", "title"=>html("Type"), "sortable"=>true, "searchable"=>true, "class"=>"text-nowrap", "halign"=>"left", "align"=>"left"),
				array("field"=>"wor_color", "alter"=>"color", "title"=>html("Couleur"), "sortable"=>false, "searchable"=>false, "class"=>"text-nowrap", "halign"=>"left", "align"=>"center", "width"=>100),
				
			);
			$this->color_conds = array();
		}
		
		public function v_createFullTable()
		{
			// *** Table object ***
			// info : new table(inv_line,level[,checkbox(true/false)][,action(true/false)])
			$table = new table($this->table, $this->level, false);
			// *** create header ***
			$head = $table->createHead($this->picto, ngettext($this->label, $this->labels, $this->count), $this->count);
			// *** create columns title ***
			$thead = $table->createTableHead($this->columns);
			// *** create table lines ***
			$tbody = $table->createTableBody($this->key, $this->values, $this->color_conds);
			// *** create table ***
			$param = array("height"=>"auto", "show-columns"=>"false", "pagination"=>"false", "search"=>true, "classes"=>"table table-no-bordered table-hover table-light", "striped"=>"false", "new-record"=>"true", "export-table"=>"false", "filter-table"=>"wor_state,wor_type");
			$this->json['html'] = $table->createTable($head, $param, $thead, $tbody);
		}
		
		public function v_createTr()
		{
			// *** Table object ***
			// info : new table(inv_line,level[,checkbox(true/false)][,action(true/false)])
			$table = new table($this->table, $this->level, false);
			// *** create columns title ***
			$table->createTableHead($this->columns);
			$this->json['html'] = $table->createJsonTr($this->key, $this->values, $this->color_conds);
		}
		
		public function v_createLineTable($idparent)
		{
			// *** Table object ***
			// info : new table(inv_line,level[,checkbox(true/false)][,action(true/false)])
			$table = new table($this->table, $this->level, false, true);
			// *** create columns title ***
			$thead = $table->createTableHead($this->columns,"thead-light");
			// *** create table lines ***
			$tbody = $table->createTableBody($this->key, $this->values, $this->color_conds);
			// *** create add line ***
			$tfoot = $table->createTableFoot(count($this->columns), $idparent);
			
			// *** create table ***
			$param = array("height"=>"auto", "show-columns"=>"false", "pagination"=>"false", "search"=>"false", "new-record"=>"false", "classes"=>"table table-bordered table-sm", "striped"=>"false", "export-table"=>"false", "filter-table"=>"");
			$this->json['html'] = $table->createTableLine($param, $thead, $tbody, $tfoot);
		}
		
		public function v_createCard($doc_document, $sto_stock)
		{
			// *** Input object ***
			$input = new input($this->values, $this->cryptfields);
			// info : input->create(type, label, name, mandatory(true/false)[, defaultValue[, divSize[, inputClass[, info(array)]]]])
			// info : defaultValue = $this->dataSent['newValue']??"" for newFrom
			$inputs = $input->create("text", "Dénomination", "wor_name", true, "", "col-md-6");
			$inputs .= $input->create("radio", "Type", "wor_type", false, "", "col-md-4", "no-line", array("contents"=>$this->wor_type));
			$inputs .= $input->create("radio", "Etat", "wor_state", false, "", "col-md-2", "no-line", array("contents"=>$this->wor_state));
			$inputs .= $input->create("color", "Couleur", "wor_color", false, "", "col-md-2");
			$inputs .= $input->create("text", "Login", "wor_login", false, "", "col-md-2");
			$inputs .= $input->create("password", "Nouveau mot de passe", "wor_new_password", false, "", "col-md-2");
			$inputs .= $input->create("text", "Taux horaire", "wor_rate", false, "", "col-md-2");
			$inputs .= $input->create("date", "Date entrée", "wor_date_in", false, "", "col-md-2");
			$inputs .= $input->create("date", "Date sortie", "wor_date_out", false, "", "col-md-2");
			$inputs .= $input->create("uploadDrop", "Photo", "wor_picture", false, "", "col-md-3", "", array("table"=>$this->table,"type"=>"img","maxSize"=>"5","resize"=>"h240:pictures/,:w800:large/"));
			$inputs .= $input->create("select", "Qualification", "wor_level", false, "", "col-md-2", "", array("contents"=>$this->wor_level));
			$inputs .= $input->create("textarea", "Commentaire", "wor_remark", false, "", "col-md-7", "", array("row"=>3));
			
			$inputs2 = $input->create("text", "Prénom", "wor_firstname", false, "", "col-md-6");
			$inputs2 .= $input->create("text", "Nom", "wor_lastname", false, "", "col-md-6");
			$inputs2 .= $input->create("text", "N° NIR", "wor_nir", false, "", "col-md-3");
			$inputs2 .= $input->create("text", "N° carte identité", "wor_idcard", false, "", "col-md-3");
			$inputs2 .= $input->create("text", "Mobile", "wor_mobile", false, "", "col-md-3");
			$inputs2 .= $input->create("date", "Date de naissance", "wor_birthdate", false, "", "col-md-3");
			$inputs2 .= $input->create("text", "Adresse", "wor_address", false, "", "col-md-6");
			$inputs2 .= $input->create("select", gettext("Commune"), "wor_idcity", false, "", "col-md-6", "", array("idlist"=>"1","table"=>"cit_city","search"=>"true","searchDB"=>"true"));
			$inputs2 .= $input->create("textarea", "Commentaire", "wor_remark_1", false, "", "col-md-12", "", array("row"=>2));
			
			$inputs3 = $input->create("select", "Agence d'intérim", "idagency", false, "", "col-md-6", "", array("idlist"=>"1","table"=>"age_agency","search"=>"true","filterChild"=>null,"none-result-text"=>"Créer une agence"));
			$inputs3 .= $input->create("textarea", "Commentaire", "wor_remark_2", false, "", "col-md-12", "", array("row"=>2));
			
			$inputs4 = $input->create("text", "Entreprise", "wor_company_name", false, "", "col-md-6");
			$inputs4 .= $input->create("text", "Siret", "wor_siret", false, "", "col-md-6");
			$inputs4 .= $input->create("textarea", "Commentaire", "wor_remark_3", false, "", "col-md-12", "", array("row"=>2));
			
			// *** Div object ***
			$div = new div();
			// info : div->create(divClass, elem1[, elem2[, elem3[, ...]]])
			$div1 = $div->create("row", $inputs);
			$div2 = $div->create("row", $inputs2);
			$div3 = $div->create("row", $inputs3);
			//$div4 = $div->create("row", $inputs4);
			
			// *** related stock ***
			$div_sto = $div->create("", $sto_stock->json['html']);
			
			// *** Button object ***
			$button = new button($this->table, $this->idrecord, $this->action);
			// info : button->create(type[, buttonClass])
			$btn_save 	= $button->create("save", "btn-mod");
			$btn_close = $button->create("close");
			$btn_delete = $button->create("delete", "btn-left btn-mod");
			$btn_pdf	= "";
			// ex : $btn_pdf	= $button->create("pdf");
			$btn_mail	= "";
			// ex : $btn_mail 	= $button->create("mail");
			
			// *** Form object ***
			$form = new form();
			// info : form->create("normal/upload", formId, formClass, elem1[, elem2[, elem3[, ...]]])
			$form1 = $form->create("normal", $this->table."_form", "", $div1);
			$form2 = $form->create("normal", $this->table."_form", "wor_worker_1_form", $div2);
			$form3 = $form->create("normal", $this->table."_form", "wor_worker_2_form", $div3);
			$form4 = $form->create("normal", "sto_stock_form", "in-tabs", $div_sto);
			$formDoc = $form->create("dropzone", $this->table."_form", "", $doc_document->json['html']);
			
			// *** Tab object ***
			$tab = new tab(1);
			// info : tab->create(label, tabClass, form)
			$tabs = $tab->create("Infos générales", "underline", $form1);
			$tabs = $tab->create("Infos employé", "underline ml-2", $form2);
			$tabs = $tab->create("Infos intérim", "underline ml-2", $form3);
			$tabs = $tab->create(ngettext("Document","Documents",$doc_document->count).'<span class="ml-2 badge badge-secondary">'.$doc_document->count.'</span>', "other underline ml-2 nav-document", $formDoc);
			$tabs = $tab->create(ngettext("Equipement","Equipements",$sto_stock->count).'<span class="ml-2 badge badge-secondary">'.$sto_stock->count.'</span>', "other underline ml-2", $form4);
			//$tabs = $tab->create("Infos sous-traitant", "underline ml-2", $form4);
			
			// *** Body variable ***
			// info : $body = $input[.$div][.$form][.tabs]
			$body = $tabs;
			
			// *** Title variable ***
			$title = $this->values->wor_name ?? $this->newtext;
			// *** Page object ***
			// info : new page(type, id, label, pageClass, body, buttons)
			$page = new page("modal", $this->table."_modal", $this->picto." ".$title, "modal-xl", $body, $btn_save.$btn_close.$btn_delete);
			$this->json['html'] = $page->create(array($this->values->insert_date??date("y-m-d"), $this->values->update_date??date("y-m-d")));
		}
		
		public function v_createHomepage()
		{
			$worker = $this->values[0];
			switch($worker['wor_type']){
				case 1 :
				case 3 :
					$html = '
					<div class="bg-dark text-white w-100 p-3 text-center" style="font-size:1.5rem;" id="title">'.gettext("Choisissez une option ...").'</div>
		
					<div class="d-flex flex-column align-content-around" id="menu_home">
					';
					if(LOCATION != 'mobile'){
						$html .= '
						<div class="p-3 mt-3">
							<button type="button" id="to_timesheet" onclick="worker_action({action:\'showTimesheetHome\'})" class="btn btn-client-light btn-lg btn-block" autocomplete="off" data-loading-text="<i class=\'fas fa-spinner fa-spin mr-2\'></i>En cours...">
								<i class="fal fa-clock mr-3 fa-2x align-middle"></i>'.gettext("Mon emploi du temps").'
							</button>
						</div>
						';
					}
					$html .= '
						<div class="p-3">
							<button type="button" id="to_planning" onclick="worker_action({action:\'showPlanning\'})" class="btn btn-client-light btn-lg btn-block" autocomplete="off" data-loading-text="<i class=\'fas fa-spinner fa-spin mr-2\'></i>En cours...">
								<i class="fal fa-tasks mr-3 fa-2x align-middle"></i>'.gettext("Mon planning").'
							</button>
						</div>
						<div class="p-3">
							<button type="button" id="to_shop" onclick="worker_action({action:\'showMaterial\'})" class="btn btn-client-light btn-lg btn-block" autocomplete="off" data-loading-text="<i class=\'fas fa-spinner fa-spin mr-2\'></i>En cours...">
								<i class="fal fa-tools mr-3 fa-2x align-middle"></i>'.gettext("Mon équipement").'
							</button>
						</div>
					</div>
					';
					break;
			}
			$this->json['html'] = $html;
		}
		
		public function v_createLogin()
		{
			$html = '';
			foreach($this->values as $worker){
				$picture = imgExist("upload/pictures/", $worker['wor_picture'], "images/worker.png");
				$html .= '
					<div class="rounded d-flex flex-column box justify-content-between align-items-center" data-idworker="'.encrypt($worker['idworker']).'" data-wor_name="'.$worker['wor_name'].'" data-wor_login="'.$worker['wor_login'].'" data-wor_picture="'.$picture.'" data-toggle="modal" data-target="#modal_password">
						<img src="'.$picture.'" style="background-color:'.$worker['wor_color'].';">
						<div class="mt-3 p-2">
							'.$worker['wor_name'].'
						</div>
					</div>
				';
			}
			$html .= '';
			$this->json['html'] = $html;
		}
		
		public function v_createListGroup()
		{
			$html = '
			';
			foreach($this->values as $worker){
				$html .= '
					<a class="list-group-item list-group-item-action list-group-item-client-light d-flex justify-content-between align-items-center" data-id="'.$worker['idworker'].'">
						'.$worker['wor_name'].'
						<span class="badge">'.$worker['tot_art'].'</span>
					</a>
				';
			}
			$html .= '
			';
			$this->json['html'] = $html;
		}
		
		public function __destruct()
		{
		}
	}