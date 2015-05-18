<?php

require_once __DIR__.'/vendor/autoload.php';

use PhpAmqpLib\Connection\AMQPConnection;

use PhpAmqpLib\Message\AMQPMessage;

$connection= new AMQPConnection("localhost",5672,"guest","guest");

$channel=$connection->channel();

$exchange_name="logs-direct";

$exchange_type="direct";

#args:exchange_name,exchange_type,passive,durable,auto_delete

$channel->exchange_declare($exchange_name,$exchange_type,false,false,false);



$index=0;

$severities=array("debug","info","warning","error");

while(true){

    $serverity_index=array_rand($severities,1);

    $serverity=$severities[$serverity_index];



	$line="The $index message is at $serverity level.";

	$index=$index+1;

	$message= new AMQPMessage($line);

	$channel->basic_publish($message,$exchange_name,$serverity);

	sleep(1);

}



$channel->close();

$connection->close();



?>