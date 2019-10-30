<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 20.01.2017
 * Time: 21:44
 */
namespace Jungle\Data\Record\Query {
	
	use Jungle\Util\Value\Massive;
	use Jungle\Util\Value\String;

	/**
	 * Class ConditionResolver
	 * @package Jungle\Data\Record\Query
	 *
	 * Компиляция Исходного запроса к Хранилищу
	 * на основе приведенных данных Условия и Базовой Схемы
	 *
	 * Высчитывание Джоинов исходя из аггрегационных путей используемых в условиях, инклудах и т.п
	 *
	 */
	class ConditionResolver{

		const TYPE_KEY = '::type';
		const TYPE_BLOCK = '(...)';

		protected $bracket_open     = '{';
		protected $bracket_close    = '}';

		/** @var  SimpleQuery */
		protected $query;

		protected $left;

		protected $operator;

		protected $right;

		/**
		 * @param SimpleQuery $query
		 * @return $this
		 */
		public function setQuery(SimpleQuery $query){
			$this->query = $query;
			return $this;
		}

		public function handleConditionBlock(array $block){
			$a = [];$b = false;
			foreach($block as $condition){
				if(is_array($condition)){
					if($condition && ($cc = $this->handleConditionArray($condition))){
						if($b) $a[] = 'AND';
						$b = true;
						$a[] = $cc;
					}
				}else{
					$b = false;
					$a[] = $condition?:'AND';
				}
			}
			return $a;
		}

		public function handleConditionArray(array $condition){
			if(isset($condition[self::TYPE_KEY])){
				if($condition[self::TYPE_KEY] === 'block'){
					unset($condition[self::TYPE_KEY]);
					return $this->handleConditionBlock($condition);
				}else{
					unset($condition[self::TYPE_KEY]);
				}
			}elseif(isset($condition[0]) && is_string($condition[0]) && $condition[0]===self::TYPE_BLOCK){
				array_shift($condition);
				return $this->handleConditionBlock($condition);
			}else{
				if(Massive::isAssoc($condition, true)){
					$a = [];
					foreach($condition as $k => $v){
						$a[] = ['{'.$k.'}','=',$v];
					}
					return $this->handleConditionBlock($a);
				}else{
					list($left, $operator, $right) = array_replace([null,null,null],$condition);
					return $this->handleCondition($left, $operator, $right);
				}
			}
			return null;
		}


		public function handleCondition($left, $operator, $right){
			$this->left     = $left;
			$this->operator = $operator;
			$this->right    = $right;
			if(String::isCovered($this->left,$this->bracket_open,$this->bracket_close)){
				$this->left = String::trimSides($this->left,$this->bracket_open,$this->bracket_close);
				$this->left = $this->handlePath($this->left);
			}
			if($this->operator){
				$this->operator = $this->handleOperator($this->operator);
				if($this->right && is_string($this->right)){
					if(String::isCovered($this->right,$this->bracket_open,$this->bracket_close)){
						$this->right = String::trimSides($this->right,$this->bracket_open,$this->bracket_close);
						$this->right = $this->handlePath($this->right);
					}
				}
			}
			return [$this->left, $this->operator,$this->right];
		}


		public function handleOperator($operator){
			if($operator === '=' && $this->right === null){
				return 'IS NULL';
			}elseif($operator === '!=' && $this->right === null){
				return 'IS NOT NULL';
			}
			return $operator;
		}


		public function handlePath($path){
			$extra = null;
			if(($pos = strpos($path,':')) !== false){
				$extra = substr($path, $pos+1);
				$path = substr($path, 0,$pos);
			}


			/**
			 * Здесь мы должны узнать что за путь был передан нам
			 * Это может быть путь к одиночным связям
			 * Путь к MANY связям
			 * Но один метод handlePath не может справиться с множественными аггрегациями.
			 * Потому-что нам нужно контролировать итоговое выходящее условие [left,operator,right]
			 * В случаях с аггрегациями, мы можем выдать [base.id, 'IN' | 'NOT IN', {SUB-QUERY}] [null, 'EXISTS' | 'NOT EXISTS', {SUB-QUERY}]
			 * В другом случае, мы можем задать осн. запросу JOIN{INNER|LEFT}, GROUP BY{base.id}, HAVING, используя
			 * присутствуюшие аггрегации в текущем объекте
			 */

			if(!isset($this->paths[$path])){
				$identifier = $this->match_path($path,$extra);
				$this->paths[$path] = $identifier;
				return [ 'identifier' => $identifier ];
			}
			return [ 'identifier' => $this->paths[$path] ];
		}

	}
}

