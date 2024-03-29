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
	 * Interface OriginalDataAwareInterface
	 * @package Jungle\Data\Record\Util\Schema\OuterInteraction
	 */
	interface OriginalDataAwareInterface{

		/**
		 * @param $original_data
		 * @return mixed
		 */
		public function setOriginalData($original_data);

		/**
		 * @return mixed
		 */
		public function getOriginalData();

	}
}

