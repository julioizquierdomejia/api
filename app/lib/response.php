<?php
namespace App\Lib;

class Response
{
	public $result     		= null;
	public $response   		= false;
	public $message    		= 'Error in proccess.';
	public $href       		= null;
	public $function   		= null;
	public $id_inserted 	= null;
	
	//public $filter     = null;
	
	public function SetResponse($response, $m = '')
	{
		$this->response = $response;
		$this->message = $m;

		if(!$response && $m = '') $this->response = 'Error in proccess.';
	}

	public function SetIdInserted($id_inserted, $m = '')
	{
		$this->id_inserted = $id_inserted;
	}
}