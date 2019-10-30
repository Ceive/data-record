<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 28.01.2017
 * Time: 18:02
 */
namespace Jungle\Data\Storage\Db\Definition {
	
	/**
	 * Interface ExpressionInterface
	 * @package Jungle\Data\Storage\Db\Definition
	 */
	interface ExpressionInterface{

		/**
		 * @param DefinitionProcessor $processor
		 * @return string
		 */
		public function render(DefinitionProcessor $processor);

	}
}

