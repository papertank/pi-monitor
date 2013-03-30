<?php namespace Monitor\Models;

class Report extends \Eloquent {

	public static $table = 'reports';

	public function logs()
	{
		return $this->has_many('Log');
	}

}