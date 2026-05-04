<?php 
    class listgroup{
		private $tablename;
		private $table;
		private $level;
		
		private $items;
		private $result;

        public function __construct($tablename, $level)
        {
        	$this->tablename 		= $tablename;
			//$this->table			= new $tablename();
			$this->level			= $level;
			
			$this->result			= "";
			$this->items			= "";
        }
		
		public function createItem($idrecord, $content)
		{
			$this->items .= '
				<a class="list-group-item list-group-item-action p-3" data-id="'.$idrecord.'" data-target="#s'.$idrecord.'">
					<div class="row">
			            '.$content.'
			        </div>
				</a>
			';
			return $this->items;
		}
		
		public function createList($idparent)
		{
			$this->result = '
				<div class="list-group" id="'.$this->tablename.'_list" role="tablist" data-idparent="'.$idparent.'">
			';
			$this->result .= $this->items;
			$this->result .= '
					<div class="list-group-item list-group-item-action list-add-line text-center" onclick="listgroupAction(\''.$this->tablename.'\',\''.$idparent.'\',\'\',\'newCardLine\')"><i class="fal fa-plus"></i></div>
                </div>
			';
			
			return $this->result;
		}
		
        public function __destruct()
        {
        }
    }