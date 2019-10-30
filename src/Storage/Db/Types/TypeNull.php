<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 29.01.2017
 * Time: 3:29
 */
namespace Jungle\Data\Storage\Db\Types {
	
	use Jungle\Data\Storage\Db\Dialect;

	/**
	 * Class TypeNull
	 * @package Jungle\Data\Storage\Db\Types
	 */
	class TypeNull extends Type{

		/**
		 * @param $sql_value
		 * @param Dialect $dialect
		 * @return string
		 */
		public function convertToDatabaseValue($sql_value, Dialect $dialect){
			return 'NULL';
		}

		/**
		 * @param $sql_value
		 * @param Dialect $dialect
		 * @return null
		 */
		public function convertToPHPValue($sql_value, Dialect $dialect){
			return NULL;
		}

		/**
		 * @return int
		 */
		public function getBindingType(){
			return \PDO::PARAM_NULL;
		}
	}
}

