<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 22.05.2016
 * Time: 20:18
 */
namespace Ceive\DataRecord\Util\Schema\OuterInteraction {

	/**
	 * Interface OriginalDataTransientInterface
	 * @package Jungle\Data\Record\Util\Schema\OuterInteraction
	 */
	interface OriginalDataTransientInterface{

		/**
		 * @return bool
		 */
		public function hasModifiedOriginalData();

		/**
		 * @return mixed
		 */
		public function getModifiedOriginalData();

		/**
		 * @return $this
		 */
		public function applyModifiedOriginalData();

	}
}

