<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 18.09.2016
 * Time: 15:10
 */
namespace Ceive\DataRecord\Util\Validation {

	/**
	 * Interface ValueCheckerInterface
	 * @package Jungle\Data\Record\Util\Schema\ValueType
	 */
	interface ValueCheckerInterface{

		/**
		 * @param $value
		 * @return boolean
		 */
		public function check($value);

	}
}

