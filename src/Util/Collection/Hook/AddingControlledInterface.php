<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 20.05.2016
 * Time: 21:00
 */
namespace Ceive\DataRecord\Util\Collection\Hook {

	/**
	 * Interface AddingControlledInterface
	 * @package Jungle\Data\Record\Util\Collection\Hook
	 */
	interface AddingControlledInterface{

		/**
		 * @param callable $hook
		 * @return mixed
		 */
		public function addAddingHook(callable $hook);

		/**
		 * @param callable $hook
		 * @return mixed
		 */
		public function removeAddingHook(callable $hook);

	}
}

