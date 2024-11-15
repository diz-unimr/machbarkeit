<?php

declare(strict_types=1);
// SPDX-FileCopyrightText: Nattika Jugkaeo <nattika.jugkaeo@uni-marburg.de>
// SPDX-License-Identifier: AGPL-3.0-or-later

namespace OCA\Machbarkeit\Db;

header('Content-Type: application/json');

use OCP\AppFramework\Db\QBMapper;
use OCP\DB\QueryBuilder\IQueryBuilder;
use OCP\IDBConnection;

/**
 * @template-extends QBMapper<Filter>
 */
class FilterMapper extends QBMapper {
	public function __construct(IDBConnection $db) {
		parent::__construct($db, 'machbarkeit_filter_options', Filter::class);
	}

	public function filters($filter_options_ids) {
		// $filter_options_ids = null;

		$qb = $this->db->getQueryBuilder();

		if ($filter_options_ids) {
			$ids = json_decode($filter_options_ids, true);
			$qb->select('*')
				->from('machbarkeit_filter_options')
				->where($qb->expr()->in('id', $qb->createNamedParameter($ids, IQueryBuilder::PARAM_INT_ARRAY)));
		} else {
			$qb->select('*')
				->from('machbarkeit_filter_options');
		}

		return $this->findEntities($qb);
	}
}
