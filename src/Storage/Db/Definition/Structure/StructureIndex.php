<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 24.01.2017
 * Time: 6:00
 */
namespace Jungle\Data\Storage\Db\Definition\Structure {

	/**
	 * Class StructureIndex
	 * @package Jungle\Data\Storage\Db\Definition\Structure
	 */
	class StructureIndex{

		/** @var  string */
		public $name;

		/** @var  string */
		public $type;

		/** @var string */
		public $algo;

		/** @var string[] */
		public $size = [];

		/** @var string[] */
		public $column = [];

		/**
		 * @param $column
		 * @param string $direction
		 * @param null $size
		 * @return $this
		 */
		public function column($column, $direction = 'ASC', $size = null){
			$this->column[$column]  = $direction;
			$this->size[$column]    = $size;
			return $this;
		}

	}
}

