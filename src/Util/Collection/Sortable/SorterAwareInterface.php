<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 20.05.2016
 * Time: 20:58
 */
namespace Ceive\DataRecord\Util\Collection\Sortable {

	/**
	 * Interface SorterAwareInterface
	 * @package Jungle\Data\Record\Util\Collection
	 */
	interface SorterAwareInterface{

		/**
		 * @param array|SorterInterface|null $sorter
		 * @return mixed
		 */
		public function setSorter($sorter = null);

		/**
		 * @return mixed
		 */
		public function getSorter();

	}
}

