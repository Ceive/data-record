<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 20.09.2016
 * Time: 13:36
 */
namespace Ceive\DataRecord\Util {
	
	use Ceive\DataRecord\Util\Record\PropertyRegistryInterface;
	use Ceive\DataRecord\Util\Record\PropertyRegistryRemovableInterface;
	use Ceive\DataRecord\Util\Record\PropertyRegistryTransientInterface;

	/**
	 * Interface RecordInterface
	 * @package Jungle\Data\Record\Util
	 */
	interface RecordInterface extends PropertyRegistryInterface, PropertyRegistryRemovableInterface, PropertyRegistryTransientInterface{}

}

