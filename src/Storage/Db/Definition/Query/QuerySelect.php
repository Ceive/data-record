<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 23.01.2017
 * Time: 2:44
 */
namespace Ceive\DataRecord\Storage\Db\Definition\Query {

	use Ceive\DataRecord\Storage\Db\Definition\DefinitionProcessor;
	use Ceive\DataRecord\Storage\Db\Definition\Expression;
	use Ceive\DataRecord\Storage\Db\Definition\ExpressionBlock;
	use Ceive\DataRecord\Storage\Db\Definition\ExpressionReference;
	use Ceive\DataRecord\Storage\Db\Definition\ExpressionValue;

	/**
	 * Class QuerySelect
	 * @package Jungle\Data\Storage\Db
	 */
	class QuerySelect extends QueryExtended{

		/** @var array [{alias} => {definition: QuerySelect|Expression|string}] */
		protected $columns = [];

		/** @var array [{i} => {column_identifier}] */
		protected $group_by = [];

		/** @var array as where, but identifier must be for aggregations */
		protected $having = [];

		/** @var  null|int */
		protected $offset;

		/** @var bool  */
		protected $shared_mode = false;

		/** @var bool  */
		protected $for_update = false;

		/** @var null|bool HIGH_PRIORITY | STRAIGHT_JOIN */
		protected $priority;

		/** @var null|bool  */
		protected $result_scale;

		/** @var null|bool  */
		protected $calc_total;

		/** @var  null|bool */
		protected $native_cache;



		/**
		 * @param string $column
		 * @param bool|true $merge
		 * @return $this
		 */
		public function groupBy($column, $merge = true){
			if(!$merge)$this->order_by = [];
			$this->group_by[$column] = 1;
			return $this;
		}

		/**
		 * @param $limit
		 * @param null $from_offset
		 * @return $this
		 */
		public function limit($limit, $from_offset = null){
			$this->limit = $limit;
			if($from_offset!==null)$this->offset = $from_offset;
			return $this;
		}

		/**
		 * @param $from_offset
		 * @return $this
		 */
		public function offset($from_offset){
			$this->offset = $from_offset;
			return $this;
		}

		public function resetAll(){
			parent::resetAll();
			$this->offset = null;
			$this->native_cache = null;
			$this->calc_total = null;
			$this->result_scale = null;
			$this->for_update = null;
			$this->shared_mode = null;
			$this->group_by = [];
			$this->columns = [];
			$this->having = [];
		}


		/**
		 * @param $condition
		 * @param string $operator
		 * @param $merge
		 * @return $this
		 */
		public function having($condition, $operator = 'AND', $merge = true){
			if(!$merge) $this->having = [];
			if($this->having) $this->having[] = $operator;
			$this->having[] = $condition;
			return $this;
		}

		/**
		 * @param $condition
		 * @param bool|true $merge
		 * @return $this
		 */
		public function andHaving($condition, $merge = true){
			return $this->having($condition,'AND',$merge);
		}

		/**
		 * @param $condition
		 * @param bool|true $merge
		 * @return $this
		 */
		public function orHaving($condition, $merge = true){
			return $this->having($condition,'OR',$merge);
		}

		/**
		 * @param array $conditions
		 * @param string $operator
		 * @param bool|true $merge
		 * @return $this
		 */
		public function havingPredicate(array $conditions, $operator = 'AND', $merge = true){
			if(!$merge) $this->where = [];
			if($this->where) $this->where[] = $operator;
			foreach($conditions as $column => $value){
				$this->where[] = [$column,'=', new ExpressionValue($value) ];
			}
			return $this;
		}

		/**
		 * @param array $collate
		 * @param string $operator
		 * @param bool|true $merge
		 * @return $this
		 */
		public function havingCollate(array $collate, $operator = 'AND', $merge = true){
			if(!$merge) $this->having = [];
			if($this->having) $this->having[] = $operator;
			foreach($collate as $column => $value){
				$this->having[] = [$column,'=', new ExpressionReference($value) ];
			}
			return $this;
		}
		/**
		 * @param array $conditions
		 * @param string $operator
		 * @param bool|true $merge
		 * @return $this
		 */
		public function havingArray(array $conditions, $operator = 'AND', $wrap_in_block = false, $merge =true){
			if(!$merge) $this->having = [];
			if($this->having) $this->having[] = $operator;
			if($wrap_in_block){
				$this->having[] = $block = new ExpressionBlock($conditions);
				if(is_array($wrap_in_block)){
					$block->operandsAs($wrap_in_block);
				}
			}else{
				foreach($conditions as $condition){
					$this->having[] = $condition;
				}
			}
			return $this;
		}
		/**
		 * @param string $column
		 * @param null $alias
		 * @param bool|true $merge
		 * @return $this
		 */
		public function column($column, $alias = null, $merge = true){
			if(!$merge) $this->columns = [];
			if($alias){
				$this->columns[$alias] = $column;
			}else{
				$this->columns[] = $column;
			}
			return $this;
		}

		/**
		 * @param QuerySelect|\Ceive\DataRecord\Storage\Db\Definition\Expression|string $definition
		 * @param $alias
		 * @param bool $merge
		 * @return $this
		 */
		public function columnExpression($definition, $alias = null, $merge = true){
			if(!$merge) $this->columns = [];
			if(is_array($definition)){
				$definition = array_replace(['',[],[]],$definition);
				$definition = Expression::specifyRaw($definition[0],$definition[1],$definition[2]);
			}elseif(is_string($definition)){
				$definition = Expression::specifyRaw($definition);
			}
			if($alias){
				$this->columns[$alias] = $definition;
			}else{
				$this->columns[] = $definition;
			}
			return $this;
		}

		/**
		 * @param bool|true $shared
		 * @return $this
		 */
		public function shared($shared = true){
			$this->shared_mode = $shared;
			return $this;
		}

		/**
		 * @param bool|true $forUpdate
		 * @return $this
		 */
		public function forUpdate($forUpdate = true){
			$this->for_update = $forUpdate;
			return $this;
		}

		/**
		 * @param null $scale
		 * SQL_BIG_RESULT | SQL_SMALL_RESULT
		 * @return $this
		 */
		public function scale($scale = null){
			$this->result_scale = $scale;
			return $this;
		}

		/**
		 * @param null $cache
		 * SQL_CACHE | SQL_NO_CACHE
		 * @return $this
		 */
		public function cache($cache = null){
			$this->native_cache = $cache;
			return $this;
		}

		/**
		 * @param null $calc
		 * @return $this
		 */
		public function calcTotal($calc = null){
			$this->calc_total = $calc;
			return $this;
		}

		public function render(DefinitionProcessor $processor){
			return $processor->prepareSelect($this);
		}

	}
}

