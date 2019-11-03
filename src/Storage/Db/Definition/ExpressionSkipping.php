<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 23.01.2017
 * Time: 6:48
 */
namespace Ceive\DataRecord\Storage\Db\Definition {

	/**
	 * Class ExpressionSkipping
	 * @package Jungle\Data\Storage\Db\Definition\Expression
	 *
	 * Класс для пропуска какого-то токена в выражениях
	 *
	 */
	class ExpressionSkipping extends Expression{

		/** @var ExpressionSkipping */
		protected static $instance;
		/**
		 * @return ExpressionSkipping
		 */
		public static function here(){
			return self::$instance?:self::$instance = new self();
		}

		/**
		 * @param DefinitionProcessor $processor
		 * @return null
		 */
		public function render(DefinitionProcessor $processor){
			return null;
		}
	}
}

