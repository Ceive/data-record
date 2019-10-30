<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 23.01.2017
 * Time: 2:37
 */
namespace Jungle\Data\Storage\Db\Definition {

	/**
	 * Class Definition
	 * @package Jungle\Data\Storage\Db\Definition
	 */
	abstract class Definition extends Expression{


		/**
		 * @param array|string $property
		 * @return array
		 * @throws \Exception
		 */
		public function getProperty($property){
			if(is_array($property)){
				$a = [];
				foreach($property as $key){
					if(!property_exists($this,$key)){
						throw new \Exception('Property "'.$key.'" not exists in "'.get_called_class().'"');
					}
					$a[$key] = $this->{$key};
				}
				return $a;
			}else{
				if(!property_exists($this,$property)){
					throw new \Exception('Property "'.$property.'" not exists in "'.get_called_class().'"');
				}
				return $this->{$property};
			}
		}

	}

}

