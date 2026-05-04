<?php 
    class div{
		
		private $result;

        public function __construct()
        {
        	$this->result = "";
        }

		public function create()
		{
			$vars = func_get_args();
			$class = $vars[0];
			array_shift($vars);
			$value = implode("", $vars);
			$this->result = '<div class="'.$class.'">'.$value.'</div>';
			return $this->result;
		}
		
		public function dashboard($lines, $align="text-right", $color="text-white")
		{
			$html = '
			<div class="card-body d-flex align-items-end flex-column '.$align.' '.$color.'">
				<ul class="list-group list-group-flush">
			';
			foreach($lines as $line){
				$html .= '
					<li class="list-group-item" data-idrecord="'.encrypt($line["idline"]).'">'.$line["line"].'</li>
				';
			}
			$html .= '
				</ul>
			</div>
			';
			return $html;
		}
		
        public function __destruct()
        {
        }
    }