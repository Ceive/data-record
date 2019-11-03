<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 20.09.2016
 * Time: 14:07
 */
namespace Ceive\DataRecord\Util\Validation\Message {

	/**
	 * Interface ValidatorMessageInterface
	 * @package Jungle\Data\Record\Util\Validation\Message
	 */
	interface ValidatorMessageInterface extends ExpertizeMessageInterface{

		/**
		 * @return string
		 */
		public function getFieldName();

	}
}

