<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 07.03.2016
 * Time: 15:26
 */
namespace Ceive\DataRecord\Storage\Db\Lexer {

	use Ceive\DataRecord\Storage\Db\Lexer\SignManager\SignPool;
	use Ceive\DataRecord\Storage\Db\Lexer\SignManager\SignTypePool;
	use Jungle\Util\Smart\Keyword\Manager as Mgr;
	use Jungle\Util\Smart\Keyword\Storage;

	/**
	 * Class Pool
	 * @package Jungle\Data\Storage\Db\Lexer\Lexer
	 */
	class SignManager extends Mgr{

		/**
		 * @param Storage $storage
		 */
		public function __construct(Storage $storage){
			$this->addPool((new SignPool($storage)));
			$this->addPool((new SignTypePool($storage)));
		}

	}
}

