<?php

declare(strict_types=1);
// SPDX-FileCopyrightText: Nattika Jugkaeo <nattika.jugkaeo@uni-marburg.de>
// SPDX-License-Identifier: AGPL-3.0-or-later

namespace OCA\Machbarkeit\Db;

use OCP\AppFramework\Db\QBMapper;
use OCP\DB\QueryBuilder\IQueryBuilder;
use OCP\IDBConnection;

/**
 * @template-extends QBMapper<OntologyConcept>
 */
class OntologyConceptMapper extends QBMapper
{
	public function __construct(IDBConnection $db)
	{
		parent::__construct($db, 'machbarkeit_concepts', OntologyConcept::class);
	}


	public function find(int $moduleId): array
	{
		/* @var $qb IQueryBuilder */
		$qb = $this->db->getQueryBuilder();
		$qb->select('*')
			->from('machbarkeit_concepts')
			->where($qb->expr()->eq('module_id', $qb->createNamedParameter($moduleId, IQueryBuilder::PARAM_INT)));
		return $this->findEntities($qb);
	}
}
