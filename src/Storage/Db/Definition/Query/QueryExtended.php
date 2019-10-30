<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 23.01.2017
 * Time: 2:49
 */
namespace Jungle\Data\Storage\Db\Definition\Query {

	use Jungle\Data\Storage\Db\Definition\ExpressionBlock;
	use Jungle\Data\Storage\Db\Definition\ExpressionReference;
	use Jungle\Data\Storage\Db\Definition\ExpressionValue;

	/**
	 * Class QueryExtended
	 * @package Jungle\Data\Storage\Db
	 */
	abstract class QueryExtended extends Query{

		const JOIN_TYPE_CROSS   = null;
		const JOIN_TYPE_INNER   = 'INNER';
		const JOIN_TYPE_LEFT    = 'LEFT';
		const JOIN_TYPE_RIGHT   = 'RIGHT';


		/** @var array [#=>type, #=>table, #=>alias, #=>on] */
		protected $joins = [];

		/** @var array [] */
		protected $where = [];

		/** @var  null|int */
		protected $limit;

		/** @var array [{identifier} => {direction:ASC|DESC}] | [{i} => {identifier}](direction:ASC) */
		protected $order_by = [];

		/**
		 * @param $table
		 * @param $alias
		 * @param $condition
		 * @param string $type
		 */
		public function join($table, $alias, $condition, $type = self::JOIN_TYPE_INNER){
			$this->joins[$alias?:$table] = [
				'type'  => $type,
				'table' => $table,
				'alias' => $alias,
				'on'    => $condition
			];
		}

		/**
		 * @param $table
		 * @param $alias
		 * @param $condition
		 */
		public function leftJoin($table, $alias, $condition){
			$this->joins[$alias?:$table] = [
				'type'  => self::JOIN_TYPE_LEFT,
				'table' => $table,
				'alias' => $alias,
				'on'    => $condition
			];
		}

		/**
		 * @param $table
		 * @param $alias
		 * @param $condition
		 */
		public function rightJoin($table, $alias, $condition){
			$this->joins[$alias?:$table] = [
				'type'  => self::JOIN_TYPE_RIGHT,
				'table' => $table,
				'alias' => $alias,
				'on'    => $condition
			];
		}

		/**
		 * @param array $collation
		 * @param string $operator_delimiter
		 * @param bool|true $merge
		 */
		public function wherePredicate(array $collation, $operator_delimiter = 'AND', $merge = true){
			if(!$merge) $this->where = [];
			if($this->where) $this->where[] = $operator_delimiter;
			foreach($collation as $column => $value){
				$this->where[] = [$column,'=', new ExpressionValue($value) ];
			}
		}

		/**
		 * @param array $collation
		 * @param string $operator_delimiter
		 * @param bool|true $merge
		 */
		public function whereCollate(array $collation, $operator_delimiter = 'AND', $merge = true){
			if(!$merge) $this->where = [];
			if($this->where) $this->where[] = $operator_delimiter;
			foreach($collation as $column => $value){
				$this->where[] = [$column,'=', new ExpressionReference($value) ];
			}
		}

		/**
		 * @param $condition
		 * @param string $operator
		 * @param $merge
		 * @return $this
		 */
		public function where($condition, $operator = 'AND', $merge = true){
			if(!$merge) $this->where = [];
			if($this->where) $this->where[] = $operator;
			$this->where[] = $condition;
			return $this;
		}

		/**
		 * @param array $conditions
		 * @param string $operator
		 * @param bool $wrap_in_block
		 * @param bool|true $merge
		 * @return $this
		 */
		public function whereArray(array $conditions, $operator = 'AND', $wrap_in_block = false, $merge =true){
			if(!$merge) $this->where = [];
			if($this->where) $this->where[] =$operator;
			if($wrap_in_block){
				$this->where[] = $block = new ExpressionBlock($conditions);
				if(is_array($wrap_in_block)){
					$block->operandsAs($wrap_in_block);
				}
			}else{
				foreach($conditions as $condition){
					$this->where[] = $condition;
				}
			}
			return $this;
		}

		/**
		 * @param $condition
		 * @param bool|true $merge
		 * @return $this
		 */
		public function andWhere($condition, $merge = true){
			return $this->where($condition,'AND',$merge);
		}

		/**
		 * @param $condition
		 * @param bool|true $merge
		 * @return $this
		 */
		public function orWhere($condition, $merge = true){
			return $this->where($condition,'OR',$merge);
		}

		/**
		 * @param string $column
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
		 * @param ...$who
		 * @return $this
		 */
		public function reset(...$who){
			foreach($who as $property){
				$this->{$property} = [];
			}
			return $this;
		}

		public function resetAll(){
			$this->limit    = null;
			$this->alias    = null;
			$this->source   = null;
			$this->priority = null;

			$this->order_by = [];
			$this->joins    = [];
			$this->where    = [];

		}

	}
}

