<?php

/**
 * @author Laurent Anezo
 * @copyright 2016
 */
	function debugPhp($text){
		echo '
		<script type="text/javascript">
			console.log("'.$text.'");
		</script>
		';
	}
	
 	function dump(){
		$arr = func_get_args();
		echo '<div class="p-3">';
		echo debug_backtrace()[0]['file']." : line ".debug_backtrace()[0]['line'];
		echo "<pre>";
		foreach($arr as $a){
			echo "<br>";
			var_dump($a);
		}
		echo "</pre></div>";
		exit;
	}
	
	function debug(){
		$arr = func_get_args();
		echo '<div class="p-3">';
		echo debug_backtrace()[0]['file']." : line ".debug_backtrace()[0]['line'];
		echo "<pre>";
		foreach($arr as $a){
			echo "<br>";
			var_dump($a);
		}
		echo "</pre></div>";
		exit;
	}
	
	function print_l($array){
		echo json_encode($array);
	}
	
	function contains($word, $text){
	    return strpos($text, $word) !== false;
	}
	
	function changeDate($action, $date=null){ // action = "+3D", "-5M", "+1Y"
		if(isset($date)){
			$newDate = new DateTime($date);
		}else{
			$newDate = new DateTime();
		}
		$way = substr($action,0,1);
		$duration = substr($action,1);
		if($way == "+"){
			$newDate->add(new DateInterval('P'.$duration));
		}else{
			$newDate->sub(new DateInterval('P'.$duration));
		}
		return $newDate->format('Y-m-d');
	}
	
	function checkDecrypt($tableName,$data){
		$table = new $tableName();
		foreach($data as $key=>$value){
			if (in_array($key, $table->cryptfields)) {
				if($value != ""){
					$data[$key] = strongDecrypt($value);
				}
			}else{
				//$data[$key] = htmlentities($value,ENT_QUOTES,"UTF-8");
			}
		}
		return $data;
	}
	
	function num2com($num)
	{
		$fullNum = str_pad($num,10,"0");
		$mod = $fullNum % 97;
		if($mod == 0){$mod = 97;}
		$com = "+++".substr($fullNum,0,3)."/".substr($fullNum,3,4)."/".substr($fullNum,7,3).str_pad($mod,2,"0",STR_PAD_LEFT)."+++";
		return $com;
	}
	
	function getPercent($num,$pc,$round=2)
	{
		return round($num*$pc/100,$round);
	}
	
	function addPercent($num,$pc,$round=2)
	{
		return round($num+($num*$pc/100),$round);
	}
	
	function remove_line($str, $nbr)
	{
		return ceil(strlen($str) / $nbr);
	}
	
    function br2nl($string)
    {
 		return preg_replace('<br[[:space:]]*/?'.'[[:space:]]*>',chr(13).chr(10),$string);
    }
    
    function addCookie($c_name, $c_value, $c_days)
	{
		setcookie($c_name, $c_value, [
		    'expires' => time() + (86400 * $c_days),
		    'path' => COOKIE_PATH,
		    'domain' => COOKIE_DOMAIN,
		    'secure' => true,
		    'httponly' => true,
		    'samesite' => 'Strict',
		]);
	}
	
	function delCookie($c_name)
	{
		setcookie($c_name, "", -1, COOKIE_PATH, COOKIE_DOMAIN);
		//setcookie($c_name, "", time()-3600);
		unset($_COOKIE[$c_name]);
	}
	
	function getIp()
	{
		//whether ip is from share internet
		if (!empty($_SERVER['HTTP_CLIENT_IP'])){
			$ip_address = $_SERVER['HTTP_CLIENT_IP'];
		}
		//whether ip is from proxy
		elseif (!empty($_SERVER['HTTP_X_FORWARDED_FOR'])){
			$ip_address = $_SERVER['HTTP_X_FORWARDED_FOR'];
		}
		//whether ip is from remote address
		else{
			$ip_address = $_SERVER['REMOTE_ADDR'];
		}
		return $ip_address;
	}
	
	function getCity($ip)
	{
        $geo = unserialize(file_get_contents('http://www.geoplugin.net/php.gp?ip='.$ip.''));
		return $geo['geoplugin_city'];
    }
	
	function rgba2hex ($rgba)
	{
		$sRegex = '/rgba?\(\s?([0-9]{1,3}),\s?([0-9]{1,3}),\s?([0-9]{1,3}),?\s?([0-9]+\.[0-9]{1,2})?/i';
		preg_match($sRegex, $rgba, $matches);
		if(count($matches) < 3){
	        return 0;
		}else{
			$iRed   = $matches[1];
			$iGreen = $matches[2]; 
			$iBlue  = $matches[3];
			$iTrans  = ($matches[4]??1)*255;
			$sHexValue = dechex($iRed) . dechex($iGreen) . dechex($iBlue) . dechex($iTrans);
			return '#'.$sHexValue;
		}
	}
	
	function adjustBrightness($hex, $percent)
	{
	    // Steps should be between -255 and 255. Negative = darker, positive = lighter
	    $steps = max(-255, min(255, $percent));

	    // Normalize into a six character long hex string
	    $hex = str_replace('#', '', $hex);
	    if (strlen($hex) == 3) {
	        $hex = str_repeat(substr($hex,0,1), 2).str_repeat(substr($hex,1,1), 2).str_repeat(substr($hex,2,1), 2);
	    }

	    // Split into three parts: R, G and B
	    $color_parts = str_split($hex, 2);
	    $return = '#';

	    foreach ($color_parts as $color) {
	        $color   = hexdec($color); // Convert to decimal
	        $color   = max(0,min(255,$color + $steps)); // Adjust color
	        $return .= str_pad(dechex($color), 2, '0', STR_PAD_LEFT); // Make two char hex code
	    }

	    return $return;
	}
	
    function html($str)
	{
		return htmlentities($str, ENT_QUOTES, "UTF-8");
	}
    
    function to7bit($text,$from_enc)
	{
        $text = mb_convert_encoding($text,'HTML-ENTITIES',$from_enc);
        // On vire les accents
        $out_text = preg_replace(array('/ß/','/&(..)lig;/', '/&([aouAOU])uml;/','/&(.)[^;]*;/'), array('ss',"$1","$1".'e',"$1"), $text);
        // On vire les espaces multiples
        $out_text= preg_replace('/\s[\s]+/','-',$out_text);
        // On vire les ()
        $out_text = strtr($out_text, array('(' => '', ')' => ''));
        // On vire tout ce qui n'est pas alphanumérique
        $out_text = preg_replace('/[\s\W]+/','-',$out_text);
        // On met en minuscule
        $out_text = strtolower($out_text);
        // On supprime les "-" en fin de chaine
        while (substr($out_text,strlen($out_text)-1,1)=="-"){
        	$out_text = substr($out_text,0,-1);
        }
        // On renvoie la chaîne transformée
        return $out_text;
	}
    
    function getSlug($string, $slug = '-', $extra = '')
	{
		if (strpos($string = htmlentities($string, ENT_QUOTES, 'UTF-8'), '&') !== false){
			$string = html_entity_decode(preg_replace('~&([a-z]{1,2})(?:acute|caron|cedil|circ|grave|lig|orn|ring|slash|tilde|uml);~i', '$1', $string), ENT_QUOTES, 'UTF-8');
		}
		return strtolower(trim(preg_replace('~[^0-9a-z' . preg_quote($extra, '~') . ']++~i', $slug, $string), $slug));
	}
    
    function encrypt($sData)
	{
        $id=(double)$sData*CRYPT_NUMBER;
        return base64_encode($id);
    }
        
    function decrypt($sData)
	{
        $url_id=base64_decode($sData);
        $id=(double)$url_id/CRYPT_NUMBER;
        return $id;
    }

	function simpleEncrypt($text){
		return hash($text);
	}
	
    // Strong Crypting
	function strongEncrypt($text)
	{
		if(!empty($text)){
			$encrypted = openssl_encrypt($text, "AES-256-ECB", CRYPT_KEY, OPENSSL_RAW_DATA);
			$encrypted = base64_encode($encrypted);
			return $encrypted;
		}else{
			return $text;
		}
	}
	
	function strongDecrypt($text)
	{
		if(!empty($text)){
			$text = base64_decode($text);
			$ivSize = openssl_cipher_iv_length("AES-256-ECB");
			$iv = substr($text, 0, $ivSize);
			$text = openssl_decrypt(substr($text, $ivSize), "AES-256-ECB", CRYPT_KEY, OPENSSL_RAW_DATA, $iv);
		}
		return $text;
	}

	function imgExist($path, $file, $replace)
	{
		$filepath = dirname(__FILE__) .'/../'.$path.$file;
		if(($file != "")&&(file_exists($filepath))){
			return $path.$file;
		}else{
			return $replace;
		}
	}
	
	function alterData($type, $data)
	{
		if (strip_tags(trim($data??"")) != ""){
			switch($type){
				case "email" : $newData = "<i class='fal fa-envelope'></i> <a onclick='event.stopPropagation()' href='mailto:$data'>$data</a>"; break;
				case "url" :
					$data = preg_replace('#^https?://#', '', rtrim($data,'/'));
					$newData = "<i class='fal fa-globe'></i> <a onclick='event.stopPropagation()' href='//$data' target='_blank'>$data</a>";
					break;
				case "address" : $newData = "<a onclick='event.stopPropagation()' target='_blank' href='https://www.google.com/maps/place/".urlencode(str_replace("<br>","",$data))."'><i class='fal fa-map-marker'></i></a> $data"; break;
				case "plus" : $newData = "<i class='fal fa-plus'></i> $data"; break;
				case "phone" : 
					//$pref = "+33-";
					//$tel = $pref.getSlug(substr($data,1),"-");
					$tel = getSlug($data,"-");
					$newData = "<i class='fal fa-phone-alt'></i> <a onclick='event.stopPropagation()' href='tel:$tel' rel='nofollow'>$data</a>"; break;
				case "mobile" : $newData = "<i class='fal fa-mobile'></i> $data"; break;
				case "euro" : $newData = number_format(floatval($data), 2, ",", ".")." €"; break;
				case "euroNull" : $newData = $data > 0 ? number_format($data, 2, ",", ".")." €" : (is_numeric($data) ? "" : $data); break;
				case "percentNull" : $newData = $data > 0 ? number_format($data, 2, ",", ".") : (is_numeric($data) ? "" : $data); break;
				case "percent" : $newData = number_format($data, 2, ",", ".")." %"; break;
				case "color" : $newData = "<div style='background-color:$data; width:100px;'>&nbsp;</div>"; break;
				case "badge" : $newData = "<span class='badge badge-secondary p-2'>$data</span>"; break;
				case "photo" : 
					$data = imgExist("upload/pictures/", $data, "images/worker.png");
					$newData = "<img style='width: 100%;' src='".$data."'>"; break;
				case "brand" : $newData = "<img style='width: inherit;' src='upload/brandLogo/".$data."'>"; break;
				case "date-be" : $newData = date('d-m-Y',strtotime($data)); break;
				case "date/be" : $newData = date('d/m/Y',strtotime($data)); break;
				case "day" : $newData = ucfirst(IntlDateFormatter::formatObject(new DateTime($data, new DateTimeZone('Europe/Paris')), 'eeee dd/MM', 'fr')); break; //strftime('%A %d/%m',strtotime($data))
				case "hour-minute" : $newData = substr($data,0,-3); break;
				case "int-hour" : $newData = number_format($data, 0, ",", ".").":00"; break;
				case "hour" : $newData = substr($data,0,-6); break;
				case "year" : $newData = date('Y',strtotime($data)); break;
				case "week" : $newData = date('W',strtotime($data)); break;
				case "date-letter" : $newData = ucwords(IntlDateFormatter::formatObject(new DateTime($data, new DateTimeZone('Europe/Paris')), 'eeee dd MMMM y', 'fr')); break;
				case "month-letter" : $newData = ucwords(IntlDateFormatter::formatObject(new DateTime($data, new DateTimeZone('Europe/Paris')), 'MMMM y', 'fr')); break;
				case "small-date-letter" : $newData = ucwords(IntlDateFormatter::formatObject(new DateTime($data, new DateTimeZone('Europe/Paris')), 'dd MMM', 'fr')); break; //strftime('%d %b',strtotime($data))
				case "small-time" : $newData = ucwords(IntlDateFormatter::formatObject(new DateTime($data, new DateTimeZone('Europe/Paris')), 'HH:mm', 'fr')); break;
				//case "time" : $newData = substr($data,0,-6); break;
				case "date-time" : $newData = ucwords(IntlDateFormatter::formatObject(new DateTime($data, new DateTimeZone('Europe/Paris')), 'dd-MM-y HH:mm', 'fr')); break;
				case "lapstime" : 
					$date = new DateTime($data);
					$now = new DateTime();
					$interval = $now->diff($date);
					$newData =  $interval->y;
					break;
				case "line-copy" :
					$newData = '
					<div class="input-group input-group-sm">
						<div class="input-group-prepend">
							<button class="btn btn-outline-success line-copy" type="button"><i class="fas fa-angle-double-right"></i></button>
						</div>
						<input type="text" class="form-control text-right" value="0">
					</div>
					';
					break;

				default : $newData = $data;
			}
		}else{
			$newData = $data;
		}
		//if($newData == ""){$newData = "-";}
		return $newData;
	}
	
	function build_calendar($month,$year,$data) {
		$holidays = array('0101','1904','2204','0105','0805','3005','0906','1006','1407','1508','0111','1111','2512');
	    $daysOfWeek = array('Lundi','Mardi','Mercredi','Jeudi','Vendredi','Samedi','Dimanche');
		
		if($month == 1){
			$prevMonth = 12;
			$prevYear = $year - 1;
		}else{
			$prevMonth = $month - 1;
			$prevYear = $year;
		}
		if($month == 12){
			$nextMonth = 1;
			$nextYear = $year + 1;
		}else{
			$nextMonth = $month + 1;
			$nextYear = $year;
		}

	    // What is the first day of the month in question?
	    $firstDayOfMonth = mktime(0,0,0,$month,1,$year);
	    $numberDays = date('t',$firstDayOfMonth);
	    $dateComponents = getdate($firstDayOfMonth);
		$monthName = strftime("%B", $firstDayOfMonth);

	    // What is the index value (0-6) of the first day of the
	    // month in question.
	    $dayOfWeek = $dateComponents['wday'] == 0 ? 6 : $dateComponents['wday']-1;
		

		$calendar = '
			<div class="table-container">
				<div class="table-header box">
					<h1><i class="fa fa-calendar"></i> Planning interventions</h1>
					<h1>'.$monthName.' '.$year.'</h1>
					<div class="btn-group btn-group-lg" role="group" aria-label="..." style="margin-top:15px;">
						<button type="button" onclick="int_intervention_action(\''.$prevMonth.'\',\''.$prevYear.'\',\'planning\',\'\')" class="btn btn-grey"><i class="fa fa-chevron-left" aria-hidden="true"></i></button>
						<button type="button" onclick="int_intervention_action(\''.date("m").'\',\''.date("Y").'\',\'planning\',\'\')" class="btn btn-grey">Aujourd\'hui</button>
						<button type="button" onclick="int_intervention_action(\''.$nextMonth.'\',\''.$nextYear.'\',\'planning\',\'\')" class="btn btn-grey"><i class="fa fa-chevron-right" aria-hidden="true"></i></button>
					</div>
				</div>
		';
	    $calendar .= '
				<div class="box boxday">
		';
	    foreach($daysOfWeek as $day) {
			$calendar .= '<div class="td">'.$day.'</div>';
	    }
		$calendar .= '
				</div>
		';
	    $currentDay = 1;
	    $calendar .= '
				<div class="box boxflex">
		';
		// jours mois précédent
		for($i=0; $i<$dayOfWeek; $i++){ 
	        $calendar .= '
					<div class="td grey"><span class="day-number"></span></div>'; 
	    }
	    
	    $month = str_pad($month, 2, "0", STR_PAD_LEFT);
	  
	    while ($currentDay <= $numberDays) {
	        if ($dayOfWeek == 7) {
	            $dayOfWeek = 0;
	            $calendar .= '</div><div class="box boxflex">';
	        }
	        $currentDayRel = str_pad($currentDay, 2, "0", STR_PAD_LEFT);
	        $date = "$year-$month-$currentDayRel";
			$calendar .= '
					<div class="td'.(in_array("$currentDayRel$month", $holidays)?' nowork':'').'">
						<span class="day-number'.(date("Y-m-d")==$date?' badge':'').'" rel="'.$date.'">'.$currentDay.'</span>
						<div class="info">
			';
			$keys = array_keys(array_column($data, 'numday'), $currentDay);
			
			foreach($keys as $key){ 
				$calendar .= '
							<button 
								class="btn btn-sm btn-block" 
								style="background-color: '.$data[$key]['usr_color'].';" 
								type="button" 
								data-iduser='.$data[$key]['iduser'].' 
								data-toggle="popover"
								data-city="<em>'.$data[$key]['city'].'</em><br>"
							>
							'.$data[$key]['usr_firstname'].' <span class="badge">'.$data[$key]['tot'].'</span>
							</button>
				';
			}
			
			$calendar .= '
						</div>
					</div>';
	        // Increment counters
	        $currentDay++;
	        $dayOfWeek++;
	    }
	    // Complete the row of the last week in month, if necessary
	    if ($dayOfWeek != 7) { 
	        $remainingDays = 7 - $dayOfWeek;
			// jours mois suivant
			for($i=0; $i<$remainingDays; $i++){ 
		        $calendar .= '<div class="td grey"><span class="day-number"></span></div>'; 
		    }
	    }
	    
	    $calendar .= "</div>";
		$calendar .= "</div>";

	    return $calendar;

	}