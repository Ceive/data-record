<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 21.01.2017
 * Time: 18:32
 */
namespace Ceive\DataRecord\Query {

	/**
	 * Class Scope
	 * @package Jungle\Data\Record\Query
	 */
	class Scope{

		public $column_aliases = [];

		public $container_aliases = [];

		/**
		 * @param $path
		 * @return mixed
		 */
		public function getAlias($path){
			if(isset($this->column_aliases[$path])){
				return $this->column_aliases[$path];
			}
			if(isset($this->container_aliases[$path])){
				return $this->container_aliases[$path];
			}
			return null;
		}

		/**
		 * @param $path
		 * @param $context_alias
		 */
		public function setContainerAlias($path, $context_alias){
			$this->container_aliases[$path] = $context_alias;
		}

		/**
		 * @param $path
		 * @param $condition
		 * @return string
		 */
		public function prepareContainerAlias($path, $condition){
			$condition = $condition?md5(serialize($condition)):'';
			return strtr($path,['.'=>'__']) . ($condition?'_' . md5(serialize($condition)):'');
		}

		/**
		 * @param $path
		 * @param $column_alias
		 */
		public function setColumnAlias($path, $column_alias){
			$this->column_aliases[$path] = $column_alias;
		}

	}
}

