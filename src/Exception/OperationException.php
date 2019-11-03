<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 25.07.2016
 * Time: 0:15
 */
namespace Ceive\DataRecord\Exception {

	use Ceive\DataRecord\Operation;
	use Ceive\DataRecord\ORMException;
    use Ceive\DataRecord\Record;
    use Ceive\DataRecord\RecordAware;

	/**
	 * Class OperationException
	 * @package Jungle\Data\Record\Exception
	 */
	class OperationException extends ORMException implements RecordAware{

		/** @var  Operation  */
		protected $operation;

		/** @var  Record */
		protected $record;

		/**
		 * OperationException constructor.
		 * @param string $message
		 * @param Record $record
		 * @param Operation $operation
		 * @param \Exception $prev
		 */
		public function __construct($message, Record $record, Operation $operation,\Exception $prev){
			$this->operation = $operation;
			$this->record = $record;
			parent::__construct($message,0,$prev);
		}

		/**
		 * @return int
		 */
		public function getOperation(){
			return $this->operation;
		}

		/**
		 * @return Record
		 */
		public function getRecord(){
			return $this->record;
		}
	}
}

