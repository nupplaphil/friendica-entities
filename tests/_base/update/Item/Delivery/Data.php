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

namespace Friendica\Domain\Entity\Item\Delivery;

use Friendica\BaseEntity;
use Friendica\Network\HTTPException\NotImplementedException;

/**
 * Entity class for table item-delivery-data
 *
 * Delivery data for items
 */
class Data extends BaseEntity
{
	/**
	 * @var int
	 * Item id
	 */
	private $iid;

	/**
	 * @var string
	 * External post connectors add their network name to this comma-separated string to identify that they should be delivered to these networks during delivery
	 */
	private $postopts;

	/**
	 * @var string
	 * Additional receivers of the linked item
	 */
	private $inform;

	/**
	 * @var string
	 * Initial number of delivery recipients, used as item.delivery_queue_count
	 */
	private $queueCount = '0';

	/**
	 * @var string
	 * Number of successful deliveries, used as item.delivery_queue_done
	 */
	private $queueDone = '0';

	/**
	 * @var string
	 * Number of unsuccessful deliveries, used as item.delivery_queue_failed
	 */
	private $queueFailed = '0';

	/**
	 * @var string
	 * Number of successful deliveries via ActivityPub
	 */
	private $activitypub = '0';

	/**
	 * @var string
	 * Number of successful deliveries via legacy DFRN
	 */
	private $legacyDfrn = '0';

	/**
	 * @var string
	 * Number of successful deliveries via Diaspora
	 */
	private $diaspora = '0';

	/**
	 * @var string
	 * Number of successful deliveries via OStatus
	 */
	private $ostatus = '0';

	/**
	 * {@inheritDoc}
	 */
	public function toArray()
	{
		return [
			'iid' => $this->iid,
			'postopts' => $this->postopts,
			'inform' => $this->inform,
			'queue_count' => $this->queueCount,
			'queue_done' => $this->queueDone,
			'queue_failed' => $this->queueFailed,
			'activitypub' => $this->activitypub,
			'legacy_dfrn' => $this->legacyDfrn,
			'diaspora' => $this->diaspora,
			'ostatus' => $this->ostatus,
		];
	}

	/**
	 * @return int
	 */
	public function getIid()
	{
		return $this->iid;
	}

	/**
	 * Get Item
	 *
	 * @return Item
	 */
	public function getItem()
	{
		//@todo use closure
		throw new NotImplementedException('lazy loading for id is not implemented yet');
	}

	/**
	 * @return string
	 */
	public function getPostopts()
	{
		return $this->postopts;
	}

	/**
	 * @param string $postopts
	 */
	public function setPostopts(string $postopts)
	{
		$this->postopts = $postopts;
	}

	/**
	 * @return string
	 */
	public function getInform()
	{
		return $this->inform;
	}

	/**
	 * @param string $inform
	 */
	public function setInform(string $inform)
	{
		$this->inform = $inform;
	}

	/**
	 * @return string
	 */
	public function getQueueCount()
	{
		return $this->queueCount;
	}

	/**
	 * @param string $queueCount
	 */
	public function setQueueCount(string $queueCount)
	{
		$this->queueCount = $queueCount;
	}

	/**
	 * @return string
	 */
	public function getQueueDone()
	{
		return $this->queueDone;
	}

	/**
	 * @param string $queueDone
	 */
	public function setQueueDone(string $queueDone)
	{
		$this->queueDone = $queueDone;
	}

	/**
	 * @return string
	 */
	public function getQueueFailed()
	{
		return $this->queueFailed;
	}

	/**
	 * @param string $queueFailed
	 */
	public function setQueueFailed(string $queueFailed)
	{
		$this->queueFailed = $queueFailed;
	}

	/**
	 * @return string
	 */
	public function getActivitypub()
	{
		return $this->activitypub;
	}

	/**
	 * @param string $activitypub
	 */
	public function setActivitypub(string $activitypub)
	{
		$this->activitypub = $activitypub;
	}

	/**
	 * @return string
	 */
	public function getLegacyDfrn()
	{
		return $this->legacyDfrn;
	}

	/**
	 * @param string $legacyDfrn
	 */
	public function setLegacyDfrn(string $legacyDfrn)
	{
		$this->legacyDfrn = $legacyDfrn;
	}

	/**
	 * @return string
	 */
	public function getDiaspora()
	{
		return $this->diaspora;
	}

	/**
	 * @param string $diaspora
	 */
	public function setDiaspora(string $diaspora)
	{
		$this->diaspora = $diaspora;
	}

	/**
	 * @return string
	 */
	public function getOstatus()
	{
		return $this->ostatus;
	}

	/**
	 * @param string $ostatus
	 */
	public function setOstatus(string $ostatus)
	{
		$this->ostatus = $ostatus;
	}
}
