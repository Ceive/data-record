<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 22.11.2016
 * Time: 12:57
 */
namespace Ceive\DataRecord\ValidationValue {

	use Ceive\DataRecord;
	use Ceive\DataRecord\Validation\ValidationCollector;

	/**
	 * Class CheckLengthBytes
	 * @package Jungle\Data\Record\Validator
	 */
	class CheckLengthBytes extends CheckLength{

		/** @var string */
		public $type = 'CheckLengthBytes';

		/**
		 * @param $field_name
		 * @param $value
		 * @param ValidationCollector $collector
		 */
		function validate($field_name, $value, ValidationCollector $collector){
			$len = strlen($value); // Число байт
			if(is_string($value) && $len > $this->max || $len < $this->min){
				$collector->error($field_name, $this);
			}
		}
	}
}

