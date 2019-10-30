<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 24.01.2017
 * Time: 6:00
 */
namespace Jungle\Data\Storage\Db\Definition\Structure {

	/**
	 * Class StructureForeignKey
	 * @package Jungle\Data\Storage\Db\Definition\Structure
	 */
	class StructureForeignKey{

		const FK_RESTRICT   = 'restrict';
		const FK_SET_NULL   = 'set null';
		const FK_CASCADE    = 'cascade';

		/**
		 * @var string|array
		 * @see Dialect::sourceSpecifier()
		 */
		public $references_source;

		/**
		 * @var array [{$origin_column} => {$references_column}]
		 * column only names without mask(*) and without sources specifiers
		 */
		public $collation = [];

		/** @var string */
		public $on_references_update = self::FK_RESTRICT;

		/** @var string */
		public $on_references_delete = self::FK_RESTRICT;


		/**
		 * @param $origin_column
		 * @param $references_column
		 * @return $this
		 */
		public function collate($origin_column, $references_column){
			$this->collation[$origin_column] = $references_column;
			return $this;
		}

		/**
		 * @param string $reaction
		 * @return $this
		 */
		public function onReferencesUpdate($reaction = self::FK_RESTRICT){
			$this->on_references_update = $reaction;
			return $this;
		}

		/**
		 * @param string $reaction
		 * @return $this
		 */
		public function onReferencesDelete($reaction = self::FK_RESTRICT){
			$this->on_references_delete = $reaction;
			return $this;
		}

	}
}

