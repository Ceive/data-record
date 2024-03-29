<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 06.06.2016
 * Time: 2:53
 */
namespace Ceive\DataRecord\Util\Collection {

	/**
	 * Interface DeployedInterface
	 * @package Jungle\Data\Record\Util\Collection
	 *
	 * Интерфейс для вызова развертывания коллекции из хранилища
	 *
	 */
	interface DeployedInterface{

		/**
		 * @return mixed
		 */
		public function deploy();

	}
}

