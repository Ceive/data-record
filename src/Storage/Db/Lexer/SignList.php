<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 07.03.2016
 * Time: 14:18
 */
namespace Ceive\DataRecord\Storage\Db\Lexer {

	/**
	 * Class SignInterfaceList
	 * @package Jungle\Data\Storage\Db\Lexer\Lexer
	 */
	class SignList implements SignInterface{

		/** @var SignInterface[] */
		protected $tokens = [];

		/** @var bool  */
		protected $empty_allowed = false;

		/**
		 * @param SignInterface $token
		 * @return $this
		 */
		public function addToken(SignInterface $token){
			$this->tokens[] = $token;
			return $this;
		}

		/**
		 * @return array
		 */
		public function getTokens(){
			return $this->tokens;
		}

		/**
		 *
		 */
		public function addEmpty(){
			$this->empty_allowed = true;
		}

		/**
		 * @param $position
		 * @param Context $context
		 * @param null $dialect
		 * @param null $nextPoint
		 * @return false|Token
		 */
		public function recognize(Context $context, $position = 0, $dialect = null, & $nextPoint = null){

			foreach($this->tokens as $token){
				$np = null;
				$result = $token->recognize($context,$position,$dialect,$np);
				if($result){
					$nextPoint = $np;
					return $result;
				}
			}
			if($this->empty_allowed){
				return true;
			}else{
				return false;
			}
		}

	}
}

