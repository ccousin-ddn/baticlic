<?php 
    class tab{
		
		private $ul;
		private $div;
		public $numTab;
		private $activeTab;
		private $result;

        public function __construct($activeTab=1)
        {
        	$this->ul			= "";
			$this->div			= "";
			$this->numTab		= 1;
			$this->activeTab	= $activeTab;
			$this->result		= "";
        }

		public function create()
		{
			$vars = func_get_args();
			$label = $vars[0];
			$class = $vars[1];
			$value = $vars[2];
			
			$ul = '<ul class="nav nav-tabs" role="tablist">';
			$div = '<div class="tab-content">';

			$this->ul .= '
					<li class="nav-item '.$class.'" id="nav-tab'.$this->numTab.'">
						<a class="nav-link '.($this->numTab == $this->activeTab ? "active":"").'" data-toggle="tab" role="tab" aria-controls="tab'.$this->numTab.'" href="#tab'.$this->numTab.'" aria-expanded="true">'.$label.'</a>
					</li>';
			$this->div .= '
					<div id="tab'.$this->numTab.'" class="tab-pane p-3 fade '.($this->numTab == $this->activeTab ? "show active":"").'" role="tabpanel">
						'.$value.'
					</div>';
			$this->numTab ++;

			$ul 	= $ul.$this->ul.'</ul>';
			$div 	= $div.$this->div.'</div>';
			
			$this->result = $ul.$div;
			return $this->result;
		}
		
        public function __destruct()
        {
        }
    }