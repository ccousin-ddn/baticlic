<?php
/**
*** Juillet 2019@SoluFile 
**/
	require_once (dirname(__FILE__)."/../models/m_sho_shop.php");
	
	class sho_shop_view extends sho_shop_model {
		
		public function __construct()
		{
			parent::__construct();
			
			$this->columns = array(
				array("field"=>"sho_name", "alter"=>"", "title"=>html("Dénomination"), "sortable"=>true, "searchable"=>true, "class"=>"text-nowrap", "halign"=>"left", "align"=>"left"),
				array("field"=>"sho_type", "alter"=>"array", "title"=>html("Type"), "sortable"=>true, "searchable"=>true, "class"=>"text-nowrap", "halign"=>"left", "align"=>"left"),
				array("field"=>"wor_name", "alter"=>"", "title"=>html("Responsable"), "sortable"=>true, "searchable"=>true, "class"=>"text-nowrap", "halign"=>"left", "align"=>"left"),
				array("field"=>"tot_qty", "alter"=>"badge", "title"=>html("Quantité"), "sortable"=>true, "searchable"=>true, "class"=>"text-nowrap", "halign"=>"right", "align"=>"right"),
				array("field"=>"tot_amount", "alter"=>"euro", "title"=>html("Valeur"), "sortable"=>true, "searchable"=>true, "class"=>"text-nowrap", "halign"=>"right", "align"=>"right"),
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
			$param = array("height"=>"auto", "show-columns"=>"false", "pagination"=>"false", "search"=>true, "classes"=>"table table-no-bordered  table-hover table-light", "striped"=>"false", "new-record"=>"true", "export-table"=>"false", "filter-table"=>"sho_type,wor_worker");
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
		
		public function v_createCard($sto_stock)
		{
			// *** Input object ***
			$input = new input($this->values, $this->cryptfields);
			// info : input->create(type, label, name, mandatory(true/false)[, defaultValue[, divSize[, inputClass[, info(array)]]]])
			// info : defaultValue = $this->dataSent['newValue']??"" for newFrom
			$inputs = $input->create("text", "Dénomination", "sho_name", true, "", "col-md-6");
			$inputs .= $input->create("select", "Responsable", "idworker", true, "", "col-md-6", "", array("idlist"=>"2","table"=>"usr_user","search"=>"true","filterChild"=>null));
			$inputs .= $input->create("radio", "Type", "sho_type", false, "", "col-md-12", "no-line", array("contents"=>$this->sho_type));
			$inputs .= $input->create("textarea", "Commentaire", "sho_remark", false, "", "col-md-6", "", array("row"=>1));
			
			// *** Button object ***
			$button = new button($this->table, $this->idrecord, $this->action);
			// info : button->create(type[, buttonClass])
			$btn_save 	= $button->create("save", "btn-mod");
			$btn_close = $button->create("close");
			$btn_delete = $button->create("delete", "btn-mod btn-left");
			$btn_stockIn = '
				<div class="d-flex justify-content-end">
					<button type="button" id="btn_stock_in" class="btn btn-outline-info" onclick="table_action({tablename:\'mov_movement\',action:\'newFromShop\',dataSent:{idshop:\''.decrypt($this->idrecord).'\',mov_type:1}})" autocomplete="off" data-loading-text="<i class=\'fal fa-spinner fa-pulse mr-2\'></i>'.gettext("En cours...").'">
						<i class="fal fa-sign-in-alt mr-2"></i>
						'.gettext("Entrée de stock").'
					</button>
				</div>
			';
			$btn_stockOut = '
				<div class="d-flex justify-content-end">
					<button type="button" id="btn_stock_out" class="btn btn-outline-info" onclick="table_action({tablename:\'mov_movement\',action:\'newFromShop\',dataSent:{idshop:\''.decrypt($this->idrecord).'\',mov_type:2}})" autocomplete="off" data-loading-text="<i class=\'fal fa-spinner fa-pulse mr-2\'></i>'.gettext("En cours...").'">
						<i class="fal fa-sign-out-alt mr-2"></i>
						'.gettext("Sortie de stock").'
					</button>
				</div>
			';
			
			$btns = '
				<div class="col-md-12 mt-3 d-flex flex-row justify-content-between align-items-center">
					<div>'.$btn_stockIn.'</div>
					<div>'.$btn_stockOut.'</div>
				</div>
			';
			// *** Div object ***
			$div = new div();
			// info : div->create(divClass, elem1[, elem2[, elem3[, ...]]])
			$div1 = $div->create("row", $inputs, $btns);
			
			// *** related stock ***
			$div_sto = $div->create("", $sto_stock->json['html']);
			
			$buttons_form = '
			<div class="d-flex flex-column w-100">
				<div class="d-flex flex-row justify-content-between align-items-center">
					<div>'.$btn_stockIn.'</div>
					<div>'.$btn_stockOut.'</div>
				</div>
				<div class="divider py-1 bg-dark"><hr></div>
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
			$form2 = $form->create("normal", "sto_stock_form", "in-tabs", $div_sto);
			
			// *** Tab object ***
			$tab = new tab(1);
			// info : tab->create(label, tabClass, form)
			$tabs = $tab->create("Gestion", "underline", $form1);
			$tabs = $tab->create(ngettext("Stock","Stock",$sto_stock->count).'<span class="ml-2 badge badge-secondary">'.$sto_stock->count.'</span>', "other underline ml-2", $form2);
			
			// *** Body variable ***
			// info : $body = $input[.$div][.$form][.tabs]
			$body = $tabs;
			
			// *** Title variable ***
			$title = $this->values->sho_name ?? gettext("Nouveau ".$this->label);
			// *** Page object ***
			// info : new page(type, id, label, pageClass, body, buttons)
			$page = new page("modal", $this->table."_modal", $this->picto." ".$title, "modal-lg", $body, $btn_save.$btn_close.$btn_delete);
			$this->json['html'] = $page->create(array($this->values->insert_date??date("y-m-d"), $this->values->update_date??date("y-m-d")));
		}
		
		public function v_createListGroup()
		{
			$html = '
			';
			foreach($this->values as $shop){
				$html .= '
					<a class="list-group-item list-group-item-action list-group-item-client-light d-flex justify-content-between align-items-center" data-id="'.$shop['idshop'].'">
						'.$shop['sho_name'].'
						<span class="badge">'.$shop['tot_qty'].'</span>
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