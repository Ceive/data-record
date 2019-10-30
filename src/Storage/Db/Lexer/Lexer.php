<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 07.03.2016
 * Time: 13:39
 */
namespace Jungle\Data\Storage\Db\Lexer {

	use Jungle\Data\Storage\Db\Lexer\SignManager;
	use Jungle\Data\Storage\Db\Sql;

	/**
	 * Class Lexer
	 * @package Jungle\Data\Storage\Db
	 *
	 * Определяющий модуль для парсинга и контроля SQL запросов
	 *
	 */
	class Lexer{

		/** @var SignManager  */
		protected $signManager;

		/**
		 * @param SignManager $manager
		 * @return $this
		 */
		public function setSignManager(SignManager $manager){
			$this->signManager = $manager;
			return $this;
		}

		public function getSignManager(){
			return $this->signManager;
		}

		/**
		 * @param $tokenName
		 * @return Sign
		 */
		public function getSign($tokenName){
			return $this->signManager->getPool('SignPool')->get($tokenName);
		}


		/**
		 * @param $sql
		 * @return Context
		 */
		public function createContext($sql){
			return new Context($sql,$this);
		}

		/**
		 * @param $sql
		 * @return false|Token
		 */
		public function recognize($sql){
			if($sql instanceof Sql){
				$sql = $sql->getSql();
			}
			$context = $this->createContext($sql);
			/** @var Sign $token */
			foreach($this->getSignManager()->getPool('SignPool')->getKeywords() as $token){
				if(($recognized = $token->recognize($context))){
					return $recognized;
				}
			}
			return false;
		}

	}
}

