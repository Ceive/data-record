<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 28.01.2017
 * Time: 0:41
 */
namespace Ceive\DataRecord\Storage\Db\Driver {

	/**
	 * Class PDOStatement
	 * @package Jungle\Data\Storage\Db\Driver
	 */
	class PDOStatement extends \PDOStatement implements Statement{
		/**
		 * Protected constructor.
		 */
		protected function __construct()
		{
		}

		/**
		 * {@inheritdoc}
		 */
		public function setFetchMode($fetchMode, $arg2 = null, $arg3 = null)
		{
			// This thin wrapper is necessary to shield against the weird signature
			// of PDOStatement::setFetchMode(): even if the second and third
			// parameters are optional, PHP will not let us remove it from this
			// declaration.
			try {
				if ($arg2 === null && $arg3 === null) {
					return parent::setFetchMode($fetchMode);
				}

				if ($arg3 === null) {
					return parent::setFetchMode($fetchMode, $arg2);
				}

				return parent::setFetchMode($fetchMode, $arg2, $arg3);
			} catch (\PDOException $exception) {
				throw new PDOException($exception);
			}
		}

		/**
		 * {@inheritdoc}
		 */
		public function bindValue($param, $value, $type = \PDO::PARAM_STR)
		{
			try {
				return parent::bindValue($param, $value, $type);
			} catch (\PDOException $exception) {
				throw new PDOException($exception);
			}
		}

		/**
		 * {@inheritdoc}
		 */
		public function bindParam($column, &$variable, $type = \PDO::PARAM_STR, $length = null, $driverOptions = null)
		{
			try {
				return parent::bindParam($column, $variable, $type, $length, $driverOptions);
			} catch (\PDOException $exception) {
				throw new PDOException($exception);
			}
		}

		/**
		 * {@inheritdoc}
		 */
		public function execute($params = null)
		{
			try {
				return parent::execute($params);
			} catch (\PDOException $exception) {
				throw new PDOException($exception);
			}
		}

		/**
		 * {@inheritdoc}
		 */
		public function fetch($fetchMode = null, $cursorOrientation = null, $cursorOffset = null)
		{
			try {
				if ($fetchMode === null && $cursorOrientation === null && $cursorOffset === null) {
					return parent::fetch();
				}

				if ($cursorOrientation === null && $cursorOffset === null) {
					return parent::fetch($fetchMode);
				}

				if ($cursorOffset === null) {
					return parent::fetch($fetchMode, $cursorOrientation);
				}

				return parent::fetch($fetchMode, $cursorOrientation, $cursorOffset);
			} catch (\PDOException $exception) {
				throw new PDOException($exception);
			}
		}

		/**
		 * {@inheritdoc}
		 */
		public function fetchAll($fetchMode = null, $fetchArgument = null, $ctorArgs = null)
		{
			try {
				if ($fetchMode === null && $fetchArgument === null && $ctorArgs === null) {
					return parent::fetchAll();
				}

				if ($fetchArgument === null && $ctorArgs === null) {
					return parent::fetchAll($fetchMode);
				}

				if ($ctorArgs === null) {
					return parent::fetchAll($fetchMode, $fetchArgument);
				}

				return parent::fetchAll($fetchMode, $fetchArgument, $ctorArgs);
			} catch (\PDOException $exception) {
				throw new PDOException($exception);
			}
		}

		/**
		 * {@inheritdoc}
		 */
		public function fetchColumn($columnIndex = 0)
		{
			try {
				return parent::fetchColumn($columnIndex);
			} catch (\PDOException $exception) {
				throw new PDOException($exception);
			}
		}
	}
}

