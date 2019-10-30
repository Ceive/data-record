<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 15.11.2016
 * Time: 18:40
 */
namespace Jungle\Data\Record\Relation {

	use Jungle\Data\Record;
	use Jungle\Data\Record\Collection;

	/**
	 * Class MyRelationship
	 * @package Jungle\Data\Record\Relation
	 */
	class Relationship extends Collection implements \ArrayAccess{


		/** @var bool  */
		protected $auto_sort = true;

		/** @var  bool */
		protected $auto_deploy = true;

		/** @var  bool */
		protected $dirty_capturing = true;

		/** @var  bool  */
		protected $level = self::LEVEL_CHECKPOINT;

		/** @var  Record */
		protected $holder;

		/** @var  RelationMany */
		protected $holder_relation;

		/**
		 * MyRelationship constructor.
		 * @param Record|null $holder
		 * @param RelationMany $holder_relation
		 * @param Collection $ancestor
		 */
		public function __construct(Record $holder, RelationMany $holder_relation, Collection $ancestor){

			$this->holder = $holder;
			$this->holder_relation = $holder_relation;
			$state = $holder->getRecordState();
			if($state === Record::STATE_LOADED){
				$this->applyCondition();
			}elseif($state === Record::STATE_NEW){
				// Выставляем сразу что деплой якобы производился т.к холдер только создается
				// и у него могут быть связи не иначе как только с объектами в памяти
				$this->deployed = true;
			}

			$this->setAncestor($ancestor);


		}
		/**
		 * Отчистка и последующее заполнение из родительской коллекции
		 * Актуализация по Родителю, Сбрасывает Dirty состояние
		 */
		public function refresh(){
			if($this->ancestor && $this->holder->getRecordState()===Record::STATE_LOADED){
				$this->reset();
				$this->items = [];
				foreach($this->ancestor->items as $item){
					$this->_add($item);
				}
				/** @var Collection $descendant */
				foreach($this->descendants as $descendant){
					$descendant->refresh();
				}
			}
		}

		public function applyCondition(){
			if($this->holder->getRecordState() === Record::STATE_LOADED){
				$this->setContainCondition(
					$this->holder_relation->referencedCondition($this->holder)
				);
			}
		}




		public function offsetExists($offset){
			if(!$this->deployed && $this->auto_deploy){
				$this->deploy();
			}
			return isset($this->items[$offset]);
		}

		public function offsetGet($offset){
			if(!$this->deployed && $this->auto_deploy){
				$this->deploy();
			}
			return isset($this->items[$offset])?$this->items[$offset]:null;
		}

		public function offsetSet($offset, $value){

		}

		public function offsetUnset($offset){

		}



	}
}

