<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 19.09.2016
 * Time: 11:51
 */
namespace Ceive\DataRecord\Util\Validation\Rule {
	
	use Ceive\DataRecord\Util\Validation\Rule;

	/**
	 * Class Callback
	 * @package Jungle\Data\Record\Util\Validation\Rule
	 */
	class Callback extends Rule{

		/** @var  string */
		protected $type = 'Anonymous';

		/** @var  callable */
		protected $callback;

		/**
		 * @param $value
		 * @param array $parameters
		 * @return bool
		 */
		public function check($value,array $parameters = []){
			return call_user_func($this->callback, $parameters);
		}

		/**
		 * @param $value
		 * @return bool
		 */
		protected function _expertize($value){}
	}
}

