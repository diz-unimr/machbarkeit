<?php

// SPDX-FileCopyrightText: 2024 Sebastian Stöcker <sebastian.stoecker@uni-marburg.de>
// SPDX-License-Identifier: AGPL-3.0-or-later

namespace OCA\Machbarkeit\Migration;

use OCP\DB\QueryBuilder\IQueryBuilder;
use OCP\IConfig;
use OCP\IDBConnection;
use OCP\Migration\IOutput;
use OCP\Migration\IRepairStep;
use Psr\Log\LoggerInterface;

class InitData implements IRepairStep {

	/** @var LoggerInterface */
	protected $logger;
	/** @var IConfig */
	protected $config;
	/** @var IDBConnection */
	protected $db;

	public function __construct(LoggerInterface $logger, IConfig $config, IDBConnection $db) {
		$this->logger = $logger;
		$this->db = $db;
		$this->config = $config;
	}

	/**
	 * Returns the step's name
	 */
	public function getName() {
		return 'Init data step!';
	}

	/**
	 * @param IOutput $output
	 */
	public function run(IOutput $output) {
		$previousVersion = $this->config->getAppValue('machbarkeit', 'installed_version', false);
		if (!$previousVersion) {
			$this->storeInitialData();
		}
	}

	protected function storeInitialData() {
		$this->logger->info('Loading ontology data', ['app' => 'Machbarkeit']);

		$query = $this->db->getQueryBuilder();

		// add modules first
		$query
			->insert('machbarkeit_modules')
			->values(
				[
					'id' => $this->db->quote(1),
					'display' => $this->db->quote('Person'),
					'version' => $this->db->quote('1.0.0')
				]
			)->executeStatement();

		$query
			->insert('machbarkeit_modules')
			->values(
				[
					'id' => $this->db->quote(2),
					'display' => $this->db->quote('Prozedur'),
					'version' => $this->db->quote('1.0.0'),
				]
			)->executeStatement();

		// ontology concepts
		$query->insert('machbarkeit_concepts')->values(
			[
				'id' => $this->db->quote(1),
				'display' => $this->db->quote('Gegenwärtiges chronologisches Alter'),
				'selectable' => $query->createNamedParameter(true, IQueryBuilder::PARAM_BOOL),
				'module_id' => $this->db->quote(1),
			]
		)->executeStatement();

		$query->insert('machbarkeit_concepts')->values(
			[
				'id' => $this->db->quote(2),
				'display' => $this->db->quote('Bildgebende Diagnostik'),
				'selectable' => $query->createNamedParameter(false, IQueryBuilder::PARAM_BOOL),
				'module_id' => $this->db->quote(2),
			]
		)->executeStatement();

		$query->insert('machbarkeit_concepts')->values(
			[
				'id' => $this->db->quote(3),
				'display' => $this->db->quote('Andere bildgebende Verfahren'),
				'selectable' => $query->createNamedParameter(true, IQueryBuilder::PARAM_BOOL),
				'module_id' => $this->db->quote(2),
				'parent_id' => $this->db->quote(2),
			]
		)->executeStatement();

		$query->insert('machbarkeit_concepts')->values(
			[
				'id' => $this->db->quote(4),
				'display' => $this->db->quote('Elektrische Impedanztomographie'),
				'selectable' => $query->createNamedParameter(true, IQueryBuilder::PARAM_BOOL),
				'module_id' => $this->db->quote(2),
				'parent_id' => $this->db->quote(3),
			]
		)->executeStatement();

		$query->insert('machbarkeit_concepts')->values(
			[
				'id' => $this->db->quote(5),
				'display' => $this->db->quote('Elektroimpedanzspektroskopie der Haut'),
				'selectable' => $query->createNamedParameter(true, IQueryBuilder::PARAM_BOOL),
				'module_id' => $this->db->quote(2),
				'parent_id' => $this->db->quote(3),
			]
		)->executeStatement();

		$query->insert('machbarkeit_concepts')->values(
			[
				'id' => $this->db->quote(6),
				'display' => $this->db->quote('Knochendichtemessung (alle Verfahren)'),
				'selectable' => $query->createNamedParameter(true, IQueryBuilder::PARAM_BOOL),
				'module_id' => $this->db->quote(2),
				'parent_id' => $this->db->quote(3),
			]
		)->executeStatement();


		$query->insert('machbarkeit_concepts')->values(
			[
				'id' => $this->db->quote(7),
				'display' => $this->db->quote('Radiofrequenzspektroskopie von Brustgewebe'),
				'selectable' => $query->createNamedParameter(true, IQueryBuilder::PARAM_BOOL),
				'module_id' => $this->db->quote(2),
				'parent_id' => $this->db->quote(3),
			],
		)->executeStatement();
	}
}
