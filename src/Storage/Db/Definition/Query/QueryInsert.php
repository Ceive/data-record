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
	use Ceive\DataRecord\Storage\Db\Definition\ExpressionSkipping;


	/**
	 * Class QueryInsert
	 * @package Jungle\Data\Storage\Db
	 */
	class QueryInsert extends Query{

		/** @var array [{column_name}, ..., ...] */
		protected $columns = [];

		/** @var array|QuerySelect  */
		protected $source_collection;

		/** @var  bool|null */
		protected $on_duplicate;

		/** @var null|bool DELAYED=true | LOW_PRIORITY=false */
		protected $priority;

		/**
		 * @param QuerySelect $select_query
		 * @return QuerySelect
		 */
		public function select(QuerySelect $select_query){
			$this->source_collection = $select_query;
			return $select_query;
		}


		/**
		 * @param \array[] ...$rows
		 * @return $this
		 */
		public function rows(array ...$rows){
			foreach($rows as $row){
				$this->source_collection[] = $row;
			}
			return $this;
		}

		/**
		 * @param ...$row
		 * @return $this
		 */
		public function row(...$row){
			$this->source_collection[] = $row;
			return $this;
		}

		/**
		 * @param array $rows
		 * @return $this
		 */
		public function rowsArray(array $rows){
			foreach($rows as $row){
				$this->source_collection[] = $row;
			}
			return $this;
		}

		/**
		 * @param array $row
		 * @return $this
		 */
		public function rowArray(array $row){
			$this->source_collection[] = $row;
			return $this;
		}

		/**
		 * @param array[] $sheets
		 * @return $this
		 */
		public function sheets(array $sheets){
			foreach($sheets as $sheet){
				$this->source_collection[] = $this->order_data($sheet);
			}
			return $this;
		}

		/**
		 * @param array $sheet
		 * @return $this
		 */
		public function sheet(array $sheet){
			$this->source_collection[] = $this->order_data($sheet);
			return $this;
		}

		/**
		 * @param array $data
		 * @return array
		 */
		protected function order_data(array $data){
			$a = [];
			foreach($this->columns as $col){
				$a[] = array_key_exists($col,$data)?$data[$col]:ExpressionSkipping::here();
			}
			return $a;
		}

		/**
		 * @param null $ignore
		 * @return $this
		 */
		public function onDuplicateIgnore($ignore = null){
			$this->on_duplicate = $ignore;
			return $this;
		}

		/**
		 * @param $assigning
		 * @return $this
		 */
		public function onDuplicateUpdate($assigning){
			$this->on_duplicate = $assigning;
			return $this;
		}

		/**
		 * @param null $priority
		 * DELAYED=true | LOW_PRIORITY=false
		 * @return $this
		 */
		public function priority($priority = null){
			$this->priority = $priority;
			return $this;
		}

		public function render(DefinitionProcessor $processor){
			$collector= $processor->prepareInsert($this);
			return $collector->__toString();
		}

	}
}

