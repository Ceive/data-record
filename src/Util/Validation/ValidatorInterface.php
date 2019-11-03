<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 20.09.2016
 * Time: 13:45
 */
namespace Ceive\DataRecord\Util\Validation {

	/**
	 * Interface ValidatorInterface
	 * @package Jungle\Data\Record\Util\Validation
	 */
	interface ValidatorInterface{

		/**
		 * @param $object
		 * @param array $parameters
		 * @return
		 */
		public function validate($object, array $parameters = []);

	}
}

