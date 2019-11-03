<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 20.01.2017
 * Time: 4:49
 */
namespace Ceive\DataRecord\Query {

	use Ceive\DataRecord\Locator\Path;
	use Ceive\DataRecord\Locator\Point;
	use Ceive\DataRecord\Relation\RelationForeign;
	use Ceive\DataRecord\Relation\RelationSchema;
	use Ceive\DataRecord\Schema\Schema;
	use Ceive\DataRecord\Storage\Db;
	use Jungle\Util\Value\Massive;
	use Jungle\Util\Value\String;


	/**
	 * Нужны:
	 * Джоины для склеивания связей в линию
	 * Вложенные запросы для COUNT, IN, для вхождения в Many отношения
	 */

	/**
	 *
	 * Вхождение в связанную коллекцию : Разбор полетов
	 *
	 * Зачем входить в коллекцию?
	 * Чтобы узнать есть ли в коллекции записи по условию (HAVE) в колличестве > 0 AND < 5
	 * Чтобы узнать нет ли в коллекции записей по условию (NOT HAVE) в колличествах > 0 AND < 5
	 * Чтобы сделать условие по колличеству записей в этой коллекции
	 * Чтобы Сделать GROUP_CONCAT какого-то поля каждого объекта(Аналогия Collection::listProperty($property_name))
	 *
	 */
	/**
	 * 2 типа вхождения в множественные связи.
	 * Использование LEFT JOIN - Быстрее чем под-запросы
	 * Использование под запросов - Изолированное окружение при доступе к статистике связанных объектов у каждого
	 *
	 * For Preliminary optimization
	 * $scope = $record->getLoadedScope();
	 * $scope['profile.first_name'];
	 * $scope['notes:count'];
	 * $scope['notes:sum'];
	 *
	 * @TODO Сделать родительские Запросы, и тип использования в родительском разпросе (INLINE, SUB)
	 * @TODO Найти применение @переменным
	 * @TODO Покрыть кейсы выборок. Выборка AVG, SUM, MAX, MIN, COUNT (Для мультивхождений)
	 *
	 * GROUP BY and HAVING используется в случае JOIN в мульти таблицы.
	 * GROUP BY - Для базового идентификатора GROUP BY base.id
	 * HAVING может использоваться для любого аггрегационного джоина, и он не относится к ПОД запросам
	 *
	 * JOIN коллекций, есть ОГРАНИЧИВАЮЩИЙ [INNER] и НЕ ОГРАНИЧИВАЮЩИЙ [LEFT] данные на выходе
	 * COUNT, AVG и т.д получаются только для тех аггрегаций которые учавствуют в результатах
	 *
	 * Подсчет общего
	 * Подсчет по условиям
	 * То что мы хотим видеть в результате, можно получить 2 мя путями:
	 * Либо это выбирается в локальном запросе
	 * Либо с помощью под запроса на каждую псевдо колонку отдельный под запрос
	 */

	/**
	 * Class Query
	 * @package Jungle\Data\Record\Query
	 */
	class Query{

		const TYPE_KEY = '::type';
		const TYPE_BLOCK = '(...)';

		/** @var  Query */
		public $parent;


		/** @var Schema */
		public $base;

		/** @var array */
		public $columns     = [];

		/** @var null  */
		public $table       = null;

		/** @var null  */
		public $alias       = null;

		/** @var array  */
		public $joins       = [];

		/** @var array  */
		public $having      = [];

		/** @var array  */
		public $order_by    = [];

		/** @var array  */
		public $group_by    = [];

		/** @var array  */
		public $where       = [];


		public $left;

		public $operator;

		public $right;




		/** @var {path} => {source} */
		public $sources = [];

		/** @var {path} => {alias} */
		public $aliases = [];

		/** @var {path} => {aliased_identifier} */
		public $paths = [];

		/** @var Schema[] {path} => {schema}  */
		public $schemas = [];

		/** @var Point[] {path} => {metadata}  */
		public $containers = [];


		/**
		 * Query constructor.
		 * @param Schema $schema
		 * @param null $alias
		 */
		public function __construct(Schema $schema, $alias = null){
			if(!$alias){
				$alias = $schema->getName();
				if(($alias_pos = strrpos($alias,'\\'))!==false){
					$alias = strtolower(substr($alias,$alias_pos+1));
				}
			}
			$this->setBase($schema, $alias);
		}

