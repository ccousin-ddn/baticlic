<?php
/**
*** Juillet 2019@SoluFile 
**/
	require_once (dirname(__FILE__)."/../views/v_quo_line.php");
	
	class quo_line extends quo_line_view {
		
		public function __construct()
		{
			parent::__construct();
			
			$this->label 		= gettext("Ligne Devis");
			$this->labels 		= gettext("Ligne Deviss");
			$this->picto 		= '<i class=""></i>';
			$this->level		= 1;
			
			$this->cryptfields	= array("");
			
			$this->lnk			= array(); // GROUP_CONCAT(idlnk) idlnk
			
			//$this->arrayName	= array(0=>"No",1=>"Yes");
		}

		public function c_fullTable(){
			if($this->checkUserRight()){
				// *** model ***
				if(empty($this->dataSent)){
					$withFilter=false;
					$this->json['info']="false";
				}else{
					if(isset($this->dataSent['value'])){
						unset($this->dataSent['value']);
					}
					$withFilter=true;
					$this->json['info']="true";
				}
				if($this->m_getAll($withFilter)){
					// *** view ***
					$this->v_createFullTable();
					$this->json['code'] = 1;
					return $this->json;
				}else{
					throw new Exception('PHP : Error in controller fullTable', 200);
				}
			}
		}
		
		public function c_tableByParent(){
			if($this->checkUserRight()){
				// *** model ***
				$this->conds = array("idquotation = ".decrypt($this->idrecord));
				if($this->m_getAll()){
					// *** view ***
					// title
					$tot = 0;
					$count = false;
					foreach($this->values as $key=>$arr){
						if(in_array(strtolower($arr['lin_unit']??''), array("titre","option"))){
							$count = true;
							$this->values[$key]['lin_quantity'] = "";
							$this->values[$key]['lin_pu'] = "";
							$this->values[$key]['lin_total'] = "";
							$this->values[$key]['vat_percent'] = "";
							$this->values[$key]['lin_step_percent'] = "";
							$this->values[$key]['lin_step_total'] = "";
							$this->values[$key]['art_name'] = "<strong>".$arr['art_name']."</strong>";
							$this->values[$key]['lin_unit'] = "<strong>".$arr['lin_unit']."</strong>";
						}
						if(strtolower($arr['lin_unit']??'') == "total"){
							$this->values[$key]['lin_quantity'] = "";
							$this->values[$key]['lin_pu'] = "";
							$this->values[$key]['lin_total'] = "<strong>".number_format($tot, 3, '.', '')."</strong>";
							$this->values[$key]['vat_percent'] = "";
							$this->values[$key]['lin_step_percent'] = "";
							$this->values[$key]['lin_step_total'] = "";
							$this->values[$key]['art_name'] = "<strong>".$arr['art_name']."</strong>";
							$this->values[$key]['lin_unit'] = "<strong>".$arr['lin_unit']."</strong>";
							$tot = 0;
							$count = false;
						}
						if($count){
							$tot += $arr['lin_total'];
						}
					}
					//var_dump($this->values); exit;
					$this->v_createLineTable($this->idrecord);
					$this->json['code'] = 1;
					return true;
				}else{
					throw new Exception('PHP : Error in controller tableByParent', 201);
				}
			}
		}
		
		public function c_newLine(){
			if($this->checkUserRight()){
				// *** model ***
				$this->m_newRecord();
				$this->values->idquotation = decrypt($this->idrecord);
				$this->values->idvat = $this->dataSent["idvat"];
				$this->values = (array) $this->values;
				if($this->m_insert(http_build_query($this->values))){
					$this->json['code'] = "newLineInserted";
					$this->json['idparent'] = encrypt($this->result['idparent']);
					$this->json['info'] = getText("Ligne Devis ajouté");
					// *** view ***
					$this->values['idline'] = $this->result['idparent'];
					
					$this->values = (object) $this->values;
					$this->v_editLine();
					
					return $this->json;
				}else{
					throw new Exception('PHP : Error in controller newLine', 202);
				}
			}
		}
		
		public function c_insertLine(){
			if($this->checkUserRight()){
				// *** model ***
				$this->m_newRecord();
				$idquotation = decrypt($this->dataSent['idquotation']);
				$index = $this->dataSent['oldIndex'];
				
				$this->m_updateOrderBeforeInsert($idquotation, $index);
				
				$this->values->idquotation = $idquotation;
				$this->values = (array) $this->values;

				if($this->m_insert(http_build_query($this->values))){
					$this->conds = array("idline = ".$this->result['idparent']);
					$this->update(array("lin_order"=>$index)); // because trigger beforeInsert
					
					$this->json['code'] = "lineInserted";
					$this->json['idparent'] = encrypt($this->result['idparent']);
					$this->json['info'] = getText("Ligne Devis ajouté");
					// *** view ***
					$this->values['idline'] = $this->result['idparent'];
					
					$this->values = (object) $this->values;
					$this->v_editLine();
					
					return $this->json;
				}else{
					throw new Exception('PHP : Error in controller newLine', 202);
				}
			}
		}
		
		public function c_lineMove(){
			if($this->checkUserRight()){
				// *** model ***
				if($this->m_updateOrder()){
					$this->json['code'] = "lineMoved";
					// *** view ***
					return $this->json;
				}else{
					throw new Exception('PHP : Error in controller listMove', 203);
				}
			}
		}
		
		public function c_editLineTable(){
			if($this->checkUserRight()){
				// *** model ***
				$res = $this->m_getById(decrypt($this->idrecord));
				$this->values = (object) $this->values[0];
				$this->values->lin_qty_formula = $this->values->lin_qty_formula ?? $this->values->lin_quantity;
				$this->values->lin_pu_formula = $this->values->lin_pu_formula ?? $this->values->lin_pu;
				//var_dump($this->values);
				// *** view ***	
				if($res){
					$this->v_editLine();
					$this->json['code'] = "lineEdited";
					return $this->json;
				}else{
					throw new Exception('PHP : Error in controller editLineTable', 212);
				}
			}
		}
		
		public function c_search(){
			if($this->checkUserRight()){
				// *** model ***
				$this->conds = array("lin_name LIKE '%".$this->dataSent['value']."%'");
				$this->dataSent = array();
				if($this->m_getAll(false)){
					// *** view ***
					$this->v_createFullTable();
					$this->json['code'] = 1;
					return $this->json;
				}else{
					throw new Exception('PHP : Error in controller search', 210);
				}
			}
		}
		
		public function c_showCard(){
			if($this->checkUserRight()){
				// *** model ***
				if($this->idrecord == "0"){
					$res = $this->m_newRecord(false);
					// info : if true => $this->idrecord = encrypt($this->result['idparent']);
				}else{
					$res = $this->m_getById(decrypt($this->idrecord));
					$this->values = (object) $this->values[0];
				}
				
				// *** doc_document ***
				//$doc_document = new doc_document();
				//$doc_document->info = $this->table;
				//$doc_document->idrecord = $this->idrecord;
				//$res_document = $doc_document->c_selectByParent();
				$doc_document->json['html'] = "";
				
				// *** view ***	
				if($res){
					$this->v_createCard($doc_document->json['html']);
					$this->json['code'] = 1;
					return $this->json;
				}else{
					throw new Exception('PHP : Error in controller showCard', 211);
				}
			}
		}

		public function c_insert(){
			if($this->checkUserRight()){
				// *** model ***
				$this->duplicateKey = false;
				if($this->m_insert($this->dataSent)){
					$this->json['code'] = "inserted";
					$this->json['idparent'] = encrypt($this->result['idparent']);
					$this->json['info'] = getText("Ligne Devis ajouté");
					return $this->json;
				}else{
					throw new Exception('PHP : Error in controller insert', 220);
				}
			}
		}

		public function c_insertFrom(){
			if($this->checkUserRight()){
				// model
				$this->duplicateKey = false;
				if($this->m_insert($this->dataSent)){
					$this->json['code'] = "insertedFrom";
					$this->json['idparent'] = $this->result['idparent'];
					$this->json['info'] = getText("Ligne Devis ajouté");
					return $this->json;
				}else{
					throw new Exception('PHP : Error in controller insertFrom', 221);
				}
			}
		}

		public function c_update(){
			if($this->checkUserRight()){
				// *** model ***
				parse_str($this->dataSent, $data_line);
				if($this->m_update(decrypt($this->idrecord), $data_line)){
					// *** new data to update table ***
					if($this->m_getById(decrypt($this->idrecord))){
						// Title
						$data = $this->values[0];
						if(in_array(strtolower($data['lin_unit']??""), array("titre","option","total"))){
							$this->values[0]['lin_quantity'] = "";
							$this->values[0]['lin_pu'] = "";
							$this->values[0]['lin_total'] = "";
							$this->values[0]['art_name'] = "<strong>".$this->values[0]['art_name']."</strong>";
							$this->values[0]['lin_unit'] = "<strong>".$this->values[0]['lin_unit']."</strong>";
						}
						$this->v_createTr(array("action"=>true,"edit"=>true,"delete"=>true));
						
						// Get lines total 
						$idquotation = decrypt($data_line['idparent']);
						$this->m_getTotal($idquotation);
						// Update quotation
						$quotation = new quo_quotation();
						$quotation->c_updateTotal($idquotation, $this->total);
						
						//$this->json = $quotation->json;
						$this->json['code'] = "lineUpdated";
						return array_merge($this->json, $quotation->json);
					}else{
						throw new Exception('PHP : Error in controller update', 222);
					}
				}
			}
		}
		
		public function c_updateVAT(){
			if($this->checkUserRight()){
				// *** model ***
				$idquotation = decrypt($this->idrecord);
				if($this->m_updateVAT($idquotation, $this->dataSent['idvat'])){
					$this->m_getTotal($idquotation);
					// Update quotation
					$quotation = new quo_quotation();
					$quotation->c_updateTotal($idquotation, $this->total);
					
					$this->json['code'] = "lineUpdated";
					return array_merge($this->json, $quotation->json);
				}else{
					throw new Exception('PHP : Error in controller update', 222);
				}
			}
		}
		
		public function c_updateWithActualIndex(){
			if($this->checkUserRight()){
				// *** model ***
				$idquotation = decrypt($this->idrecord);
				if($this->m_updateWithActualIndex($idquotation, floatval($this->dataSent['index']))){
					$this->m_getTotal($idquotation);
					// Update quotation
					$quotation = new quo_quotation();
					$quotation->m_update($idquotation, array("quo_actual_index"=>floatval($this->dataSent['index'])));
					$quotation->c_updateTotal($idquotation, $this->total);
					
					$this->json['code'] = "quoActualIndexUpdated";
					$this->json['info'] = "Devis mis à jour";
					return array_merge($this->json, $quotation->json);
				}else{
					throw new Exception('PHP : Error in controller update', 222);
				}
			}
		}

		public function c_delete(){
			if($this->checkUserRight()){
				if($this->m_delete(decrypt($this->idrecord))){
					// Get lines total 
					$idquotation = decrypt($this->dataSent['idparent']);
					$this->m_getTotal($idquotation);
					// Update quotation
					$quotation = new quo_quotation();
					$quotation->dataSent = "";
					$quotation->c_updateTotal($idquotation, $this->total);
						
					$this->json['code'] = "deleted";
					$this->json['info'] = getText("Ligne Devis supprimé");
					return array_merge($this->json, $quotation->json);
				}else{
					throw new Exception('PHP : Error in controller delete', 223);
				}
			}
		}

		public function c_undo(){
			if($this->checkUserRight()){
				// *** model ***
				if($this->m_getById(decrypt($this->idrecord))){
					// *** view ***
					$this->values['operations'] = '<div class="btn-group" role="group" aria-label=""><button type="button" class="btn btn-sm btn-outline-secondary line-edit" data-toggle="tooltip" data-placement="top" title="'.gettext("Editer la ligne").'"><i class="far fa-edit"></i></button><button type="button" class="btn btn-sm btn-outline-danger line-delete" data-toggle="tooltip" data-placement="top" title="'.gettext("Supprimer la ligne").'"><i class="far fa-trash-alt"></i></button></div>';
					$this->v_createTr();
					$this->json['code'] = "lineUpdated";
					return $this->json;
				}else{
					throw new Exception('PHP : Error in controller update', 222);
				}
			}
		}

		public function __destruct()
		{
		}
	}