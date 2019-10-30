<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 24.01.2017
 * Time: 4:46
 */
namespace Jungle\Data\Storage\Db\Definition\Structure {

	/**
	 * Class StructureColumn
	 * @package Jungle\Data\Storage\Db\Definition\Structure
	 */
	class StructureColumn{

		public $name;

		public $type;

		public $default;

		public $nullable = false;

		public $is_pk = false;

		public $is_ai = false;

		public $is_zerofill = false;

		public $is_unique = false;

		public $on_update_statement;

	}
}

