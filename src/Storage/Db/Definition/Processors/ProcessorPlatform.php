<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 29.01.2017
 * Time: 19:22
 */
namespace Ceive\DataRecord\Storage\Db\Definition\Processors {
	
	use Ceive\DataRecord\Storage\Db\Definition\DefinitionProcessor;
	use Ceive\DataRecord\Storage\Db\Platform;

	/**
	 * Class ProcessorPlatform
	 * @package Jungle\Data\Storage\Db\Definition\Processors
	 */
	class ProcessorPlatform extends DefinitionProcessor{

		/** @var Platform  */
		protected $platform;

		/**
		 * ProcessorPlatform constructor.
		 * @param Platform $platform
		 */
		public function __construct(Platform $platform){
			parent::__construct();
			$this->platform = $platform;
		}

		/**
		 * @param $source
		 * @return null|string
		 */
		public function sourceSpecifier($source){
			return $this->platform->sourceSpecifier($source);
		}

		/**
		 * @param $identifier
		 * @return null|string
		 */
		public function tableIdentifier($identifier){
			return $this->platform->tableIdentifier($identifier);
		}

		/**
		 * @param $identifier
		 * @return null|string
		 */
		public function databaseIdentifier($identifier){
			return $this->platform->databaseIdentifier($identifier);
		}

		/**
		 * @param $identifier
		 * @return null|string
		 */
		public function aliasIdentifier($identifier){
			return $this->platform->aliasIdentifier($identifier);
		}

		/**
		 * @param $keyword
		 * @return string
		 */
		public function keywordIdentifier($keyword){
			return $this->platform->keywordIdentifier($keyword);
		}

		/**
		 * @param $identifier
		 * @param bool|true $allow_mask
		 * @return null|string
		 */
		public function columnIdentifier($identifier, $allow_mask = true){
			return $this->platform->columnIdentifier($identifier, $allow_mask);
		}

		/**
		 * @param $identifier
		 * @return null|string
		 */
		public function escapeIdentifier($identifier){
			return $this->platform->escapeIdentifier($identifier);
		}
	}
}

