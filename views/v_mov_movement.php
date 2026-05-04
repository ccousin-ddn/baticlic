<?php
/**
*** Juillet 2019@SoluFile 
**/
	require_once (dirname(__FILE__)."/../models/m_mov_movement.php");
	
	class mov_movement_view extends mov_movement_model {
		
		public function __construct()
		{
			parent::__construct();
			
			$this->columns = array(
				array("field"=>"mov_date", "alter"=>"date-be", "title"=>html("Date"), "sortable"=>true, "searchable"=>true, "class"=>"text-nowrap", "halign"=>"left", "align"=>"left", "sorter"=>"sortDate"),
				array("field"=>"usr_firstname", "alter"=>"", "title"=>html("Responsable"), "sortable"=>true, "searchable"=>true, "class"=>"text-nowrap", "halign"=>"left", "align"=>"left"),
				array("field"=>"sho_name", "alter"=>"", "title"=>html("Magasin"), "sortable"=>true, "searchable"=>true, "class"=>"text-nowrap", "halign"=>"left", "align"=>"left"),
				array("field"=>"mov_type", "alter"=>"array", "title"=>html("Type"), "sortable"=>true, "searchable"=>true, "class"=>"text-nowrap", "halign"=>"left", "align"=>"left"),
				array("field"=>"mov_place", "alter"=>"array", "title"=>html("Source / Destination"), "sortable"=>true, "searchable"=>true, "class"=>"text-nowrap", "halign"=>"left", "align"=>"left"),
				array("field"=>"art_code", "alter"=>"", "title"=>html("Code article"), "sortable"=>true, "searchable"=>true, "class"=>"text-nowrap", "halign"=>"left", "align"=>"left"),
				array("field"=>"art_description", "alter"=>"", "title"=>html("Article"), "sortable"=>true, "searchable"=>true, "class"=>"text-nowrap", "halign"=>"left", "align"=>"left"),
				array("field"=>"mov_quantity", "alter"=>"", "title"=>html("Quantité"), "sortable"=>true, "searchable"=>true, "class"=>"text-nowrap", "halign"=>"left", "align"=>"left"),
				
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
			$param = array("height"=>"auto", "show-columns"=>"false", "pagination"=>"false", "search"=>true, "classes"=>"table table-no-bordered  table-hover table-light", "striped"=>"false", "new-record"=>"true", "export-table"=>"false", "filter-table"=>"mov_year,mov_month,sho_type");
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
		
		public function v_createCard($doc_document="")
		{
			// *** Input object ***
			$input = new input($this->values, $this->cryptfields);
			// info : input->create(type, label, name, mandatory(true/false)[, defaultValue[, divSize[, inputClass[, info(array)]]]])
			// info : defaultValue = $this->dataSent['newValue']??"" for newFrom
			$inputs = $input->create("select", "Initiateur", "iduser", true, "", "col-md-6", "", array("idlist"=>"1","table"=>"usr_user","search"=>"true","filterChild"=>null));
			//$inputs .= $input->create("select", "Magasin concerné", "idshop", true, "", "col-md-6", "", array("idlist"=>"1","table"=>"sho_shop","search"=>"true","filterChild"=>null));
			$inputs .= $input->create("select", "Magasin concerné", "idshop", true, "", "col-md-6", "", array("idlist"=>"1","table"=>"sho_shop","search"=>"true","filterChild"=>null));
			$inputs .= $input->create("select", "Type de mouvement", "mov_type", true, "", "col-md-4", "", array("contents"=>$this->mov_type));
			$inputs .= $input->create("select", $this->values->mov_type==1?"Source":"Destination", "mov_place", true, "", "col-md-4 idsource", "", array("contents"=>$this->mov_place));
			switch($this->values->mov_place){
				case 1 : 
					$inputs .= $input->create("select", "Magasin", "mov_idplace", false, "", "col-md-4 idplace", "", array("idlist"=>"1","table"=>"sho_shop","search"=>"true","filterChild"=>null));
					break;
				case 2 : 
					$inputs .= $input->create("select", "Commande", "mov_idplace", false, "", "col-md-4 idplace", "", array("idlist"=>"2","table"=>"sup_order","conds"=>array("ord_status > 3"),"search"=>"true","filterChild"=>null));
					break;
				case 3 : 
					$inputs .= $input->create("select", "Chantier", "mov_idplace", false, "", "col-md-4 idplace", "", array("idlist"=>"3","table"=>"job_job","search"=>"true", "data"=>array("subtext"),"filterChild"=>null));
					break;
				case 4 : 
					$inputs .= $input->create("select", "Véhicule", "mov_idplace", false, "", "col-md-4 idplace", "", array("idlist"=>"2","table"=>"veh_vehicle","search"=>"true","filterChild"=>null));
					break;
				case 5 : 
					$inputs .= $input->create("select", "Manuel", "mov_idplace", false, "", "col-md-4 idplace d-none", "", array("contents"=>array(),"search"=>"true"));
					break;
			}
			$inputs .= $input->create("select", "Article", "idarticle", true, "", "col-md-6", "", array("idlist"=>"1","table"=>"art_article","search"=>"true","filterChild"=>null,"data"=>array("subtext")));
			$inputs .= $input->create("text", "Quantité", "mov_quantity", true, "", "col-md-3");
			$inputs .= $input->create("date", "Date", "mov_date", true, "", "col-md-3");
			

			// *** Div object ***
			$div = new div();
			// info : div->create(divClass, elem1[, elem2[, elem3[, ...]]])
			$div1 = $div->create("row", $inputs);
			
			// *** related documents ***
			$div_doc = $doc_document !="" ? $div->create("row", $doc_document) : "";
			
			// *** Button object ***
			$button = new button($this->table, $this->idrecord, $this->action);
			// info : button->create(type[, buttonClass])
			if(decrypt($this->idrecord) > 0){
				$btn_save = "";
			}else{
				$btn_save = $button->create("save");
			}
			
			$btn_close = $button->create("close");
			$btn_delete = $button->create("delete", "btn-left");
			$btn_pdf	= "";
			// ex : $btn_pdf	= $button->create("pdf");
			$btn_mail	= "";
			// ex : $btn_mail 	= $button->create("mail");
			
			// *** Form object ***
			$form = new form();
			// info : form->create("normal/upload", formId, formClass, elem1[, elem2[, elem3[, ...]]])
			$form1 = $form->create("normal", $this->table."_form", "", $div1, $div_doc, $btn_pdf, $btn_mail);
			
			// *** Tab object ***
			// ex : $tab = new tab(1);
			// info : tab->create(label, tabClass, form)
			// ex : $tabs = $tab->create("Onglet1", "", $form1);
			// ex : $tabs = $tab->create("Onglet2", "", $form2);
			
			// *** Body variable ***
			// info : $body = $input[.$div][.$form][.tabs]
			$body = $form1;
			
			// *** Title variable ***
			$title = isset($this->values->mov_creation_date)?$this->label.' du '.alterData("date-time",$this->values->mov_creation_date):gettext("Nouveau ".$this->label);
			// *** Page object ***
			// info : new page(type, id, label, pageClass, body, buttons)
			$page = new page("modal", $this->table."_modal", $this->picto." ".$title, "modal-lg", $body, $btn_save.$btn_close.$btn_delete);
			$this->json['html'] = $page->create(array($this->values->insert_date??date("y-m-d"), $this->values->update_date??date("y-m-d")));
		}
		
		public function __destruct()
		{
		}
	}