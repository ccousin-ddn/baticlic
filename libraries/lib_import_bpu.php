<?php
	require_once "lib_include.php";
	
	// https://github.com/shuchkin/simplexlsx
	use Shuchkin\SimpleXLSX;
	use Shuchkin\SimpleXLS;
	
    class import_bpu{
    	
		private $file;
		
		private $fileType_csv = "text/plain";
		private $fileType_xls = "application/vnd.ms-excel";
		private $fileType_xlsx = "application/vnd.openxmlformats-officedocument.spreadsheetml.sheet";
		
		private $fileType;
		private $fileName;
		private $fileExtension;
		private $fileMimeType;
		private $fileCharset;
		
		public $rows;
		public $tot_columns;
		public $tot_rows;

        public function __construct($slug)
        {
        	$this->file = '../'.UPLOAD_PATH.$slug;
			$this->fileName = pathinfo($this->file)['filename'];
			$this->fileExtension = pathinfo($this->file)['extension'];
			
			$finfo = new finfo();
			$this->fileMimeType = $finfo->file($this->file, FILEINFO_MIME_TYPE);
			$this->fileCharset = $finfo->file($this->file, FILEINFO_MIME_ENCODING);
			
			//var_dump($this->fileName,$this->fileExtension,$this->fileMimeType,$this->fileCharset);
			
			if($this->fileCharset != "utf-8"){
				$this->fileName = utf8_decode($this->fileName);
			}
        }
		
		private function checkDateXLS($date)
		{
			$oDate = DateTime::createFromFormat('d/m/Y', '01/01/1900');
			$oDate->add(new DateInterval('P'.($date-2).'D'));
			$nDate = $oDate->format('d/m/Y');
			$tempDate = explode('/', $nDate);
			if(checkdate($tempDate[1], $tempDate[0], $tempDate[2])){
				return true;
			}else{
				return false;
			}
		}
		
		private function dateXLS($date)
		{ 
			$oDate = DateTime::createFromFormat('d/m/Y', '01/01/1900');
			$oDate->add(new DateInterval('P'.($date-2).'D'));
			$nDate = $oDate->format('d/m/Y');
			$tempDate = explode('/', $nDate);
			if(checkdate($tempDate[1], $tempDate[0], $tempDate[2])){
				return $nDate;
			}else{
				return $date;
			}
		}
		
		public function getInfo()
		{
			var_dump($this->fileMimeType, $this->fileCharset);
		}
		
		public function getTitle()
		{
			switch($this->fileMimeType){
				case $this->fileType_xls :
					if ($xls = SimpleXLS::parseFile($this->file)){
						$this->rows = $xls->rows();
					} else {
						echo SimpleXLS::parseError();
					}
					break;
				case $this->fileType_xlsx :
					if ($xlsx = SimpleXLSX::parse($this->file)){
						$this->rows = $xlsx->rows();
					}else{
					    echo SimpleXLSX::parseError();
					}
					break;
			}
			// remove empty lines before titles
			foreach($this->rows as $key=>$row){
				array_splice($row, 7);
				$this->rows[$key] = $row;
				
				if(!empty($row[0])&&!empty($row[1])&&!empty($row[2])){
					break;
				}else{
					unset($this->rows[$key]);
				}
			}
			
			// remove empty lines after titles
			foreach($this->rows as $key=>$row){
				array_splice($row, 7);
				$this->rows[$key] = $row;
				
				if(empty($row[0])&&empty($row[1])){
					unset($this->rows[$key]);
				}
			}
			
			$this->tot_rows = count($this->rows);
		}
		
		public function getRows()
		{
			switch($this->fileMimeType){
				case $this->fileType_xls :
					if ($xls = SimpleXLS::parseFile($this->file)){
						$this->rows = $xls->rows();
					} else {
						echo SimpleXLS::parseError();
					}
					break;
				case $this->fileType_xlsx :
					if ($xlsx = SimpleXLSX::parse($this->file)){
						$this->rows = $xlsx->rows();
					}else{
					    echo SimpleXLSX::parseError();
					}
					break;
			}
			//var_dump($_POST);
			// remove empty lines
			foreach($this->rows as $key=>$row){
				array_splice($row, 10);
				$this->rows[$key] = $row;
				
				if(!isset($_POST['simple'])){
					if(empty($row[0])&&empty($row[1])&&empty($row[2])){
						unset($this->rows[$key]);
					}
					if(!empty($row[0])&&empty($row[1])){
						unset($this->rows[$key]);
					}
					if(empty($row[0])&&!empty($row[1])&&empty($row[2])){
						unset($this->rows[$key]);
					}
				}else{
					if(empty($row[0])&&empty($row[1])&&empty($row[2])){
						unset($this->rows[$key]);
					}
				}
			}
			//$this->rows = array_values($this->rows);
			
			//$this->tot_columns = count($this->rows[0]);
			$this->tot_rows = count($this->rows);
		}
		
		public function showTable()
		{
			$columnType = array();
			//var_dump($this->rows);
			//$heads = current($this->rows);
			$val_options = array(0=>"",1=>"Catégorie",2=>"Code",3=>"Désignation",4=>"Description",5=>"Unité",6=>"PU");
			$options = '';
			foreach($val_options as $ndx=>$option){
				$options .= '<option value="'.$ndx.'">'.$option.'</option>';
			}
			
			$html = '
				<table id="set_fields" class="table table-striped" data-toggle="table" data-search="false">
					<thead>
						<tr>
							<th><select name="col0" class="form-control selectpicker" data-dropup-auto="false" data-title="Colonne">'.$options.'</select></th>
							<th><select name="col1" class="form-control selectpicker" data-dropup-auto="false" data-title="Colonne">'.$options.'</select></th>
							<th><select name="col2" class="form-control selectpicker" data-dropup-auto="false" data-title="Colonne">'.$options.'</select></th>
							<th><select name="col3" class="form-control selectpicker" data-dropup-auto="false" data-title="Colonne">'.$options.'</select></th>
							<th><select name="col4" class="form-control selectpicker" data-dropup-auto="false" data-title="Colonne">'.$options.'</select></th>
							<th><select name="col5" class="form-control selectpicker" data-dropup-auto="false" data-title="Colonne">'.$options.'</select></th>
							<th><select name="col6" class="form-control selectpicker" data-dropup-auto="false" data-title="Colonne">'.$options.'</select></th>
						</tr>
					</thead>
					<tbody>
			';
			
			for($x=0; $x < min($this->tot_rows,20); $x++){
				$trs = $this->rows[$x];
				if($x <= 10){
					$html .= '	<tr>';
				}
				foreach($trs as $key=>$tr){
					//print_l($trs); exit;
					// try to find input type
					if(strlen($tr) < 50){
						if(filter_var($tr, FILTER_VALIDATE_EMAIL)){
							!isset($columnType[$key])?$columnType[$key]="text,mail,2":$columnType[$key]=$columnType[$key];
						}
					}
					if (strpos($tr,"www") !== false){
						!isset($columnType[$key])?$columnType[$key]="text,url,3":$columnType[$key]=$columnType[$key];
					}
					if((strpos($tr,"/") > 2)&&(strpos($tr,"/") <= 5)){
						!isset($columnType[$key])?$columnType[$key]="number,formatted,1":$columnType[$key]=$columnType[$key];
					}
					if(is_int($tr)){
						if($tr > 8000){
							if($this->checkDateXLS($tr)){
								!isset($columnType[$key])?$columnType[$key]="time,date,0":$columnType[$key]=$columnType[$key];
								$tr = $this->dateXLS($tr);
							}
						}else{
							!isset($columnType[$key])?$columnType[$key]="number,decimal,0":$columnType[$key]=$columnType[$key];
						}
					}
					if(strlen($tr) > 50){
						!isset($columnType[$key])?$columnType[$key]="text,textarea,1":$columnType[$key]=$columnType[$key];
					}
					if($x <= 10){
						$html .= '	<td class="text-truncate">'.mb_strimwidth($tr, 0, 50, "...").'</td>';
					}
				}
				if($x <= 10){
					$html .= '	</tr>';
				}
			}
			
			$html .= '
					</tbody>
				</table>
			';
			//$_SESSION['colTypes'] = $columnType;
			return $html;
		}
		
        public function __destruct()
        {
        }
    }
?>