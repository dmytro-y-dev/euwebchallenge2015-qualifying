<?php

$config = array(
	'mysql' => array(
		'host' => 'localhost',
	 	'user' => 'root',
		'pass' => '',
	  'database' => 'jobs_scheduler'
	),
	'rabbitmq' => array(
		'host' => 'localhost',
		'port' => 5672,
	 	'user' => 'guest',
		'pass' => 'guest',
	  'vhost' => '/'
	)
);

define('AMQP_DEBUG', false);
