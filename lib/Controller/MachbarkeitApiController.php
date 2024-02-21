<?php

declare(strict_types=1);
// SPDX-FileCopyrightText: Nattika Jugkaeo <nattika.jugkaeo@uni-marburg.de>
// SPDX-License-Identifier: AGPL-3.0-or-later

namespace OCA\Machbarkeit\Controller;

use OCA\Machbarkeit\AppInfo\Application;
use OCA\Machbarkeit\Service\MachbarkeitService;
use OCP\AppFramework\ApiController;
use OCP\AppFramework\Http\DataResponse;
use OCP\IRequest;

class MachbarkeitApiController extends ApiController {
	private MachbarkeitService $service;
	private ?string $userId;
	public function __construct(IRequest $request,
		MachbarkeitService $service,
		?string $userId) {
		parent::__construct(Application::APP_ID, $request);
		$this->service = $service;
		$this->userId = $userId;
	}

	/**
	 * @CORS
	 * @NoCSRFRequired
	 * @NoAdminRequired
	 */
	public function getMetadata(): DataResponse {
		return new DataResponse("DEBUG TEST");
	}
}
