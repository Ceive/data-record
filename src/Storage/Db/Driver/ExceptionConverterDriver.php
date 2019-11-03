<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 29.01.2017
 * Time: 17:28
 */
namespace Ceive\DataRecord\Storage\Db\Driver {

	/**
	 * Interface ExceptionConverterDriver
	 * @package Jungle\Data\Storage\Db\Driver
	 */
	interface ExceptionConverterDriver{
		/**
		 * Converts a given DBAL driver exception into a standardized DBAL driver exception.
		 *
		 * It evaluates the vendor specific error code and SQLSTATE and transforms
		 * it into a unified {@link Doctrine\DBAL\Exception\DriverException} subclass.
		 *
		 * @param string                                $message   The DBAL exception message to use.
		 * @param DriverException $exception The DBAL driver exception to convert.
		 *
		 * @return DriverException An instance of one of the DriverException subclasses.
		 */
		public function convertException($message, DriverException $exception);
	}
}

