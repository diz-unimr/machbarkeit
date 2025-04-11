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


	public function find(int $module_id): array {
		/* @var $qb IQueryBuilder */
		$qb = $this->db->getQueryBuilder();
		$qb->select('*')
			->from('machbarkeit_concepts')
			->where($qb->expr()->eq('module_id', $qb->createNamedParameter($module_id, IQueryBuilder::PARAM_INT)));
		return $this->findEntities($qb);
	}

	public function findFromCode(string $code): array {
		/* @var $qb IQueryBuilder */
		$qb = $this->db->getQueryBuilder();
		/* $qb->select('*')
			->from('machbarkeit_concepts')
			->where($qb->expr()->eq('code', $qb->createNamedParameter($code, IQueryBuilder::PARAM_STR)));
		$qb->select('*')
			->from('machbarkeit_concepts')
			->where('termCodes @> :code')  // PostgreSQL JSONB contains operator
			->setParameter('code', json_encode([["code" => "asd"]], JSON_UNESCAPED_UNICODE)); */

		$query = 'SELECT c.* from oc_machbarkeit_concepts c ' .
			"WHERE jsonb_typeof(c.termCodes) = 'array'
			AND EXISTS (
				SELECT 1
				FROM jsonb_array_elements(c.termCodes) AS elem
				WHERE elem->>'code' = :code
			)";
		$param = ['code' => $code];
		$result = $this->findEntitiesWithRawQuery($query, $param, $qb->getParameterTypes());
		return $result;
		/* return $this->findEntities($qb); */
	}

	public function findAll(int $module_id): array {
		$qb = $this->db->getQueryBuilder();
		$qb->select('*')
			->from('machbarkeit_concepts')
			->where($qb->expr()->eq('module_id', $qb->createNamedParameter($module_id, IQueryBuilder::PARAM_INT)))
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

	public function select_sql() {
		$qb = $this->db->getQueryBuilder();
		$module_id = 1;
		$query = 'SELECT * from oc_accounts';
		$param = ['module_id' => $module_id];
		$result = $this->findEntitiesWithRawQuery($query, $param, $qb->getParameterTypes());
		return $result;
	}

	public function getOntologyTree(string $searchText, int $module_id): array {
		$qb = $this->db->getQueryBuilder();
		if ($searchText === '_null_') {
			$qb->select('*')
				->from('machbarkeit_concepts')
				->where($qb->expr()->eq('module_id', $qb->createNamedParameter($module_id, IQueryBuilder::PARAM_INT)))
				->andWhere($qb->expr()->isNull('parent_id'));
			$ontology = 'WITH RECURSIVE ontology AS ( ' . $qb->getSQL() .
				'UNION ALL SELECT c.* FROM oc_machbarkeit_concepts c ' .
				'JOIN ontology on c.parent_id = ontology.id ) ' .
				'SELECT * from ontology';
			$result = $this->findEntitiesWithRawQuery($ontology, $qb->getParameters(), $qb->getParameterTypes());
			return $result;
		} else {
			$query = 'SELECT c.* from oc_machbarkeit_concepts c ' .
				'WHERE c.module_id = :module_id 
					AND (lower(c.display) LIKE :searchText 
						OR jsonb_path_exists(c.termCodes::jsonb, CAST(:jsonPath AS jsonpath))
					) 
					AND c.selectable = true';
			$param = ['module_id' => $module_id, 'searchText' => '%' . strtolower($searchText) . '%', 'jsonPath' => '$[*] ? (@.code like_regex ".*' . $searchText . '.*")'];
			$result = $this->findEntitiesWithRawQuery($query, $param, $qb->getParameterTypes());
			return $result;
		}
	}

	protected function findEntitiesWithRawQuery(string $query, array $params, array $types) {
		try {
			$cursor = $this->db->executeQuery($query, $params, $types);

			$entities = [];

			/* fetchAssociative */
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

function mapTermCodes($entity) {
	$entity->termCodesArray = json_decode($entity->termCodes, true);
	return $entity;
}
