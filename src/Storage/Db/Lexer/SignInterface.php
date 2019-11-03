<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 07.03.2016
 * Time: 20:08
 */
namespace Ceive\DataRecord\Storage\Db\Lexer {

	/**
	 * Interface SignInterface
	 * @package Jungle\Data\Storage\Db\Lexer\Lexer
	 */
	interface SignInterface{

		/**
		 * @param Context $context
		 * @param int $position
		 * @param null $dialect
		 * @param null $nextPoint
		 * @return mixed
		 */
		public function recognize(Context $context, $position = 0, $dialect = null, & $nextPoint = null);



	}
}

