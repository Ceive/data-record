<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 23.01.2017
 * Time: 10:54
 */
namespace Jungle\Data\Storage\Db {

	use Jungle\Data\Storage\Db\Definition\Query;

	/**
	 * Interface Driver
	 * @package Jungle\Data\Storage\Db
	 */
	interface Driver{

		/**
		 * @param array $params
		 * @param null $username
		 * @param null $password
		 * @param array $driverOptions
		 * @return Connection
		 */
		public function connect(array $params, $username = null, $password = null, array $driverOptions = array());

		/**
		 * @return mixed
		 */
		public function getPlatform();

		/**
		 * @return string
		 */
		public function getName();

		/**
		 * @param Connection $conn
		 * @return string
		 */
		public function getDatabase(Connection $conn);
	}
}

