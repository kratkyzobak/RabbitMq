<?php

namespace Kdyby\RabbitMq;

use PhpAmqpLib\Message\AMQPMessage;



/**
 * @author Alvaro Videla <videlalvaro@gmail.com>
 * @author Filip Procházka <filip@prochazka.su>
 */
class Producer extends AmqpMember implements IProducer
{

	/**
	 * @var string
	 */
	protected $contentType = 'text/plain';

	/**
	 * @var string
	 */
	protected $deliveryMode = 2;



	public function setContentType($contentType)
	{
		$this->contentType = $contentType;

		return $this;
	}



	public function setDeliveryMode($deliveryMode)
	{
		$this->deliveryMode = $deliveryMode;

		return $this;
	}



	protected function getBasicProperties()
	{
		return ['content_type' => $this->contentType, 'delivery_mode' => $this->deliveryMode];
	}


    /**
     * Publishes the message and merges additional properties with basic properties
     *
     * @param string $msgBody
     * @param string $routingKey IF not provided or set to null, used default routingKey from configuration of this producer
     * @param array $additionalProperties
     */
    public function publish($msgBody, $routingKey = '', $additionalProperties = array())
    {
        if ($this->autoSetupFabric) {
            $this->setupFabric();
        }

        if ($this -> routingKey && (func_num_args() <= 1 || $routingKey === NULL)) {
            // routingKey parameter not provided or set to NULL, use default
            $routingKey = $this -> routingKey ?: '';
        }

        $msg = new AMQPMessage((string) $msgBody, array_merge($this->getBasicProperties(), $additionalProperties));
        $this->getChannel()->basic_publish($msg, $this->exchangeOptions['name'], (string) $routingKey);
    }
}
