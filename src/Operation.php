<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 21.11.2016
 * Time: 15:54
 */
namespace Ceive\DataRecord {


	/**
	 * Основная идея: Обеспечивает слежение за вложенным сохранением связей
	 * Должен гарантировать прозрачную работу с общим представлением текущего сохранения
	 * Фиксировать иерархию сохранения.
	 * Соблюдать зависимость от инициатора сохранения.
	 * Класс дает возможность работать с операцией за пределами @see Record::save
	 *
	 * Концепцией подхода класс похож на ValidationResult который так-же фиксирует ошибки во вложенных сохранениях и дает отчетность в какой ветке связей произошла ошибка
	 *
	 *
	 *  По идее операция сохранения на один момент может быть только 1 общая, если не считать вложенные связи которые
	 *  так-же могут сохранятся.
	 *  Поэтому при любом @see Record::save мы статично контролируем текущую операцию и предварительные установки
	 *  которые дают понять является ли производная операция сохранением связанных записей или же это абсолютно первый запуск в текущий момент(RecordOperationInitiator).
	 * Class Operation
	 *
	 * @package Jungle\Data\Record
	 */
	class Operation implements RecordAware{

		/** @var Record[] */
		public $operation_records = [ ];

		/** @var array */
		public $relation_points = [ ];

		/** @var array */
		public $parameters = [ ];


		/**
		 * @return bool
		 */
		public function isEmpty(){
			return empty($this->operation_records);
		}

		/**
		 * Стартуем обработку объекта
		 * @param Record $record
		 * @return void
		 */
		public function startRecordCapture(Record $record){
			$this->operation_records[] = $record;
		}

		/**
		 * Заканчиваем обработку Объекта
		 * Останавливаем запись
		 * @param Record $record
		 * @return void
		 */
		public function endRecordCapture(Record $record){
			array_pop($this->operation_records);
		}

		/**
		 * Стартуем обработку RelationField
		 * @param $relation_key
		 * @return void
		 */
		public function startRelationCapture($relation_key){
			$this->relation_points[] = $relation_key;
		}

		/**
		 * Заканчиваем обработку RelationField
		 * @param $relation_key
		 * @return void
		 */
		public function endRelationCapture($relation_key){
			$finished_relation_key = array_pop($this->relation_points);
			if($finished_relation_key !== $relation_key){
				// error
			}
		}

		/**
		 * Получение пройденного пути в текущий момент
		 * profile.contacts
		 * @return null|string
		 */
		public function getElapsedPath(){
			return $this->relation_points ? implode('.', $this->relation_points) : null;
		}

		/**
		 *
		 * Проверка, вошла ли текущая операция и находится ли в обработке связей
		 * @return bool
		 */
		public function inRelationPath(){
			return count($this->operation_records) && count($this->relation_points);
		}

		/**
		 * Получение Главного Объекта-Инициатора
		 * @return Record|null
		 */
		public function getRecord(){
			return $this->operation_records ? $this->operation_records[0] : null;
		}



		/**
		 * Получение названия RelationField относительно Главного объекта-инициатора, если @see inRelationPath
		 * @return string
		 */
		public function getInitRelationName(){
			return isset($this->relation_points[0]) ? $this->relation_points[0] : null;
		}

		
		
		/**
		 * Получение текущего уровня глубины при последовательном вхождении в реляции
		 * @return int
		 */
		public function getDepth(){
			return count($this->relation_points);
		}
		
		/**
		 * @see getElapsedPath
		 * @return null|string
		 */
		public function getPath(){
			return $this->relation_points ? implode('.', $this->relation_points) : null;
		}
		
		
		/**
		 * Получение предыдущего объекта породившего текущую обработку.
		 * @see getCurrentInitiator
		 * @return Record|null
		 */
		public function getPrevRecord(){
			return $this->operation_records ? array_slice($this->operation_records, -1, 1)[0] : null;
		}
		/**
		 * @see getPrevRecord
		 * @return Record|null
		 */
		public function getCurrentInitiator(){
			return $this->operation_records ? array_slice($this->operation_records, -1, 1)[0] : null;
		}
		
		/**
		 * @see getCurrentInitiatorRelation
		 * @return mixed
		 */
		public function getLastRelationName(){
			return $this->relation_points ? array_slice($this->relation_points, -1, 1)[0] : null;
		}
		/**
		 * @see getLastRelationName
		 * @return string|null
		 */
		public function getCurrentInitiatorRelation(){
			return $this->relation_points ? array_slice($this->relation_points, -1, 1)[0] : null;
		}
		
		/**
		 * Получение текущего объекта в операции, над которым проводится обработка сейчас
		 * @return Record|null
		 */
		public function getCurrentRecord(){
			return $this->operation_records ? array_slice($this->operation_records,-1,1)[0] : null;
		}

	}
}

