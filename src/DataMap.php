<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 20.05.2016
 * Time: 20:50
 */
namespace Ceive\DataRecord {

	use Ceive\DataRecord\Schema\Schema;

	/**
	 * Class DataMap - Предвестник ORM ~ Моделей
	 * @package modelX
	 */
	class DataMap extends Record{

		/**
		 * DataMap constructor.
		 * @param Schema $schema
		 * @param null $data
		 */
		public function __construct(Schema $schema, $data = null){
			parent::__construct();
			$this->setSchema($schema);
			if($data!==null){
				$this->setRecordState(self::STATE_LOADED, $data);
			}else{
				$this->setRecordState(self::STATE_NEW, $data);
			}
			$this->onConstruct();
		}

	}

}

