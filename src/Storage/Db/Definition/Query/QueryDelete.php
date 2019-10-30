<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 23.01.2017
 * Time: 2:45
 */
namespace Jungle\Data\Storage\Db\Definition\Query {

	use Jungle\Data\Storage\Db\Definition\DefinitionProcessor;

	/**
	 * Class QueryDelete
	 * @package Jungle\Data\Storage\Db
	 */
	class QueryDelete extends QueryExtended{

		/** @var  null|bool QUICK=true | LOW_PRIORITY=false */
		protected $priority;

		public function render(DefinitionProcessor $processor){
			$collector= $processor->prepareDelete($this);
			return $collector->__toString();
		}
	}
}

