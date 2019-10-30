<?php
/**
 * @author Alexey Kutuzov <lexus27.khv@gmail.com>
 * @author www.doctrine-project.org
 *
 * Project: jungle
 * IDE: PhpStorm
 * Date: 24.01.2017
 * Time: 5:02
 */
namespace Jungle\Data\Storage\Db {

	use Jungle\Data\Storage\Db\Driver\Connection as DriverConnectionInterface;
	use Jungle\Data\Storage\Db\Driver\Statement as DriverStatementInterface;
	use Jungle\Data\Storage\Db\Types\Type;

	/**
	 * Interface Connection
	 * @package Jungle\Data\Storage\Db
	 */
	class Connection implements DriverConnectionInterface{

		const PARAM_INT_ARRAY     = 'string[]';
		const PARAM_STR_ARRAY        = 'int[]';

		/** @var int  */
		protected $transaction_nesting_level = 0;

		/** @var bool  */
		protected $transaction_nesting_savepoints = false;

		/** @var bool  */
		protected $transaction_rollback_only = false;

		/** @var array  */
		protected $params = [];

		/** @var  DriverConnectionInterface */
		protected $conn;

		/** @var bool  */
		protected $is_connected = false;

		/** @var Driver  */
		protected $driver;

		/** @var  bool */
		protected $auto_commit = false;

		/** @var  int */
		protected $default_fetch_mode = \PDO::FETCH_ASSOC;



		/**
		 * Connection constructor.
		 * @param array $params
		 * @param Driver $driver
		 */
		public function __construct(array $params, Driver $driver){
			if(isset($params['pdo'])){
				$this->conn = $params['pdo'];
				$this->is_connected = true;
				unset($params['pdo']);
			}
			$this->params = $params;
			$this->driver = $driver;
		}

		public function getParams(){
			return $this->params;
		}

		/**
		 * @return string|null
		 */
		public function getHost(){
			return isset($this->params['host'])?$this->params['host']:null;
		}

		/**
		 * @return string|null
		 */
		public function getPort(){
			return isset($this->params['port'])?$this->params['port']:null;
		}

		/**
		 * @return string|null
		 */
		public function getUsername(){
			return isset($this->params['username'])?$this->params['username']:null;
		}

		/**
		 * @return string|null
		 */
		public function getPassword(){
			return isset($this->params['password'])?$this->params['password']:null;
		}

		/**
		 * @return bool
		 */
		public function connect(){
			if($this->is_connected){
				return false;
			}
			$driverOptions = isset($this->params['driver_options']) ? $this->params['driver_options'] : array();
			$user = isset($this->params['username']) ? $this->params['username'] : null;
			$password = isset($this->params['password']) ? $this->params['password'] : null;

			$this->conn = $this->driver->connect($this->params, $user, $password, $driverOptions);
			$this->is_connected = true;

			if(!$this->auto_commit){
				$this->beginTransaction();
			}
			return true;
		}

		/**
		 * @return bool
		 */
		public function isConnected(){
			return $this->is_connected;
		}

		/**
		 * @return bool
		 */
		public function isAutoCommit(){
			return $this->auto_commit;
		}

		/**
		 * @return bool
		 */
		public function isRollbackOnly(){
			return $this->transaction_rollback_only;
		}

		/**
		 * @return mixed
		 */
		public function getWrappedConnection(){
			$this->connect();
			return $this->conn;
		}
		
		public function setFetchMode($default_fetch_mode = \PDO::FETCH_ASSOC){
			$this->default_fetch_mode = $default_fetch_mode;
			return $this;
		}

		/**
		 * @param $statement
		 * @param array $params
		 * @param array $types
		 * @return DriverStatementInterface
		 */
		public function executeQuery($statement, array $params=[], array $types=[]){
			$this->connect();
			if($params){
				$stmt = $this->conn->prepare($statement);
				if($types){
					$this->_bindTypedParams($stmt, $params, $types);
					$stmt->execute();
				}else{
					$stmt->execute($params);
				}
			}else{
				$stmt = $this->conn->query($statement);
			}
			$stmt->setFetchMode($this->default_fetch_mode);
			return $stmt;
		}

		/**
		 * Prepares and executes an SQL query and returns the result as an associative array.
		 *
		 * @param string $sql    The SQL query.
		 * @param array  $params The query parameters.
		 * @param array  $types  The query parameter types.
		 *
		 * @return array
		 */
		public function fetchAll($sql, array $params = [], $types = []){
			return $this->executeQuery($sql, $params, $types)->fetchAll();
		}

		/**
		 * @param $statement
		 * @param array $params
		 * @param array $types
		 * @return DriverStatementInterface
		 */
		public function fetchArray($statement, array $params, array $types = []){
			$stmt = $this->executeQuery($statement, $params, $types);
			$stmt->setFetchMode(\PDO::FETCH_NUM);
			return $stmt;
		}

