<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 24.01.2017
 * Time: 4:27
 */
namespace Jungle\Data\Storage\Db\Definition {

	/**
	 * Class ExpressionComposite
	 * @package Jungle\Data\Storage\Db\Definition
	 */
	class ExpressionComposite extends ExpressionBlock{

		use OperandRegulatorTrait;

		/** @var  string */
		protected $operator = self::OPERATOR_AND;

		/** @var array  */
		protected $parts = [];

		/**
		 * ExpressionComposite constructor.
		 * @param $operator
		 * @param array $parts
		 */
		public function __construct($operator,array $parts){
			$this->parts = $parts;
			$this->operator = $operator;
		}

		/**
		 * @param DefinitionProcessor $processor
		 * @return string
		 */
		public function render(DefinitionProcessor $processor){
			$a = [];
			$collector = $processor->getCollector();
			foreach($this->parts as $part){
				if(is_array($part)){
					list($left ,$operator, $right) = $part;
					$a[] = ExpressionCollation::process(
						$left,$operator,$right, $processor, $collector, false,
						$this->operands_recognize[0], $this->operands_recognize[1]
					);
				}else{
					$a[] = $processor->processExpression($part,$collector,$this->operands_recognize[0]); // expression
				}
			}
			return '('.implode(') '.$this->operator.' (', $a).')';
		}

	}
}

