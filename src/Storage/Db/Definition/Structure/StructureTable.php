<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 24.01.2017
 * Time: 5:59
 */
namespace Ceive\DataRecord\Storage\Db\Definition\Structure {

	/**
	 * Class StructureTable
	 * @package Jungle\Data\Storage\Db\Definition\Structure
	 */
	class StructureTable{

		/** @var  string|null */
		public $database;

		/** @var  string */
		public $name;

		/** @var StructureColumn[] */
		public $columns = [];

		/** @var StructureForeignKey[]  */
		public $foreign_keys = [];

		/** @var StructureIndex[] */
		public $indexes = [];

		/** @var  string */
		public $engine;

		/** @var  string */
		public $comment;

		/** @var  string */
		public $default_names_collation;

		/**
		 * @param $name
		 * @param $type
		 * @param $size
		 * @param $default
		 * @param $on_update
		 */
		public function addColumn($name, $type, $size, $default, $on_update){

		}

	}
}