		public function setBase(Schema $schema, $alias){
			$full_identifier = $schema->getDefaultSource();

			$this->base = $schema;
			$this->alias = $alias?:$full_identifier;
			$this->aliases[$alias] = $alias;
			$this->sources[$alias] = $full_identifier;
			$this->schemas[$alias] = $schema;
		}


		public function __old(){
			$condition = [
				// означает что в запрос подставиться JOIN так как профиль, присоединяется линейно
				['{profile.first_name}', 'LIKE', '%beef%'],
				['{profile.hierarchy.title}', 'LIKE', '%beef%'],
				// username IS NULL,
				['{username}','=',NULL],
				// создает аггрегационный контекст, без условий и без захвата аггрегаций
				['{notes:count}','>',5], // у множества, режим общего, тотально (без условий и ограничений)
				['{notes:average({id})}','>',5], // у множества, режим общего, тотально (без условий и ограничений)
				['{notes}','HAVE',[
					// можно сделать захват агрегаций SUM({number}) as number_sum
					// можно делать проверку по агрегациям SUM({number}) > 1000
					'each'      => 'note',
					'count'     => ['> 5','<= 10'],
					'condition' => [
						['{title}','LIKE','%john%'],
						// если связи FK то сравнить используя локальные поля
						// если используется объект то подставить его PK или отражение локальных FK
						['{editor.id}','=','{user.id}']
					]
				]],
			];

			$includes = [

				'profile',
				'members',
				'members' => [
					'average' => '{}'
				],

				'public_notes' => [
					'aggregate' => 'notes',
					'capture' => [
						['count','*']
					],
				],

				['{notes}','SELECT',[
					'capture' => [
						['concat','{id}'],
						['average','{numbers}'],
						['count'],
						['average','{numbers}'],
						['average','{numbers}']
					],
					'aggregate_condition' => [
						['count','between',[5,10]],
						['average({numbers})','between',[5,10]]
					],
					'condition' => []
				]],
			];

			$sort_by = [
				'profile.first_name' => 'ASC'
			];

			$usages = [];


			$select_query = [
				'schema' => 'App/Model/User',
				'include' => [
					'profile',
					'members:count'
				],
				'condition' => [
					['{profile.first_name}', 'LIKE', '%beef%']
				],
				'sort' => [
					'profile.first_name' => 'ASC'
				]
			];

			$result_query = [
				'table'     => 'ex_user',
				'alias'     => 'u',
				'columns'   => ['u.username','p.first_name','m_count','....','....'],
				'where'     => [
					['p.first_name','LIKE','%beef%'],
					['u.id','=','p.id']
				],
				'joins'     => [[
					'type'      => 'type',
					'table'     => '',
					'alias'     => 'p',
					'on'        => [
						['u.id','=','p.id']
					]
				]],
			];


			// Выборка записей по условиям с использованием связанных записей!!

			// Выборка записей с захватом данных для связанных путей!! |=   SELECT &, profile, {notes}
			$this->where = $this->handleConditionBlock($condition);
		}

		/**
		 * @param $definition
		 */
		public function prepare($definition){
			$definition = array_replace([
				'alias'     => null,
				'include'   => null,
				'sort'      => null,
				'shuffle'   => false,
				'offset'    => null,
				'limit'     => null,
			],$definition);

			$condition = $definition['condition'];

			if($definition['alias']){
				$this->alias = $definition['alias'];
			}

			if($definition['include']){

			}
			if($definition['shuffle']){
				$this->orderBy('RAND()');
			}
			if($definition['condition']){
				$this->where = $this->handleConditionBlock($condition);
			}

			if($definition['sort']){
				foreach($definition['sort'] as $name => $direction){

				}
			}
		}
		
		public function prepareContainerAlias($path, $condition){
			$path = $this->getDefaultPath() . '.' . $path;
			return strtr($path,['.'=>'__']) . ($condition?'_' . strtr((string)crc32(serialize($condition)),['-' => 'M']):'');
		}

