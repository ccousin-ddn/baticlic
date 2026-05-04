<?php
/**
*** Juillet 2019@SoluFile 
**/
	require_once (dirname(__FILE__)."/../models/m_doc_document.php");
	
	class doc_document_view extends doc_document_model {
		
		public function __construct()
		{
			parent::__construct();
		}
		
		public function v_show($label)
		{
			if($label){
				$doc_result = '
					<div class="col-md-12">
						<div class="form-group">
							<label class="d-block" for="doc_real_name">'.gettext("Document(s) lié(s)").'</label>
						</div>
					</div>
				';
			}else{
				$doc_result = '
				';
			}
			foreach($this->values as $result){
				$record = (object) $result;
				$doc_result .= $this->createThumb($record, encrypt($record->iddocument));
			}
			$doc_result .= '
						<i class="dz-message fas fa-plus fa-5x text-success absolute-center" 
						data-max-size="'.$this->maxSize.'" 
						data-type="'.$this->type.'"
						data-resize="'.$this->resize.'">
						</i>
						<input type="hidden" id="docFrom" value="'.$this->info.'">
						<input type="hidden" id="idFrom" value="'.$this->idrecord.'">
			';
			$this->json['html'] = $doc_result;
			//return $doc_result;
		}
		
		public function createThumb($record, $idrecord)
		{
			$resizes = explode(",",$this->resize);
			$resize = explode(":",$resizes[0]);
			$smallRep = $resize[1]??PREVIEW_PATH;
			$resize = explode(":",end($resizes));
			$largeRep = $resize[1]??PREVIEW_PATH;
			
			if($_SESSION['usr_level'] > 0){
				$trash = '
						<div class="thumb-del" onclick="upload_action(\''.$idrecord.'\', \'delete\',{resize:\''.$this->resize.'\'});return false">
							<i class="fal fa-trash fa-2x"></i>
						</div>
				';
			}else{
				$trash = '';
			}
			
			$class = 'card doc-thumbnail media d-flex justify-content-between align-items-center upload_'.$idrecord;
			$date = '<small>'.alterData("date-time",$record->doc_creation_date).'</small>';
			$input = '<input class="form-control no-focus" type="text" name="doc_info" value="'.($record->doc_info??$record->doc_real_name).'" data-iddocument="'.$idrecord.'">';
			
			$thumb = '
			';
			switch ($record->doc_type){ // TYPE#1=>"Picture",2=>"Pdf",3=>"Video",4=>"xls",5=>"doc",9=>"Other"
				case 1 :
					$thumb .= '
							<div class="'.$class.'">
								'.$date.'
								<div class="zoomable" href="'.SITE_DIRECTORY.DIRECTORY_SEPARATOR.UPLOAD_PATH.$largeRep.$record->doc_slug_name.'?'.date("ymdhmi").'">
									<img src="'.SITE_DIRECTORY.DIRECTORY_SEPARATOR.UPLOAD_PATH.$smallRep.$record->doc_slug_name.'?'.date("ymdhmi").'" title="'.$record->doc_real_name.'" class="img-thumbnail">
									<button class="btn btn-light btn-close-zoom d-none" type="button"><i class="fal fa-times-circle mr-2"></i>Fermer</button>
								</div>
								'.$input.'
					';
					$thumb .= $trash;
					$thumb .= '
							</div>
					';
					break;
				case 2 : // pdf
					$thumb .= '
							<div class="'.$class.'">
								'.$date.'
								<div class="zoomPdf">
									<img src="'.SITE_DIRECTORY.DIRECTORY_SEPARATOR.UPLOAD_PATH.$smallRep.substr($record->doc_slug_name,0,-4).'.png?'.date("ymdhmi").'" title="'.$record->doc_real_name.'" class="img-thumbnail">
									<embed style="display:none;" src="'.SITE_DIRECTORY.DIRECTORY_SEPARATOR.UPLOAD_PATH.$record->doc_slug_name.'?'.date("ymdhmi").'" width="100%" height="100%" type="application/pdf"/>
									<button class="btn btn-light btn-close-zoom d-none" type="button"><i class="fal fa-times-circle mr-2"></i>Fermer</button>
								</div>
								'.$input.'
					';
					$thumb .= $trash;
					$thumb .= '
							</div>
					';
					break;
				case 999 : // pdf
					$thumb .= '
							<div class="'.$class.'">
								<a href="'.SITE_DIRECTORY.DIRECTORY_SEPARATOR.UPLOAD_PATH.$record->doc_slug_name.'?'.date("ymdhmi").'" target="_blank">
									<img src="'.SITE_DIRECTORY.DIRECTORY_SEPARATOR.UPLOAD_PATH.$smallRep.substr($record->doc_slug_name,0,-4).'.png?'.date("ymdhmi").'" title="'.$record->doc_real_name.'" class="img-thumbnail">
								</a>
								'.$input.'
					';
					$thumb .= $trash;
					$thumb .= '
							</div>
					';
					break;
				case 3 : // video
					$thumb .= '
							<div class="'.$class.'">
								'.$date.'
								<a href="'.SITE_DIRECTORY.DIRECTORY_SEPARATOR.UPLOAD_PATH.$record->doc_slug_name.'" target="_blank" class="picto">
									<i class="fad fa-film-alt fa-4x" title="'.$record->doc_real_name.'"></i>
								</a>
								'.$input.'
					';
					$thumb .= $trash;
					$thumb .= '
							</div>
					';
					break;
				case 4 : // xls
					$thumb .= '
							<div class="'.$class.'">
								'.$date.'
								<a href="'.SITE_DIRECTORY.DIRECTORY_SEPARATOR.UPLOAD_PATH.$record->doc_slug_name.'?'.date("ymdhmi").'" target="_blank" class="picto">
									<i class="fad fa-file-excel fa-4x" title="'.$record->doc_real_name.'"></i>
								</a>
								'.$input.'
					';
					$thumb .= $trash;
					$thumb .= '
							</div>
					';
					break;
				case 5 : // doc
					$thumb .= '
							<div class="'.$class.'">
								'.$date.'
								<a href="'.SITE_DIRECTORY.DIRECTORY_SEPARATOR.UPLOAD_PATH.$record->doc_slug_name.'?'.date("ymdhmi").'" target="_blank" class="picto">
									<i class="fad fa-file-word fa-4x" title="'.$record->doc_real_name.'"></i>
								</a>
								'.$input.'
					';
					$thumb .= $trash;
					$thumb .= '
							</div>
					';
					break;
				case 9 : // other
					$thumb .= '
							<div class="'.$class.'">
								'.$date.'
								<a href="'.SITE_DIRECTORY.DIRECTORY_SEPARATOR.UPLOAD_PATH.$record->doc_slug_name.'" target="_blank" class="picto">
									<i class="fad fa-file fa-4x" title="'.$record->doc_real_name.'"></i>
								</a>
								'.$input.'
					';
					$thumb .= $trash;
					$thumb .= '
							</div>
					';
					break;
			}
			
			return $thumb;
		}
				
		public function __destruct()
		{
		}
	}