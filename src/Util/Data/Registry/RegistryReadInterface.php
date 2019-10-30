<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 22.05.2016
 * Time: 20:16
 */
namespace Jungle\Util\Data\Registry {

	/**
	 * Interface RegistryReadInterface
	 * @package Jungle\Util\Data\Registry
	 */
	interface RegistryReadInterface{

		/**
		 * @param $key
		 * @return mixed
		 */
		public function get($key);

		/**
		 * @param $key
		 * @return mixed
		 */
		public function has($key);

	}
}

