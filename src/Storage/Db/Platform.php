<?php
/*
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 23.01.2017
 * Time: 15:36
 */
namespace Ceive\DataRecord\Storage\Db {

	/**
	 * Class Platform
	 * @package Jungle\Data\Storage\Db
	 */
	abstract class Platform{

		const STR_AS_COLUMN = 'column';
		const STR_AS_VALUE  = 'value';
		const STR_AS_RAW    = 'raw';

		/** @var  string  */
		protected $escape_char = '`';

		/** @var  string  */
		protected $column_mask_char = '*';

		/** @var  string  */
		protected $path_delimiter = '.';
		
		/** @var  int  */
		protected $processing_nesting_level = 0;

		/**
		 * @return string
		 */
		public function getEscapeChar(){
			return $this->escape_char;
		}

		/**
		 * @return string
		 */
		public function getColumnMaskChar(){
			return $this->column_mask_char;
		}

		/**
		 * @return string
		 */
		public function getPathDelimiter(){
			return $this->path_delimiter;
		}









		/**
		 * @return int
		 */
		public function getNestingLevel(){
			return $this->processing_nesting_level;
		}

		/**
		 * @return $this
		 */
		public function nestingDeeper(){
			$this->processing_nesting_level++;
			return $this;
		}

		/**
		 * @return $this
		 */
		public function nestingHigher(){
			if($this->processing_nesting_level > 0){
				$this->processing_nesting_level--;
			}
			return $this;
		}

		/**
		 * @param array $columns
		 * @return string
		 */
		public function columnListSpecifier(array $columns){
			foreach($columns as &$column){
				$column = $this->columnSpecifier($column,false);
			}
			return implode(', ', $columns);
		}

		/**
		 * Расширенные возможности указания списка определения Колонок, Например в SELECT *
		 * @param array $columns
		 * @return string
		 */
		public function columnListSelection(array $columns){
			$a = [];
			foreach($columns as $alias => $column){
				$alias = is_string($alias)? $this->aliasIdentifier($alias) : null;
				$a[] = $this->columnSpecifier($column, true).($alias?' AS ' . $alias:'');
			}
			return implode(', ',$a);
		}

		/**
		 * @param $column
		 * @param bool|true $allow_mask
		 * @return null|string
		 */
		public function columnSpecifier($column, $allow_mask = true){
			if(!$column) return null;
			if(!is_array($column)) $column = explode($this->path_delimiter,$column);
			$column = array_replace([null,null,null],array_reverse(array_diff($column,[''])));
			if(count($column) > 3) return null; // allowed only $db,$table,$column
			list($column,$table,$db) = $column;
			$a = [];
			if($db && ($db = $this->databaseIdentifier($db))){
				$a[] = $db;
			}
			if($table && ($table = $this->tableIdentifier($table))){
				$a[] = $table;
			}
			if($column && ($column = $this->columnIdentifier($column, $allow_mask))){
				$a[] = $column;
			}else{
				return null;
			}
			return $a?implode($this->path_delimiter,$a):null;
		}

		/**
		 * @param $source
		 * @return null|string
		 */
		public function sourceSpecifier($source){
			if(!$source) return null;
			if(!is_array($source)) $source = explode($this->path_delimiter,$source);
			$source = array_replace([null,null],array_reverse(array_diff($source,[''])));
			if(count($source) > 2) return null; // allowed only $db,$table,$column
			list($table,$db) = $source;
			$a = [];
			if($db && ($db = $this->databaseIdentifier($db))){
				$a[] = $db;
			}
			if($table && ($table = $this->tableIdentifier($table))){
				$a[] = $table;
			}else{
				return null;
			}
			return $a?implode($this->path_delimiter,$a):null;
		}


		/**
		 * @param $identifier
		 * @return null|string
		 */
		public function tableIdentifier($identifier){
			return $this->escapeIdentifier($identifier);
		}

		/**
		 * @param $identifier
		 * @return null|string
		 */
		public function databaseIdentifier($identifier){
			return $this->escapeIdentifier($identifier);
		}

		/**
		 * @param $identifier
		 * @return null|string
		 */
		public function aliasIdentifier($identifier){
			return $this->escapeIdentifier($identifier);
		}

		/**
		 * @param $keyword
		 * @return string
		 */
		public function keywordIdentifier($keyword){
			return preg_replace('@[^\w]+@','',$keyword);
		}

		/**
		 * @param $identifier
		 * @param bool|true $allow_mask
		 * @return null|string
		 */
		public function columnIdentifier($identifier, $allow_mask = true){
			$ec = $this->escape_char;
			$identifier = trim(strtr($identifier,[$ec=>'',' '=>'',$this->path_delimiter=>'']));
			if($identifier === '*'){
				// if not allowed, null return
				return $allow_mask?$identifier:null;
			}
			if(!$identifier || is_numeric($identifier) || is_bool($identifier)){
				return null; // is not allowed $identifier
			}
			return $ec.$identifier.$ec;
		}

		/**
		 * @param $identifier
		 * @return null|string
		 */
		public function escapeIdentifier($identifier){
			$ec = $this->escape_char;
			$identifier = trim(strtr($identifier,[$ec=>'',' '=>'',$this->path_delimiter=>'', $ec=>'']));
			if(!$identifier || is_numeric($identifier) || is_bool($identifier)){
				return null; // is not allowed $identifier
			}
			return $ec.$identifier.$ec;
		}

		/**
		 * @return bool
		 */
		public function supportsSavepoints(){
			return true;
		}

		/**
		 * @return bool
		 */
		public function supportsReleaseSavepoints(){
			return true;
		}

		/**
		 * @param $savepoint
		 * @return string
		 */
		public function createSavePoint($savepoint){
			return 'CREATE SAVEPOINT '. $this->escapeIdentifier($savepoint);
		}

		/**
		 * @param $savepoint
		 * @return string
		 */
		public function releaseSavePoint($savepoint){
			return 'RELEASE SAVEPOINT '. $this->escapeIdentifier($savepoint);
		}

		/**
		 * @param $savepoint
		 * @return string
		 */
		public function rollbackSavePoint($savepoint){
			return 'ROLLBACK SAVEPOINT '. $this->escapeIdentifier($savepoint);
		}


	}
}

