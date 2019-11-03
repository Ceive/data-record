<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 01.06.2016
 * Time: 23:06
 */
namespace Ceive\DataRecord\Util\Collection\Sortable {

	/**
	 * Class Collection
	 * @package Jungle\Data\Record\Util\Collection
	 */
	abstract class Collection extends \Ceive\DataRecord\Util\Collection implements
		CollectionInterface,
		SorterAwareInterface{


		/** @var  SorterInterface */
		protected $sorter;

		/**
		 * @return mixed
		 */
		public function sort(){
			if($this->sorter){
				if(!$this->sorter->sort($this->items)){

				}
			}
			return $this;
		}

		/**
		 * @param array|SorterInterface|null $sorter
		 * @return mixed
		 */
		public function setSorter($sorter = null){
			$this->sorter = $sorter;
			return $this;
		}

		/**
		 * @return SorterInterface
		 */
		public function getSorter(){
			return $this->sorter;
		}

	}
}

