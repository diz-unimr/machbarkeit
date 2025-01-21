<?php

declare(strict_types=1);
// SPDX-FileCopyrightText: Nattika Jugkaeo <nattika.jugkaeo@uni-marburg.de>
// SPDX-License-Identifier: AGPL-3.0-or-later

namespace OCA\Machbarkeit\Db;

use OC\DB\Exceptions\DbalException;
use OCP\AppFramework\Db\QBMapper;
use OCP\DB\QueryBuilder\IQueryBuilder;
use OCP\IDBConnection;

header('Content-Type: application/json'); // Set JSON header
header('Access-Control-Allow-Origin: *');
/**
 * @template-extends QBMapper<OntologyConcept>
 */
class OntologyConceptMapper extends QBMapper {
	public function __construct(IDBConnection $db) {
		parent::__construct($db, 'machbarkeit_concepts', OntologyConcept::class);
	}


	public function find(int $moduleId): array {
		/* @var $qb IQueryBuilder */
		$qb = $this->db->getQueryBuilder();
		$qb->select('*')
			->from('machbarkeit_concepts')
			->where($qb->expr()->eq('module_id', $qb->createNamedParameter($moduleId, IQueryBuilder::PARAM_INT)));
		return $this->findEntities($qb);
	}

	public function findFromCode(string $code): array {
		/* @var $qb IQueryBuilder */
		$qb = $this->db->getQueryBuilder();
		$qb->select('*')
			->from('machbarkeit_concepts')
			->where($qb->expr()->eq('code', $qb->createNamedParameter($code, IQueryBuilder::PARAM_STR)));
		return $this->findEntities($qb);
	}

	public function findAll(int $moduleId): array {
		$qb = $this->db->getQueryBuilder();
		$qb->select('*')
			->from('machbarkeit_concepts')
			->where($qb->expr()->eq('module_id', $qb->createNamedParameter($moduleId, IQueryBuilder::PARAM_INT)))
			->andWhere($qb->expr()->isNull('parent_id'));
		// or 'where id = [id]' for sub tree
		$ontology = 'WITH RECURSIVE ontology AS ( ' . $qb->getSQL() .
			'UNION ALL SELECT c.* FROM oc_machbarkeit_concepts c ' .
			'JOIN ontology on c.parent_id = ontology.id ) ' .
			'SELECT * from ontology';
		// TODO build tree (with children)
		$result = $this->findEntitiesWithRawQuery($ontology, $qb->getParameters(), $qb->getParameterTypes());
		// $term_code = json_encode($result[0]->termCode);
		// $result[0]->termCode = $term_code;
		return $result;
	}

	public function searchOntology(string $searchText, int $moduleId) {
		$qb = $this->db->getQueryBuilder();
		$query = 'SELECT c.* from oc_machbarkeit_concepts c ' .
			'WHERE c.module_id = :moduleId AND lower(c.display) LIKE :searchText AND c.selectable = true';
		$param = ['moduleId' => $moduleId, 'searchText' => '%' . strtolower($searchText) . '%'];
		$result = $this->findEntitiesWithRawQuery($query, $param, $qb->getParameterTypes());
		return $result;
	}

	protected function findEntitiesWithRawQuery(string $query, array $params, array $types) {
		try {
			$cursor = $this->db->executeQuery($query, $params, $types);

			$entities = [];

			while ($row = $cursor->fetch()) {
				$entities[] = $this->mapRowToEntity($row);
			}

			$cursor->closeCursor();
		} catch (DbalException $e) {
			echo 'Caught exception: ',  $e->getMessage(), "\n";
		}

		return $entities;
	}
}
