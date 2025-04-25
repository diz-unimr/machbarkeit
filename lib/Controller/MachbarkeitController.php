<?php

declare(strict_types=1);
// SPDX-FileCopyrightText: Nattika Jugkaeo <nattika.jugkaeo@uni-marburg.de>
// SPDX-License-Identifier: AGPL-3.0-or-later

namespace OCA\Machbarkeit\Controller;

use OCA\Machbarkeit\AppInfo\Application;
use OCA\Machbarkeit\Service\MachbarkeitService;
use OCP\AppFramework\Controller;
use OCP\AppFramework\Http\Attribute\CORS;
use OCP\AppFramework\Http\Attribute\NoAdminRequired;
use OCP\AppFramework\Http\Attribute\NoCSRFRequired;
use OCP\AppFramework\Http\JSONResponse;
use OCP\IRequest;

class MachbarkeitController extends Controller {
	#[NoCSRFRequired]
	private MachbarkeitService $service;
	private ?string $userId;

	public function __construct(
		IRequest $request,
		MachbarkeitService $service,
		?string $userId,
	) {
		parent::__construct(Application::APP_ID, $request);
		$this->service = $service;
		$this->userId = $userId;
	}

	#[CORS]
	#[NoCSRFRequired]
	#[NoAdminRequired]
	public function getMetadata(): JSONResponse {
		return new JSONResponse($this->service->readCsv());
	}

	public function getOntology(): JSONResponse {
		return new JSONResponse($this->service->readOntology());
	}

	public function getModules() {
		return $this->service->getModules();
	}

	public function findOntology(string $code) {
		$ontology = $this->service->getOntologyFromCode($code);
		return $ontology;
	}

	public function getOntologyTree(string $textSearch, int $module_id) {
		$ontology = $this->service->getOntologyTree($textSearch, $module_id);
		if ($textSearch === '_null_') {
			$ontologyTree = $this->service->buildTree($ontology, null);
			return $ontologyTree;
		}
		return $ontology;
	}

	public function getFilters($filter_options_ids = null) {
		return $this->service->getFilters($filter_options_ids);
	}

	public function getConcepts() {
		return $this->service->getConcepts(1);
	}

	public function getRequest($criteria) {
		return $this->service->getFhirRequest($criteria);
	}

	public function selectSql() {
		return $this->service->select_sql();
	}
}
