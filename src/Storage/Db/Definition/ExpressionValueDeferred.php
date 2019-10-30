<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 29.01.2017
 * Time: 1:07
 */
namespace Jungle\Data\Storage\Db\Definition {
	
	/**
	 * Class ExpressionValueDeferred
	 * @package Jungle\Data\Storage\Db\Definition
	 */
	class ExpressionValueDeferred extends Expression{

		public $type = 'deferred';

		public $name;

		public $bind_type;

		/**
		 * @return ExpressionValueDeferred
		 */
		public static function here(){
			static $d;
			return !$d?$d = new self():$d;
		}

		public function __construct($name = null, $type = null){
			$this->name = $name;
			$this->bind_type = $type;
		}

		/**
		 * @param DefinitionProcessor $processor
		 * @return string
		 */
		public function render(DefinitionProcessor $processor){
			$processor->getCollector()->bind(null,$this->bind_type, $this->name );
			return $this->name?':'.$this->name:'?';
		}
	}
}

