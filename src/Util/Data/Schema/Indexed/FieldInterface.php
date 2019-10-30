<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 20.05.2016
 * Time: 18:55
 */
namespace Jungle\Util\Data\Schema\Indexed {

	/**
	 * Interface FieldInterface
	 * @package Jungle\Util\Data\Schema\Indexed
	 */
	interface FieldInterface{

		/**
		 * @return bool
		 */
		public function isPrimary();

		/**
		 * @return bool
		 */
		public function isUnique();

	}
}

