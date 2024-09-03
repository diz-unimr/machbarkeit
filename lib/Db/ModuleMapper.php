<?php

declare(strict_types=1);
// SPDX-FileCopyrightText: Nattika Jugkaeo <nattika.jugkaeo@uni-marburg.de>
// SPDX-License-Identifier: AGPL-3.0-or-later

namespace OCA\Machbarkeit\Db;

use OCP\AppFramework\Db\DoesNotExistException;
use OCP\AppFramework\Db\QBMapper;
use OCP\DB\QueryBuilder\IQueryBuilder;
use OCP\IDBConnection;

/**
 * @template-extends QBMapper<Module>
 */
class ModuleMapper extends QBMapper {
	public function __construct(IDBConnection $db) {
		parent::__construct($db, 'module', Module::class);
	}

	/**
	 * @throws \OCP\AppFramework\Db\MultipleObjectsReturnedException
	 * @throws DoesNotExistException
	 */
	public function find(int $id): Module {
		/* @var $qb IQueryBuilder */
		$qb = $this->db->getQueryBuilder();
		$qb->select('*')
			->from('machbarkeit_modules')
			->where($qb->expr()->eq('id', $qb->createNamedParameter($id, IQueryBuilder::PARAM_INT)));
		return $this->findEntity($qb);
	}

	/**
	 * @return array
	 */
	public function findModules(): array {
		/* @var $qb IQueryBuilder */
		$qb = $this->db->getQueryBuilder();
		$qb->select('*')
			->from('machbarkeit_modules');
		return $this->findEntities($qb);
	}
}
