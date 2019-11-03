<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 20.05.2016
 * Time: 20:54
 */
namespace Ceive\DataRecord {

    use Exception;
    use InvalidArgumentException;
    use Ceive\DataRecord\Exception\FieldAccessViolation;
	use Ceive\DataRecord\Exception\FieldException;
	use Ceive\DataRecord\Exception\FieldReadonlyViolation;
	use Ceive\DataRecord\Exception\FieldUnexpectedValue;
	use Ceive\DataRecord\Relation\Relation;
	use Ceive\DataRecord\Relation\RelationForeign;
	use Ceive\DataRecord\Relation\RelationMany;
	use Ceive\DataRecord\Relation\RelationSchema;
	use Ceive\DataRecord\Relation\Relationship;
	use Ceive\DataRecord\Schema\Schema;
	use Ceive\DataRecord\Validation\ValidationResult;
	use Ceive\DataRecord\Storage\Exception\DuplicateEntry;
	use Ceive\DataRecord\Storage\Exception\Operation;
	use Ceive\DataRecord\Util\Collection;
	use Ceive\DataRecord\Util\Record\PropertyRegistryInterface;
	use Ceive\DataRecord\Util\Record\PropertyRegistryTransientInterface;
	use Ceive\DataRecord\Util\Schema\OuterInteraction\SchemaAwareInterface;
	use Ceive\DataRecord\Util\Storage;

    use Jungle\Http\UploadedFile;
	use Jungle\Util\Value\String;
	
