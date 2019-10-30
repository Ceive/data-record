<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 17.11.2016
 * Time: 23:00
 */
namespace Jungle\Data\Record\Schema\IdentifyTaker {
	
	use Jungle\Data\Record\Schema\Schema;

	/**
	 * Class IdentifyTakerSource
	 * @package Jungle\Data\Record\Schema\IdentifyTaker
	 */
	class IdentifyTakerSource extends IdentifyTaker{

		/**
		 * @param Schema $schema
		 * @return string
		 */
		public function take(Schema $schema){
			return $schema->getDefaultSource();
		}
	}
}

