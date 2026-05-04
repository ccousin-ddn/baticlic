<?php
	require_once "lib_include.php";
	
	// https://github.com/shuchkin/simplexlsx
	use Shuchkin\SimpleXLSX;
	use Shuchkin\SimpleXLS;
	
    class importsheet{
    	
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

        public function __construct()
        {
        	$this->file = $_FILES["sheet"]["tmp_name"];
			$this->fileType = $_FILES["sheet"]["type"];
			$this->fileName = pathinfo($_FILES["sheet"]["name"])['filename'];
			$this->fileExtension = pathinfo($_FILES["sheet"]["name"])['extension'];
			
			$finfo = new finfo();
			$this->fileMimeType = $finfo->file($this->file, FILEINFO_MIME_TYPE);
			$this->fileCharset = $finfo->file($this->file, FILEINFO_MIME_ENCODING);
			
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
		
		public function getRows()
		{
			switch($this->fileMimeType){
				case $this->fileType_xls :
					if ($xls = SimpleXLS::parseFile($this->file)){
						$this->rows = $xls->rows();
						
						foreach($this->rows as $key=>$row){
							array_splice($row, 10);
							$this->rows[$key] = $row;
							//var_dump( $xls->rowEx(0,$key) );
							foreach($xls->rowEx(0,$key) as $cell){
								if(isset($cell['colspan'])){
									unset($this->rows[$key]);
								}
							}
						}
						
					} else {
						echo SimpleXLS::parseError();
					}
					break;
				case $this->fileType_xlsx :
					if ($xlsx = SimpleXLSX::parse($this->file)){
						$this->rows = $xlsx->rows();
						//print_r( $xlsx->rowsEx() );
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
			$heads = $this->rows[0];
			$html = '
				<form class="importedit" id="newFromFile_form">
					<div class="tablewrap">
						<table id="set_fields" class="table table-striped" data-toggle="table" data-search="false">
							<thead>
								<tr>
			';
			foreach($heads as $head){
				$html .= '			<th><input name="inp_label[]" style="padding:5px;" type="text" value="'.$head.'"></th>';
			}
			$html .= '
								</tr>
							</thead>
							<tbody>
			';
			
			for($x=1; $x < min($this->tot_rows,20); $x++){
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
					</div>
				</form>
			';
			//$_SESSION['colTypes'] = $columnType;
			return $html;
		}
		
        public function __destruct()
        {
        }
    }
	
	if(isset($_POST["submit"])) {
		$test = new importsheet();
		//$test->getInfo();
		$test->getRows(); //var_dump($test->tot_columns, $test->tot_rows, $test->rows);
		var_dump($test->rows);
		//echo $test->showTable();
	}
?>
<!DOCTYPE html>
<html>
<body>

<form action="lib_importsheet.php" method="post" enctype="multipart/form-data">
    <input type="file" name="sheet" id="sheet" accept=".xls,.xlsx">
	<p></p>
	<input id="simple" name="simple" type="checkbox" value="1">Simple
	<p></p>
    <input type="submit" value="Analyser" name="submit">
</form>

</body>
</html>