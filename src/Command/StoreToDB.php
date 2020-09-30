<?php

namespace App\Command;

use Symfony\Component\Console\Command\Command;
use Symfony\Component\Console\Input\InputInterface;
use Symfony\Component\DependencyInjection\ContainerInterface;
use Symfony\Component\Console\Output\OutputInterface;
use App\Entity\Message;
use PhpAmqpLib\Connection\AMQPStreamConnection;
use Symfony\Component\Dotenv\Dotenv;

class StoreToDB extends Command
{
    // the name of the command (the part after "bin/console")
    protected static $defaultName = 'store-to-db';

    private $container;

    public function __construct(ContainerInterface $container)
    {
        parent::__construct();
        $this->container = $container;
    }

    protected function execute(InputInterface $input, OutputInterface $output)
    {

        function consumeFilteredResults($_connection, $callback){
            $_channel = $_connection->channel();
            // $queueName = 'raw_results';
            $queueName = $_ENV['QUEUE'];
            $res = $_channel->basic_consume($queueName, '', false, true, false, false, $callback);
            
            while (count($_channel->callbacks)) {
                echo "Waiting for messages. To exit press CTRL+C\n";
                $_channel->wait();
            }
            $_channel->close();
            return $res;
        }

        $writeToDB = function ($msg) {
        
            $bodyObj = json_decode($msg->body);
            $message = new Message();
        
            $message->setValue($bodyObj->value);
            $message->setProfileId($bodyObj->profileId);
            $message->setTimestamp($bodyObj->timestamp);
            $message->setGatewayEui($bodyObj->gatewayEui);
            $message->setEndpointId($bodyObj->endpointId);
            $message->setClusterId($bodyObj->clusterId);
            $message->setAttributeId($bodyObj->attributeId);
        
            $em = $this->container->get('doctrine')->getManager();

            $em->persist($message);
            $em->flush();
            echo " ----- Message with value " . $bodyObj->value . " received and saved to database.\n";
        };

        // $connection = new AMQPStreamConnection('localhost', 5672, 'guest', 'guest');
        $connection = new AMQPStreamConnection($_ENV['HOST_NAME'], 5672, $_ENV['USER_NAME'], $_ENV['PASSWORD']);

        consumeFilteredResults($connection, $writeToDB);

        $connection->close();

        return 1;
    }

}