		public function handleCondition($left, $operator, $right){
			if(HelperString::isCovered($left,'{','}')){
				$left = HelperString::trimSides($left,'{','}');
				$left = $this->pathInfo($left);
				$left_is_path = true;
			}
			if($operator){
				$operator = $this->handleOperator($left, $operator, $right);
				if($right && is_string($right)){
					if(HelperString::isCovered($right,'{','}')){
						$right = HelperString::trimSides($right,'{','}');
						$right = $this->pathInfo($right);
						$right_is_path = true;
					}
				}
			}
			if( ($have = strcasecmp($operator,'have')===0) || ($not_have = strcasecmp($operator,'not have')===0) ){

				if($left instanceof Path && $left->hasMany()){
					/**
					 * @var Schema $origin_schema
					 * @var Schema $schema
					 * @var RelationSchema $relation
					 */
					$origin_path    = $left->getPrevPath($this->getDefaultPath());
					$origin_schema  = $this->schemas[$origin_path];
					$relation       = $left->point->relation;
					$path           = $left->path;
					$condition      = $right['condition'];

					if(!$right['each']){
						$alias = $this->prepareContainerAlias($path, $condition);
					}else{
						$alias = $right['each'];
					}
					$subquery = $this->subQuery(
						$left->getSchema(),
						$alias,
						[$relation->referenced_fields[0]],
						$left->getPoint()->getCollation(),
						$right['condition']
					);
					$left = $origin_path . '.' . $origin_schema->getOriginal($relation->fields[0]);
					$right = $subquery;
					$operator = strtr(strtolower($operator),['have'=>'in']);
				}
			}else{
				$aggregate_condition = false;
				if($left instanceof Path && $left->extra && $left->hasMany()){
					$left = $this->handleManyAggregation(
						$left->getPrevPath($this->getDefaultPath()),$left->path,
						$left->extra,$left->point->getCollation()
					);
					if(is_string($left)){
						$aggregate_condition = true;
					}
				}
				if($right instanceof Path && $right->extra && $right->hasMany()){
					$right = $this->handleManyAggregation(
						$right->getPrevPath($this->getDefaultPath()),$right->path,
						$right->extra,$right->point->getCollation()
					);
					if(is_string($right)){
						$aggregate_condition = true;
					}
				}
				if($aggregate_condition){
					// IF(CONDITION_DEPTH < 1) ELSE USE SUBQUERIES
					// enable grouping
					$this->group_by = $this->alias . '.' . $this->base->getPkOriginal();
					$this->having([
						[$left,$operator,$right]
					]);
					return null;
				}
			}
			if($this->to_having){
				$this->having([ [$left,$operator,$right] ]);
				$this->to_having = false;
				return null;
			}
			if($left === null && $operator === null && $right === null){
				return null;
			}else{
				return [
					is_string($left) && isset($left_is_path)?$this->toDataBaseIdentifier($left):$left,
					$operator,
					is_string($right) && isset($right_is_path)?$this->toDataBaseIdentifier($right):$right
				];
			}
		}

		public $aggregate_paths = [];

		public $to_having = false;

		/**
		 * @param $origin_path
		 * @param $path
		 * @param $aggregation_query
		 * @param $collation
		 * @param null $condition
		 * @return string|Query|bool
		 *
		 * string - Означает, что все укладывается в JOIN.
		 * Query - Если JOIN не достаточно, и аггрегация запущена в под-запрос.
		 * FALSE - Если данный операнд нельзя подставлять
		 *
		 * @throws \Exception
		 */
		public function handleManyAggregation($origin_path, $path, $aggregation_query, $collation, $condition = null){
			$into = $this->schemas[$path];
			$alias = $this->aliases[$path];
			$source = $into->getDefaultSource();

			$join_condition = $this->resolveCollation($collation,$origin_path,$path);
			if($condition){
				$join_condition = array_merge($join_condition, $condition);
			}

			$aggregate = $this->parseAggregate($aggregation_query);
			$fn = $aggregate['function'];
			$args = $aggregate['arguments'];
			foreach($args as & $arg){
				if($arg['type'] === 'field'){
					$arg = $alias . '.' .$into->getOriginal($arg['identifier']);
				}else{
					$arg = $arg['identifier'];
				}
			}
			if(!$args){
				if(strcasecmp($fn,'count')===0){
					$args[] = $alias.'.'.$into->getPkOriginal();
				}
				$aggregate_query_normalized = $aggregation_query;
			}else{
				$aggregate_query_normalized = trim(preg_replace('@[\W]+@','_',$aggregation_query),'_');
			}
			$aggregate_column_definition = $fn . ($args?'('.implode(', ',$args).')':'()');
			$aggregate_column_alias = "{$alias}_{$aggregate_query_normalized}";
			$this->paths[$path.':'.$aggregation_query] = $aggregate_column_alias;

			$this->join($source,$alias,$join_condition,'LEFT');
			$this->column($aggregate_column_definition,$aggregate_column_alias);
			$this->aggregate_paths[$path.':'.$aggregation_query] = $aggregate_column_alias;
			return $aggregate_column_alias;
		}

