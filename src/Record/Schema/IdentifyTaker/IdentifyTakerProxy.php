<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 17.11.2016
 * Time: 23:05
 */
namespace Jungle\Data\Record\Schema\IdentifyTaker {
	
	use Jungle\Data\Record\Schema\Schema;

	/**
	 * Class IdentifyTakerProxy
	 * @package Jungle\Data\Record\Schema\IdentifyTaker
	 */
	class IdentifyTakerProxy extends IdentifyTaker{


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
		 * @return mixed
		 */
		public function take(Schema $schema){
			return $schema->getIdentity()?: $this->aliasTaker->take($schema) ;
		}
	}
}