		/**
		 * @param $statement
		 * @param array $params
		 * @param array $types
		 * @return DriverStatementInterface
		 */
		public function fetchAssoc($statement, array $params, array $types = []){
			$stmt = $this->executeQuery($statement, $params, $types);
			$stmt->setFetchMode(\PDO::FETCH_ASSOC);
			return $stmt;
		}

		/**
		 * @param $statement
		 * @param array $params
		 * @param array $types
		 * @param int $column_index
		 * @return mixed
		 */
		public function fetchColumn($statement, array $params, array $types = [ ], $column_index = 0){
			$stmt = $this->executeQuery($statement, $params, $types);
			return $stmt->fetchColumn($column_index);
		}



		/**
		 * @param $query
		 * @param array $params
		 * @param array $types
		 * @return int
		 */
		public function executeUpdate($query, array $params, array $types = null){
			$this->connect();
			if($params){
				$stmt = $this->conn->prepare($query);
				if ($types) {
					$this->_bindTypedParams($stmt, $params, $types);
					$stmt->execute();
				} else {
					$stmt->execute($params);
				}
				$result = $stmt->rowCount();
			} else {
				$result = $this->conn->exec($query);
			}
			return $result;
		}

		/**
		 * @param string $query
		 * @return Statement
		 */
		public function prepare($query){
			$stmt = new Statement($query,$this);
			return $stmt;
		}

		/**
		 * @param null $statement
		 * @param $args
		 * @return DriverStatementInterface|null
		 */
		public function query($statement, ...$args){
			$this->connect();
			$statement = $this->conn->query($statement, ...$args);
			$statement->setFetchMode($this->default_fetch_mode);
			return $statement;
		}
		/**
		 * @param string $statement
		 * @return int
		 */
		public function exec($statement){
			$this->connect();
			$result = $this->conn->exec($statement);
			return $result;
		}


		/**
		 * @param $source
		 * @param array $data
		 * @return int
		 */
		public function simpleInsert($source, array $data){
			$platform = $this->getPlatform();
			$values = array_values($data);
			$columns = array_keys($data);
			$columns = $platform->columnListSpecifier($columns);
			$source = $platform->sourceSpecifier($source);
			$sql = /** @lang text */
				'INSERT INTO ' . $source . '(' . $columns . ') VALUES (' . array_fill(0,count($values),'?') . ');';
			return $this->executeUpdate($sql, $values);
		}

		/**
		 * @param $source
		 * @param array $where
		 * @param array $data
		 * @param null $limit
		 * @return int
		 */
		public function simpleUpdate($source, array $where, array $data, $limit = null){
			$dialect = $this->getPlatform();
			if($data){
				$params = array_keys($data);

				$set = [];
				foreach($data as $column => $value){
					$set[] = $dialect->columnSpecifier($column) . ' = ?';
				}
				$set = "\r\nSET ( " . implode(",\r\n", $set) . " )";

				if($where){
					$params = array_merge($params,array_values($where));
					$_ = [];
					foreach($where as $column => $value){
						$_[] = $dialect->columnSpecifier($column) . ' = ?';
					}
					$where = "\r\nWHERE (" . implode(" AND ", $_) . ')';
				}

				$sql = 'UPDATE '. $dialect->sourceSpecifier($source) . $set . $where;
				$sql.= $limit!==null? (' LIMIT '.intval($limit).';'):';';

				return $this->executeUpdate($sql, $params);

			}
			return 0;
		}

		/**
		 * @param $source
		 * @param array $where
		 * @param null $limit
		 * @return int
		 */
		public function simpleDelete($source, array $where, $limit = null){
			$dialect = $this->getPlatform();
			$params = [];
			if($where){
				$_ = [];
				foreach($where as $column => $value){
					$_[] = $dialect->columnSpecifier($column) . ' = ?';
				}
				$where = "\r\nWHERE (" . implode(" AND ", $_) . ')';
				$params = array_values($where);
			}
			$sql = 'DELETE '. $dialect->sourceSpecifier($source) . $where;
			$sql.= $limit!==null? (' LIMIT '.intval($limit).';'):';';
			return $this->executeUpdate($sql, $params);
		}

		/**
		 * @param string $input
		 * @param int $type
		 * @return string
		 */
		public function quote($input, $type = \PDO::PARAM_STR){
			$this->connect();
			list($value, $bindingType) = $this->getBindingInfo($input, $type);
			return $this->conn->quote($value, $bindingType);
		}

		/**
		 * @return integer The last error code.
		 */
		public function errorCode(){
			$this->connect();
			return $this->conn->errorCode();
		}

		/**
		 * @return array The last error information.
		 */
		public function errorInfo(){
			$this->connect();
			return $this->conn->errorInfo();
		}

		/**
		 * @param string|null $seqName Name of the sequence object from which the ID should be returned.
		 * @return string A string representation of the last inserted ID.
		 */
		public function lastInsertId($seqName = null){
			$this->connect();
			return $this->conn->lastInsertId($seqName);
		}



