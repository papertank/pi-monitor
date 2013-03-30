<?php

use Monitor\Libraries\Server, Monitor\Models\Report;

class Monitor_Reports_Task {

    public function run($arguments)
    {
    	$reports = Report::all();
    	echo "Reports: \n\n";
    	if ( $reports )
    	{
    		foreach ( $reports as $report )
    		{
    			echo $report->id."\t".$report->location.':'.$report->port."\t".$report->type."\t".$report->frequency."\t".( $report->active ? 'Active' : 'Disabled' )."\n";
    		}

    		return;
    	}

    	echo " -- No Reports Found. Use monitor::reports:create to create one -- \n";
    	return;
    }

    public function create($arguments)
    {
    	$params = array();
    	foreach ( $arguments as $argument )
    	{
    		if ( strpos($argument, ':') !== false )
    		{
    			list($key, $value) = explode(':', $argument, 2);
    			$params[$key] = $value;
    		}
    	}

    	if ( empty($params) )
    	{
    		echo "Error: No arguments passed";
    	}

    	$rules = array(
            'location' => 'required',
            'port' => 'numeric',
            'type' => 'required|in:available,changed',
            'frequency' => 'required|in:often,daily',
        );

        $validation = Validator::make($params, $rules);

        if ( $validation->fails() )
        {
        	echo "Please correct the following errors:\n";
        	echo implode("\n", $validation->errors->all());
        	return;
        }

        $report = Report::create(array(
        	'location' => $params['location'],
        	'port' => ( isset( $params['port'] ) ? $params['port'] : '80' ),
        	'type' => ( isset( $params['type'] ) ? $params['type'] : 'available' ),
        	'frequency' => ( isset( $params['frequency'] ) ? $params['frequency'] : 'often' ),
        	'active' => 1,
        ));

        echo "Succesfully created report\n";

    	return;
    }

    public function edit($arguments)
    {
    	$params = array();
    	foreach ( $arguments as $argument )
    	{
    		if ( strpos($argument, ':') !== false )
    		{
    			list($key, $value) = explode(':', $argument, 2);
    			$params[$key] = $value;
    		}
    	}

    	if ( empty($params) )
    	{
    		echo "Error: No arguments passed";
    	}

    	$rules = array(
    		'id' => 'required|exists:reports,id',
            'port' => 'numeric',
            'type' => 'in:available,changed',
            'frequency' => 'in:often,daily',
            'active' => 'numeric|in:1,0',
        );

        $validation = Validator::make($params, $rules);

        if ( $validation->fails() )
        {
        	echo "Please correct the following errors:\n";
        	echo implode("\n", $validation->errors->all());
        	return;
        }

        $report = Report::find($params['id']);

        if ( ! $report )
        {
        	echo "Could not find that report";
        	return;
        }

        $report->location = ( isset( $params['location'] ) ? $params['location'] : $report->location );
        $report->port = ( isset( $params['port'] ) ? $params['port'] : $report->port );
        $report->type = ( isset( $params['type'] ) ? $params['type'] : $report->type );
        $report->frequency = ( isset( $params['frequency'] ) ? $params['frequency'] : $report->frequency );

        $report->save();

        echo "Succesfully updated report\n";

    	return;
    }

    public function cron($arguments)
    {
    	$frequency = 'often';

    	if ( isset($arguments[0]) && !empty($arguments[0]) )
    	{
    		$frequency = $arguments[0];
    	}

    	if ( ! Server::online('google.com') )
    	{
    		$message = "Can't talk to Google, so what\'s the point in going on?";
    		\Log::info($message);
    		echo $message."\n";
    		return;
    	}

    	$reports = Report::where('active','=',1)
    							  ->where('frequency', '=', $frequency)
    							  ->get();

    	foreach ( $reports as $report )
    	{
    		if ( $report->type == 'available' )
    		{
    			$online = Server::online($report->location, $report->port);
    			if ( $report->online && ! $online )
    			{
    				Event::fire('monitor.server_down', array($report) );
    			}
    			elseif ( !$report->online && $online )
    			{
    				Event::fire('monitor.server_up', array($report) );
    			}
    		}
    	}

    	return;
    }

}