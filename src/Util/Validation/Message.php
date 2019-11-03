<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 18.09.2016
 * Time: 17:15
 */
namespace Ceive\DataRecord\Util\Validation {

	use Ceive\DataRecord\Util\Exception;

	/**
	 * Class Message
	 * @package Jungle\Data\Record\Util\Validation
	 */
	abstract class Message extends Exception implements MessageInterface{

		/** @var  string */
		protected $type;

		/**
		 * Message constructor.
		 * @param string $type
		 * @param string $systemMessage
		 */
		public function __construct($type, $systemMessage = 'Unexpected Data'){
			$this->type = $type!==null?$type:$this->type;
			parent::__construct($systemMessage);
		}

		/**
		 * @return string
		 */
		public function getType(){
			return $this->type;
		}

	}
}

