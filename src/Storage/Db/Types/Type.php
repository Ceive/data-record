<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 27.01.2017
 * Time: 23:39
 */
namespace Ceive\DataRecord\Storage\Db\Types {

	use Ceive\DataRecord\Storage\Db\Platform;

	/**
	 * Class Type
	 * @package Jungle\Data\Storage\Db\Types
	 */
	abstract class Type{

		protected static $loaded_types = [];
		protected static $types_classmap = [
			'null'      => TypeNull::class,
			'string'    => TypeString::class,
			'blob'      => TypeBlob::class,
			'float'     => TypeFloat::class,
			'integer'   => TypeInteger::class,
			'boolean'   => TypeBoolean::class,
		];

		/**
		 * @param $type
		 * @return Type|null
		 */
		public static function getType($type){
			if($type instanceof Type){
				return $type;
			}
			if(!isset(self::$loaded_types[$type])){
				if(!self::$types_classmap[$type]){
					return null;
				}else{
					$class = self::$types_classmap[$type];
					return self::$loaded_types[$type] = new $class;
				}
			}
			return self::$loaded_types[$type];
		}

		/**
		 * @return string
		 */
		public function __toString(){
			$name = get_called_class();
			if( ($pos = strrpos($name,'\\')) !== false){
				$name = substr($name,$pos+1);
			}
			return strtr($name,['Type','']);
		}

		/**
		 * @param $sql_value
		 * @param Platform $platform
		 * @return string
		 */
		public function convertToDatabaseValue($sql_value, Platform $platform){
			return $sql_value;
		}

		/**
		 * @param $sql_value
		 * @param Platform $platform
		 * @return null
		 */
		public function convertToPHPValue($sql_value, Platform $platform){
			return $sql_value;
		}

		/**
		 * @return int
		 */
		public function getBindingType(){
			return \PDO::PARAM_STR;
		}

	}
}

