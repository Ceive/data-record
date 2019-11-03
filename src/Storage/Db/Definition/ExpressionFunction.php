<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 23.01.2017
 * Time: 2:37
 */
namespace Ceive\DataRecord\Storage\Db\Definition {

	/**
	 * Class ExpressionFunction
	 * @package Jungle\Data\Storage\Db
	 */
	class ExpressionFunction extends Expression{

		public $type = 'function';

		/** @var string */
		public $name;

		/** @var Expression[]|ExpressionValue[]|string */
		public $arguments = [];

		/**
		 * ExpressionFunction constructor.
		 * @param $name
		 * @param array $arguments
		 */
		public function __construct($name, array $arguments){
			$this->name = $name;
			$this->arguments = $arguments;
		}

		/**
		 * @param DefinitionProcessor $processor
		 * @return string
		 */
		public function render(DefinitionProcessor $processor){
			$a = [];
			$collector = $processor->getCollector();
			foreach($this->arguments as $arg){
				$a[] = $processor->processExpression($arg, $collector,$processor::STR_AS_COLUMN);
			}
			return "{$this->name}(". implode(', ', $a) .")";
		}

	}
}

