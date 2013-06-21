<?php

Autoloader::namespaces(array(
  'Monitor' => Bundle::path('monitor'),
));

Event::listen('monitor.server_down', function($report){

	$now = date('Y-m-d H:i:s');

	$log = Monitor\Models\Log::create(array(
		'report_id' => $report->id,
		'type' => 'offline',
		'message' => $report->location.' is DOWN',
		'created_at' => $now,
	));

	$report->online = 0;
	$report->save();

	$subject = "DOWN Alert: $report->location is offline";
	$message = $subject."\n $report->location is offline at $now";

	$postmark = new Postmark();
    $postmark->to(Config::get('monitor::email.to'));
    $postmark->from_name('Server Alerting');
    $postmark->subject($subject);
    $postmark->txt_body($message);

    $response = $postmark->send();
});

Event::listen('monitor.server_up', function($report){

	$now = date('Y-m-d H:i:s');

	$log = Monitor\Models\Log::create(array(
		'report_id' => $report->id,
		'type' => 'online',
		'message' => $report->location.' is UP',
		'created_at' => date('Y-m-d H:i:s'),
	));

	$report->online_at = date('Y-m-d H:i:s');
	$report->online = 1;
	$report->save();

	$subject = "UP Alert: $report->location is online";
	$message = $subject."\n $report->location is online at $now";

	$postmark = new Postmark();
    $postmark->to(Config::get('monitor::email.to'));
    $postmark->from_name('Server Alerting');
    $postmark->subject($subject);
    $postmark->txt_body($message);

    $response = $postmark->send();

});