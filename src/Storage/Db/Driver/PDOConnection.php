<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 28.01.2017
 * Time: 0:34
 */
namespace Ceive\DataRecord\Storage\Db\Driver {

	use PDO;

	/**
	 * Class PDOConnection
	 * @package Jungle\Data\Storage\Db\Driver
	 */
	class PDOConnection extends PDO implements Connection, ServerInfoAwareInterface{

		/**
		 * @param string      $dsn
		 * @param string|null $user
		 * @param string|null $password
		 * @param array|null  $options
		 *
		 * @throws PDOException in case of an error.
		 */
		public function __construct($dsn, $user = null, $password = null, array $options = null){
			try {
				parent::__construct($dsn, $user, $password, $options);
				$this->setAttribute(PDO::ATTR_STATEMENT_CLASS, [PDOStatement::class,[]]);
				$this->setAttribute(PDO::ATTR_ERRMODE, PDO::ERRMODE_EXCEPTION);
			} catch (\PDOException $exception) {
				throw new PDOException($exception);
			}
		}

		/**
		 * {@inheritdoc}
		 */
		public function exec($statement){
			try {
				return parent::exec($statement);
			} catch (\PDOException $exception) {
				throw new PDOException($exception);
			}
		}

		/**
		 * {@inheritdoc}
		 */
		public function getServerVersion(){
			return PDO::getAttribute(PDO::ATTR_SERVER_VERSION);
		}

		/**
		 * {@inheritdoc}
		 */
		public function prepare($prepareString, $driverOptions = array()){
			try {
				return parent::prepare($prepareString, $driverOptions);
			} catch (\PDOException $exception) {
				throw new PDOException($exception);
			}
		}

		/**
		 * {@inheritdoc}
		 * @param
		 */
		public function query($statement, ...$args){

			try {
				$argsCount = count($args);
				if($argsCount == 3) {
					return parent::query($statement, $args[0], $args[1], $args[2]);
				}elseif($argsCount == 2){
					return parent::query($statement, $args[0], $args[1]);
				}elseif($argsCount == 1){
					return parent::query($statement, $args[0]);
				}
				return parent::query($statement);
			} catch (\PDOException $exception) {
				throw new PDOException($exception);
			}
		}

		/**
		 * {@inheritdoc}
		 */
		public function quote($input, $type = \PDO::PARAM_STR){
			return parent::quote($input, $type);
		}

		/**
		 * {@inheritdoc}
		 */
		public function lastInsertId($name = null){
			return parent::lastInsertId($name);
		}

		/**
		 * {@inheritdoc}
		 */
		public function requiresQueryForServerVersion(){
			return false;
		}

	}
}