		/**
		 * Starts a transaction by suspending auto-commit mode.
		 *
		 * @return void
		 */
		public function beginTransaction(){
			$this->connect();
			++$this->transaction_nesting_level;
			if($this->transaction_nesting_level == 1) {
				$this->conn->beginTransaction();
			}elseif($this->transaction_nesting_savepoints) {
				$this->createSavepoint($this->_getTransactionSavepointName());
			}
		}



		/**
		 * Commits the current transaction.
		 * @return void
		 */
		public function commit(){
			if($this->transaction_nesting_level == 0){
				// no transactions
			}
			if($this->transaction_rollback_only){
				// rollback only
			}
			$this->connect();

			if($this->transaction_nesting_level == 1){
				$this->conn->commit();
			} elseif ($this->transaction_nesting_savepoints) {
				$this->releaseSavepoint($this->_getTransactionSavepointName());
			}

			--$this->transaction_nesting_level;

			if(!$this->auto_commit && !$this->transaction_nesting_level){
				$this->beginTransaction();
			}

		}

		/**
		 * Commits all current nesting transactions.
		 */
		private function commitAll(){
			while($this->transaction_nesting_level){
				if(!$this->auto_commit && $this->transaction_nesting_level) {
					$this->commit();
					return;
				}
				$this->commit();
			}
		}

		/**
		 * Cancels any database changes done during the current transaction.
		 */
		public function rollBack(){
			if($this->transaction_nesting_level == 0){
				// no transactions
			}
			$this->connect();
			if($this->transaction_nesting_level == 1){
				$this->transaction_nesting_level = 0;
				$this->conn->rollBack();
				$this->transaction_rollback_only = false;
				if(!$this->auto_commit){
					$this->beginTransaction();
				}
			}elseif($this->transaction_nesting_savepoints){
				$this->rollbackSavepoint($this->_getTransactionSavepointName());
				--$this->transaction_nesting_level;
			}else{
				$this->transaction_rollback_only = true;
				--$this->transaction_nesting_level;
			}
		}

		/**
		 * @return void
		 */
		public function createSavepoint($savepoint){
			$dialect = $this->getPlatform();
			if(!$dialect->supportsSavepoints()){
				// not supported
			}
			$this->conn->exec($dialect->createSavePoint($savepoint));
		}

		/**
		 * @param $savepoint
		 */
		public function releaseSavepoint($savepoint){
			$dialect = $this->getPlatform();
			if(!$dialect->supportsSavepoints()){
				// not supported
			}
			if($dialect->supportsReleaseSavepoints()) {
				$this->conn->exec($dialect->releaseSavePoint($savepoint));
			}
		}

		/**
		 * @param $savepoint
		 */
		public function rollbackSavepoint($savepoint){
			$dialect = $this->getPlatform();
			if(!$dialect->supportsSavepoints()){
				// not supported
			}
			$this->conn->exec($dialect->rollbackSavePoint($savepoint));
		}

		/**
		 * @return string
		 */
		protected function _getTransactionSavepointName(){
			return 'SAVEPOINT '.$this->transaction_nesting_level;
		}

		/**
		 * @param DriverStatementInterface $stmt
		 * @param array $params
		 * @param array $types
		 */
		protected function _bindTypedParams($stmt, $params, $types){
			if(isset($types)){
				foreach($params as $k => $val){
					$type = isset($types[$k])?$types[$k]:null;
					list($val, $type) = $this->getBindingInfo($val, $type);
					$stmt->bindValue($k, $val, $type);
				}
			}else{
				foreach($params as $k => $val){
					list($val, $type) = $this->getBindingInfo($val, null);
					$stmt->bindValue($k, $val, $type);
				}
			}
		}

		/**
		 * @return Platform
		 */
		public function getPlatform(){
			return $this->driver->getPlatform();
		}

		/**
		 * @param $input
		 * @param null $type
		 * @return array
		 */
		public function getBindingInfo($input, $type = null){
			if(!isset($type)){
				$type = $this->detectTypeByVartype($input);
			}
			if(is_string($type)) $type = Type::getType($type);
			if($type instanceof Type){
				$input = $type->convertToDatabaseValue($input, $this->getPlatform());
				$bindingType = $type->getBindingType();
			}else{
				$bindingType = $type; // PDO::PARAM_* constants
			}
			return [$input, $bindingType];
		}


		/**
		 * @param $value
		 * @return string
		 */
		public function detectTypeByVartype($value){
			switch(gettype($value)){
				case 'null':        return \PDO::PARAM_NULL;
				case 'string':      return \PDO::PARAM_STR;
				case 'integer':     return \PDO::PARAM_INT;
				case 'double':      return 'float';
				case 'boolean':     return \PDO::PARAM_BOOL;
			}
			return 'string';
		}

	}
}

