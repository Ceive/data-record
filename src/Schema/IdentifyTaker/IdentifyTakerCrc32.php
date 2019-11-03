<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 17.11.2016
 * Time: 22:59
 */
namespace Ceive\DataRecord\Schema\IdentifyTaker {

	use Ceive\DataRecord\Schema\Schema;

	/**
	 * Class IdentifyTakerCrc32
	 * @package Jungle\Data\Record\Schema\IdentifyTaker
	 */
	class IdentifyTakerCrc32 extends IdentifyTaker{

		/** @var IdentifyTaker */
		private $aliasTaker;

		/**
		 * IdentifyTakerCrc32 constructor.
		 * @param IdentifyTaker $aliasTaker
		 */
		public function __construct(IdentifyTaker $aliasTaker){
			$this->aliasTaker = $aliasTaker;
		}

		/**
		 * @param Schema $schema
		 * @return int
		 */
		public function take(Schema $schema){
			return crc32($this->aliasTaker->take($schema));
		}
	}
}

