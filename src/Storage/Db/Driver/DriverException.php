<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 28.01.2017
 * Time: 0:38
 */
namespace Jungle\Data\Storage\Db\Driver {

	/**
	 * Interface DriverException
	 * @package Jungle\Data\Storage\Db\Driver
	 */
	interface DriverException{

		/**
		 * Returns the driver specific error code if available.
		 *
		 * Returns null if no driver specific error code is available
		 * for the error raised by the driver.
		 *
		 * @return integer|string|null
		 */
		public function getErrorCode();

		/**
		 * Returns the driver error message.
		 *
		 * @return string
		 */
		public function getMessage();

		/**
		 * Returns the SQLSTATE the driver was in at the time the error occurred.
		 *
		 * Returns null if the driver does not provide a SQLSTATE for the error occurred.
		 *
		 * @return string|null
		 */
		public function getSQLState();

	}
}

