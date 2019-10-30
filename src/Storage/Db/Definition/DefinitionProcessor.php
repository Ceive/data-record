<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 29.01.2017
 * Time: 19:15
 */
namespace Jungle\Data\Storage\Db\Definition {
	
	use Jungle\Data\Storage\Db\Definition\Query\QueryDelete;
	use Jungle\Data\Storage\Db\Definition\Query\QueryInsert;
	use Jungle\Data\Storage\Db\Definition\Query\QuerySelect;
	use Jungle\Data\Storage\Db\Definition\Query\QueryUpdate;

	/**
	 * Class DefinitionProcessor
	 * @package Jungle\Data\Storage\Db\Definition
	 */
	abstract class DefinitionProcessor{

		const STR_AS_COLUMN = 'column';
		const STR_AS_VALUE  = 'value';
		const STR_AS_RAW    = 'raw';

		/** @var  PayloadCollector */
		protected $collector;

		/** @var  int  */
		protected $processing_nesting_level = 0;

		/** @var  string  */
		protected $escape_char = '`';

		/** @var  string  */
		protected $column_mask_char = '*';

		/** @var  string  */
		protected $path_delimiter = '.';


		public function __construct(){
			$this->collector = new PayloadCollector();
		}

		/**
		 * @return PayloadCollector
		 */
		public function getCollector(){
			return $this->collector;
		}

		/**
		 * @return int
		 */
		public function getNestingLevel(){
			return $this->processing_nesting_level;
		}

		/**
		 * @return $this
		 */
		public function nestingDeeper(){
			$this->processing_nesting_level++;
			return $this;
		}

		/**
		 * @return $this
		 */
		public function nestingHigher(){
			if($this->processing_nesting_level > 0){
				$this->processing_nesting_level--;
			}
			return $this;
		}



		/**
		 * @param QueryInsert $query
		 * @return PayloadCollector
		 * @throws \Exception
		 */
		public function prepareInsert(QueryInsert $query){
			try{
				$this->nestingDeeper();

				$collector = $this->collector;
				if($this->processing_nesting_level === 1){
					$collector->reset();
				}

				/**
				 * @var $source
				 * @var $source_collection
				 * @var $priority
				 * @var $on_duplicate
				 * @var $columns
				 */
				extract($query->getProperty([
					'source', 'columns', 'source_collection',
					'priority', 'on_duplicate'
					// TODO priority, on_duplicate
				]));

				$source = $this->sourceSpecifier($source);

				if(!$source_collection){
					$collector->setRaw("INSERT INTO {$source}() VALUES ();");
					return $collector;
				}

				$sql = 'INSERT INTO';

				$sql.= ' '.$this->sourceSpecifier($source);
				$sql.='('.$this->columnListSpecifier($columns).')';
				$sql.=' VALUES ';
				if($source_collection instanceof QuerySelect){
					$sql.= '(' . $this->prepareSelect($source_collection) . ')';
				}else{
					$_ = [];
					foreach($source_collection as $item){
						$a = [];
						foreach($item as $value){
							$a[] = $this->processExpression($value, $collector);
						}
						$_[] = '('.implode(', ', $a).')';
					}
					$sql.= implode(', ',$_);
				}
				$collector->setRaw($sql.';');
				return $collector;
			}finally{
				$this->nestingHigher();
			}
		}



