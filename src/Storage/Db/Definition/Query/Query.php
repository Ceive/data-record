<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 23.01.2017
 * Time: 10:56
 */
namespace Ceive\DataRecord\Storage\Db\Definition\Query {
	
	use Ceive\DataRecord\Storage\Db\Definition\Definition;

	/**
	 * Class Query
	 * @package Jungle\Data\Storage\Db
	 *
	 * @TODO Поддержка вложенных запросов (Под-запросы в columns, where, joins)
	 * @TODO Гибкая поддержка функций и их аргументов (Функции в columns, where, having, joins)
	 *
	 * @JOIN
	 *      SOURCE_IDENTIFIER | SUB_QUERY
	 *      ALIAS_IDENTIFIER - define
	 *      CONDITION_BLOCK
	 *
	 * @COLUMN
	 *      SPECIAL_MARK[ * , COLUMN_IDENTIFIER.* ]
	 *      COLUMN_IDENTIFIER | COLUMN_DEFINITION[aggregate function, SUB_QUERY]
	 *      ALIAS_IDENTIFIER - define
	 *
	 * @FUNCTION
	 *      FUNCTION_IDENTIFIER - string
	 *      FUNCTION_ARGUMENT[] comma separated list EXPRESSION
	 *
	 * @FUNCTION_ARGUMENT
	 *      EXPRESSION
	 *
	 * @EXPRESSION
	 *      OPERATOR + IDENTIFY_SPECIFIER | FUNCTION | SUB_QUERY | EXPRESSION
	 *      IDENTIFY_SPECIFIER | FUNCTION | SUB_QUERY | EXPRESSION
	 *
	 * @CONDITION_BLOCK
	 *      CONDITION_DELIMITER_OPERATOR
	 *      CONDITION_EXPRESSION
	 *      CONDITION_BLOCK
	 *
	 * @CONDITION_DELIMITER_OPERATOR - AND | OR
	 * @CONDITION_EXPRESSION
	 *      [{OPERAND} {OPERATOR} {OPERAND}]
	 * @OPERAND
	 *      EXPRESSION
	 *
	 *
	 * @IDENTIFY_SPECIFIER: SOURCE_IDENTIFIER | ALIAS_IDENTIFIER
	 * @SOURCE_IDENTIFIER: `DATABASE_SPECIFIER`.`TABLE_SPECIFIER` | `TABLE_SPECIFIER`
	 *      DATABASE_SPECIFIER - `database`
	 *      TABLE_SPECIFIER - `table`
	 *      `database`.`table` - dotted separated
	 *      ------------------------
	 *      TABLE_SPECIFIER - `table`
	 *
	 * @ALIAS_IDENTIFIER: `alias`; кастомно Определяется в запросе(define)
	 *
	 *
	 *
	 * > EXPRESSION
	 * > SUB_QUERY
	 *
	 *
	 */
	abstract class Query extends Definition{

		/** @var  string SourceSpecifier */
		protected $source;

		/** @var  string AliasSpecifier */
		protected $alias;

		/** @var  bool|null */
		protected $priority;

		/**
		 * @param $source
		 * @param $alias
		 * @return $this
		 */
		public function source($source, $alias = null){
			$this->source = $source;
			$this->alias = $alias;
			return $this;
		}


		/**
		 * @param bool|null $priority
		 * @return $this
		 */
		public function priority($priority = null){
			$this->priority = $priority;
			return $this;
		}

	}
}

