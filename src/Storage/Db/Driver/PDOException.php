<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 28.01.2017
 * Time: 0:36
 */
namespace Ceive\DataRecord\Storage\Db\Driver {

	/**
	 * Class PDOException
	 * @package Jungle\Data\Storage\Db\Driver
	 */
	class PDOException extends \PDOException implements DriverException{

		/** @var int|mixed  */
		public $errorCode;

		/** @var  array */
		public $errorInfo;

		/** @var string  */
		public $sqlState;

		public function __construct(\PDOException $exception){

			parent::__construct($exception->getMessage(), 0, $exception);

			$this->code      = $exception->getCode();
			$this->errorInfo = $exception->errorInfo;
			$this->errorCode = isset($exception->errorInfo[1]) ? $exception->errorInfo[1] : $exception->getCode();
			$this->sqlState  = isset($exception->errorInfo[0]) ? $exception->errorInfo[0] : $exception->getCode();

		}

		/**
		 * {@inheritdoc}
		 */
		public function getErrorCode(){
			return $this->errorCode;
		}

		/**
		 * {@inheritdoc}
		 */
		public function getSQLState(){
			return $this->sqlState;
		}
	}
}

