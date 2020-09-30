<?php

// src/Command/CreateUserCommand.php
namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\Console\Output\OutputInterface;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use PhpAmqpLib\Message\AMQPMessage;
use Symfony\Component\Console\Input\InputArgument;
use Symfony\Component\Dotenv\Dotenv;

class ConsumeData extends Command
{
    // the name of the command (the part after "bin/console")
    protected static $defaultName = 'consume-data';

    protected function configure()
    {
        $this
            ->addArgument('number_of_messages', InputArgument::OPTIONAL, 'How many messages do you want to consume?');
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        // Read the arguments
        $numberOfMessages = $input->getArgument('number_of_messages');
        if ($numberOfMessages == null) {
            $numberOfMessages = 1;
        }

        // Use of this function for proper conversion from hexadecimal to decimal. There were problems with the default hexdec() function for greater numbers.
        function bcHexDec($hex)
        {
            $dec = 0;
            $len = strlen($hex);
            for ($i = 1; $i <= $len; $i++) {
                $dec = bcadd($dec, bcmul(strval(hexdec($hex[$i - 1])), bcpow('16', strval($len - $i))));
            }
            return $dec;
        }

        // Create routing key
        function createRoutingKey($jsonObject)
        {
            $_routingKey = array();
            foreach ($jsonObject as $key => $value) {
                $value = (string) $value;
                $_routingKey[count($_routingKey)] = bcHexDec($value);
            }
            return implode(".", array_slice($_routingKey, 0, 5));
        }

        // Create payload
        function createPayload($jsonObject)
        {
            $arr_payload = array(
                'gatewayEui' => bcHexDec($jsonObject->gatewayEui),
                'profileId' => bcHexDec($jsonObject->profileId),
                'endpointId' => bcHexDec($jsonObject->endpointId),
                'clusterId' => bcHexDec($jsonObject->clusterId),
                'attributeId' => bcHexDec($jsonObject->attributeId),
                'value' => $jsonObject->value,
                'timestamp' => $jsonObject->timestamp
            );
            return json_encode($arr_payload);
        }

        for ($i = 0; $i < $numberOfMessages; $i++) {

            $apiData = file_get_contents($_ENV['MESSAGES_FROM_API_URL']);
            $apiDataObj = json_decode($apiData);

            $routingKey = createRoutingKey($apiDataObj);
            $payload = createPayload($apiDataObj);
            
            $exchangeName = $_ENV['EXCHANGE'];
            $queueName = $_ENV['QUEUE'];
            
            // for localhost testing
            // $connection = new AMQPStreamConnection('localhost', 5672, 'guest', 'guest');

            $connection = new AMQPStreamConnection($_ENV['HOST_NAME'], 5672, $_ENV['USER_NAME'], $_ENV['PASSWORD']);
            $channel = $connection->channel();

            // for localhost testing
            // $channel->exchange_declare(
            //     $exchangeName,
            //     'direct', # type
            //     false,    # passive
            //     false,    # durable
            //     false     # auto_delete
            // );
            // $channel->queue_declare($queueName, false, false, false, false);
            // $channel->queue_bind($queueName, $exchangeName, $routingKey);

            $msg = new AMQPMessage($payload);
            $channel->basic_publish($msg, $exchangeName, $routingKey);
            echo "Message with routing key: " . $routingKey . " sended to " . $queueName . " queue.\n";
            $channel->close();
            $connection->close();
        }
        return 1;
    }
}
