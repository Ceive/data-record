<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 23.01.2017
 * Time: 7:21
 */
namespace Ceive\DataRecord\Storage\Db\Definition {

	use Ceive\DataRecord\Storage\Db\Connection;

	/**
	 * Class ExpressionValue
	 * @package Jungle\Data\Storage\Db
	 */
	class ExpressionValue extends Expression{

		/** @var  string|null binding name */
		public $name;

		/** @var mixed */
		public $value;

		/** @var  mixed|null  NUMBER|STRING */
		public $bind_type;

		/** @var null  */
		public $type = 'value';

		/**
		 * ExpressionValue constructor.
		 * @param $value
		 * @param null $type
		 * @param null $name
		 */
		public function __construct($value = null, $type = null, $name = null){
			$this->value = $value;
			$this->type = $type;
			$this->name = $name;
		}

		/**
		 * @param null $value
		 * @param null $type
		 * @param null $name
		 * @param bool|false $allow_null_object
		 * @return ExpressionValue|ExpressionValueNull
		 */
		public static function bindHere($value = null, $type = null, $name = null, $allow_null_object = false){
			if($value===null && in_array($type, [\PDO::PARAM_NULL,'null',null],true) && !$name && $allow_null_object){
				return ExpressionValueNull::here();
			}else{
				return new ExpressionValue($value, $type, $name);
			}
		}

		/**
		 * @param DefinitionProcessor $processor
		 * @return string
		 */
		public function render(DefinitionProcessor $processor){
			$collector = $processor->getCollector();
			$collector->bind($this->value,$this->bind_type, $this->name);
			$array = is_array($this->value) || in_array($this->bind_type,[Connection::PARAM_INT_ARRAY,Connection::PARAM_STR_ARRAY],true);
			return ($array?'(':'').($this->name?(':'.$this->name):'?').($array?')':'');
		}
	}
}

