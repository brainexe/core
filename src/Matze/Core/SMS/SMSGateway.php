<?php

namespace Matze\Core\SMS;

use NexmoMessage;

/**
 * @Service(public=false)
 */
class SMSGateway extends NexmoMessage {

	/**
	 * @var string
	 */
	protected $_sender;

	/**
	 * @Inject({"%sms.api_key%", "%sms.api_secret%", "%sms.sender%"})
	 */
	public function __construct($api_key, $api_secret, $sender) {
		parent::NexmoMessage($api_key, $api_secret);

		$this->_sender = $sender;
	}

	/**
	 * @param string $to
	 * @param string $message
	 * @param null $unicode
	 * @return array|bool|\stdClass
	 */
	public function sendText($to, $message, $unicode=null) {
		return parent::sendText($to, $this->_sender, $message, $unicode);
	}
} 
