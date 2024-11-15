<?php

declare(strict_types=1);
// SPDX-FileCopyrightText: Nattika Jugkaeo <nattika.jugkaeo@uni-marburg.de>
// SPDX-License-Identifier: AGPL-3.0-or-later

namespace OCA\Machbarkeit\Db;

use JsonSerializable;

use OCP\AppFramework\Db\Entity;

class Filter extends Entity implements JsonSerializable {
	public ?string $filterName = null;
	public ?string $type = null;
	public string $filterOptions = '';

	public function jsonSerialize(): array {
		return [
			'id' => $this->id,
			'filterName' => $this->filterName,
			'type' => $this->type,
			'filterOptions' => $this->filterOptions,
		];
	}
}
