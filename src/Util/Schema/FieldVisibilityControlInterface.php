<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 20.05.2016
 * Time: 18:55
 */
namespace Ceive\DataRecord\Util\Schema {

	/**
	 * Interface FieldVisibilityControlInterface
	 * @package Jungle\Data\Record\Util\Schema
	 */
	interface FieldVisibilityControlInterface{

		/**
		 * @return bool
		 */
		public function isReadonly();

		/**
		 * @return bool
		 */
		public function isPrivate();

	}
}