		/**
		 * @param QueryDelete $query
		 * @return PayloadCollector
		 */
		public function prepareDelete(QueryDelete $query){
			try{
				$this->nestingDeeper();
				$collector = $this->collector;
				if($this->processing_nesting_level === 1){
					$collector->reset();
				}

				/**
				 *
				 * @var $priority
				 * @var $source
				 * @var $alias
				 * @var $joins
				 * @var $where
				 * @var $order_by
				 * @var $limit
				 *
				 */
				extract($query->getProperty([
					'priority', 'source', 'alias', 'joins',  'where', 'order_by', 'limit',
				]));

				$sql = 'DELETE';
				$sql.=' '.$this->sourceSpecifier($source) . $this->processAliasSQL($alias);
				if($joins){
					$sql.=' '.$this->processJoinsSql($joins);
				}
				if($where && $where = $this->processConditional($where)){
					$sql.=' WHERE '.$where;
				}
				if($order_by){
					$sql.=' ' . $this->processOrderBySQL($order_by);
				}
				if($limit){
					$sql.=' ' . $this->processLimitSQL($limit);
				}

				$collector->setRaw($sql.';');
				return $collector;
			}finally{
				$this->nestingHigher();
			}
		}

		/**
		 * @param QueryUpdate $query
		 * @return PayloadCollector
		 */
		public function prepareUpdate(QueryUpdate $query){
			try{
				$this->nestingDeeper();
				$collector = $this->collector;
				if($this->processing_nesting_level === 1){
					$collector->reset();
				}
				/**
				 * @var $priority
				 * @var $source
				 * @var $alias
				 * @var $joins
				 * @var $assigning
				 * @var $where
				 * @var $order_by
				 * @var $limit
				 */
				extract($query->getProperty([
					'priority', 'source', 'alias', 'joins',
					'assigning', 'where', 'order_by', 'limit',
				]));
				$sql = 'UPDATE';
				$sql.=' '.$this->sourceSpecifier($source) . $this->processAliasSQL($alias);
				if($joins){
					$sql.=' '.$this->processJoinsSql($joins);
				}
				if($assigning){
					$sql.=' '.$this->processAssigning($assigning);
				}
				if($where && $where = $this->processConditional($where)){
					$sql.=' WHERE '.$where;
				}
				if($order_by){
					$sql.=' ' . $this->processOrderBySQL($order_by);
				}
				$collector->setRaw($sql.';');
				return $collector;
			}finally{
				$this->nestingHigher();
			}
		}

		/**
		 * @param QuerySelect $query
		 * @return PayloadCollector|string
		 */
		public function prepareSelect(QuerySelect $query){
			try{
				$this->nestingDeeper();
				$collector = $this->collector;
				$nested = false;
				if($this->processing_nesting_level === 1){
					$collector->reset();
				}elseif($this->processing_nesting_level > 1){ // используется как вложенный запрос.
					$nested = true;
				}
				/**
				 * @var $columns
				 * @var $source
				 * @var $alias
				 * @var $joins
				 * @var $priority
				 * @var $where
				 * @var $group_by
				 * @var $order_by
				 * @var $having
				 * @var $offset
				 * @var $limit
				 *
				 * @var $for_update
				 * @var $shared_mode
				 * @var $result_scale
				 * @var $native_cache
				 * @var $calc_total
				 */
				extract($query->getProperty([
					'priority', 'result_scale', 'native_cache', 'calc_total',
					'columns', 'source', 'alias',
					'joins', 'where', 'order_by', 'limit', 'offset',
					'group_by', 'having', 'for_update', 'shared_mode',
				]));
				$sql = 'SELECT';
				if(isset($priority)){
					if(!$priority){
						$sql.=' LOW_PRIORITY';
					}elseif($priority){
						$sql.=' HIGH_PRIORITY';
					}
				}
				if(isset($result_scale)){
					if(!$result_scale){
						$sql.=' SQL_SMALL_RESULT';
					}elseif($result_scale){
						$sql.=' SQL_BIG_RESULT';
					}
				}
				if(isset($calc_total) && $calc_total){
					$sql.=' SQL_CALC_FOUND_ROWS';
				}
				if(isset($native_cache)){
					if($native_cache){
						$sql.=' SQL_CACHE';
					}else{
						$sql.=' SQL_NO_CACHE';
					}

				}

				$sql.= ' ' . $this->columnListSelection($columns)
				       . ' FROM ' . $this->sourceSpecifier($source)
				       . $this->processAliasSQL($alias);

				if($joins){
					$sql.= ' '.$this->processJoinsSQL($joins);
				}
				if($where = $this->processConditional($where)){
					$sql.=' WHERE ' . $where;
				}
				if($order_by){
					$sql.= ' '.$this->processOrderBySQL($order_by);
				}
				if($limit){
					$sql.= ' '.$this->processLimitSQL($limit, $offset);
				}
				if($group_by){
					$sql.= ' '.$this->processGroupBySQL(array_keys($group_by));
				}
				if($having = $this->processConditional($having)){
					$sql.=' HAVING ' . $having;
				}
				if(isset($shared_mode) && $shared_mode){
					$sql.= ' '.$this->getLockInSharedSQL();
				}
				if(isset($for_update) && $for_update){
					$sql.= ' '.$this->getForUpdateSQL();
				}

				if($nested){
					return '(' . $sql . ')';
				}else{
					$collector->setRaw($sql.';');
					return $collector;
				}

			}finally{
				$this->nestingHigher();
			}
			return null;
		}

