<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 18.09.2016
 * Time: 17:32
 */
namespace Ceive\DataRecord\Util\Validation\Message {

	/**
	 * Interface RuleMessageInterface
	 * @package Jungle\Data\Record\Util\Validation\Message
	 */
	interface RuleMessageInterface extends ExpertizeMessageInterface{

		/**
		 * @return array
		 */
		public function getParams();

	}
}

