<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 18.09.2016
 * Time: 15:12
 */
namespace Ceive\DataRecord\Util\Validation\Rule {
	
	use Ceive\DataRecord\Util\Validation\Rule;

	/**
	 * Class PresenceOf
	 * @package Jungle\Data\Record\Util\Validation\Rule
	 */
	class PresenceOf extends Rule{

		/** @var string  */
		protected $type = 'PresenceOf';

		/**
		 * @param $value
		 * @return bool
		 */
		protected function _expertize($value){
			return !empty($value);
		}
	}
}

