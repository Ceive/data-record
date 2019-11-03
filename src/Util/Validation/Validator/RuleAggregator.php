<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 20.09.2016
 * Time: 14:44
 */
namespace Ceive\DataRecord\Util\Validation\Validator {
	
	use Ceive\DataRecord\Util\Validation\Message\ValidatorMessage;
	use Ceive\DataRecord\Util\Validation\MessageInterface;
	use Ceive\DataRecord\Util\Validation\Rule;
	use Ceive\DataRecord\Util\Validation\Validator;

	/**
	 * Class RuleAggregator
	 * @package Jungle\Data\Record\Util\Validation\Validator
	 */
	class RuleAggregator extends Validator{

		/** @var string  */
		protected $type = 'Field';

		/** @var  Rule[] */
		protected $rule_collection = [];

		/**
		 * RuleAggregator constructor.
		 * @param array $field_name
		 * @param Rule[] $rules
		 */
		public function __construct($field_name, array $rules ){
			$this->field_name = $field_name;
			$this->rule_collection = $rules;
		}

		/**
		 * @param Rule $rule
		 * @return $this
		 */
		public function addRule(Rule $rule){
			$this->rule_collection[] = $rule;
			return $this;
		}

		/**
		 * @param $object
		 * @return mixed
		 */
		protected function _expertize($object){
			$value = $object->{$this->field_name};
			$messages = [];
			foreach($this->rule_collection as $rule){
				$result = $rule->check($value);
				if($result instanceof MessageInterface){
					$messages[] = $messages;
				}
			}
			return empty($messages)?true:$messages;
		}

		/**
		 * @param MessageInterface[] $messages
		 * @return ValidatorMessage
		 */
		protected function _prepareMessage($messages){
			return new ValidatorMessage($this->type, $this->field_name, [], $messages);
		}
	}
}

