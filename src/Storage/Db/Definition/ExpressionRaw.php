<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 23.01.2017
 * Time: 13:30
 */
namespace Ceive\DataRecord\Storage\Db\Definition {

	/**
	 * Class ExpressionRaw
	 * @package Jungle\Data\Storage\Db\Definition\Value
	 */
	class ExpressionRaw extends Expression{

		public $type = 'raw';

		public $raw;

		public $params = [];

		public $types = [];

		/**
		 * ExpressionRaw constructor.
		 * @param $raw
		 * @param array $params
		 * @param array $types
		 */
		public function __construct($raw,array $params = [],array $types = []){
			$this->raw = $raw;
			$this->params = $params;
			$this->types = $types;
		}

		/**
		 * @param DefinitionProcessor $processor
		 * @return string
		 */
		public function render(DefinitionProcessor $processor){
			if($this->raw){
				$collector = $processor->getCollector();
				if($this->params){
					$collector->bindGroup($this->params, $this->types);
				}
				return $this->raw;
			}
			return '';
		}
	}
}

