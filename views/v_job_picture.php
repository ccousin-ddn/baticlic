<?php
/**
*** Janvier 2022@SoluFile 
**/
	require_once (dirname(__FILE__)."/../models/m_job_picture.php");
	
	class job_picture_view extends job_picture_model {
		
		public function __construct()
		{
			parent::__construct();
			// sorter : sortDate / sortEuro
			$this->columns = array(
				array("field"=>"idjob", "alter"=>"", "title"=>html("Chantier"), "sortable"=>true, "searchable"=>true, "class"=>"text-nowrap", "halign"=>"left", "align"=>"left", "sorter"=>"", "card-visible"=>"true"),
				array("field"=>"idworker", "alter"=>"", "title"=>html("Compagnon"), "sortable"=>true, "searchable"=>true, "class"=>"text-nowrap", "halign"=>"left", "align"=>"left", "sorter"=>"", "card-visible"=>"true"),
				array("field"=>"pic_name", "alter"=>"", "title"=>html("Photo"), "sortable"=>true, "searchable"=>true, "class"=>"text-nowrap", "halign"=>"left", "align"=>"left", "sorter"=>"", "card-visible"=>"true"),
				
			);
			$this->columns_pdf = array(
				//array("field"=>"", "alter"=>"", "title"=>html(""), "style"=>"", "class"=>""),
			);
			$this->color_conds = array(
				//array("column"=>"", "query"=>" == 0", "class"=>"td-warning")
			);
		}
		
		public function v_createPictures()
		{
			$html = '';
			foreach ($this->values as $pic){
				$html .= '
				<figure class="figure col-4 doc-thumbnail" id="picture_'.encrypt($pic['idpicture']).'">
					<img src="'.SITE_DIRECTORY.DIRECTORY_SEPARATOR.UPLOAD_PATH."pictures/".$pic['pic_name'].'" class="figure-img img-fluid rounded" alt="'.$pic['pic_name'].'">
					<figcaption class="figure-caption">'.alterData("date-be",$pic['pic_date']).'</figcaption>
				';
				if($_SESSION['usr_level'] > 0){
					$html .= '
							<div class="thumb-del" onclick="table_action({tablename:\'job_picture\', idrecord:\''.encrypt($pic['idpicture']).'\', action:\'deleteJobPicture\'});return false">
								<i class="far fa-trash-alt fa-2x"></i>
							</div>
					';
				}
				$html .= '
				</figure>
				';
			}
			
			$this->json['html'] = $html;
		}
		
		public function __destruct()
		{
		}
	}