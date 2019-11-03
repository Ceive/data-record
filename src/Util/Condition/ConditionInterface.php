<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 22.05.2016
 * Time: 20:27
 */
namespace Ceive\DataRecord\Util\Condition {

	use Ceive\DataRecord\Util\Schema\OuterInteraction\ValueAccessAwareInterface;

	/**
	 * Interface ConditionInterface
	 * @package Jungle\Data\Record\Util\Condition
	 */
	interface ConditionInterface{

		/**
		 * @param \Ceive\DataRecord\Util\Record\PropertyRegistryInterface|mixed $data
		 * @param null|ValueAccessAwareInterface|callable $access - if data is outer original data
		 * @return bool
		 */
		public function __invoke($data, $access = null);

		/**
		 * @return array
		 */
		public function toStorageCondition();

	}
}

