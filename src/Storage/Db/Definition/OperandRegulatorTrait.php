<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 28.01.2017
 * Time: 21:21
 */
namespace Jungle\Data\Storage\Db\Definition {
	
	use Jungle\Data\Storage\Db\Platform;

	trait OperandRegulatorTrait{

		protected $operands_recognize = [
			Platform::STR_AS_COLUMN,
			Platform::STR_AS_VALUE
		];

		/**
		 * @return $this
		 */
		public function leftStrAsReference(){
			$this->operands_recognize[0] = Platform::STR_AS_COLUMN;
			return $this;
		}

		/**
		 * @return $this
		 */
		public function rightStrAsReference(){
			$this->operands_recognize[1] = Platform::STR_AS_COLUMN;
			return $this;
		}

		/**
		 * @return $this
		 */
		public function leftStrAsValue(){
			$this->operands_recognize[1] = Platform::STR_AS_VALUE;
			return $this;
		}

		/**
		 * @return $this
		 */
		public function rightStrAsValue(){
			$this->operands_recognize[1] = Platform::STR_AS_VALUE;
			return $this;
		}

		/**
		 * @param null $left
		 * @param null $right
		 * @return $this
		 */
		public function operandsAs($left = null, $right = null){
			if(isset($left)) $this->operands_recognize[0] = $left;
			if(isset($right)) $this->operands_recognize[1] = $right;
			return $this;
		}

	}
}

