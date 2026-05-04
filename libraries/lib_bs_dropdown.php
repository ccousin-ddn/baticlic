<?php 
    class dropdown{
		private $tablename;
		private $values;
		
		private $items;
		private $result;

        public function __construct($tablename, $values)
        {
        	$this->tablename 		= $tablename;
			$this->values 			= $values;
			
			$this->result			= "";
			$this->items			= "";
        }
		
		public function createMenu()
		{
			$this->createItems();
			$this->result .= '
				<div class="dropdown-menu dropdown-menu-right">
					'.$this->items.'
				</div>
			';
			return $this->result;
		}
		
		private function createItems()
		{
			foreach($this->values as $item)
			{
				$this->items .= '
					<a class="dropdown-item" data-id="'.encrypt($item['id']).'">'.$item['val'].'</a>
				';
			}
		}
		
        public function __destruct()
        {
        }
    }