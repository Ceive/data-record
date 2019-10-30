<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 20.05.2016
 * Time: 20:59
 */
namespace Jungle\Util\Data\Collection\Enumeration\Unique {
	
	interface CollectionInterface{

		/**
		 * @param $item
		 * @param bool $checkUnique
		 * @return mixed
		 */
		public function add($item, $checkUnique = true);


		/**
		 * @param $item
		 * @return mixed
		 */
		public function remove($item);

	}
}

