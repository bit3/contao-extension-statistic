<?php

namespace ContaoCommunityAlliance\UsageStatistic\ServerBundle\Controller;

use Doctrine\ORM\EntityManager;

abstract class AbstractEntityManagerAwareController
{
	/**
	 * @var EntityManager
	 */
	protected $entityManager;

	/**
	 * @return EntityManager
	 */
	public function getEntityManager()
	{
		return $this->entityManager;
	}

	/**
	 * @param EntityManager $entityManager
	 *
	 * @return DefaultController
	 */
	public function setEntityManager(EntityManager $entityManager)
	{
		$this->entityManager = $entityManager;
		return $this;
	}
}