		/**
		 * @param array $joins
		 * @return string
		 */
		public function processJoinsSql(array $joins){
			$_ = [];
			foreach($joins as $join){
				if(isset($join['type']) && $join['type']){
					$_[] = strtoupper($join['type']) . ' JOIN '
					       . $this->sourceSpecifier($join['source'])
					       . $this->processAliasSQL($join['alias'])
					       . ($join['on']? ' ON '.$this->processConditional($join['on']):'');
				}else{
					$_[] = $this->sourceSpecifier($join['source'])
					       . $this->processAliasSQL($join['alias']);
				}
			}
			return implode(' ', $_);
		}

		/**
		 * @param array $assigning
		 * @return string
		 */
		public function processAssigning(array $assigning){
			$a = [];
			foreach($assigning as $k => $v){
				$a[] = $this->columnSpecifier($k)
				       . ' = '
				       . $this->processExpression($v,$this->collector,self::STR_AS_VALUE);
			}
			return 'SET '.implode(', ', $a);
		}

		/**
		 * @param array $order_by
		 * @return string
		 */
		public function processOrderBySQL(array $order_by){
			$_ = [];
			foreach($order_by as $reference => $direction){
				$_[] = $this->columnSpecifier($reference,false) . ' ' . (strcasecmp($direction,'DESC')===0?'DESC':'ASC');
			}
			return 'ORDER BY ' . implode(', ',$_);
		}

		/**
		 * @param null $alias
		 * @return string
		 */
		public function processAliasSQL($alias = null){
			return ($alias?' AS ' . $this->aliasIdentifier($alias):'');
		}

		/**
		 * @param $limit
		 * @param null $offset
		 * @return string
		 */
		public function processLimitSQL($limit, $offset = null){
			$limit = intval($limit);
			if($offset){
				$_ = [$limit, intval($offset?:0)];
				return 'LIMIT '.implode(', ', $_);
			}else{
				return 'LIMIT '.$limit;
			}
		}

		/**
		 * @param $group_by
		 * @return string
		 */
		public function processGroupBySQL($group_by){
			$_ = [];
			foreach($group_by as $reference){
				$_[] = $this->columnSpecifier($reference,false);
			}
			return 'GROUP BY ' . implode(', ',$_);
		}

		/**
		 * @return string
		 */
		public function getLockInSharedSQL(){
			return 'LOCK IN SHARED MODE';
		}

		/**
		 * @return string
		 */
		public function getForUpdateSQL(){
			return 'FOR UPDATE';
		}

