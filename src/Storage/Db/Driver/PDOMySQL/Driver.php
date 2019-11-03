<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 28.01.2017
 * Time: 0:01
 */
namespace Ceive\DataRecord\Storage\Db\Driver\PDOMySQL {

	use Ceive\DataRecord\Storage\Db\Connection;
	use Ceive\DataRecord\Storage\Db\DBALException;
	use Ceive\DataRecord\Storage\Db\Driver\AbstractMySQLDriver;
	use Ceive\DataRecord\Storage\Db\Driver\PDOConnection;
	use Ceive\DataRecord\Storage\Db\Driver\PDOException;
	use Ceive\DataRecord\Storage\Db\Platform;

	/**
	 * Class MySQL
	 * @package Jungle\Data\Storage\Db\Driver
	 */
	class Driver extends AbstractMySQLDriver{

		/** @var  Platform */
		protected $platform;

		/**
		 * @param array $params
		 * @param null $username
		 * @param null $password
		 * @param array $driverOptions
		 * @return Connection
		 * @throws DBALException
		 */
		public function connect(array $params, $username = null, $password = null, array $driverOptions = [ ]){
			try {
				$conn = new PDOConnection(
					$this->constructPdoDsn($params),
					$username,
					$password,
					$driverOptions
				);
			} catch (PDOException $e) {
				throw DBALException::driverException($this, $e);
			}
			return $conn;
		}
		/**
		 * Constructs the MySql PDO DSN.
		 *
		 * @param array $params
		 *
		 * @return string The DSN.
		 */
		protected function constructPdoDsn(array $params)
		{
			$dsn = 'mysql:';
			if (isset($params['host']) && $params['host'] != '') {
				$dsn .= 'host=' . $params['host'] . ';';
			}
			if (isset($params['port'])) {
				$dsn .= 'port=' . $params['port'] . ';';
			}
			if (isset($params['dbname'])) {
				$dsn .= 'dbname=' . $params['dbname'] . ';';
			}
			if (isset($params['unix_socket'])) {
				$dsn .= 'unix_socket=' . $params['unix_socket'] . ';';
			}
			if (isset($params['charset'])) {
				$dsn .= 'charset=' . $params['charset'] . ';';
			}

			return $dsn;
		}
		/**
		 * @return mixed
		 */
		public function getPlatform(){
			if(!$this->platform){
				$this->platform = new \Ceive\DataRecord\Storage\Db\Platforms\MySQLPlatform();
			}
			return $this->platform;
		}

		/**
		 * @return string
		 */
		public function getName(){
			return 'Driver';
		}

	}
}

