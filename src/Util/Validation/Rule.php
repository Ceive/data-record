<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 18.09.2016
 * Time: 15:06
 */
namespace Ceive\DataRecord\Util\Validation {

	/**
	 * Class Rule
	 * @package Jungle\Data\Record\Util\Schema\ValueType
	 */
	abstract class Rule extends ExpertizeAbstract implements ValueCheckerInterface{

		/**
		 * @param $value
		 * @param array $parameters
		 * @return bool
		 */
		public function check($value,array $parameters = []){
			return $this->expertize($value, $parameters);
		}

		/**
		 * @param $result
		 * @return \Ceive\DataRecord\Util\Validation\MessageInterface
		 */
		protected function _prepareMessage($result){
			return new Message\RuleMessage($this->type,$this->getParams());
		}


	}
}

