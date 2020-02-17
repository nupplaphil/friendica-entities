<?php

/**
 * @copyright Copyright (C) 2020, Friendica
 *
 * @license GNU APGL version 3 or any later version
 *
 * This program is free software: you can redistribute it and/or modify
 * it under the terms of the GNU Affero General Public License as
 * published by the Free Software Foundation, either version 3 of the
 * License, or (at your option) any later version.
 *
 * This program is distributed in the hope that it will be useful,
 * but WITHOUT ANY WARRANTY; without even the implied warranty of
 * MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
 * GNU Affero General Public License for more details.
 *
 * You should have received a copy of the GNU Affero General Public License
 * along with this program.  If not, see <https://www.gnu.org/licenses/>.
 *
 * Used to check/generate entities for the Friendica codebase
 */

declare(strict_types=1);

namespace Friendica\Domain\Entity\Item;

use Friendica\BaseEntity;

/**
 * Entity class for table item-uri
 *
 * URI and GUID for items
 */
class Uri extends BaseEntity
{
	/** @var int */
	private $id;

	/**
	 * @var string
	 * URI of an item
	 */
	private $uri;

	/**
	 * @var string
	 * A unique identifier for an item
	 */
	private $guid;

	/**
	 * @var string
	 * A complete wrong value!
	 */
	private $wrong = '0';

	/**
	 * {@inheritDoc}
	 */
	public function toArray()
	{
		return [
			'id' => $this->id,
			'uri' => $this->uri,
			'guid' => $this->guid,
		];
	}

	/**
	 * @return int
	 */
	public function getId()
	{
		return $this->id;
	}

	/**
	 * @return string
	 */
	public function getUri()
	{
		return $this->uri;
	}

	/**
	 * @param string $uri
	 */
	public function setUri(string $uri)
	{
		$this->uri = $uri;
	}

	/**
	 * @return string
	 */
	public function getGuid()
	{
		return $this->guid;
	}

	/**
	 * @param string $guid
	 */
	public function setGuid(string $guid)
	{
		$this->guid = $guid;
	}

	/**
	 * @return string
	 */
	public function getWrong()
	{
		return $this->wrong;
	}

	/**
	 * @param string $wrong
	 */
	public function setWrong(string $wrong)
	{
		$this->wrong = $wrong;
	}
}
