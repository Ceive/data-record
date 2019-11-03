<?php
/**
 * Created by PhpStorm.
 * User: Alexey
 * Date: 07.03.2016
 * Time: 14:31
 */
namespace Ceive\DataRecord\Storage\Db\Lexer {

	use Jungle\Util\Named\NamedInterface;

	/**
	 * Class Token
	 * @package Jungle\Data\Storage\Db\Lexer\Lexer
	 */
	class Token implements NamedInterface{

		/** @var Context  */
		protected $context;

		/** @var Sign  */
		protected $sign;

		/** @var  string */
		protected $recognized;

		/** @var  int */
		protected $position;

		/** @var  Token[]|TokenGroup[] */
		protected $after_sequence = [];

		/** @var  Token[]|TokenGroup[] */
		protected $before_sequence = [];

		/**
		 * @Constructor
		 *
		 * @param Context $context
		 * @param SignInterface $token
		 * @param $recognized
		 * @param $position
		 */
		public function __construct(Context $context, SignInterface $token, $recognized, $position){
			$this->context      = $context;
			$this->sign         = $token;
			$this->recognized   = $recognized;
			$this->position     = $position;
		}

		/**
		 * @param Token[]|TokenGroup[] $sequence
		 * @return $this
		 */
		public function setAfterSequence($sequence){
			$this->after_sequence = $sequence;
			return $this;
		}

		/**
		 * @param Token[]|TokenGroup[] $sequence
		 * @return $this
		 */
		public function setBeforeSequence($sequence){
			$this->before_sequence = $sequence;
			return $this;
		}

		/**
		 * @return SignInterface
		 */
		public function getSign(){
			return $this->sign;
		}

		/**
		 * @return string
		 */
		public function getRecognized(){
			return $this->recognized;
		}

		/**
		 * @return Context
		 */
		public function getContext(){
			return $this->context;
		}

		/**
		 * @return string
		 */
		public function getName(){
			return $this->sign->getName();
		}

		/**
		 * @param $name
		 * @return $this
		 */
		public function setName($name){
			return $this;
		}

		/**
		 * @return Token[]|TokenGroup[]
		 */
		public function getAfterSequence(){
			return $this->after_sequence;
		}

		/**
		 * @return Token[]|TokenGroup[]
		 */
		public function getBeforeSequence(){
			return $this->before_sequence;
		}

	}
}

