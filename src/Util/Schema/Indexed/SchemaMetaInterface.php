<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 20.05.2016
 * Time: 18:56
 */
namespace Ceive\DataRecord\Util\Schema\Indexed {

	/**
	 * Interface SchemaMetaInterface
	 * @package Jungle\Data\Record\Util\Schema\Indexed
	 */
	interface SchemaMetaInterface{

		/**
		 * @return string
		 */
		public function getPk();

		/**
		 * @param string $fieldName
		 * @return bool
		 */
		public function isPrimaryField($fieldName);

		/**
		 * @param string $fieldName
		 * @return bool
		 */
		public function isUniqueField($fieldName);

	}
}

