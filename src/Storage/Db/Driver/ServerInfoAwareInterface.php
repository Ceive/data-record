<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 28.01.2017
 * Time: 0:43
 */
namespace Jungle\Data\Storage\Db\Driver {

	/**
	 * Interface ServerInfoAwareInterface
	 * @package Jungle\Data\Storage\Db\Driver
	 */
	interface ServerInfoAwareInterface{
		/**
		 * Returns the version number of the database server connected to.
		 *
		 * @return string
		 */
		public function getServerVersion();

		/**
		 * Checks whether a query is required to retrieve the database server version.
		 *
		 * @return boolean True if a query is required to retrieve the database server version, false otherwise.
		 */
		public function requiresQueryForServerVersion();
	}
}

