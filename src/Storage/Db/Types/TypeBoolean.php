<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 27.01.2017
 * Time: 23:42
 */
namespace Jungle\Data\Storage\Db\Types {

	use Jungle\Data\Storage\Db\Low\Platforms;

	/**
	 * Class TypeBoolean
	 * @package Jungle\Data\Storage\Db\Types
	 */
	class TypeBoolean extends Type{

		public function getBindingType(){
			return \PDO::PARAM_BOOL;
		}

		public function convertToPHPValue($sql_value, Platform $dialect){
			return $sql_value===null?null:boolval($sql_value);
		}

	}
}

