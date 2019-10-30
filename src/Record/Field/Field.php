<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 14.11.2016
 * Time: 2:51
 */
namespace Jungle\Data\Record\Field {

	use Jungle\Data\Record\Schema\Schema;

	/**
	 * Class SimpleField
	 * @package Jungle\Data\Record\Field
	 *
	 * Идея поля проста, нужно обеспечить определение достаточно типичным для любого поля.
	 *
	 * сюда даже не подходит тип значения поля, это можно обеспечить в наследниках
	 * единственной возможностью данного поля может быть стабилизатор, который просто меняет значение со входа,
	 * обычно трансформирует из одного типа-переменной в нужный тип
	 *
	 *
	 * Обеспечить проверки значения к полю возможно во внешней среде.
	 *
	 * Поле должно обеспечивать примитивный набор его характеристик
	 *
	 * $original_key будет заменять $mapping хранящийся в схеме
	 *
	 */
	class Field{

		/**
		 * Native field type identifier
		 * @var string
		 */
		protected $field_type = 'string';

		/**
		 * Field name
		 * @var string
		 */
		public $name;

		/**
		 * Value can be NULL by default
		 * @var bool
		 */
		public $nullable = true;

		/**
		 * Default field value, if nullable === false, field must be required
		 * @var null
		 */
		public $default = null;

		/**
		 * SimpleField constructor.
		 * @param $name
		 * @param bool|false $nullable
		 * @param null $default
		 * @param array $options
		 */
		public function __construct($name, $nullable = false, $default = null,array $options = []){
			$this->name = $name;
			if(!is_bool($nullable)){
				if(is_null($default)){
					$this->nullable = is_null($nullable);
					$this->default = $nullable;
				}
			}else{
				$this->nullable = $nullable;
				$this->default = $default;
			}
			foreach($options as $k => $v){
				if($k !== 'field_type')$this->{$k} = $v;
			}
		}



		/**
		 * Конвертация значения пришедшего из хранилища
		 * @param $value
		 * @return mixed
		 */
		public function decode($value){
			return $value;
		}

		/**
		 * Конвертация значения в значение хранимое в базе данных
		 * @param $value
		 * @return mixed
		 */
		public function encode($value){
			return $value;
		}
		
		/**
		 * Стабилизация значения перед валидацией
		 * @param $value
		 * @return mixed
		 */
		public function stabilize($value){
			if($this->nullable && empty($value)){
				return null;
			}
			return $value;
		}

		/**
		 * Родной для поля валидатор, в отличие от полноценных, не передает сообщения, кроме типа поля
		 * @param $value
		 * @return bool
		 */
		public function validate($value){
			return true;
		}

		/**
		 * Системный Тип поля
		 * @return string
		 */
		public function getFieldType(){
			return $this->field_type;
		}

		/**
		 * Инициализация данного поля в схеме
		 * @param Schema $schema
		 */
		public function initialize(Schema $schema){

		}

	}
}

