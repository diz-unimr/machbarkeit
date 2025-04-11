<?php

declare(strict_types=1);
// SPDX-FileCopyrightText: Nattika Jugkaeo <nattika.jugkaeo@uni-marburg.de>
// SPDX-License-Identifier: AGPL-3.0-or-later

namespace OCA\Machbarkeit\Db;

use JsonSerializable;

use OCP\AppFramework\Db\Entity;

class Filter extends Entity implements JsonSerializable {
	public ?string $filter_name = null;
	public ?string $type = null;
	public string $filter_options = '';

	public function jsonSerialize(): array {
		return [
			'id' => $this->id,
			'filter_name' => $this->filter_name,
			'type' => $this->type,
			'filter_options' => $this->filter_options,
		];
	}
}
