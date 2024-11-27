<?php

declare(strict_types=1);
// SPDX-FileCopyrightText: Nattika Jugkaeo <nattika.jugkaeo@uni-marburg.de>
// SPDX-License-Identifier: AGPL-3.0-or-later

namespace OCA\Machbarkeit\Controller;

use OCA\Machbarkeit\AppInfo\Application;
use OCA\Machbarkeit\Service\MachbarkeitService;
use OCP\AppFramework\Controller;
use OCP\AppFramework\Http\Attribute\NoCSRFRequired;
use OCP\AppFramework\Http\JSONResponse;
use OCP\IRequest;

class MachbarkeitController extends Controller
{
	#[NoCSRFRequired]
	private MachbarkeitService $service;
	private ?string $userId;

	public function __construct(
		IRequest $request,
		MachbarkeitService $service,
		?string $userId
	) {
		parent::__construct(Application::APP_ID, $request);
		$this->service = $service;
		$this->userId = $userId;
	}

	/*
	 * @CORS
	 * @NoCSRFRequired
	 * @NoAdminRequired
	 */
	public function getMetadata(): JSONResponse
	{
		return new JSONResponse($this->service->readCsv());
	}

	public function getOntology(): JSONResponse
	{
		return new JSONResponse($this->service->readOntology());
	}

	public function getUiProfile(): JSONResponse
	{
		// TODO remove test call
		$modules = $this->getModules();
		// $concepts = $this->service->getConcepts(2);
		$ontology = $this->service->getOntology(2);

		return new JSONResponse($this->service->readUiProfile());
	}

	public function getModules()
	{
		return $this->service->getModules();
	}

	public function getOntologyTree(int $module_id)
	{
		$ontology = $this->service->getOntology($module_id);
		$ontologyTree = $this->service->buildTree($ontology, null);
		return $ontologyTree;
	}

	public function getSearchOntology(string $textSearch, int $module_id)
	{
		$ontology = $this->service->getSearchOntology($textSearch, $module_id);
		return $ontology;
	}

	public function getFilters($filter_options_ids = null)
	{
		return $this->service->getFilters($filter_options_ids);
	}

	public function getConcepts()
	{
		return $this->service->getConcepts(1);
	}

	public function getRequest($criteria)
	{
		return $this->service->getFhirRequest($criteria);
	}
}