		/**
		 * @param $conditional
		 * @return string
		 */
		public function processConditional($conditional){
			if(!$conditional){
				return '';
			}
			if($conditional instanceof Expression){
				return $conditional->render($this);
			}elseif(is_array($conditional)){
				$operator = true;
				$a = [];
				$collector = $this->collector;
				foreach($conditional as $item){
					if(is_string($item)){
						$operator = true;
						$a[] = strtoupper($item);
						continue;
					}
					if(!$operator){
						$a[] = 'AND';
					}
					if(is_array($item)){
						$skip = Expression::specifySkipping();
						$item = array_replace([$skip,$skip,$skip], $item);
						$a[] = ExpressionCollation::process($item[0],$item[1],$item[2], $this, $collector, false);
					}else{
						$a[] = $this->processExpression($item,$collector);
					}
					$operator = false;
				}
				return implode(' ', $a);
			}else{
				return '';
			}
		}



		/**
		 * @param $value
		 * @param PayloadCollector $collector
		 * @param string $recognize
		 * @return string
		 */
		public function processExpression($value, PayloadCollector $collector, $recognize = self::STR_AS_COLUMN){
			if($value instanceof Expression){
				return $value->render($this);
			}elseif(is_array($value)){
				$collector->bind($value, null);
				return '(?)';
			}elseif(is_numeric($value)){
				$collector->bind($value, null);
				return '?';
			}elseif($recognize === self::STR_AS_COLUMN){
				return $this->columnSpecifier($value);
			}elseif($recognize === self::STR_AS_VALUE){
				$collector->bind($value, null);
				return '?';
			}elseif($recognize === self::STR_AS_RAW){
				return $value;
			}else{
				return '';
			}
		}



		/**
		 * @param array $columns
		 * @return string
		 */
		public function columnListSpecifier(array $columns){
			foreach($columns as &$column){
				$column = $this->columnSpecifier($column,false);
			}
			return implode(', ', $columns);
		}

		/**
		 * Расширенные возможности указания списка определения Колонок, Например в SELECT *
		 * @param array $columns
		 * @return string
		 */
		public function columnListSelection(array $columns){
			$a = [];
			foreach($columns as $alias => $column){
				$alias = is_string($alias)? $this->aliasIdentifier($alias) : null;
				if($column instanceof Expression){
					$a[] = $column->render($this).($alias?' AS ' . $alias:'');
				}else{
					$a[] = $this->columnSpecifier($column, true).($alias?' AS ' . $alias:'');
				}
			}
			return implode(', ',$a);
		}

		/**
		 * @param $column
		 * @param bool|true $allow_mask
		 * @return null|string
		 */
		public function columnSpecifier($column, $allow_mask = true){
			if(!$column) return null;
			if(!is_array($column)) $column = explode($this->path_delimiter,$column);
			$column = array_replace([null,null,null],array_reverse(array_diff($column,[''])));
			if(count($column) > 3) return null; // allowed only $db,$table,$column
			list($column,$table,$db) = $column;
			$a = [];
			if($db && ($db = $this->databaseIdentifier($db))){
				$a[] = $db;
			}
			if($table && ($table = $this->tableIdentifier($table))){
				$a[] = $table;
			}
			if($column && ($column = $this->columnIdentifier($column, $allow_mask))){
				$a[] = $column;
			}else{
				return null;
			}
			return $a?implode($this->path_delimiter,$a):null;
		}



		/**
		 * @param $source
		 * @return null|string
		 */
		abstract public function sourceSpecifier($source);


		/**
		 * @param $identifier
		 * @return null|string
		 */
		abstract public function tableIdentifier($identifier);

		/**
		 * @param $identifier
		 * @return null|string
		 */
		abstract public function databaseIdentifier($identifier);

		/**
		 * @param $identifier
		 * @return null|string
		 */
		abstract public function aliasIdentifier($identifier);

		/**
		 * @param $keyword
		 * @return string
		 */
		abstract public function keywordIdentifier($keyword);

		/**
		 * @param $identifier
		 * @param bool|true $allow_mask
		 * @return null|string
		 */
		abstract public function columnIdentifier($identifier, $allow_mask = true);

		/**
		 * @param $identifier
		 * @return null|string
		 */
		abstract public function escapeIdentifier($identifier);

	}
}

