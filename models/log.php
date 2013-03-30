<?php namespace Monitor\Models;

class Log extends \Eloquent {

	public static $table = 'logs';
	public static $timestamps = false;

	public function report()
	{
		return $this->belongs_to('Report');
	}

}