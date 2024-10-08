<?php

declare(strict_types=1);
// SPDX-FileCopyrightText: Nattika Jugkaeo <nattika.jugkaeo@uni-marburg.de>
// SPDX-License-Identifier: AGPL-3.0-or-later

namespace OCA\Machbarkeit\Db;

use OC\DB\Exceptions\DbalException;
use OCP\AppFramework\Db\QBMapper;
use OCP\DB\QueryBuilder\IQueryBuilder;
use OCP\IDBConnection;

/**
 * @template-extends QBMapper<OntologyFilters>
 */
class OntologyFiltersMapper extends QBMapper
{
    public function __construct(IDBConnection $db)
    {
        parent::__construct($db, 'machbarkeit_filters', OntologyFilters::class);
    }

    public function filter($moduleId, $conceptId): array
    {
        $qb = $this->db->getQueryBuilder();
        $qb->select('*')
            ->from('machbarkeit_filters')
            ->where($qb->expr()->eq('module_id', $qb->createNamedParameter($moduleId, IQueryBuilder::PARAM_INT)))
            ->andWhere($qb->expr()->eq('concept_id', $qb->createNamedParameter($conceptId, IQueryBuilder::PARAM_INT)));
        return $this->findEntities($qb);
    }
}
