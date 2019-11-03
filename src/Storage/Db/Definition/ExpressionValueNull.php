<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 23.01.2017
 * Time: 9:36
 */
namespace Ceive\DataRecord\Storage\Db\Definition {

	/**
	 * Class ExpressionValueNull
	 * @package Jungle\Data\Storage\Db\Value
	 */
	class ExpressionValueNull extends ExpressionValue{

		/** @var ExpressionValueNull */
		protected static $instance;

		public $value     = null;

		public $bind_type = \PDO::PARAM_NULL;

		/**
		 * @return ExpressionValueNull
		 */
		public static function here(){
			return self::$instance?:self::$instance = new self();
		}
	}
}

