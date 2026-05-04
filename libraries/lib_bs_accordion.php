<?php 
    class accordion{
		
		private $ul;
		private $div;
		private $numPanel;
		private $activePanel;
		private $result;

        public function __construct($activePanel=1)
        {
        	$this->ul			= "";
			$this->div			= "";
			$this->numPanel		= 1;
			$this->activePanel	= $activePanel;
			$this->result		= "";
        }

		public function create($value)
		{
			$panel = '
			<div class="panel-group" id="accordion">
				'.$value.'
			</div>
			';
			return $panel;
		}
		
		public function panel($title, $table, $titleInfo='', $class='')
		{
			if($titleInfo != ""){
				$this->result = '
					<div class="panel panel-default">
	                    <div class="panel-heading">
							<a class="'.($this->numPanel == $this->activePanel ? "":"collapsed").'" data-toggle="collapse" data-parent="#accordion" href="#collapse'.$this->numPanel.'">
	                        	<h4 class="panel-title text_e2sit_color2">
									'.$title.'
									<div class="pull-right info-'.$table.'">'.$titleInfo.'<i class="fa fa-check pull-right i-green"></i></div>
	                        	</h4>
							</a>
	                    </div>
	                    <div id="collapse'.$this->numPanel.'" data-table="'.$table.'" class="panel-collapse collapse '.($this->numPanel == $this->activePanel ? "in":"").'">
	                        <div class="panel-body">
								<div style="text-align:center; width:100%">
									<i class="fa fa-cog fa-spin fa-3x fa-fw"></i>
								</div>
	                        </div>
	                    </div>
	                </div>
				';	
			}else{
				$this->result = '
					<div class="panel panel-default">
	                    <div class="panel-heading">
							<a class="'.($this->numPanel == $this->activePanel ? "":"collapsed").'" data-toggle="collapse_disabled" data-parent="#accordion" href="#collapse'.$this->numPanel.'">
	                        	<h4 class="panel-title text_e2sit_color2 e2sit_disabled">
									'.$title.'
									<div class="pull-right info-'.$table.'"><i class="fa fa-unlock-alt pull-right i-red" aria-hidden="true"></i></div>
	                        	</h4>
							</a>
	                    </div>
						<div id="collapse'.$this->numPanel.'" data-table="'.$table.'" class="panel-collapse collapse '.($this->numPanel == $this->activePanel ? "in":"").'">
	                        <div class="panel-body">
								<div style="text-align:center; width:100%">
									<i class="fa fa-cog fa-spin fa-3x fa-fw"></i>
								</div>
	                        </div>
	                    </div>
	                </div>
			';
			}
			
			
			$this->numPanel ++;

			return $this->result;
		}
		
        public function __destruct()
        {
        }
    }