<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 19.09.2016
 * Time: 15:35
 */
namespace Jungle\Data\Record\Exception {

	use Jungle\Data\Record\Field\Field;
	use Jungle\Util\Data\Validation\Message\ValidatorMessage;

	/**
	 * Class FieldValidatorMessage
	 * @package Jungle\Data\Record\Field\Field
	 */
	class FieldValidatorMessage extends ValidatorMessage{

		/** @var  \Jungle\Data\Record\Field\Field */
		protected $field;

		/**
		 * FieldValidatorMessage constructor.
		 * @param Field $field
		 * @param \Jungle\Util\Data\Validation\MessageInterface[] $messages
		 * @param array $params
		 * @param string $systemMessage
		 */
		public function __construct(Field $field, array $messages,array $params = null, $systemMessage = ''){
			$this->field = $field;
			parent::__construct('FieldValidator', $field->getName(),$params, $messages, $systemMessage);
		}

		/**
		 * @return Field|string
		 */
		public function getField(){
			return $this->field;
		}

	}
}

