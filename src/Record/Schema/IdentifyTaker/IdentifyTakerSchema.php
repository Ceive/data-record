<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 17.11.2016
 * Time: 23:01
 */
namespace Jungle\Data\Record\Schema\IdentifyTaker {
	
	use Jungle\Data\Record\Schema\Schema;

	/**
	 * Class IdentifyTakerSchema
	 * @package Jungle\Data\Record\Schema\IdentifyTaker
	 */
	class IdentifyTakerSchema extends IdentifyTaker{

		/**
		 * @param Schema $schema
		 * @return mixed
		 */
		public function take(Schema $schema){
			return $schema->getName();
		}
	}
}

