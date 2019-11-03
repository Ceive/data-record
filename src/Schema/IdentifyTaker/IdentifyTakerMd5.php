<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 17.11.2016
 * Time: 23:04
 */
namespace Ceive\DataRecord\Schema\IdentifyTaker {
	
	use Ceive\DataRecord\Schema\Schema;

	class IdentifyTakerMd5 extends IdentifyTaker{
		
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
			return md5($this->aliasTaker->take($schema));
		}
	}
}

