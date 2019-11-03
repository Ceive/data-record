<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 24.01.2017
 * Time: 4:52
 */
namespace Ceive\DataRecord\Storage\Db\Definition {

	use Ceive\DataRecord\Storage\Db\Platform;

	/**
	 * Class ExpressionCollation
	 * @package Jungle\Data\Storage\Db\Definition
	 */
	class ExpressionCollation extends Expression{

		use OperandRegulatorTrait;

		const PLUS = '+';
		const MINUS = '-';
		const DIVISION = '/';
		const MULTIPLY = '*';

		const LIKE  = 'LIKE';
		const EQ    = '=';
		const NE    = '!=';
		const GT    = '>';
		const GE    = '>=';
		const GL    = '<>';
		const LT    = '<';
		const LE    = '<=';

		public $type = 'collation';

		/** @var  Expression|ExpressionSkipping|string|int|double|null */
		protected $left;

		/** @var  string */
		protected $operator;

		/** @var  Expression|ExpressionSkipping|string|int|double|null */
		protected $right;

		/** @var bool  */
		protected $isolated = false;

		/**
		 * Expression constructor.
		 * @param $left
		 * @param $operator
		 * @param $right
		 * @param bool $isolated
		 */
		public function __construct($left, $operator, $right, $isolated = false){
			$this->left     = $left;
			$this->operator = $operator;
			$this->right    = $right;
			$this->isolated = $isolated;
		}

		/**
		 * @param $operand
		 * @return Expression
		 */
		public static function only($operand){
			return new ExpressionCollation($operand,null,ExpressionSkipping::here());
		}

		/**
		 * @param $operand
		 * @param null $operator
		 * @return Expression
		 */
		public static function onlyLeft($operand, $operator = null){
			return new ExpressionCollation($operand,$operator,ExpressionSkipping::here());
		}

		/**
		 * @param $operand
		 * @param null $operator
		 * @return Expression
		 */
		public static function onlyRight($operand, $operator = null){
			return new ExpressionCollation(ExpressionSkipping::here(),$operator,$operand);
		}

		/**
		 * @param DefinitionProcessor|Platform $processor
		 * @return string
		 */
		public function render(DefinitionProcessor $processor){
			return self::process(
				$this->left,
				$this->operator,
				$this->right,
				$processor,
				null,
				$this->isolated,
				$this->operands_recognize[0],
				$this->operands_recognize[1]
			);
		}

		/**
		 * @param $left
		 * @param $operator
		 * @param $right
		 * @param DefinitionProcessor $combiner
		 * @param PayloadCollector $collector
		 * @param bool $isolated
		 * @param string $left_operand_priority
		 * @param string $right_operand_priority
		 * @return string
		 */
		public static function process($left, $operator, $right,
			DefinitionProcessor $combiner,
			PayloadCollector $collector = null,
			$isolated = false,
			$left_operand_priority = Platform::STR_AS_COLUMN,
			$right_operand_priority = Platform::STR_AS_VALUE
		){
			$a = [];
			if(!$collector)$collector = $combiner->getCollector();
			if(!$left instanceof ExpressionSkipping && ($left = $combiner->processExpression($left, $collector, $left_operand_priority)))
				$a[] = $left;
			if($operator)
				$a[] = $operator;
			if(!$right instanceof ExpressionSkipping && ($right = $combiner->processExpression($right, $collector, $right_operand_priority)))
				$a[] = $right;

			if($isolated){
				return '('.implode(' ', $a).')';
			}else{
				return implode(' ', $a);
			}
		}

	}
}

