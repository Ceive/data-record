<?php
/**
 * Created by Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>.
 * Author: Kutuzov Alexey Konstantinovich <lexus.1995@mail.ru>
 * Project: jungle
 * IDE: PhpStorm
 * Date: 17.11.2016
 * Time: 22:59
 */
namespace Jungle\Data\Record\Schema\IdentifyTaker {

	use Jungle\Data\Record\Schema\Schema;

	/**
	 * Class IdentifyTaker
	 * @package Jungle\Data\Record\Schema\IdentifyTaker
	 */
	abstract class IdentifyTaker{

		abstract public function take(Schema $schema);

	}
}

