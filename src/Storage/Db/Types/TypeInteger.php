<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 27.01.2017
 * Time: 23:42
 */
namespace Ceive\DataRecord\Storage\Db\Types {
	
	use Ceive\DataRecord\Storage\Db\Low\Platforms;

	class TypeInteger extends Type{

		public function getBindingType(){
			return \PDO::PARAM_INT;
		}

		public function convertToPHPValue($sql_value, Platform $dialect){
			return $sql_value===null?null:intval($sql_value);
		}
	}
}

