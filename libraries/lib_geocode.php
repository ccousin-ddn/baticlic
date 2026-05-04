<?php 
    class geocode{
		
		private $api_key;
		private $lat;
		private $lng;

        public function __construct($activePanel=1)
        {
        }

		public function getGeoloc($address)
		{
			$address = urlencode($address);
			$url = "https://maps.googleapis.com/maps/api/geocode/json?address={$address}&key=".GOOGLE_API_GEO;
			$resp_json = file_get_contents($url);
			$resp = json_decode($resp_json, true);
			if($resp['status']=='OK'){
				$this->lat = $resp['results'][0]['geometry']['location']['lat'] ?? 0;
				$this->lng = $resp['results'][0]['geometry']['location']['lng'] ?? 0;
				return array("lat"=>str_replace(',', '.',$this->lat), "lng"=>str_replace(',', '.',$this->lng), "status"=>"ok");
			}else{
				return array("status"=>"Error","info"=>$resp['status']);
			}
		}
		
        public function __destruct()
        {
        }
    }