	/**
	 *
	 * aggregations_include = [
	 *      'profile'
	 *      'notes'
	 * ]
	 *
	 *
	 * query('profile.id',newValue);
	 *
	 * hasChangesProperty('profile.id');
	 * getProperty('profile.id');
	 * getRelated('profile');
	 *
	 * hasIn('notes',[
	 *      ['id','=',2]
	 * ])
	 *
	 * Class Record
	 * @package modelX
	 */
	abstract class Record
		implements PropertyRegistryInterface,
		PropertyRegistryTransientInterface,
		Exportable,
		\Iterator,
		\ArrayAccess,
		\Serializable,
		\JsonSerializable,
		SchemaAwareInterface{



		/** @var array  */
		protected static $_default_snapshots = [];

		protected static $_service_properties = [

			'_property_iterator_index',
			'_property_iterator_count',


			'_idx',
			'_class',
			'_schema',

			'_record_state',
			'_operation_made',
			'_operation_options',
			'_property_safe',
			'_initialized',

			'_snapshot',

			'_original',
			'_validation',
		];


		/** @var   */
		protected static $instantiatedRecordsCount = 0;



		/** @var  int */
		private $_property_iterator_index = 0;

		/** @var  int */
		private $_property_iterator_count = 0;



		const OP_NONE   = null;
		const OP_CREATE = 'create';
		const OP_UPDATE = 'update';
		const OP_DELETE = 'delete';

		const STATE_NEW     = 'new';
		const STATE_LOADED  = 'loaded';
		const STATE_DELETED = 'deleted';


		/** @var string  */
		protected $_class;

		/** @var  int */
		protected $_idx;

		/** @var  \Ceive\DataRecord\Schema\Schema */
		protected $_schema;

		/** @var bool  */
		protected $_property_safe = false;

		/** @var bool */
		protected $_initialized = false;

		/** @var  string */
		protected $_operation_made = self::OP_NONE;

		/** @var array  */
		protected $_operation_options = [];

		/** @var string  */
		protected $_record_state = self::STATE_NEW;

		/** @var  mixed */
		protected $_original;

		/** @var  ValidationResult|null */
		protected $_validation;

		/** @var  Snapshot|null */
		protected $_snapshot;

		/** @var  Snapshot|null */
		protected $_related_snapshot;

		/** @var array  */
		protected $_related = [];

		/**
		 * Record constructor.
		 */
		public function __construct(){
			try{
				$this->_property_safe = false;

				$this->_class = get_called_class();
				$this->_idx = ++self::$instantiatedRecordsCount;

				$this->_check_schema();

				if(!isset(self::$_default_snapshots[$this->_class])){
					$exclude = array_flip(static::$_service_properties);
					$data = get_object_vars($this);
					$data = array_diff_key($data,$exclude);
					self::$_default_snapshots[$this->_class] = $data;
				}

				$data = [];
				// выставим свойства по умолчанию
				foreach($this->_schema->getDefaultAttributes() as $k => $v){
					$data[$k] = $this->{$k} = $v;
				}

				if(!$this->_schema->isRecordMaking()){

					// Важно: Через схему вызовется Record::setInitialized, а в нем Record::_apply_state
					// $original_sync === false
					// $analyzed_data === schema fields defaults
					$this->_schema->initializeRecord($this, $data);
				}
			}finally{
				$this->_property_safe = true;
			}
		}

		/**
		 * @return string
		 */
		public function getOperationMade(){
			return $this->_operation_made;
		}

		/**
		 *
		 */
		protected function _check_schema(){

			$schema_manager = SchemaManager::getDefault();
			$schema = $schema_manager->getLoadedSchema($this->_class);
			if(!$schema){
				$schema = $schema_manager->factorySchema($this->_class);
				$schema_manager->initializeSchema($schema, $this);
			}

			$this->_schema = $schema;
		}

		/**
		 * Основной метод для универсального сброса актуального состояния физических полей объекта
		 *
		 * Сбрасывает снапшот полей на состояние $data,
		 * @param array $analyzed_data может быть взят из свойств объекта или из оригинала.
		 * Должен содержать значения всех полей схемы
		 *
		 * @param bool|false $original_sync управляет синхронизацией с оригиналом,
		 * если тру то состояние свойств изменится на текущий оригинал или на умолчания.
		 * и если $analyzed_data NULL то снапшот будет создан можно сказать из оригинала,
		 * т.к оригинал предварительно будет выставлен в свойства объекта
		 * на основе которых формируется $data в этом методе
		 *
		 * $data может быть передан по предварительному анализу $this->{field_name} из вне
		 *
		 */
		protected function _apply_state($original_sync = false, array $analyzed_data = null){
			if($original_sync){

				if($this->_original === null){
					foreach($this->_schema->getDefaultAttributes() as $k => $v){
						$this->{$k} = $v;
					}
				}else{
					foreach($this->_schema->decodeRaw($this->_original) as $k => $v){
						$this->{$k} = $v;
					}
				}
			}
			if($analyzed_data === null){
				if(!$this->_snapshot){
					$analyzed_data = [];
					foreach($this->_schema->getDefaultAttributes() as $k => $v){
						$analyzed_data[$k] = $this->{$k};
					}
				}else{
					$analyzed_data = $this->data();
				}
			}
			$this->_snapshot = new Snapshot($analyzed_data);

			$relations = $this->_schema->getRelations();
			if($relations){
				$this->_related_snapshot = new Snapshot($this->_related);
			}
		}

		/**
		 * @param Schema $schema
		 * @return $this
		 */
		public function setSchema(Schema $schema){
			$this->_schema = $schema;
			return $this;
		}

		/**
		 * @return Schema
		 */
		public function getSchema(){
			return $this->_schema;
		}

		/**
		 * @param string $record_state
		 * @param $original_data
		 * @return $this
		 */
		public function setRecordState($record_state = self::STATE_LOADED, $original_data = []){

			// защита от изменения состояния если запись инициализована
			if($this->_initialized){
				throw new \LogicException('Record is already initialized!');
			}

			$this->_record_state = $record_state;

			if($this->_original !== $original_data){
				$this->_original = $original_data;
			}

			return $this;
		}

		/**
		 * @return string
		 */
		public function getRecordState(){
			return $this->_record_state;
		}

		/**
		 * @return mixed
		 */
		public function getOriginalData(){
			return $this->_original;
		}

		/**
		 * @param bool $initialized
		 * @param array $_data
		 * @return $this
		 */
		public function setInitialized($initialized = true,array $_data = null){
			if(($old = $this->_initialized) !== $initialized){
				$this->_initialized = $initialized;
				if($initialized === true){

					// Пере-Инициализация снимка данных актуального состояния
					$this->_apply_state($this->_original !== null, $_data);

					if($this->_record_state === self::STATE_LOADED){
						$this->_schema->afterFetch($this);
					}else{
						$this->_schema->justConstruct($this);
					}
					$this->onRecordReady();
				}
			}
			return $this;
		}



		/**
		 * @return bool
		 */
		public function isInitialized(){
			return $this->_initialized;
		}

		/**
		 * @return int
		 */
		public static function getStatusInstantiatedRecordsCount(){
			return self::$instantiatedRecordsCount;
		}



		/**
		 * @return Record|Record[]|Relationship|mixed
		 * @throws Exception
		 */
		public function getTitleValue(){
			return $this->getProperty($this->_schema->tk);
		}

		/**
		 * @return mixed
		 */
		public function getPkValue(){
			return $this->getProperty($this->_schema->pk);
		}



		public function getBootField(){
			return null;
		}

		public function getBootValue(){
			return null;
		}


		/**
		 * @return string
		 */
		public function getSource(){
			return null;
		}

		/**
		 * @return string
		 */
		public function getWriteSource(){
			return $this->getSource();
		}

		/**
		 * @return string
		 */
		public function getStorageService(){
			return 'database';
		}

		/**
		 * @return Storage|string
		 */
		public function getWriteStorageService(){
			return $this->getStorageService();
		}




		/**
		 * @return array
		 */
		protected function data(){
			$a = [];
			foreach($this->_schema->fields as $k => $v){
				$a[$k] = $this->{$k};
			}
			return $a;
		}


		/**
		 * Функция для получения значений, с возможностью указания нужных свойств в наборе
		 * @param array|null $name_list
		 * @return array
		 */
		public function getProperties(array $name_list = null){
			if($name_list === null){
				return $this->data();
			}else{
				$a = [];
				foreach(array_intersect(array_keys($this->_schema->fields),$name_list) as $key){
					$a[$key] = $this->{$key};
				}
				return $a;
			}
		}


		/**
		 * @Complex-Triggered
		 * @param array $data
		 * @param null|string[]|string|int[]|int $whiteList
		 * @param null|string[]|string|int[]|int $blackList
		 * @param $actual
		 * @return $this
		 */
		public function assign(array $data, $whiteList = null, $blackList = null, $actual = false){
			$related_data = array_intersect_key($data,$this->_schema->relations);
			$data = array_intersect_key($data,$this->_schema->fields);
			if($whiteList !== null){
				if(!is_array($whiteList)){
					if(!is_numeric($whiteList) || !is_string($whiteList)){
						throw new InvalidArgumentException('White list allow value types: array or string or numeric');
					}
					$whiteList = [ $whiteList ];
				}
				$related_data = array_intersect_key($related_data, array_flip($whiteList) );
				$data = array_intersect_key($data, array_flip($whiteList) );
			}
			if($blackList !== null){
				if(!is_array($blackList)){
					if(!is_numeric($blackList) || !is_string($blackList)){
						throw new InvalidArgumentException('White list allow value types: array or string or numeric');
					}
					$blackList = [ $blackList ];
				}
				$related_data = array_diff_key($related_data, array_flip($blackList) );
				$data = array_diff_key($data, array_flip($blackList) );
			}

			// выставляем значения в свойства объекта
			foreach($data as $key => $val){
				$this->{$key} = $val;
			}
			foreach($related_data as $key => $related){
				$this->setRelated($key, $related);
			}

			if($actual){
				// данные которые были выставленны просто перекрывают прошлые данные в раннем снапшоте
				$earliest = $this->_snapshot->earliest();
				$earliest->setData(array_replace($earliest->data(), $data));
			}

			return $this;
		}

		/**
		 * @param $key
		 * @param $value
		 * @return $this
		 * @throws FieldAccessViolation
		 * @throws ORMException
		 * @throws FieldException
		 * @throws FieldReadonlyViolation
		 * @throws FieldUnexpectedValue
		 */
		public function setProperty($key, $value){

			if(($pos = strpos($key,'.')) !==false){
				$ct = $this;
				do{
					$chunk = trim(substr($key,0,$pos),'.');
					$key = trim(substr($key,$pos+1),'.');
					if($key){
						$ct = $ct->getProperty($chunk);
					}else{
						return $ct->setProperty($chunk, $value);
					}
				}while(($pos = strpos($key,'.')) !==false);
			}

			if(array_key_exists($key, $this->_schema->fields)){
				$this->{$key} = $value;
			}
			if(isset($this->_schema->relations[$key])){
				if($this->_schema->relations[$key] instanceof RelationMany){
					$this->addRelated($key,$value);
				}else{
					$this->setRelated($key,$value);
				}
			}
			return $this;
		}

		/**
		 * @param $key
		 * @return bool
		 */
		public function hasProperty($key){
			return isset($this->_schema->fields[$key]) || isset($this->_schema->relations[$key]);
		}

		/**
		 * @param $key
		 * @return Record|Record[]|Relationship|mixed
		 * @throws Exception
		 */
		public function getProperty($key){

			if(($pos = strpos($key,'.')) !==false){
				$ct = $this;
				do{
					$chunk = trim(substr($key,0,$pos),'.');
					$key = trim(substr($key,$pos+1),'.');
					if($key){
						$ct = $ct->getProperty($chunk);
					}else{
						return $ct->getProperty($chunk);
					}
				}while(($pos = strpos($key,'.')) !==false);
			}

			if(isset($this->_schema->fields[$key])){
				return $this->{$key};
			}

			if(isset($this->_schema->relations[$key])){
				return $this->getRelated($key);
			}

			throw new Exception('Trying to getProperty "'.$key.'" not existing in schema "'.$this->_schema->getName().'"');
		}




		/**
		 * Отдает связанный объект без загрузки из бд,
		 * тоесть только если связь по ключу уже была загружена в память
		 * @param $relation_key
		 *
		 * @return bool|false в случае если связь не загружена
		 * @return null в случае если связь пустая
		 * @return Record|Relationship|bool
		 */
		public function getRelatedLoaded($relation_key){
			if(array_key_exists($relation_key, $this->_related)){
				return $this->_related[$relation_key];
			}
			if($this->_record_state===self::STATE_NEW){
				return null;
			}
			if(isset($this->_schema->relations[$relation_key])){
				$relation = $this->_schema->relations[$relation_key];
				if($relation instanceof RelationForeign){
					if(!array_diff_assoc($this->getProperties($f = $relation->getLocalFields()),array_fill_keys($f,null))){
						return null;
					}
				}
			}

			return false;
		}

		/**
		 * @param $relation_key
		 * @param array $options
		 * @return Record|Relationship|null
		 * @throws Exception
		 */
		public function getRelated($relation_key, array $options = null){
			if(array_key_exists($relation_key, $this->_related)){
				$related = $this->_related[$relation_key];
			}else{
				$relation = $this->_schema->getRelation($relation_key);
				if(!$relation){
					throw new Exception('Trying to getRelated "'.$relation_key.'" not existing in schema "'.$this->_schema->getName().'"');
				}else{
					 $this->_related[$relation_key] = $related = $relation->load($this);
				}
			}

			if($options && $related instanceof Relationship){

				$options = array_replace([
					'condition' => null,
					'limit' => null,
					'offset' => null,
					'ordering' => null,
				],$options);


				return $related->extend(
					$options['condition'],
					$options['limit'],
					$options['offset'],
					$options['ordering']
				);

			}
			return $related;
		}


		/**
		 * @param $relation_key
		 * @param Record|UploadedFile|null $object
		 * @return $this
		 * @throws Exception
		 */
		public function setRelated($relation_key, $object = null){
			if(isset($this->_schema->relations[$relation_key])){
				$r = $this->_schema->relations[$relation_key];
				if($r instanceof RelationSchema && is_object($object) && !$object instanceof Record){
					throw new InvalidArgumentException('$object Must be a "'.Record::class.'" instance');
				}

				if($r instanceof RelationMany){
					$this->addRelated($relation_key,$object);
				}else{
					$this->_related[$relation_key] = $object;
				}
			}else{

				throw new Exception('Trying to setRelated "'.$relation_key.'" not existing in schema "'.$this->_schema->getName().'"');

			}
			return $this;
		}

		/**
		 * @param $relationship_key
		 * @param Record|array $object
		 * @return $this
		 * @throws Exception
		 */
		public function addRelated($relationship_key, $object){
			if(array_key_exists($relationship_key, $this->_related)){
				$relationship = $this->_related[$relationship_key];
				if(!$relationship instanceof Relationship){
					throw new Exception('add{Related:'.$relationship_key.'} must be call to Many relation');
				}
			}else{
				$relation = $this->_schema->getRelation($relationship_key);
				if(!$relation){
					throw new Exception('Trying to addRelated "'.$relationship_key.'" not existing in schema "'.$this->_schema->getName().'"');
				}elseif(!$relation instanceof RelationMany){
					throw new Exception('add{Related:'.$relationship_key.'} must be call to Many relation');
				}else{
					$this->_related[$relationship_key] = $relationship = $relation->load($this);
				}
			}

			if(!$object instanceof Record && !is_array($object) && !$object instanceof \Iterator){
				throw new InvalidArgumentException('argument object must be Record or Record[](array or \Iterator)');
			}

			if($object instanceof Record){
				$relationship->add($object);
			}else{
				foreach($object as $record){
					$relationship->add($record);
				}
			}
			return $this;
		}

		public function __call($name, $arguments){
			$three = substr($name, 0, 3);
			switch($three){

				case 'get':
					$tail = HelperString::uncamelize(substr($name, 3),'_');
					return $this->getRelated($tail,isset($arguments[0])?$arguments[0]:null);
					break;
				case 'set':
					$tail = HelperString::uncamelize(substr($name, 3),'_');
					return $this->setRelated($tail,isset($arguments[0])?$arguments[0]:null);
					break;
				case 'add':
					$tail = HelperString::uncamelize(substr($name, 3),'_');
					return $this->addRelated($tail,$arguments);
					break;
				default:
					if(substr($name, 0, 4) === 'find'){
						$tail = HelperString::uncamelize(substr($name, 4),'_');
						foreach($this->_schema->relations as $relation){
							if($relation instanceof RelationMany && $relation->each_name === $tail){
								return $this->getRelated($relation->name, isset($arguments[0])?$arguments[0]:null);
							}
						}
					}
					break;
			}

			throw new \BadMethodCallException('Trying to call the not defined method "'.$name.'"');

		}


		/**
		 * @param bool $public
		 * @param array $include_related
		 * @return array
		 * @throws Exception
		 * @TODO control export fields & relations
		 */
		public function export( $public = true ,array $include_related = null){
			$values = [ ];
			if($this->_initialized){
				if($public){
					foreach($this->_schema->getPublicNames() as $name){
						$values[$name] = $this->getProperty($name);
					}
				}else{
					foreach($this->_schema->fields as $name => $field){
						$values[$name] = $this->getProperty($name);
					}
				}

				if($include_related){
					foreach($include_related as $relation_name => $sub_inc){
						if(is_int($relation_name)){
							$relation_name = $sub_inc;
							$sub_inc = null;
						}
						if(isset($this->_schema->relations[$relation_name])){
							$object = $this->getRelated($relation_name);
							if($object instanceof Collection){
								$values[$relation_name] = $object->listCollector(['export',$public, $sub_inc]);
							}else{
								$values[$relation_name] = $object->export($public,$sub_inc);
							}
						}
					}
				}
				return $values;
			}
			return $values;
		}


		/**
		 * Более сплоченно стандартизирует экспорт, с точки зрения всех возможных полей, и опций связанных объектов
		 * можно контролировать выборку относительных объектов, тем более это актуально для коллекций
		 * @param array $map
		 * @param bool $default_public
		 * @return array
		 * @throws Exception
		 */
		public function exportMap( array $map = null, $default_public = true){
			if(!$map){
				return $this->export($default_public);
			}
			$values = [ ];
			if($this->_initialized){
				$public = boolval(isset($map['public'])?$map['public']:$default_public);
				if($public){
					$names = $this->_schema->getPublicNames();
				}else{
					$names = array_keys($this->_schema->fields);
				}
				if(isset($map['white'])){
					$names = array_intersect($names,(array)$map['white']);
				}
				if(isset($map['black'])){
					$names = array_diff($names,array_intersect($names,(array)$map['black']));
				}
				foreach($names as $name){
					$values[$name] = $this->getProperty($name);
				}
				if(isset($map['relations'])){
					foreach($map['relations'] as $relation_name => $sub_map){
						if(is_int($relation_name)){
							$relation_name = $sub_map;
							$sub_map = null;
						}
						if(isset($this->_schema->relations[$relation_name])){
							$object = $this->getRelated($relation_name,isset($sub_map['options'])?$sub_map['options']:null);
							unset($sub_map['options']);
							if($object instanceof Collection){
								$values[$relation_name] = $object->listCollector(['exportMap',$sub_map, $default_public]);
							}else{
								$values[$relation_name] = $object->exportMap($sub_map,$default_public);
							}
						}
					}
				}
				return $values;
			}
			return $values;
		}


		/**
		 * @param null|array|string $field
		 * @return bool
		 */
		public function hasChangesProperty($field = null){
			if(is_string($field) && ($pos = strpos($field,'.')) !==false){
				$ct = $this;
				do{
					$chunk = trim(substr($field,0,$pos),'.');
					$field = trim(substr($field,$pos+1),'.');
					if($field){
						return $ct->hasChangesProperty($chunk);
					}else{
						return $ct->hasChangesProperty($chunk);
					}
				}while(($pos = strpos($field,'.')) !==false);
			}


			// получаем исходный снимок и сверяем его напрямую с текущими значениями свойств объекта

			if($field === null){
				$data = $this->_snapshot->earliest()->data();
				foreach($data as $k => $v){
					if($this->{$k} !== $v){
						return true;
					}
				}

				return false;
			}elseif(is_array($field)){
				foreach($field as $name){
					if($this->hasChangesProperty($name)){
						return true;
					}
				}
				return false;
			}else{
				if(isset($this->_schema->relations[$field])){
					return $this->hasChangesRelated($field);
				}else{
					$data = $this->_snapshot->earliest()->data();
					return $data[$field] !== $this->{$field};
				}
			}
		}

		/**
		 * @param $relation_key
		 * @return bool
		 */
		public function hasChangesRelated($relation_key = null){
			$related_snapshot_data = $this->_related_snapshot?$this->_related_snapshot->earliest()->data():[];
			if(is_string($relation_key)){
				if(isset($related_snapshot_data[$relation_key]) xor isset($this->_related[$relation_key])){
					return true;
				}
				return isset($related_snapshot_data[$relation_key])
				       && isset($this->_related[$relation_key])
				       && $related_snapshot_data[$relation_key] !== $this->_related[$relation_key];
			}
			$loaded_related = $this->_related;

			if($relation_key === null){
				$relations = array_intersect_key($this->_schema->relations, $loaded_related);
			}else{
				$relations = array_intersect_key($this->_schema->relations, $loaded_related, array_flip($relation_key));
			}
			foreach($relations as $name => $relation){
				if(isset($loaded_related[$name])){
					$loaded = $loaded_related[$name];
					if($relation instanceof RelationMany){
						/** @var Relationship $loaded */
						if($loaded->isDirty()){
							return true;
						}
					}elseif((!isset($related_snapshot_data[$name]) && $loaded) || ($loaded !== $related_snapshot_data[$name])){
						return true;
					}
				}
			}
			return false;
		}

		/**
		 * @param null|array|string $field
		 * @return array
		 */
		public function getChangedProperties($field = null){
			// получаем исходный снимок и сверяя с ним выбераем измененые значения из свойств объекта
			$a = [];
			if($field && !is_array($field)){
				$field = [$field];
			}
			if($this->_record_state === self::STATE_NEW){
				$fields = array_keys($this->_schema->fields);
				if($field){
					$fields = array_intersect($fields, $field);
				}
				foreach($fields as $key){
					$a[$key] = $this->{$key};
				}
			}else{
				$data = $this->_snapshot->earliest()->data();
				if($field){
					$data = array_intersect_key($data, array_flip($field));
				}
				foreach($data as $k => $old){
					$new = $this->{$k};
					if($new !== $old) $a[$k] = $new;
				}
			}
			return $a;
		}


		protected $_analyzed_changes;

		public function refreshAnalyzedChanges(){
			$this->_analyzed_changes = $this->analyzeChanges();
		}

		public function getAnalyzedChanges(){
			return $this->_analyzed_changes;
		}

		/**
		 * @param null $field
		 * @param bool|true|string $analyze_modified_in_collection
		 * @return array
		 */
		public function analyzeChanges($field = null, $analyze_modified_in_collection = true){
			$analyzed = [];
			if($field && !is_array($field)){
				$field = [$field];
			}
			if($this->_record_state === self::STATE_NEW){
				$fields = array_keys($this->_schema->fields);
				if($field){
					$fields = array_intersect($fields, $field);
				}
				foreach($fields as $key){
					if($this->{$key}!==null){
						$analyzed[$key] = [
							'-' => null,
							'+' => $this->{$key}
						];
					}
				}
			}else{
				$data = $this->_snapshot->earliest()->data();
				if($field){
					$data = array_intersect_key($data, array_flip($field));
				}
				foreach($data as $k => $old){
					$new = $this->{$k};
					if($new !== $old){
						$analyzed[$k] = [
							'-' => $old,
							'+' => $new
						];
					}
				}
			}

			$relations_snapshot = $this->_related_snapshot->earliest();

			$relations = $this->_schema->relations;
			$related_old = $relations_snapshot->data();

			foreach($relations as $name => $relation){
				$old    = array_key_exists($name, $related_old)?$related_old[$name]:false;
				$loaded = array_key_exists($name, $this->_related)?$this->_related[$name]:false;
				if($old === $loaded){
					$related_changes[$name] = false;
				}else{
					if($relation instanceof RelationMany){
						/** @var Relationship|false $loaded */
						if($loaded){
							$m = null;
							$dirty_detached = $loaded->getDirtyRemovedItems();
							$dirty_attached = $loaded->getDirtyAddedItems();
							if($analyze_modified_in_collection){
								if(is_bool($analyze_modified_in_collection)){
									$analyze_modified_in_collection = Relationship::CHECK_MODIFY_FIELDS;
								}
								$m = $loaded->getModified($analyze_modified_in_collection);
							}
							$many_relation = [
								'type' => 'many',
								'modify' => null,
								'change' => null
							];
							$many_relation['modify'] = $m?:null;
							if($dirty_attached || $dirty_detached){
								$many_relation['change'] = [
									'-' => $dirty_detached,
									'+' => $dirty_attached,
								];
							}
							if($m || $dirty_attached || $dirty_detached){
								$analyzed[$name] = $many_relation;
							}
						}
					}else{
						/** @var Record|null|false $loaded */
						if($loaded!==false){
							// объект не был загружен = не проверен
							if($old === false){
								$old = $relation->load($this);
								$relations_snapshot->set($name,$old);
							}

							if($old !== $loaded){
								$analyzed[$name] = [
									'type'      => 'one',
									'modify'    => null,
									'change'    => [
										'-' => $old,
										'+' => $loaded,
									],
								];
							}elseif($loaded && $loaded->hasChangesProperty()){
								$analyzed[$name] = [
									'type'      => 'one',
									'modify'    => $loaded,
									'change'    => null,
								];
							}
						}
					}


				}

			}
			return $analyzed;
		}

		/**
		 * Актуализация данных
		 */
		public function refresh(){
			if($this->_record_state === self::STATE_NEW){
				$this->_original = null;
				$this->_apply_state(true, null);
				$this->onRecordReady();
			}else{
				$item = $this->_schema->storageLoadById($this->getPkValue());
				if($item !== $this->_original){
					$this->_original = $item;
					$this->_apply_state(true, null);
					$this->onRecordReady();
				}
			}
			return $this;
		}

		/**
		 * @param null $fieldName
		 * @return mixed
		 */
		public function reset($fieldName = null){
			$data = $this->_snapshot->earliest()->data();
			if($fieldName === null){
				foreach($data as $k => $v){
					$this->{$k} = $v;
				}
			}else{
				if(array_key_exists($fieldName, $data)){
					$this->{$fieldName} = $data[$fieldName];
				}
			}
		}


		public function stabilize(){
			// Values Stabilizing
			foreach($this->_schema->fields as $name => $field){
				$this->{$name} = $field->stabilize($this->{$name});
			}
		}



		/**
		 * @param bool $throw
		 * @return bool|ValidationResult
		 * @throws ValidationResult
		 */
		public function validate($throw = true){
			// Values Stabilizing
			foreach($this->_schema->fields as $name => $field){
				$this->{$name} = $field->stabilize($this->{$name});
			}
			$this->_validation = $validation = new ValidationResult($this);
			$this->_schema->validate($this,$validation);
			if($throw){
				if($validation->hasErrors()){
					throw $validation;
				}
				return true;
			}else{
				return $validation;
			}
		}

		/**
		 * @return ValidationResult|null
		 */
		public function getRecordValidation(){
			return $this->_validation;
		}

		/**
		 * @return Snapshot
		 */
		public function getSnapshot(){
			return $this->_snapshot;
		}

		/**
		 * @return Snapshot|null
		 */
		public function getRelatedSnapshot(){
			return $this->_related_snapshot;
		}

		/**
		 * @param Operation $operation
		 * @param bool $delete
		 * @return bool
		 * @throws Operation
		 * @throws ValidationResult
		 * @throws null
		 */
		protected function _handleStorageOperationException(Operation $operation, $delete = false){
			if($this->_validation){
				// будем перехватывать только ошибку дупликата
				if($operation instanceof DuplicateEntry){
					$this->_validation->addConstraintError(ValidationResult::CONSTRAINT_DUPLICATE);
					throw $this->_validation;
				}
			}
			throw $operation;
		}





		/**
		 * @return bool
		 * @throws ORMException
		 */
		public function save(){
			if($this->_operation_made !== self::OP_NONE){
				if($this->_operation_made === self::OP_DELETE){
					throw new ORMException('Current operation execute is not allow saving record!');
				}
				return true;
			}
			$repository = $this->_schema->getRepository();
			try{
				$repository->startOperation($this);

				switch($this->_record_state){
					case self::STATE_NEW:

						if($this->_schema->beforeCreate($this)!==false){
							$this->_operation_made = self::OP_CREATE;
							$this->_operation_options = [];
							if($this->_doCreate()){
								$this->_operation_made  = self::OP_NONE;
								$this->_record_state    = self::STATE_LOADED;
								$this->_schema->onCreate($this);
								return true;
							}
						}

						break;

					case self::STATE_LOADED:

						if(!$this->_analyzed_changes = $this->analyzeChanges()){
							return true;
						}
						if($this->_schema->beforeUpdate($this)!==false){
							$this->_operation_made = self::OP_UPDATE;
							$this->_operation_options = [];
							if($this->_doUpdate()){
								$this->_schema->onUpdate($this);
								$this->_operation_made = self::OP_NONE;
								$this->onUpdate();
								$this->onSave();
								return true;
							}
						}
						break;
				}
				return false;
			}finally{
				$repository->endOperation($this);
				$this->_validation = null;
				$this->_analyzed_changes = null;
				$this->_operation_made = self::OP_NONE;
			}
		}

		/**
		 * @return bool
		 * @throws Operation
		 * @throws ValidationResult
		 * @throws Exception
		 * @throws bool
		 * @throws null
		 */
		protected function _doCreate(){
			$schema = $this->_schema;

			$pk_field = $schema->getPkField();
			$pk = $schema->getPk();

			$operation = $schema->getRepository()->currentOperationControl();

			$store = $schema->getWriteStorage($this);
			$source = $schema->getWriteSource($this);

			// допустим объект сохраняется, чтобы обеспечить функциональность событий по отношениям
			// нужно получить список Связей у которых стоит прослушка событий текущей схемы
			// Виды прослушек следующие:
			//  Создания,
			//  Обновления,
			//  Сохранения,
			//  Удаления
			// При этом объект который создает событие уже в памяти - это нужно иметь в виду
			// А объекты которые отреагируют на него, могут быть даже не загружены, поэтому следует
			// Произвести действия в базе данных либо загрузить все связанные объекты для обработки событий
			//
			// Так-же запись создающая событие может и не быть в памяти,
			// т.к обновлялась из Коллекции способом синхронизации с хранилищем
			// Тут получается что была обновленна коллекция записей и каждая запись связана
			// с другими записями что с точки зрения такой событийности пораждает иерархичное
			// или рекурсивное реагирование
			// Возможно в таком случае помогут SET CASE WHEN THEN ELSE END для связанной коллекции, но это уже методом обновления .


			$this->refreshAnalyzedChanges();
			$schema->inspectContextEventsBefore($this, $this->_analyzed_changes);
			$validation = null;
			/** @var Relation[] $relations */
			$relations = array_intersect_key($this->_schema->relations,$this->_analyzed_changes);

			try{

				if($this->{$pk}){
					// PK был выставлен
					$pk_value = $this->{$pk};
				}else{
					// до сохранения можно получить значение PK
					if(($pk_value = $schema->pkBeforeCreate($this, $pk_field, $store, $source)) ){
						$this->{$pk} = $pk_value;
						$this->_operation_options['pk_pre_generated'] = true;
					}
				}

				if($relations){
					//      ForeignRelations Save
					$store->begin();

					foreach($relations as $relation_name => $relation){
						try{
							$operation->startRelationCapture($relation_name);
							$relation->beforeRecordCreate($this);
							$relation->beforeRecordSave($this);
						}catch(ValidationResult $relatedValidation){
							if(!$validation){
								$validation = $this->validate(false);
							}
							$validation->addRelatedValidation($relation_name, $relatedValidation);
						}finally{
							$operation->endRelationCapture($relation_name);
						}
						$relation->inspectContextEventsBefore($this,$this->_analyzed_changes[$relation_name]);
					}
				}

				if(!$validation){
					// Validation
					$validation = $this->validate(false);
				}


				if($validation->hasErrors()){
					throw $validation;
				}
				$schema->beforeStorageCreate($this);

				if($this->{$pk}){
					// PK был выставлен
					$pk_value = $this->{$pk};
				}

				$data = $this->data();
				if(!$pk_value){
					unset($data[$pk]);
				}
				$original = $schema->encodeRaw($data);

				if(!$schema->storageCreate($original, $source, $store)){

					// откат в случае ошибок
					if($relations) $store->rollback();

					return false;
				}

				// Синхронизируем оригинальные данные из свойств объекта, т.к сохранение прошло успешно
				if($pk_value){
					// идентификатор был получен перед сохранением и успешно сохранился в хранилище
					$this->{$pk} = $data[$pk] = $pk_value;
					$this->_original = $original;
				}else{
					// получаем идентификатор созданной записи в хранилище
					$pk_value = $store->lastCreatedIdentifier();
					// Стабилизируем полученное значение по правилам поля
					$pk_value = $this->_schema->stabilize($this, $pk, $pk_value);

					$this->{$pk} = $data[$pk] = $pk_value;

					$this->_original = $schema->valueAccessSet($data, $pk, $pk_value);
				}

				$schema->inspectContextEventsAfter($this, $this->_analyzed_changes);

				if($relations){

					//для RelationSchemaHost запуск событий будет здесь

					foreach($relations as $relation_name => $relation){

						$relation->inspectContextEventsAfter($this, $this->_analyzed_changes[$relation_name]);

						try{
							$operation->startRelationCapture($relation_name);
							$relation->afterRecordCreate($this);
							$relation->afterRecordSave($this);
						}catch(Record\Validation\ValidationResult $relatedValidation){
							$validation->addRelatedValidation($relation_name, $relatedValidation);
						}finally{
							$operation->endRelationCapture($relation_name);
						}
					}

					// До коммита делаем выброс Валидации на верхний уровень
					if($validation->hasErrors()) throw $validation;

					// успешно завершаем транзакцию
					$store->commit();
				}else{
					//делаем выброс Валидации на верхний уровень
					if($validation->hasErrors()) throw $validation;
				}

			}catch(Exception $e){

				// откат в случае ошибок
				if($relations) $store->rollback();

				if($e instanceof Operation){
					$this->_handleStorageOperationException($e,true);
					return false;
				}else{
					throw $e;
				}
			}
			// Сбрасываем снапшот на текущее сохраненное состояние
			$this->_apply_state(false, $data);

			$this->_onCreate();
			return true;

		}

		/**
		 * @return bool
		 * @throws Operation
		 * @throws ValidationResult
		 * @throws Exception
		 * @throws bool
		 * @throws null
		 */
		protected function _doUpdate(){

			$schema = $this->_schema;


			/*
			 * Хотелось бы добавить выборку только по измененным полям
			 * В валидации и связях
			 */

			$pk_value = $this->getPkValue();
			$dynamic_update = $schema->isDynamicUpdate();

			$operation = $schema->getRepository()->currentOperationControl();

			$store = $schema->getWriteStorage($this);


			// Validation
			$validation = $this->validate(false);

			if($validation->hasErrors()){
				throw $validation;
			}


			$schema->inspectContextEventsBefore($this, $this->_analyzed_changes);

			/** @var Relation[] $relations */
			$relations = array_intersect_key($this->_schema->relations, $this->_analyzed_changes);
			$related_earliest = null;
			if($relations){
				$related_earliest = $this->_related_snapshot->earliest();
			}

			try{

				if($relations){
					// вычисление работы отношений перед сохранением
					// исходя из того что в схеме есть Отношения, нужно работать с Транзакциями
					$store->begin();
					foreach($relations as $relation_name => $relation){
						try{
							$operation->startRelationCapture($relation_name);
							$relation->beforeRecordUpdate($this, $related_earliest);
							$relation->beforeRecordSave($this, $related_earliest);
						}catch(Record\Validation\ValidationResult $relatedValidation){
							$validation->addRelatedValidation($relation_name, $relatedValidation);
						}finally{
							$operation->endRelationCapture($relation_name);
						}
						$relation->inspectContextEventsBefore($this, $this->_analyzed_changes[$relation_name]);
					}


					/*
					 * Цель: Улучшить удобство координальных изменений, и систематизировать их в мутации
					 * в автоматическом режиме (событийные модели, изменение иерархии, перенесение узла)
					 *
					 * По Множественным связям Учитывать факт:
					 * при отсоединении (сквозной: удаление; привязанный: либо удалить либо сбросить FK)
					 *
					 * Дву-стороннее связывание OPPOSITE paths
					 *      Это нужно для обратных контролей - но пока нужно глубже проанализировать этот факт.
					 * Выставление действительного связанного объекта (как будто он уже применен)
					 *      Это нужно будет в аггрегационных запросах, чтобы проставить все загруженные
					 *      за раз связанные объекта, не спровоцировав состояние dirty
					 *
					 *
					 * Нужно провести анализ изменения в Отношениях
					 * Для коллекций и сквозных коллекций это проверка на добавление/удаление записей из стека
					 * Так-же проверка каждого на наличие Модификаций
					 *
					 * Для одиночных это проверка на наличие модификаций или change(attach/detach)
					 *
					 *
					 * Нужно задать общий стандарт работы со сквозными путями, чтобы любой метод уже поддерживал это.
					 * В том числе setters, для присвоения данных связанным объектам например через assign.
					 * Но тогда рождается многозначность, если объекта НЕТ и некому выставить значение.
					 *      Возможно автоматически создать объект(нужен фантом и настройки).
					 *      Возможно выдать ошибку отсутствия.
					 *      Возможно просто проигнорировать.
					 *      Задача: Настройки и фантомы можно указывать как по дефолту, так и из вне при конкретных операциях
					 */

				}

				$schema->beforeStorageUpdate($this);

				$dirty_data = $this->getChangedProperties();

				$data = $this->data();

				if($dynamic_update){
					$to_storage = $schema->encodeRaw($dirty_data);
					$to_original = $schema->encodeRaw( $data, $this->_original);
				}else{
					$to_storage = $schema->encodeRaw( $data, $this->_original);
					$to_original = $to_storage;
				}

				if($to_storage){

					if(!$schema->storageUpdateById($to_storage, $pk_value)){
						// откат в случае ошибок
						if($relations) $store->rollback();
						return false;
					}

					// Синхронизируем оригинальные данные из свойств объекта, т.к сохранение прошло успешно
					$this->_original = $to_original;
				}

				$schema->inspectContextEventsAfter($this, $this->_analyzed_changes);
				$relations = array_intersect_key($this->_schema->relations, $this->_analyzed_changes);
				$related_earliest = null;
				if($relations){
					$related_earliest = $this->_related_snapshot->earliest();
				}

				if($relations){
					// вычисление работы отношений после сохранения
					// исходя из того что в схеме есть Отношения, нужно работать с Транзакциями
					foreach($relations as $relation_name => $relation){
						$relation->inspectContextEventsAfter($this, $this->_analyzed_changes[$relation_name]);
						try{
							$operation->startRelationCapture($relation_name);
							$relation->afterRecordUpdate($this, $related_earliest);
							$relation->afterRecordSave($this, $related_earliest);
						}catch(Record\Validation\ValidationResult $relatedValidation){
							$validation->addRelatedValidation($relation_name, $relatedValidation);
						}finally{
							$operation->endRelationCapture($relation_name);
						}
					}

					// До коммита делаем выброс Валидации на верхний уровень
					if($validation->hasErrors()) throw $validation;

					// успешно завершаем транзакцию
					$store->commit();
				}else{
					//делаем выброс Валидации на верхний уровень
					if($validation->hasErrors()) throw $validation;
				}
			}catch (Exception $e){
				// откат в случае ошибок
				if($relations) $store->rollback();

				if($e instanceof Operation){
					$this->_handleStorageOperationException($e,true);
					return false;
				}
				throw $e;
			}
			// Сбрасываем снапшот на текущее сохраненное состояние,
			// без синхронизации из оригинала т.к данные актуальны
			$this->_apply_state(false, $data);

			$this->_onUpdate($pk_value, $dirty_data);

			return true;

		}
		



		/**
		 * @return bool
		 * @throws ORMException
		 * @throws Exception
		 */
		public function delete(){
			if($this->_operation_made !== self::OP_NONE){
				if($this->_operation_made !== self::OP_DELETE){
					throw new ORMException('Already run, op:save');
				}
				return true;
			}

			if(
				$this->_record_state === self::STATE_LOADED
				&& $this->_schema->beforeDelete($this) !== false
			){
				$this->_operation_made = self::OP_DELETE;
				$this->_operation_options = [];
				if($this->_doDelete()){
					$this->_record_state = self::STATE_DELETED;
					$this->_schema->onDelete($this);
				}else{
					return false;
				}
			}

			return true;
		}

		/**
		 * @return bool
		 * @throws Exception
		 */
		protected function _doDelete(){

			$schema = $this->_schema;

			/** @var Relation[]|null $relations */
			$relations = $this->_schema->getRelations() ?: null;

			$store =  $this->_schema->getWriteStorage($this);
			try{
				if($relations){
					$store->begin();
					foreach($relations as $name => $field){
						$field->beforeRecordDelete($this);
					}
				}
				$pk         = $schema->getPk();
				$pk_value   = $this->getPkValue();

				if(!$schema->storageRemove([[$pk,'=',$pk_value]])){
					if($relations) $store->rollback();
					return false;
				}

				if($relations){
					foreach($relations as $name => $field){
						$field->afterRecordDelete($this);
					}
					$store->commit();
				}
			}catch(Exception $e){
				if($relations) $store->rollback();
				if($e instanceof Operation){
					$this->_handleStorageOperationException($e,true);
				}
				throw $e;
			}
			$this->_onDelete();
			return true;
		}

		public function onStorageSave(){ }
		public function onStorageUpdate(){ }
		public function onStorageCreate(){ }

		public function beforeStorageSave(){ }
		public function beforeStorageCreate(){ }
		public function beforeStorageUpdate(){ }



		public function beforeSave(){ }

		public function beforeCreate(){}

		public function beforeUpdate(){}

		public function beforeDelete(){ }


		public function onSave(){}

		public function onCreate(){}

		public function onUpdate(){ }

		public function onDelete(){ }


		public function afterFetch(){ }


		protected function onRecordReady(){ }

		public function onConstruct(){}


		protected function _onCreate(){}

		/**
		 * @param $id
		 * @param $dirty_data
		 */
		protected function _onUpdate($id,$dirty_data){}

		protected function _onDelete(){}





		/**
		 * @param $name
		 * @return mixed|\Ceive\DataRecord\Relation\Relationship|Record|null
		 */
		public function __get($name){
			return $this->getProperty($name);
		}

		/**
		 * @param $name
		 * @param $value
		 */
		public function __set($name, $value){
			if($this->_property_safe){
				$this->setProperty($name, $value);
			}else{
				$this->{$name} = $value;
			}
		}

		/**
		 * @param $name
		 * @return bool
		 */
		public function __isset($name){
			return $this->hasProperty($name);
		}

		/**
		 * @param $name
		 */
		public function __unset($name){}



		/**
		 * @inheritDoc
		 */
		public function offsetExists($offset){
			return $this->hasProperty($offset);
		}

		/**
		 * @inheritDoc
		 */
		public function offsetGet($offset){
			return $this->getProperty($offset);
		}

		/**
		 * @inheritDoc
		 */
		public function offsetSet($offset, $value){
			$this->setProperty($offset, $value);
		}

		/**
		 * @inheritDoc
		 */
		public function offsetUnset($offset){}


		/**
		 * @return mixed|null|Relationship|Record
		 */
		public function current(){
			$names = $this->_schema->getEnumerableNames();
			return $this->getProperty($names[$this->_property_iterator_index]);
		}

		/**
		 * @inheritDoc
		 */
		public function next(){
			$this->_property_iterator_index++;
		}

		/**
		 * @return string
		 */
		public function key(){
			$names = $this->_schema->getEnumerableNames();
			return $names[$this->_property_iterator_index];
		}

		/**
		 * @return bool
		 */
		public function valid(){
			return $this->_property_iterator_index < $this->_property_iterator_count;
		}

		/**
		 * @inheritDoc
		 */
		public function rewind(){
			$this->_property_iterator_index = 0;
			$this->_property_iterator_count = count($this->_schema->getEnumerableNames());
		}



		/**
		 * @return string
		 */
		public function serialize(){
			return serialize($this->export());
		}

		/**
		 * @param string $serialized
		 */
		public function unserialize($serialized){
			$serialized = unserialize($serialized);
			foreach($serialized as $key => $value){
				$this->setProperty($key,$value);
			}
		}

		/**
		 * @return array
		 */
		public function jsonSerialize(){
			return $this->export();
		}



	}

}

