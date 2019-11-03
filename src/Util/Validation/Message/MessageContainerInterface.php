<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 20.09.2016
 * Time: 15:22
 */
namespace Ceive\DataRecord\Util\Validation\Message {

	use Ceive\DataRecord\Util\Validation\MessageInterface;

	/**
	 * Interface MessageContainerInterface
	 * @package Jungle\Data\Record\Util\Validation\Message
	 */
	interface MessageContainerInterface{

		/**
		 * @return bool
		 */
		public function isContainer();

		/**
		 * @param array $messages
		 * @return $this
		 */
		public function appendMessages(array $messages);

		/**
		 * @return MessageInterface[] | MessageContainerInterface[]
		 */
		public function getMessages();

	}
}

