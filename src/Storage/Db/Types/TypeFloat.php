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

	/**
	 * Class TypeFloat
	 * @package Jungle\Data\Storage\Db\Types
	 */
	class TypeFloat extends Type{

		public function convertToPHPValue($sql_value, Platform $dialect){
			return $sql_value===null?null:floatval($sql_value);
		}

	}
}

