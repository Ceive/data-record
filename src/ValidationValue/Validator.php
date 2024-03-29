<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 19.11.2016
 * Time: 13:13
 */
namespace Ceive\DataRecord\ValidationValue {

	use Ceive\DataRecord;
	use Ceive\DataRecord\Validation\ValidationCollector;

	/**
	 * Class Validator
	 * @package Jungle\Data\Record\Validator
	 */
	abstract class Validator extends DataRecord\Validation\ValidationRule{

		/**
		 * @param $field_name
		 * @param $value
		 * @param ValidationCollector $collector
		 * @return mixed
		 */
		abstract function validate($field_name, $value, ValidationCollector $collector);

	}
}