		public function parseAggregate($function){
			$function = trim($function);
			$args = [];
			$l_pos = strpos($function,'(');
			if($l_pos!==false){
				$fn = substr($function,0,$l_pos);
				$l_pos = $l_pos+1;
				$r_pos = strrpos($function,')');
				$arguments = substr($function, $l_pos, ($r_pos-$l_pos));
				$arguments = explode(',',$arguments);
				$args = [];
				foreach($arguments as $argument){
					$argument = trim($argument);
					if(strpos($argument,'{')===0 && strrpos($argument,'}')!==false){
						$args[] = [
							'type' => 'field',
							'identifier' => substr($argument,1,-1)
						];
					}else{
						$args[] = [
							'type' => 'simple',
							'identifier' => $argument
						];
					}
				}
			}else{
				$fn = $function;
			}
			return [
				'function' => $fn,
				'arguments' => $args
			];
		}


		public function handleOperator($left, $operator, $right){
			if($operator === '=' && $right === null){
				return 'IS';
			}elseif($operator === '!=' && $right === null){
				return 'IS NOT';
			}
			return $operator;
		}



		/**
		 * @param $path
		 * @return mixed|null|string
		 *
		 * TODO: Оптимизировать доступ к мета данным пути
		 */
		public function pathInfo($path){
			$this->condition_hint['simplify_path'] = null;
			$query_path = $path;
			$container_path = $default_path = $this->getDefaultPath();
			$origin_schema = $this->schemas[$container_path];
			if(!isset($this->paths[$query_path])){
				$extra = null;
				if(($pos = strpos($path,':')) !== false){
					$extra = substr($path, $pos+1);
					$path = substr($path, 0,$pos);
				}
				$locator = $origin_schema->inspectPath($path);
				if($locator instanceof Point){
					$locator = new Path($origin_schema,$path,null,$extra,$locator);
				}
				$locator->extra = $extra;
				if(!$locator->isCircular()){
					$last_path = $this->points($locator);
					if(isset($this->containers[$last_path]) && $locator->hasMany()){
						return $locator;
					}elseif($locator->field){
						$container_path = $last_path;
					}else{
						return null; // ERROR
					}
				}
				$field = $locator->field;
				if(isset($this->condition_hint['simplify_path'])){
					list($container_path, $field) = $this->condition_hint['simplify_path'];
					unset($this->condition_hint['simplify_path']);
				}

				$this->paths[$query_path] =
					$this->aliases[$container_path] . '.' . $locator->getSchema()->getOriginal($field);
			}elseif(isset($this->aggregate_paths[$path])){
				$this->to_having = true;
			}
			return $this->paths[$query_path];
		}

		/** @var Path[] */
		public $analyzed_paths_cache = [];

		public function __clone(){
			$this->having =
			$this->columns =
			$this->joins =
			$this->paths = [];

		}
		/**
		 * Узнать ОТНОШЕНИЯ - выставить по пути отношения, иснтрукции и информацию по их применению.
		 * А вот различные вхождения аггрегаций будут формироваться по метаданным отношений
		 */

