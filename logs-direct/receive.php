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



list($queue_name,)=$channel->queue_declare("",false,false,true,false);

$serverities=array_slice($argv,1);

foreach($serverities as $key => $serverity){

	echo "we support log at $serverity level","\n";

}

foreach($serverities as $key => $serverity){

	$channel->queue_bind($queue_name,$exchange_name,$serverity);

}



$callback=function ($msg){

	echo $msg->body,"\n";

	$channel=$msg->delivery_info["channel"];

	$tag=$msg->delivery_info["delivery_tag"];

	$channel->basic_ack($tag);

};

$channel->basic_consume($queue_name,'',false,false,false,false,$callback);

while(count($channel->callbacks)){

	$channel->wait();

}



$channel->close();

$connection->close();



?>