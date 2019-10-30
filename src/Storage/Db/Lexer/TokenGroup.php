<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 07.03.2016
 * Time: 16:32
 */
namespace Jungle\Data\Storage\Db\Lexer {

	use Jungle\Util\Named\NamedInterface;

	/**
	 * Class TokenGroup
	 * @package Jungle\Data\Storage\Db\Lexer\Lexer
	 */
	class TokenGroup implements \Countable, NamedInterface{

		/** @var  SignGroup */
		protected $sign_group;

		/** @var Token[] */
		protected $tokens = [];

		/**
		 * @param SignGroup $group
		 */
		public function __construct(SignGroup $group){
			$this->sign_group = $group;
		}

		/**
		 * @param Token $token
		 */
		public function addToken(Token $token){
			$this->tokens[] = $token;
		}

		/**
		 * @return int
		 */
		public function count(){
			return count($this->tokens);
		}

		/**
		 * @return Token[]
		 */
		public function getTokens(){
			return $this->tokens;
		}

		/**
		 * @return null|string
		 */
		public function getName(){
			return $this->sign_group->getName();
		}

		/**
		 * @param $name
		 * @return $this
		 */
		public function setName($name){
			//throw new \Error('setName is not effect in TokenGroup');
			return $this;
		}

	}
}

