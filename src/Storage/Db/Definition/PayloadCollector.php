<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 23.01.2017
 * Time: 11:22
 */
namespace Ceive\DataRecord\Storage\Db\Definition {

	use Ceive\DataRecord\Storage\Db\Connection;
	use Jungle\Util\System;

	/**
	 * Class PayloadCollector
	 * @package Jungle\Data\Storage\Db
	 */
	class PayloadCollector implements \Serializable{

		/** @var string  */
		protected $raw = '';

		/** @var int  */
		protected $positional_index = 0;

		/** @var array  */
		protected $params = [];

		/** @var array  */
		protected $params_types = [];

		/** @var array  */
		protected $params_map;

		/** @var bool  */
		protected $cached = false;

		/**
		 * @param $raw
		 * @return $this
		 */
		public function setRaw($raw){
			$this->raw = $raw;
			return $this;
		}

		public function __construct($cached_query = null, array $types = null, array $paramsMap = null){
			if($cached_query){
				$this->cached       = true;
				$this->raw          = $cached_query;
				$this->params_types = $types;
				$this->params_map   = $paramsMap;
			}
		}

		/**
		 * @param $name
		 * @param $value
		 * @param $type
		 * @return $this
		 */
		public function bind($value, $type = null, $name = null){
			if(is_string($name)){
				$this->params[$name] = $value;
				$this->params_types[$name] = $type;
			}else{
				$index = $this->positional_index;
				$this->params[$index] = $value;
				$this->params_types[$index] = $type;
				$this->positional_index++;
			}
			return $this;
		}

		/**
		 * @param array $params
		 * @param array $params_types
		 * @return $this
		 */
		public function bindGroup(array $params, $params_types){
			if(!is_array($params_types)){
				$default_type = $params_types;
				$params_types = [];
			}else{
				$default_type = null;
			}
			foreach($params as $key => $value){
				if(is_string($key)){
					$this->params[$key] = $value;
					$this->params_types[$key] = !$params_types||!array_key_exists($key, $params_types)?$default_type:$params_types[$key];
				}else{
					$index = $this->positional_index;
					$this->params[$index] = $value;
					$this->params_types[$index] = !$params_types||!array_key_exists($key, $params_types)?$default_type:$params_types[$key];
					$this->positional_index++;
				}
			}
			return $this;
		}

		/**
		 * @param array $values
		 * @param array $params_types
		 * @param $prefix
		 * @return $this
		 */
		public function bindGroupPrefixed(array $values, array $params_types, $prefix){
			if(!is_array($params_types)){
				$default_type = $params_types;
				$params_types = [];
			}else{
				$default_type = null;
			}
			foreach($values as $i => $value){
				$key = System::uniqSysId($prefix,'db');
				$this->params[$key] = $value;
				$this->params_types[$key] = !$params_types || !array_key_exists($i, $params_types)?$default_type:$params_types[$i];
			}
			return $this;
		}

		/**
		 * @return array
		 */
		public function getParams(){
			return $this->params;
		}

		/**
		 * @return array
		 */
		public function getParamsTypes(){
			return $this->params_types;
		}



		/**
		 * @param array $params
		 * @param array $types
		 * @return string
		 */
		public function expand(array $params, array $types = null){
			$this->calculateParamsMap();
			return SQLParserUtils::expand($this->raw, $params, $types===null?$this->params_types:$types, $this->params_map);
		}

		/**
		 * @return array
		 */
		public function calculateParamsMap(){
			if($this->params_map === null){
				$this->params_map = [];
				$query = $this->raw;
				$map = SQLParserUtils::getPlaceholderPositions($query);
				$query_offset = 0;
				$array_types = [Connection::PARAM_INT_ARRAY, Connection::PARAM_STR_ARRAY];
				$_ = [];
				foreach($map as $start_pos => $name){
					$len = is_int($name)?1:strlen($name)+1;
					$start_pos += $query_offset;
					if(($type_array = in_array($this->params_types[$name],$array_types, true))
					   || (is_array($this->params[$name]) && !isset($this->params_types[$name]))
					){
						if(!$type_array){
							$this->params_types[$name] = Connection::PARAM_STR_ARRAY;
						}
						$sign = is_int($name)?'?':':'.$name;
						list($query, $start_pos) = SQLParserUtils::normalizeArrayPosition($query,$start_pos,$len, $sign,$plus);
						$_[$start_pos] = $name;
					}else{
						$_[$start_pos] = $name;
					}
				}
				$map = $_;unset($_);
				$this->params_map = $map;
			}
			return $this->params_map;
		}

		/**
		 * @return array
		 */
		public function serialize(){
			$this->calculateParamsMap();
			return serialize([ $this->params_types, $this->params_map]);
		}

		/**
		 * @param $serialized
		 */
		public function unserialize($serialized){
			list($this->params_types, $this->params_map) = unserialize($serialized);
			$this->cached = true;
		}

		public function __clone(){
			$this->cached = true;
		}

		/**
		 * @return string
		 */
		public function __toString(){
			$this->calculateParamsMap();
			return $this->raw;
		}

		/**
		 * @return $this
		 */
		public function reset(){
			$this->raw = '';
			$this->params = [];
			$this->params_types = [];
			$this->positional_index = 0;
			return $this;
		}

	}
}

