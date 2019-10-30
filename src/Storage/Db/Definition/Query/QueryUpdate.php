<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 23.01.2017
 * Time: 2:44
 */
namespace Jungle\Data\Storage\Db\Definition\Query {

	use Jungle\Data\Storage\Db\Definition\DefinitionProcessor;
	use Jungle\Data\Storage\Db\Definition\ExpressionFunction;

	/**
	 * Class QueryUpdate
	 * @package Jungle\Data\Storage\Db\Definition\Query
	 */
	class QueryUpdate extends QueryExtended{

		/** @var array  [{column}]*/
		protected $assigning   = [];

		/** @var  bool|null */
		protected $ignore_errors;

		/** @var  bool|null LOW_PRIORITY = false */
		protected $priority;



		/**
		 * @param $column
		 * @param mixed|QuerySelect|\Jungle\Data\Storage\Db\Definition\Expression|ExpressionFunction $value
		 * @param bool|true $merge
		 * @return $this
		 */
		public function assign($column, $value, $merge = true){
			if(!$merge)$this->assigning = [];
			$this->assigning[$column] = $value;
			return $this;
		}

		/**
		 * @param array $data
		 * @param bool|true $merge
		 * @return $this
		 */
		public function data(array $data, $merge = true){
			$this->assigning = $merge?array_replace($this->assigning,$data):$data;
			return $this;
		}

		public function resetAll(){
			parent::resetAll();
			$this->assigning        = [];
			$this->ignore_errors    = null;
		}

		function __get($name){
			return isset($this->assigning[$name])?$this->assigning[$name]:null;
		}
		function __set($name, $value){
			$this->assigning[$name] = $value;
		}
		function __isset($name){
			return array_key_exists($name,$this->assigning);
		}
		function __unset($name){
			unset($this->assigning[$name]);
		}

		/**
		 * @param null $ignore
		 * @return $this
		 */
		public function ignoreErrors($ignore = null){
			$this->ignore_errors = $ignore;
			return $this;
		}

		/**
		 * @param DefinitionProcessor $processor
		 * @return \Jungle\Data\Storage\Db\Definition\PayloadCollector
		 */
		public function render(DefinitionProcessor $processor){
			$collector= $processor->prepareUpdate($this);
			return $collector->__toString();
		}
	}
}