		/**
		 * @param Path $locator
		 * @return mixed|null|string
		 * @throws \Exception
		 * Возвращает полный {alias} конечного обработанного поинта.
		 *
		 * Для одиночных связей, автоматически создается джоин
		 */
		public function points(Path $locator){
			$origin_path = $this->getDefaultPath();
			$origin_schema = $this->schemas[$origin_path];

			$points = $locator->line();

			/** @var Point $point */
			foreach($points as $point){
				/**
				 * @var Schema $schema
				 * @var RelationForeign $relation
				 */
				$schema = $point->schema;
				$path = $point->path;
				$alias = $this->prepareContainerAlias($path, null);
				if(!isset($this->aliases[$path])){
					$relation = $point->relation;
					if(!$point->isMany()){
						if($locator->point === $point){
							if($relation instanceof RelationForeign
							   && $locator->isLocal()
							   && count($relation->fields)===1
							   && ($current_field = $locator->field?:$schema->pk)
							   && $current_field === $relation->referenced_fields[0]
							){
								$this->condition_hint['simplify_path'] = [$this->getDefaultPath(),$relation->fields[0]];
								// путь локатора, может быть заменен на локальный путь поля FK
								// не позволив сделать безсмысленный JOIN
								break;
							}
						}

						$table_name = $schema->getDefaultSource();
						$this->sources[$path] = $table_name;
						$this->schemas[$path] = $schema;
						$this->aliases[$path] = $alias;

						// single (Легко Джоинятся и соединяются в линию)
						$condition = [];
						foreach($relation->fields as $i => $origin_field){
							$field = $relation->referenced_fields[$i];
							$condition[] = [
								$origin_path.'.'.$origin_schema->getOriginal($origin_field),
								'=',
								$alias.'.'.$schema->getOriginal($field)
							];
						}
						$this->join($table_name, $alias, $condition);
					}else{
						$table_name = $schema->getDefaultSource();
						$this->sources[$path] = $table_name;
						$this->schemas[$path] = $schema;
						$this->aliases[$path] = $alias;

						$this->containers[$path] = true;
					}
				}
				$origin_schema = $schema;
				$origin_path = $path;
			}
			return $origin_path;
		}

		protected $condition_hint = [];








		public function toDataBaseIdentifier($identifier){
			return $identifier;
			//return ['identifier' => $identifier];
		}


		/**
		 * @param Schema $schema
		 * @param $alias
		 * @param $columns
		 * @param $collation
		 * @param $condition
		 * @return Query
		 * @throws \Exception
		 */
		public function subQuery(Schema $schema, $alias, $columns, $collation, $condition){
			$subquery = clone $this;
			$subquery->setBase($schema, $alias);
			foreach($columns as &$column){
				$column = $schema->getOriginal($column);
			}
			$subquery->columns = $columns; // выборка колонок в подзапросе
			$subquery->aliases[$this->alias] = $this->alias;// делегирование текущего псевдонима в подзапрос
			$collate_condition = $subquery->resolveCollation($collation, $this->alias, $subquery->alias);
			$subquery->where = $subquery->handleConditionBlock(array_merge($collate_condition,$condition));
			$subquery->paths = []; // сброс путей в подзапросе
			return $subquery;
		}

		/**
		 * @param array $collation
		 * @param $origin_path
		 * @param $path
		 * @return array
		 */
		public function resolveCollation(array $collation, $origin_path, $path){
			$condition = [];
			$origin_schema = $this->schemas[$origin_path];
			$schema = $this->schemas[$path];
			foreach($collation as $origin_field => $target_field){
				$condition[] = [
					$this->aliases[$origin_path] . '.' . $origin_schema->getOriginal($origin_field),
					'=',
					$this->aliases[$path] . '.' . $schema->getOriginal($target_field),
				];
			}
			return $condition;
		}

		/**
		 * @param $path
		 * @return Schema
		 */
		public function getContextSchema($path){
			if(isset($this->schemas[$path])){
				return $this->schemas[$path];
			}
			return null;
		}

		/**
		 * @param $path
		 * @param Point $point
		 * @return $this
		 */
		public function attachContainer($path, Point $point){
			$this->containers[$path] = $point;
			return $this;
		}


		/** @var  string */
		public $default_path;

		/**
		 * @return string
		 */
		public function getDefaultPath(){
			return $this->default_path?:$this->alias;
		}

		/**
		 * Обработка блока условий
		 * @param array $block
		 * @return array
		 */
		public function handleConditionBlock(array $block){
			$a = [];$b = false;
			foreach($block as $condition){
				if(is_array($condition)){
					if($condition && ($cc = $this->handleConditionArray($condition))){
						if($b){
							$a[] = 'AND';
						}
						$b = true;
						$a[] = $cc;
					}
				}else{
					$b = false;
					$a[] = $condition?:'AND';
				}
			}
			return $a;
		}

