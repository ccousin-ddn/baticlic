<?php
/**
*** Juillet 2019@SoluFile 
**/
	require_once (dirname(__FILE__)."/../models/m_art_article.php");
	
	class art_article_view extends art_article_model {
		
		public function __construct()
		{
			parent::__construct();
			
			$this->columns = array(
				array("field"=>"art_code", "alter"=>"", "title"=>html("Code article"), "sortable"=>true, "searchable"=>true, "class"=>"text-nowrap", "halign"=>"left", "align"=>"left"),
				array("field"=>"art_description", "alter"=>"", "title"=>html("Description"), "sortable"=>true, "searchable"=>true, "class"=>"text-break", "halign"=>"left", "align"=>"left"),
				array("field"=>"art_type", "alter"=>"array", "title"=>html("Type"), "sortable"=>true, "searchable"=>true, "class"=>"text-nowrap", "halign"=>"left", "align"=>"left"),
				array("field"=>"cat_name", "alter"=>"", "title"=>html("Catégorie"), "sortable"=>true, "searchable"=>true, "class"=>"text-nowrap", "halign"=>"left", "align"=>"left"),
				array("field"=>"subcat_name", "alter"=>"", "title"=>html("Sous-catégorie"), "sortable"=>true, "searchable"=>true, "class"=>"text-nowrap", "halign"=>"left", "align"=>"left"),
				array("field"=>"art_unit", "alter"=>"", "title"=>html("Unité"), "sortable"=>true, "searchable"=>true, "class"=>"text-nowrap", "halign"=>"left", "align"=>"left"),
				array("field"=>"art_rental_rate", "alter"=>"euro", "title"=>html("Tarif location"), "sortable"=>true, "searchable"=>true, "class"=>"text-nowrap", "halign"=>"left", "align"=>"left", "sorter"=>"sortEuro"),
				array("field"=>"art_price", "alter"=>"euro", "title"=>html("Prix d'achat moyen"), "sortable"=>true, "searchable"=>true, "class"=>"text-nowrap", "halign"=>"left", "align"=>"left", "sorter"=>"sortEuro"),
				
			);
			
			$this->columns_small = array(
				array("field"=>"art_code", "alter"=>"", "title"=>html("Code article"), "sortable"=>true, "searchable"=>true, "class"=>"text-nowrap", "halign"=>"left", "align"=>"left"),
				array("field"=>"art_description", "alter"=>"", "title"=>html("Article"), "sortable"=>true, "searchable"=>true, "class"=>"text-nowrap", "halign"=>"left", "align"=>"left"),
				array("field"=>"art_rental_rate", "alter"=>"euro", "title"=>html("Tarif"), "sortable"=>true, "searchable"=>true, "class"=>"text-nowrap", "halign"=>"left", "align"=>"left"),
				
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
			$param = array("height"=>"auto", "show-columns"=>"false", "pagination"=>"false", "search"=>true, "classes"=>"table table-no-bordered table-hover table-light", "striped"=>"false", "new-record"=>"true", "export-table"=>"false", "filter-table"=>"art_type,art_category");
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
			$table = new table($this->table, $this->level, false, false);
			// *** create columns title ***
			$thead = $table->createTableHead($this->columns_small,"thead-card");
			// *** create table lines ***
			$tbody = $table->createTableBody($this->key, $this->values, $this->color_conds);
			// *** create add line ***
			$tfoot = $table->createTableFoot(count($this->columns_small), $idparent);
			
			// *** create table ***
			$param = array("height"=>"auto", "show-columns"=>"false", "pagination"=>"false", "search"=>"false", "new-record"=>"false", "classes"=>"table table-bordered table-sm", "striped"=>"false", "export-table"=>"false", "filter-table"=>"");
			$this->json['html'] = $table->createTableLine($param, $thead, $tbody, $tfoot);
		}
		
		public function v_createCard($doc_document,$sup_order_line,$sto_stock)
		{
			// *** Input object ***
			$input = new input($this->values, $this->cryptfields);
			// info : input->create(type, label, name, mandatory(true/false)[, defaultValue[, divSize[, inputClass[, info(array)]]]])
			// info : defaultValue = $this->dataSent['newValue']??"" for newFrom
			$inputs = $input->create("text", "Code article", "art_code", false, "", "col-md-3");
			$inputs .= $input->create("radio", "Type", "art_type", false, "", "col-md-9", "no-line", array("contents"=>$this->art_type));	
			$inputs .= $input->create("text", "Description", "art_description", false, "", "col-md-12");
		   	$inputs .= $input->create("select", "Catégorie", "idartcategory", false, "", "col-md-6", "", array("idlist"=>"1","table"=>"art_category","search"=>"true","filterChild"=>"art_subcategory","none-result-text"=>"Créer la catégorie"));
			$inputs .= $input->create("select", "Sous-catégorie", "idartsubcategory", false, "", "col-md-6", "", array("idlist"=>"1","table"=>"art_subcategory","search"=>"true","filtering"=>true,"none-result-text"=>"Créer la sous-catégorie"));
			$inputs .= $input->create("text", "UdM (Unité de Mesure)", "art_unit", false, "", "col-md-3");
			$inputs .= $input->create("text", "Tarif location", "art_rental_rate", false, "", "col-md-3");
			$inputs .= $input->create("text", "Prix d'achat moyen", "art_price", false, "", "col-md-3");
			$inputs .= $input->create("uploadDrop", "Photo article", "art_picture", false, "", "col-md-6", "", array("table"=>$this->table,"type"=>"img","maxSize"=>"5","resize"=>"w300:small/,:w800:large/"));
			$inputs .= $input->create("textarea", "Commentaire", "art_remark", false, "", "col-md-6", "", array("row"=>3));
			

			// *** Div object ***
			$div = new div();
			// info : div->create(divClass, elem1[, elem2[, elem3[, ...]]])
			$div1 = $div->create("row", $inputs);
			// *** related order_lines ***
			$div_ord = $div->create("mb-3", $sup_order_line->json["html"]);
			// *** related sto_stock ***
			$div_sto = $div->create("mb-3", $sto_stock->json["html"]);
			
			// *** Button object ***
			$button = new button($this->table, $this->idrecord, $this->action);
			// info : button->create(type[, buttonClass])
			$btn_save 	= $button->create("save", "btn-mod");
			$btn_close = $button->create("close");
			$btn_delete = $button->create("delete", "btn-left btn-mod");
			$btn_duplicate = '
				<button type="button" id="btn_tim_timesheet" class="btn btn-outline-info btn-mod" onclick="iud(\''.$this->table.'\',\''.$this->idrecord.'\',\'update\',\'none\', function(){cardAction(\''.$this->table.'\',\''.$this->idrecord.'\',\'duplicate\',\'art_article\')})" autocomplete="off" data-loading-text="<i class=\'fas fa-circle-notch fa-spin mr-2\'></i>'.gettext("En cours...").'">
					<i class="fal fa-copy mr-2"></i>
					'.gettext("Dupliquer").'
				</button>
			';
			
			$buttons_form = '
			<div class="d-flex flex-column w-100">
				<div class="d-flex flex-row justify-content-start align-items-center mb-3">
					'.$btn_duplicate.'
				</div>
				<div class="d-flex flex-row justify-content-between">
					<div class="d-flex flex-row justify-content-start align-items-center">
						<div class="">'.$btn_delete.'</div>
					</div>
					<div class="d-flex flex-row justify-content-start align-items-center">
						<div class="">'.$btn_save.'</div>
						<div class="ml-3">'.$btn_close.'</div>
					</div>
				</div>
			</div>
			';
			
			// *** Form object ***
			$form = new form();
			// info : form->create("normal/upload", formId, formClass, elem1[, elem2[, elem3[, ...]]])
			$form1 = $form->create("normal", $this->table."_form", "", $div1);
			$form_ord = $form->create("normal", "sup_order_line_form", "", $div_ord);
			$form_sto = $form->create("normal", "sto_stock_form", "", $div_sto);
			
			$tab = new tab(1);
			$tabs = $tab->create("Article", "underline", $form1);
			$tabs = $tab->create(ngettext("Historique","Historique",$sup_order_line->count).'<span class="ml-2 badge badge-secondary">'.$sup_order_line->count.'</span>', "other underline ml-2", $form_ord);
			$tabs = $tab->create(ngettext("Stock","Stock",$sto_stock->count).'<span class="ml-2 badge badge-secondary">'.$sto_stock->count.'</span>', "other underline ml-2", $form_sto);
			
			// *** Body variable ***
			// info : $body = $input[.$div][.$form][.tabs]
			$body = $tabs;
			
			// *** Title variable ***
			$title = $this->values->art_code ?? gettext("Nouvel ".$this->label);
			// *** Page object ***
			// info : new page(type, id, label, pageClass, body, buttons)
			$page = new page("modal", $this->table."_modal", $this->picto." ".$title, "modal-xl", $body, $buttons_form);
			$this->json['html'] = $page->create(array($this->values->insert_date??date("y-m-d"), $this->values->update_date??date("y-m-d")));
		}
		
		public function __destruct()
		{
		}
	}