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
	 * Interface RemovingControlledInterface
	 * @package Jungle\Data\Record\Util\Collection\Hook
	 */
	interface RemovingControlledInterface{

		/**
		 * @param callable $hook
		 * @return mixed
		 */
		public function addRemovingHook(callable $hook);

		/**
		 * @param callable $hook
		 * @return mixed
		 */
		public function removeRemovingHook(callable $hook);

	}
}

