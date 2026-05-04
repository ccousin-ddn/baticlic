<?php 
    class checkbox{
		
		private $result;
		private $box;
		private $numbox;
		private $index;

        public function __construct($index)
        {
        	$this->box			= "";
			$this->numbox		= 0;
			$this->result		= "";
			$this->index		= $index;
        }

		public function createDiv()
		{
			$div = '
			<div class="row p-3">
				'.$this->box.'
			</div>
			';
			return $div;
		}
		
		public function createBox($class='', $idrecord, $text, $select) // class='inp-opt', idrecord=$idoption, text=$value
		{
			$checked = $select == 1 ? 'checked' : '';
			$this->box .= '
				<div class="custom-control custom-checkbox col-md-6 col-lg-4 col-xl-3 mb-2">
					<input type="checkbox" class="custom-control-input '.$class.'" id="'.$class.'-'.$this->index.'-'.$this->numbox.'" value="'.$idrecord.'" '.$checked.'>
					<label class="custom-control-label" for="'.$class.'-'.$this->index.'-'.$this->numbox.'">'.$text.'</label>
				</div>
			';
			$this->numbox ++;
		}
		
        public function __destruct()
        {
        }
    }