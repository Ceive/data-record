<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 20.01.2017
 * Time: 23:25
 */
namespace Ceive\DataRecord\Query {
	
	class SimpleQuery{

		/**
		 * Тем не менее в нутри каждой составляющей может содержаться
		 * Под-Запрос (where, having, joins, columns((SELECT) as count)).
		 * Аггрегационная функция (where, having, columns(COUNT(*) as count))
		 */

		/** @var array */
		public $columns     = [];

		/** @var null  */
		public $table       = null;

		/** @var null  */
		public $alias       = null;

		/** @var array  */
		public $joins       = [];

		/** @var array  */
		public $having      = [];

		/** @var array  */
		public $order_by    = [];

		/** @var array  */
		public $group_by    = [];

		/** @var array  */
		public $where       = [];


		/**
		 * @param $source
		 * @param null $alias
		 * @return $this
		 */
		public function base($source, $alias = null){
			$this->table = $source;
			$this->alias = $alias;
			return $this;
		}

		/**
		 * @param $definition
		 * @param null $alias
		 * @return $this
		 */
		public function column($definition, $alias = null){
			$this->columns[$alias?$alias:$definition] = $definition;
			return $this;
		}

		/**
		 * @param bool|false $merge
		 * @param \array[] ...$columns
		 * @return $this
		 */
		public function columns($merge = false, array ...$columns){
			if(!$merge)$this->columns = [];
			foreach($columns as list($definition, $alias)){
				$this->columns[$alias?$alias:$definition] = $definition;
			}
			return $this;
		}

		/**
		 * @param $source
		 * @param null $alias
		 * @param null $condition
		 * @param null $type
		 * @return $this
		 */
		public function join($source, $alias = null, $condition = null, $type = null){
			$join = [
				'type' => $type,
				'condition' => $condition,
				'table' => $source,
				'alias' => $alias
			];
			if($alias)  $this->joins[$alias] = $join;
			else        $this->joins[] = $join;

			return $this;
		}

		/**
		 * @param $condition
		 * @param bool|true $merge
		 * @return $this
		 */
		public function having($condition, $merge = true){
			$this->having = $merge?array_merge($this->having, $condition):$condition;
			return $this;
		}

		/**
		 * @param $column
		 * @param bool|true $merge
		 * @return $this
		 */
		public function groupBy($column, $merge = true){
			if(!$merge)$this->order_by = [];
			$this->group_by[$column] = 1;
			return $this;
		}

		/**
		 * @param $column
		 * @param string $direction
		 * @param bool|true $merge
		 * @return $this
		 */
		public function orderBy($column, $direction = 'ASC', $merge = true){
			if(!$merge)$this->order_by = [];
			$this->order_by[$column] = $direction;
			return $this;
		}

		/**
		 * @param $condition
		 * @param bool|true $merge
		 * @return $this
		 */
		public function where($condition, $merge = true){
			$this->where = $merge?array_merge($this->where, $condition):$condition;
			return $this;
		}

		/**
		 * @param ...$who
		 * @return $this
		 */
		public function reset(...$who){
			foreach($who as $property){
				$this->{$property} = [];
			}
			return $this;
		}

		/**
		 * @param $function_name
		 * @param ...$arguments
		 * @return string
		 */
		public function func($function_name, ...$arguments){
			return $function_name.'('. implode(', ',$arguments) .')';
		}

		/**
		 * @param $function_name
		 * @param array $arguments
		 * @return string
		 */
		public function funcArray($function_name,array $arguments = []){
			return $function_name.'('. implode(', ',$arguments) .')';
		}


	}
}

