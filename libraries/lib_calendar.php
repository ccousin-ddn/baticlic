<?php 
    class calendar{
		
		private $daysOfWeek = array('Lundi','Mardi','Mercredi','Jeudi','Vendredi','Samedi','Dimanche');
		private $holidays = array('0101','1304','0105','0805','2105','0106','1407','1508','0111','1111','2512');
		private $year;
		private $month;
		private $table;
		
		public $picto;
		public $title;
		public $buttons;
		public $navigationFunction;

        public function __construct($table,$year,$month)
        {
        	$this->table = $table;
			$this->year = $year;
			$this->month = $month;
        }

		public function build_calendar($data) 
		{
			if($this->month == 1){
				$prevMonth = 12;
				$prevYear = $this->year - 1;
			}else{
				$prevMonth = $this->month - 1;
				$prevYear = $this->year;
			}
			if($this->month == 12){
				$nextMonth = 1;
				$nextYear = $this->year + 1;
			}else{
				$nextMonth = $this->month + 1;
				$nextYear = $this->year;
			}

		    // What is the first day of the month in question?
		    $firstDayOfMonth = date_create($this->year.'-'.$this->month.'-01');
		    $numberDays = cal_days_in_month(CAL_GREGORIAN, $this->month, $this->year);
			$monthName = IntlDateFormatter::formatObject($firstDayOfMonth, 'MMMM', 'fr');
		    // What is the index value (0-6) of the first day of the month in question.
		    $dayOfWeek = date_format($firstDayOfMonth, 'w')== 0 ? 6 : date_format($firstDayOfMonth, 'w')-1;

			$calendar = '
				<div id="calendar" class="bg-light my-3 p-3">
					<div class="table-header box">
						<h1 class="col_light">'.$this->picto.$this->title.'</h1>
						<h1 class="col_dark">'.$monthName.' '.$this->year.'</h1>
						'.$this->buttons.'
						<div class="btn-group" role="group">
							<button type="button" onclick="calendar_action({action:\''.$this->navigationFunction.'\', year:\''.$prevYear.'\', month:\''.$prevMonth.'\'})" class="btn btn-outline-secondary mr-1"><i class="fal fa-chevron-left fa-fw"></i></button><button type="button" onclick="calendar_action({action:\''.$this->navigationFunction.'\', year:\''.date("Y").'\', month:\''.date("m").'\'})" class="btn btn-outline-secondary">Aujourd\'hui</button><button type="button" onclick="calendar_action({action:\''.$this->navigationFunction.'\', year:\''.$nextYear.'\', month:\''.$nextMonth.'\'})" class="btn btn-outline-secondary ml-1"><i class="fal fa-chevron-right fa-fw"></i></button>
						</div>
					</div>
			';
		    $calendar .= '
					<div class="th">
			';
		    foreach($this->daysOfWeek as $day) {
				$calendar .= '<span>'.$day.'</span>';
		    }
			$calendar .= '
					</div>
			';
		    $currentDay = 1;
		    $calendar .= '
					<div class="week">
			';
			// jours mois précédent
			for($i=0; $i<$dayOfWeek; $i++){ 
		        $calendar .= '
						<div class="grey"><span class="day-number"></span></div>'; 
		    }
		    
		    $this->month = str_pad($this->month, 2, "0", STR_PAD_LEFT);
		  	$id = 0;
		    while ($currentDay <= $numberDays) {
		        if ($dayOfWeek == 7) {
		            $dayOfWeek = 0;
		            $calendar .= '</div><div class="week">';
		        }
		        $currentDayRel = str_pad($currentDay, 2, "0", STR_PAD_LEFT);
		        $date = $this->year.'-'.$this->month.'-'.$currentDayRel;
				$dayClass = "";
				// check holidays
				if(in_array($currentDayRel.$this->month, $this->holidays)){
					$dayClass = "nowork";
				}
				// check if today
				if(date("Y-m-d")==$date){
					$dayClass = "today";
				}
				$calendar .= '
						<div class="numDay '.$dayClass.'" data-date="'.$date.'" data-day="'.$currentDay.'">
							<div class="drag-container">
				';
				$keys = array_keys(array_column($data, 'numday'), $currentDay);
				
				foreach($keys as $key){ 
					$id++;
					$calendar .= '
								<button 
									id="'.$id.'"
									class="btn btn-sm btn-block drag-elem" 
									style="background-color: '.$data[$key]['color'].';" 
									type="button" 
									draggable="true"
									data-id='.encrypt($data[$key]['idcal']).' 
								>
									<div class="w-100 d-flex flex-row justify-content-between align-items-start drag-div">
										'.$data[$key]['content'].'
									</div>
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
			
		    return str_replace(array("\r\n\t", "\t"), '', $calendar);

		}
		
        public function __destruct()
        {
        }
    }