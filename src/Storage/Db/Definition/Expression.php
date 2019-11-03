<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 23.01.2017
 * Time: 6:39
 */
namespace Ceive\DataRecord\Storage\Db\Definition {

	use Ceive\DataRecord\Storage\Db\Definition\Query\QuerySelect;

	/**
	 * Class Expression
	 * @package Jungle\Data\Storage\Db\Definition\Expression
	 */
	abstract class Expression implements ExpressionInterface{

		public $type = 'expression';

		/**
		 * @param $column
		 * @return ExpressionReference
		 */
		public static function specifyColumn($column){
			return new ExpressionReference($column);
		}

		/**
		 * @return QuerySelect
		 */
		public static function specifySelect(){
			return new QuerySelect();
		}

		/**
		 * @param $name
		 * @param ...$arguments
		 * @return ExpressionFunction
		 */
		public static function specifyFunction($name,... $arguments){
			return new ExpressionFunction($name, $arguments);
		}

		/**
		 * @param ...$parts
		 * @return ExpressionComposite
		 */
		public static function specifyBlock(...$parts){
			return new ExpressionBlock(...$parts);
		}

		/**
		 * @param ...$any_of
		 * @return ExpressionComposite
		 */
		public static function specifyConditionAnyOf(... $any_of){
			return new ExpressionComposite(ExpressionComposite::OPERATOR_OR,$any_of);
		}

		/**
		 * @param ...$all_of
		 * @return ExpressionComposite
		 */
		public static function specifyConditionAllOf(... $all_of){
			return new ExpressionComposite(ExpressionComposite::OPERATOR_AND,$all_of);
		}



		/**
		 * @param $left
		 * @param $operator
		 * @param $right
		 * @param bool|false $isolated
		 * @return ExpressionCollation
		 */
		public static function specifyCollation($left, $operator, $right, $isolated = false){
			return new ExpressionCollation($left, $operator, $right, $isolated);
		}

		/**
		 * @param $raw
		 * @param array $params
		 * @param array $types
		 * @return ExpressionRaw
		 */
		public static function specifyRaw($raw, array $params = [ ], array $types = [ ]){
			return new ExpressionRaw($raw,$params,$types);
		}

		/**
		 * @param $column
		 * @return ExpressionReference
		 */
		public static function specifyReference($column){
			return new ExpressionReference($column);
		}

		/**
		 * @param $value
		 * @param null $type
		 * @param null $name
		 * @return ExpressionValue
		 */
		public static function specifyValue($value, $type = null, $name = null){
			return new ExpressionValue($value,$type,$name);
		}

		/**
		 * @param null $name
		 * @return ExpressionValueDeferred
		 */
		public static function specifyDeferred($name = null){
			if($name === null){
				return ExpressionValueDeferred::here();
			}else{
				return new ExpressionValueDeferred($name);
			}
		}

		/**
		 * @return ExpressionValueNull
		 */
		public static function specifyValueNull(){
			return ExpressionValueNull::here();
		}

		/**
		 * @return ExpressionSkipping
		 */
		public static function specifySkipping(){
			return ExpressionSkipping::here();
		}

	}
}

