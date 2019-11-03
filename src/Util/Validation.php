<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 20.09.2016
 * Time: 13:35
 */
namespace Ceive\DataRecord\Util {
	
	use Ceive\DataRecord\Util\Validation\MessageInterface;
	use Ceive\DataRecord\Util\Validation\Validator;
	
	/**
	 * Class Validation
	 * @package Jungle\Data\Record\Util
	 */
	class Validation{

		/** @var  Validator[]  */
		protected $validators = [];

		/** @var  MessageInterface[]  */
		protected $last_messages = [];

		/**
		 * @param Validator $validator
		 * @return $this
		 */
		public function addValidator(Validator $validator){
			$this->validators[] = $validator;
			return $this;
		}
		
		/**
		 * @param $object
		 * @return MessageInterface[]
		 */
		public function validate($object){
			$this->last_messages = [];
			foreach($this->validators as $validator){
				$result = $validator->validate($object);
				if($result instanceof MessageInterface){
					$this->last_messages[] = $result;
				}
			}
			return $this->last_messages;
		}

		/**
		 * @return Validation\MessageInterface[]
		 */
		public function getMessages(){
			return $this->last_messages;
		}

	}
}

