<?php
/**
*** Ao�t 2021@SoluFile 
**/
	require_once (dirname(__FILE__)."/../models/m_veh_vehicle.php");
	
	class veh_vehicle_view extends veh_vehicle_model {
		
		public function __construct()
		{
			parent::__construct();
			// sorter : sortDate / sortEuro
			$this->columns = array(
				array("field"=>"veh_num", "alter"=>"", "title"=>html("N°"), "sortable"=>true, "searchable"=>true, "class"=>"text-nowrap", "halign"=>"left", "align"=>"left", "sorter"=>""),
				array("field"=>"veh_numberplate", "alter"=>"", "title"=>html("Plaque"), "sortable"=>true, "searchable"=>true, "class"=>"text-nowrap", "halign"=>"left", "align"=>"left", "sorter"=>""),
				array("field"=>"veh_type", "alter"=>"array", "title"=>html("Type"), "sortable"=>true, "searchable"=>true, "class"=>"text-nowrap", "halign"=>"left", "align"=>"left", "sorter"=>""),
				array("field"=>"bra_name", "alter"=>"", "title"=>html("Marque"), "sortable"=>true, "searchable"=>true, "class"=>"text-nowrap", "halign"=>"left", "align"=>"left", "sorter"=>""),
				array("field"=>"veh_model", "alter"=>"", "title"=>html("Modèle"), "sortable"=>true, "searchable"=>true, "class"=>"text-nowrap", "halign"=>"left", "align"=>"left", "sorter"=>""),
				array("field"=>"veh_financial", "alter"=>"array", "title"=>html("Financement"), "sortable"=>true, "searchable"=>true, "class"=>"text-nowrap", "halign"=>"left", "align"=>"left", "sorter"=>""),
				//array("field"=>"veh_mileage", "alter"=>"", "title"=>html("KM"), "sortable"=>true, "searchable"=>true, "class"=>"text-nowrap", "halign"=>"left", "align"=>"left", "sorter"=>""),
				//array("field"=>"veh_hourly_rate", "alter"=>"euro", "title"=>html("Taux horaire"), "sortable"=>true, "searchable"=>true, "class"=>"text-nowrap", "halign"=>"right", "align"=>"right", "sorter"=>"sortEuro"),
				
			);
			$this->columns_pdf = array(
				//array("field"=>"", "alter"=>"", "title"=>html(""), "style"=>"", "class"=>""),
			);
			$this->color_conds = array(
				array("column"=>"veh_state", "query"=>" == 0", "class"=>"td-danger"),
				array("column"=>"veh_state", "query"=>" == 1", "class"=>"td-success"),
			);
		}
		
		public function v_createFullTable()
		{
			// *** Table object ***
			// info : new table($this->table,level[,checkbox(true/false)][,action(true/false)])
			$table = new table($this->table, $this->level, false);
			// *** create header ***
			$head = $table->createHead($this->picto, ngettext($this->label, $this->labels, $this->count), $this->count);
			// *** create columns title ***
			$thead = $table->createTableHead($this->columns);
			// *** create table lines ***
			$tbody = $table->createTableBody($this->key, $this->values, $this->color_conds);
			// *** create table ***
			$param = array("height"=>"auto", "show-columns"=>"false", "pagination"=>"true", "search"=>true, "classes"=>"table table-no-bordered table-hover table-light", "striped"=>"false", "new-record"=>"true", "export-table"=>"false", "filter-table"=>"");
			
			$this->json['html'] = str_replace(array("\r\n\t", "\t"), '', $table->createTable($head, $param, $thead, $tbody));
		}
		
		public function v_createTr($actions=array())
		{
			// *** Table object ***
			// info : new table($this->table,level[,checkbox(true/false)][,action(true/false)])
			$table = new table($this->table, $this->level, false, $actions);
			// *** create columns title ***
			$table->createTableHead($this->columns);
			
			$this->json['html'] = $table->createJsonTr($this->key, $this->values, $this->color_conds);
			$this->json['tdClass'] = $table->info;
		}
		
		public function v_createLineTable($idparent)
		{
			// *** Table object ***
			// info : new table(inv_line,level[,checkbox(true/false)][,(array("action"=>true/false,"read"=>true/false,"edit"=>true/false,"delete"=>true/false))])
			$table = new table($this->table, $this->level, false, array("action"=>true,"read"=>false,"edit"=>true,"delete"=>true));
			// *** create columns title ***
			$thead = $table->createTableHead($this->columns,"thead-card");
			// *** create table lines ***
			$tbody = $table->createTableBody($this->key, $this->values, $this->color_conds);
			// *** create add line ***
			$tfoot = ""; //$table->createTableFoot(count($this->columns), $idparent);
			
			// *** create table ***
			$param = array("height"=>"auto", "show-columns"=>"false", "pagination"=>"false", "search"=>"false", "new-recordtable"=>"true", "id-parent"=>"$idparent", "classes"=>"table table-sm table-card", "striped"=>"false", "export-table"=>"false", "filter-table"=>"");
			
			$this->json['html'] = $table->createTableLine($param, $thead, $tbody, $tfoot);
		}
		
		public function v_createCard($doc_document,$lnk_vehicle_article,$veh_full,$veh_maintenance,$veh_control,$veh_insurance)
		{
			// *** Input object ***
			$input = new input($this->values, $this->cryptfields);
			// info : input->create(type, label, name, mandatory(true/false)[, defaultValue[, divSize[, inputClass[, info(array)]]]]) "none-result-text"=>""
			// info : defaultValue = $this->dataSent['newValue']??"" for newFrom !!! model_crud c_newFrom case
			// info : $inputs .= $input->breakline;
			$inputs = isset($this->idparent)?$input->create("hidden", "", $this->idparent, false, "", ""):"";
			$inputs .= $input->create("select", gettext("Type"), "veh_type", false, "", "col-md-3", "", array("contents"=>$this->veh_type));
			$inputs .= $input->create("select", "Marque", "idvehbrand", false, "", "col-md-3", "", array("idlist"=>"1","table"=>"veh_brand","search"=>"false","filtering"=>false));
			$inputs .= $input->create("text", gettext("Modèle"), "veh_model", false, "", "col-md-3");
			$inputs .= $input->create("radio", "Etat", "veh_state", false, "", "col-md-3", "no-line", array("contents"=>$this->veh_state));
			$inputs .= $input->create("text", gettext("N°"), "veh_num", false, "", "col-md-1");
			$inputs .= $input->create("text", gettext("Plaque"), "veh_numberplate", true, "", "col-md-2");
			$inputs .= $input->create("select", gettext("Financement"), "veh_financial", false, "", "col-md-3", "", array("contents"=>$this->veh_financial));
			$inputs .= $input->create("date", gettext("Date (achat/fin location)"), "veh_purchase_date", false, "", "col-md-3", "", array("horizontal"=>"right","vertical"=>"bottom"));
			$inputs .= $input->create("date", gettext("Mise en circulation"), "veh_circulation_date", false, "", "col-md-3", "", array("horizontal"=>"right","vertical"=>"bottom"));
			$inputs .= $input->create("select", gettext("Carte grise"), "veh_graycard", false, "", "col-md-2", "", array("contents"=>$this->veh_graycard));
			//$inputs .= $input->create("text", gettext("Taux horaire"), "veh_hourly_rate", true, "", "col-md-3");
			$inputs .= $input->create("textarea", "Commentaire", "veh_remark", false, "", "col-md-10", "", array("row"=>1));
			//$inputs .= $input->create("text", gettext("Km"), "veh_mileage", false, "", "col-md-3");
			
			
			// *** Div object ***
			$div = new div();
			// info : div->create(divClass, $inputs)
			$divs = $div->create("row", $inputs);
			
			// *** related veh_full ***
			$div_ful = $div->create("mb-3", $veh_full->json['html']??"");
			// *** related veh_maintenance ***
			$div_mai = $div->create("mb-3", $veh_maintenance->json['html']??"");
			// *** related veh_control ***
			$div_con = $div->create("mb-3", $veh_control->json['html']??"");
			// *** related veh_tire ***
			$div_ins = $div->create("mb-3", $veh_insurance->json['html']??"");
			
			// *** related lnk_vehicle_article ***
			$div_lnk = $div->create("mt-3", $lnk_vehicle_article->json['html']);
			
			// *** Button object ***
			$button = new button($this->table, $this->idrecord, $this->action);
			// info : button->create(type[, buttonClass])
			$btn_save 	= $button->create("save", "btn-mod");
			$btn_close 	= $button->create("close");
			//$btn_cancel = $button->create("cancel");
			$btn_delete = $button->create("delete", "btn-left btn-mod");
			$btn_pdf	= "";
			// ex : $btn_pdf	= $button->create("pdf");
			$btn_mail	= "";
			// ex : $btn_mail 	= $button->create("mail");
			
			$buttons_form = '
			<div class="d-flex flex-row justify-content-start">
				<div class="">'.$btn_pdf.'</div>
				<div class="ml-3">'.$btn_mail.'</div>
			</div>
			';
			
			// *** Form object ***
			$form = new form();
			// info : form->create("normal/upload", formId, formClass, elem1[, elem2[, elem3[, ...]]])
			$form1 = $form->create("normal", $this->table."_form", "", $divs, $div_lnk, $buttons_form);
			$form_ful = $form->create("normal", "veh_full_form", "in-tabs", $div_ful);
			$form_mai = $form->create("normal", "veh_maintenance_form", "in-tabs", $div_mai);
			$form_con = $form->create("normal", "veh_control_form", "in-tabs", $div_con);
			$form_ins = $form->create("normal", "veh_insurance_form", "in-tabs", $div_ins);
			$formDoc = $form->create("dropzone", $this->table."_form", "", $doc_document->json['html']);
			
			// *** Tab object ***
			$tab = new tab(1);
			$tabs = $tab->create("Véhicule", "underline", $form1);
			$tabs = $tab->create(ngettext("Carburant","Carburants",$veh_full->count).'<span class="ml-2 badge badge-secondary">'.$veh_full->count.'</span>', "other underline ml-2", $form_ful);
			$tabs = $tab->create(ngettext("Entretien","Entretiens",$veh_maintenance->count).'<span class="ml-2 badge badge-secondary">'.$veh_maintenance->count.'</span>', "other underline ml-2", $form_mai);
			$tabs = $tab->create(ngettext("Contrôle","Contrôles",$veh_control->count).'<span class="ml-2 badge badge-secondary">'.$veh_control->count.'</span>', "other underline ml-2", $form_con);
			$tabs = $tab->create(ngettext("Assurance","Assurances",$veh_insurance->count).'<span class="ml-2 badge badge-secondary">'.$veh_insurance->count.'</span>', "other underline ml-2", $form_ins);
			$tabs = $tab->create(ngettext("Document","Documents",$doc_document->count).'<span class="ml-2 badge badge-secondary">'.$doc_document->count.'</span>', "other underline ml-2 nav-document", $formDoc);
			
			// *** Body variable ***
			// info : $body = $input[.$div][.$form][.tabs]
			$body = $tabs;
			
			// *** Title variable ***
			if(decrypt($this->idrecord) > 0){
				$title = $this->values->veh_numberplate;
			}else{
				$title = $this->newtext;
			}
			// *** Page object ***
			// info : new page(type, id, label, pageClass[or colspan], body, buttons)
			$page = new page("modal", $this->table."_modal", $this->picto." ".$title, "modal-xl noscroll", $body, $btn_save.$btn_close.$btn_delete);
			// ex : $page = new page("tr", $this->table."_tr_".$this->idrecord, "", count($this->columns)+1, $body, $btn_save.$btn_cancel.$btn_delete);

			$this->json['html'] = $page->create(array($this->values->insert_date??date("y-m-d"), $this->values->update_date??date("y-m-d")));
		}
		
		public function v_createListGroup()
		{
			$html = '
			';
			foreach($this->values as $vehicle){
				$html .= '
					<a class="list-group-item list-group-item-action list-group-item-client-light d-flex justify-content-between align-items-center" data-id="'.$vehicle['idvehicle'].'">
						<div class="d-flex flex-column">
							<div class="d-flex flex-row align-items-center">
								<i class="fal fa-'.$this->veh_type_fa[$vehicle['veh_type']].' fa-fw mr-3 ml-1"></i>
								<div class="veh-plate d-flex flex-row align-items-center text-monospace">
									<img class="mr-1" src="images/plate_fr.png">
									<strong class="w-100 text-center" style="font-size:1rem;">'.$vehicle['veh_numberplate'].'</strong>
								</div>
							</div>
							<div class="mt-1 ml-1" style="font-size:1.1rem;">
								'.$vehicle['bra_name'].' '.$vehicle['veh_model'].'
							</div>
						</div>
						<span class="badge">'.$vehicle['tot_qty'].'</span>
					</a>
				';
			}
			$html .= '
			';
			$this->json['html'] = $html;
		}
		
		public function v_createAddFuel()
		{
			//var_dump($this->fuel->values);
			$source = '';
			foreach($this->fuel->values as $fuel){
				$source .= '
					<a class="list-group-item list-group-item-action list-group-item-client-light d-flex justify-content-between align-items-center" data-idshop="'.$fuel['idshop'].'" data-idarticle="'.$fuel['idarticle'].'">
						<div class="d-flex flex-column">
							<div class="d-flex flex-row align-items-center">
								<i class="fal fa-gas-pump fa-fw mr-3 ml-1"></i>
								<strong style="font-size:1.1rem;">'.$fuel['sho_name'].'</strong>
							</div>
							<div class="mt-1 ml-1" style="font-size:1.1rem;">
								<small>'.$fuel['art_code'].'</small> '.$fuel['art_description'].'
							</div>
						</div>
						<span class="badge">'.$fuel['sto_quantity'].$fuel['art_unit'].'</span>
					</a>
				';
			}
			$html = '
			<div id="vehicles" class="card-columns mx-3 mb-5">
			';
			foreach($this->values as $vehicle){
				$html .= '
					<a class="card" data-id="'.$vehicle['idvehicle'].'">
						<div class="card-body">
							<div class="d-flex flex-row align-items-center">
								<i class="fal fa-'.$this->veh_type_fa[$vehicle['veh_type']].' fa-fw mr-3 ml-1"></i>
								<div class="veh-plate d-flex flex-row align-items-center text-monospace">
									<img class="mr-1" src="images/plate_fr.png">
									<strong class="w-100 text-center" style="font-size:1rem;">'.$vehicle['veh_numberplate'].'</strong>
								</div>
							</div>
							<div class="mt-1 ml-1" style="font-size:1.1rem;">
								'.$vehicle['bra_name'].' '.$vehicle['veh_model'].'
							</div>
						</div>
					</a>
				';
			}
			$html .= '
			</div>
			
			<div id="fuel" class="col-6">
				'.$source.'
			</div>
			<div id="full" class="col-6">
			<form id="veh_full_form" class="bg-white p-3">
				<fieldset disabled>
					<div class="form-group">
						<label for="ful_mileage">Kilométrage</label>
						<input type="number" class="form-control form-control-lg" id="ful_mileage" name="ful_mileage" placeholder="" max="999999" required="" pattern="[0-9]{6}">
					</div>
					<div class="form-group">
						<label for="ful_liter">Litres</label>
						<input type="number" class="form-control form-control-lg" id="ful_liter" name="ful_liter" placeholder="" max="999" required="" pattern="[0-9]{3}">
					</div>
					<button id="btn_save_full" type="button" class="btn btn-save btn-lg btn-block mt-5" autocomplete="off" data-loading-text="<i class=\'fal fa-spinner fa-pulse fa-fw mr-2\'></i>En cours...">
						<i class="fal fa-check mr-md-2"></i>
						<span class="d-none d-md-inline">Enregistrer</span>
					</button>
				</fieldset>
			</form>
			</div>
			';
			$this->json['html'] = $html;
		}
		
		public function __destruct()
		{
		}
	}