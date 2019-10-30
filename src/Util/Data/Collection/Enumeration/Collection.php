<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 20.05.2016
 * Time: 21:00
 */
namespace Jungle\Util\Data\Collection\Enumeration {

	use Jungle\Util\Data\Collection\Sortable\Collection as SortableCollection;

	/**
	 * Class Collection
	 * @package Jungle\Util\Data\Collection
	 */
	abstract class Collection extends SortableCollection implements CollectionInterface{

		/**
		 * @param $item
		 * @return $this
		 */
		public function add($item){
			$this->items[] = $item;
			return $this;
		}

	}
}

