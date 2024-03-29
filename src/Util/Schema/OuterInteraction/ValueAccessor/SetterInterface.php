<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 01.06.2016
 * Time: 23:46
 */
namespace Ceive\DataRecord\Util\Schema\OuterInteraction\ValueAccessor {

	/**
	 * Interface SetterInterface
	 * @package Jungle\Data\Record\Util\Schema\OuterInteraction\ValueAccessor
	 */
	interface SetterInterface{

		/**
		 * @param $data
		 * @param $key
		 * @param $value
		 * @return mixed
		 */
		public function __invoke($data, $key, $value);

	}
}

