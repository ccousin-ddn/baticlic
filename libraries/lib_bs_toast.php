<?php 
    class toast{
		
		public $fa;
		public $title;
		public $time;
		public $body;
		
		private $result;

        public function __construct()
        {
			$this->result		= '';
        }

		public function create()
		{
			$html = '
			<div class="toast" role="alert" aria-live="assertive" aria-atomic="true" data-delay="100" data-autohide="false">
	            <div class="toast-header bg-red text-white">
	                <i class="fal '.$this->fa.' mr-2"></i>
	                <strong class="mr-auto">'.$this->title.'</strong>
	                
	                <button type="button" class="ml-2 mb-1 close text-white" data-dismiss="toast" aria-label="Close">
	                    <span aria-hidden="true">&times;</span>
	                </button>
	            </div>
	            <div class="toast-body bg-white">
	                '.$this->body.'<br>
					<strong class="text-danger">'.$this->time.'</strong>
	            </div>
	        </div>
			';
			$this->result = $html;
			return $this->result;
		}
		
        public function __destruct()
        {
        }
    }