		/**
		 * Обработка пока что неизвестного массива
		 * @param array $condition
		 * @return null|void
		 */
		public function handleConditionArray(array $condition){
			if(isset($condition[self::TYPE_KEY])){
				if($condition[self::TYPE_KEY] === 'block'){
					unset($condition[self::TYPE_KEY]);
					return $this->handleConditionBlock($condition);
				}else{
					unset($condition[self::TYPE_KEY]);
				}
			}elseif(isset($condition[0]) && is_string($condition[0]) && $condition[0]===self::TYPE_BLOCK){
				array_shift($condition);
				return $this->handleConditionBlock($condition);
			}else{
				if(Massive::isAssoc($condition, true)){
					$a = [];
					foreach($condition as $k => $v){
						$a[] = ['{'.$k.'}','=',$v];
					}
					return $this->handleConditionBlock($a);
				}else{
					list($left, $operator, $right) = array_replace([null,null,null],$condition);
					return $this->handleCondition($left, $operator, $right);
				}
			}
			return null;
		}


		/**
		 * Задать базовые данные для запроса в БД
		 * @param $source
		 * @param null $alias
		 * @return $this
		 */
		public function base($source, $alias = null){
			$this->table = $source;
			$this->alias = $alias;
			return $this;
		}

		/**
		 * @param $definition
		 * @param null $alias
		 * @return $this
		 */
		public function column($definition, $alias = null){
			$this->columns[$alias?$alias:$definition] = $definition;
			return $this;
		}

		/**
		 * @param bool|false $merge
		 * @param \array[] ...$columns
		 * @return $this
		 */
		public function columns($merge = false, array ...$columns){
			if(!$merge)$this->columns = [];
			foreach($columns as list($definition, $alias)){
				$this->columns[$alias?$alias:$definition] = $definition;
			}
			return $this;
		}

		/**
		 * @param bool|false $merge
		 * @param \array[] ...$columns
		 * @return $this
		 */
		public function columnsArray($merge = false, array $columns){
			if(!$merge)$this->columns = [];
			foreach($columns as list($definition, $alias)){
				$this->columns[$alias?$alias:$definition] = $definition;
			}
			return $this;
		}

		/**
		 * @param $source
		 * @param null $alias
		 * @param null $condition
		 * @param null $type
		 * @return $this
		 */
		public function join($source, $alias = null, $condition = null, $type = null){
			$join = [
				'type' => $type,
				'condition' => $condition,
				'table' => $source,
				'alias' => $alias
			];
			if($alias)  $this->joins[$alias] = $join;
			else        $this->joins[] = $join;

			return $this;
		}

		/**
		 * @param $condition
		 * @param bool|true $merge
		 * @return $this
		 */
		public function having($condition, $merge = true){
			$this->having = $merge?array_merge($this->having, $condition):$condition;
			return $this;
		}

		/**
		 * @param $column
		 * @param bool|true $merge
		 * @return $this
		 */
		public function groupBy($column, $merge = true){
			if(!$merge)$this->order_by = [];
			$this->group_by[$column] = 1;
			return $this;
		}

		/**
		 * @param $column
		 * @param string $direction
		 * @param bool|true $merge
		 * @return $this
		 */
		public function orderBy($column, $direction = 'ASC', $merge = true){
			if(!$merge)$this->order_by = [];
			$this->order_by[$column] = $direction;
			return $this;
		}

		/**
		 * @param $condition
		 * @param bool|true $merge
		 * @return $this
		 */
		public function where($condition, $merge = true){
			$this->where = $merge?array_merge($this->where, $condition):$condition;
			return $this;
		}

		/**
		 * @param ...$who
		 * @return $this
		 */
		public function reset(...$who){
			foreach($who as $property){
				$this->{$property} = [];
			}
			return $this;
		}

		/**
		 * @param $function_name
		 * @param ...$arguments
		 * @return string
		 */
		public function func($function_name, ...$arguments){
			return $function_name.'('. implode(', ',$arguments) .')';
		}

		/**
		 * @param $function_name
		 * @param array $arguments
		 * @return string
		 */
		public function funcArray($function_name,array $arguments = []){
			return $function_name.'('. implode(', ',$arguments) .')';
		}

	}
}

