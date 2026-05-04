<?php
/**
*** Juillet 2019@SoluFile 
**/
	require_once (dirname(__FILE__)."/../models/m_sup_receipt.php");
	
	class sup_receipt_view extends sup_receipt_model {
		
		public function __construct()
		{
			parent::__construct();
			
			$this->columns = array(
				array("field"=>"sup_name", "alter"=>"", "title"=>html("Fournisseur"), "sortable"=>true, "searchable"=>true, "class"=>"text-nowrap", "halign"=>"left", "align"=>"left"),
				array("field"=>"ord_ref", "alter"=>"", "title"=>html("N° commande"), "sortable"=>true, "searchable"=>true, "class"=>"text-nowrap", "halign"=>"left", "align"=>"left"),
				array("field"=>"rec_date", "alter"=>"date-be", "title"=>html("Date réception"), "sortable"=>true, "searchable"=>true, "class"=>"text-nowrap", "halign"=>"left", "align"=>"left", "sorter"=>"sortDate"),
				array("field"=>"ord_deadline", "alter"=>"date-be", "title"=>html("Date livraison prévue"), "sortable"=>true, "searchable"=>true, "class"=>"text-nowrap", "halign"=>"left", "align"=>"left", "sorter"=>"sortDate"),
				array("field"=>"rec_delivery_note", "alter"=>"", "title"=>html("N° BL"), "sortable"=>true, "searchable"=>true, "class"=>"text-nowrap", "halign"=>"left", "align"=>"left"),
				array("field"=>"ord_title", "alter"=>"", "title"=>html("Concerne"), "sortable"=>true, "searchable"=>true, "class"=>"text-break", "halign"=>"left", "align"=>"left"),
				array("field"=>"sho_name", "alter"=>"", "title"=>html("Magasin"), "sortable"=>true, "searchable"=>true, "class"=>"text-nowrap", "halign"=>"left", "align"=>"left"),
				
			);
			$this->columns_small = array(
				array("field"=>"rec_date", "alter"=>"date-be", "title"=>html("Date réception"), "sortable"=>true, "searchable"=>true, "class"=>"text-nowrap", "halign"=>"left", "align"=>"left", "sorter"=>"sortDate"),
				array("field"=>"rec_status", "alter"=>"array", "title"=>html("Statut"), "sortable"=>true, "searchable"=>true, "class"=>"text-nowrap", "halign"=>"left", "align"=>"left"),
				array("field"=>"sho_name", "alter"=>"", "title"=>html("Magasin"), "sortable"=>true, "searchable"=>true, "class"=>"text-nowrap", "halign"=>"left", "align"=>"left"),
				array("field"=>"rec_remark", "alter"=>"", "title"=>html("Remarque"), "sortable"=>true, "searchable"=>true, "class"=>"text-break", "halign"=>"left", "align"=>"left"),
				
			);
			$this->color_conds = array(array("column"=>"rec_status", "query"=>" == 0", "class"=>"td-danger"),array("column"=>"rec_status", "query"=>" == 1", "class"=>"td-success"),array("column"=>"rec_status", "query"=>" == 2", "class"=>"td-warning"));
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
			$param = array("height"=>"auto", "show-columns"=>"false", "pagination"=>"false", "search"=>true, "classes"=>"table table-no-bordered  table-hover table-light", "striped"=>"false", "new-record"=>"false", "export-table"=>"false", "filter-table"=>"rec_status");
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
			
			// conds
			$line = $this->values[0];
			foreach($this->color_conds as $cond){
				if($line[$cond["column"]] != ""){
					$condition = "return (".$line[$cond["column"]].$cond["query"].");";
					if(eval($condition)){
						$tdClass = $cond["class"];
						break;
					}
				}
			}
			$this->json['tdClass'] = $tdClass;
		}
		
		public function v_createLineTable($idparent)
		{
			// *** Table object ***
			// info : new table(inv_line,level[,checkbox(true/false)][,action(true/false)])
			$table = new table($this->table, $this->level, false, array("action"=>true,"read"=>true));
			// *** create columns title ***
			$thead = $table->createTableHead($this->columns_small, "thead-card");
			// *** create table lines ***
			$tbody = $table->createTableBody($this->key, $this->values, array());
			// *** create add line ***
			$tfoot = "";
			
			// *** create table ***
			$param = array("height"=>"auto", "show-columns"=>"false", "pagination"=>"false", "search"=>"false", "new-record"=>"false", "classes"=>"table table-sm", "striped"=>"false", "export-table"=>"false", "filter-table"=>"");
			$this->json['html'] = $table->createTableLine($param, $thead, $tbody, $tfoot);
		}
		
		public function v_createCard($doc_document, $sup_order_line)
		{
			// *** Input object ***
			$input = new input($this->values, $this->cryptfields);
			// info : input->create(type, label, name, mandatory(true/false)[, defaultValue[, divSize[, inputClass[, info(array)]]]])
			// info : defaultValue = $this->dataSent['newValue']??"" for newFrom
			$inputs = $input->create("hidden", "", "rec_status", false, "", "");
			$inputs = $input->create("hidden", "", "idsuporder", false, "", "");
			$inputs .= $input->create("readonly", "Fournisseur", "sup_name", false, "", "col-md-3");
			$inputs .= $input->create("readonly", "N° commande", "ord_ref", false, "", "col-md-2");
			$inputs .= $input->create("date", "Date réception", "rec_date", true, "", "col-md-2");
			$inputs .= $input->create("text", "N° BL", "rec_delivery_note", false, "", "col-md-2");
			$inputs .= $input->create("select", "Réception", "rec_status", false, "", "col-md-3", "", array("contents"=>$this->rec_status));
			$inputs .= $input->create("textarea", "Concerne", "ord_title", false, "", "col-md-6", "", array("row"=>2,"readonly"=>true));
			$inputs .= $input->create("textarea", "Adresse livraison", "ord_del_address", false, "", "col-md-6", "", array("row"=>2,"readonly"=>true));
			
			$input7 = $input->create("select", "Magasin", "idshop", false, "", "col-md-6", "", array("idlist"=>"1","table"=>"sho_shop","search"=>"true","filterChild"=>null));
			$input8 = $input->create("textarea", "Commentaire", "rec_remark", false, "", "col-md-6", "", array("row"=>1));
			
			// *** Div object ***
			$div = new div();
			// info : div->create(divClass, elem1[, elem2[, elem3[, ...]]])
			$div1 = $div->create("row", $inputs);
			$div2 = $div->create("row", $input7, $input8);
			
			// *** $sup_order_line ***
			$div_line = $div->create("mb-3", $sup_order_line);
			
			// *** Button object ***
			$button = new button($this->table, $this->idrecord, $this->action);
			// info : button->create(type[, buttonClass])
			$btn_save 	= $button->create("save");
			$btn_close = $button->create("close");
			$btn_delete = $button->create("delete", "btn-left");
			$btn_pdf	= "";
			// ex : $btn_pdf	= $button->create("pdf");
			$btn_mail	= "";
			// ex : $btn_mail 	= $button->create("mail");
			
			// *** Form object ***
			$form = new form();
			// info : form->create("normal/upload", formId, formClass, elem1[, elem2[, elem3[, ...]]])
			$form1 = $form->create("upload", $this->table."_form", "", $div1, $div_line, $div2, $btn_pdf, $btn_mail);
			$formDoc = $form->create("dropzone", $this->table."_form", "", $doc_document->json['html']);
			
			// *** Tab object ***
			$tab = new tab(1);
			$tabs = $tab->create("Réception", "underline", $form1);
			$tabs = $tab->create(ngettext("Document","Documents",$doc_document->count).'<span class="ml-2 badge badge-secondary">'.$doc_document->count.'</span>', "other underline ml-2 nav-document", $formDoc);
			
			// *** Body variable ***
			// info : $body = $input[.$div][.$form][.tabs]
			$body = $tabs;
			
			// *** Title variable ***
			if(decrypt($this->idrecord) > 0){
				$title = $this->values->sup_name." - ".$this->values->ord_ref;
			}else{
				$title = gettext("Nouvelle ".$this->label);
			}
			// *** Page object ***
			// info : new page(type, id, label, pageClass, body, buttons)
			$page = new page("modal", $this->table."_modal", $this->picto." ".$title, "modal-xl", $body, $btn_save.$btn_close.$btn_delete);
			$this->json['html'] = $page->create(array($this->values->insert_date??date("y-m-d"), $this->values->update_date??date("y-m-d")));
		}
		
		public function __destruct()
		{
		}
	}