<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 24.01.2017
 * Time: 4:35
 */
namespace Ceive\DataRecord\Storage\Db\Definition {

	use Ceive\DataRecord\Storage\Db\Platforms;

	/**
	 * Class ExpressionBlock
	 * @package Jungle\Data\Storage\Db\Definition
	 */
	class ExpressionBlock extends Expression implements \ArrayAccess{

		use OperandRegulatorTrait;

		const OPERATOR_AND  = 'AND';

		const OPERATOR_OR   = 'OR';

		public $type = 'block';

		/** @var array  */
		protected $parts = [];

		/**
		 * ExpressionBlock constructor.
		 * @param ...$parts
		 */
		public function __construct(...$parts){
			$this->parts = $parts;
		}

		/**
		 * @param $expression
		 * @param string $delimiter_operator
		 * @return $this
		 */
		public function add($expression, $delimiter_operator = 'AND'){
			if($this->parts){
				$this->parts[] = $delimiter_operator;
			}
			$this->parts[] = $expression;
			return $this;
		}

		/**
		 * @see ExpressionBlock::add
		 * @param mixed $offset
		 * @param mixed $value
		 */
		public function offsetSet($offset, $value){
			if($this->parts){
				$this->parts[] = $offset;
			}
			$this->parts[] = $value;
		}

		public function offsetExists($offset){
			throw new \Exception('\ArrayAccess::offsetExists is not usable here in ExpressionBlock!');
		}
		public function offsetGet($offset){
			throw new \Exception('\ArrayAccess::offsetGet is not usable here in ExpressionBlock!');
		}
		public function offsetUnset($offset){
			throw new \Exception('\ArrayAccess::offsetUnset is not usable here in ExpressionBlock!');
		}


		/**
		 * @param DefinitionProcessor $processor
		 * @return string
		 */
		public function render(DefinitionProcessor $processor){
			$a = [];
			$collector = $processor->getCollector();
			$operator = true;
			foreach($this->parts as $part){
				if(is_string($part)){
					$operator = true;
					$a[] = strtoupper($part);
					continue;
				}
				if(!$operator){
					$a[] = 'AND';
				}
				if(is_array($part)){
					list($left ,$operator, $right) = $part;
					$a[] = ExpressionCollation::process(
						$left,$operator,$right, $processor, $collector, false,
						$this->operands_recognize[0], $this->operands_recognize[1]
					);
					$operator = false;
				}else{
					$a[] = $processor->processExpression($part,$collector);
				}
			}
			return '('.implode(' ',$a).')';
		}

	}
}

