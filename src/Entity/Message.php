<?php

namespace App\Entity;

use App\Repository\MessageRepository;
use Doctrine\ORM\Mapping as ORM;

/**
 * @ORM\Entity(repositoryClass=MessageRepository::class)
 * @ORM\Table(name="Messages")
 */
class Message
{
    /**
     * @ORM\Id
     * @ORM\GeneratedValue
     * @ORM\Column(type="integer")
     */
    private $id;

    /**
     * @ORM\Column(type="string")
     */
    private $value;

    /**
     * @ORM\Column(type="string")
     */
    private $profileId;

    /**
     * @ORM\Column(type="string")
     */
    private $timestamp;

    /**
     * @ORM\Column(type="string")
     */
    private $gatewayEui;

    /**
     * @ORM\Column(type="string")
     */
    private $endpointId;

    /**
     * @ORM\Column(type="string")
     */
    private $clusterId;

    /**
     * @ORM\Column(type="string")
     */
    private $attributeId;

    public function getId(){
        return $this->id;
    }

    public function getValue(){
        return $this->value;
    }
    
    public function setValue($value) {
        $this->value = $value;
    }

    public function getProfileId(){
        return $this->profileId;
    }

    public function setProfileId($profileId) {
        $this->profileId = $profileId;
    }

    public function getTimestamp(){
        return $this->timestamp;
    }

    public function setTimestamp($timestamp) {
        $this->timestamp = $timestamp;
    }

    public function getGatewayEui(){
        return $this->gatewayEui;
    }

    public function setGatewayEui($gatewayEui) {
        $this->gatewayEui = $gatewayEui;
    }

    public function getEndpointId(){
        return $this->endpointId;
    }

    public function setEndpointId($endpointId) {
        $this->endpointId = $endpointId;
    }

    public function getClusterId(){
        return $this->clusterId;
    }

    public function setClusterId($clusterId) {
        $this->clusterId = $clusterId;
    }

    public function getAttributeId(){
        return $this->attributeId;
    }

    public function setAttributeId($attributeId) {
        $this->attributeId = $attributeId;
    }

}
