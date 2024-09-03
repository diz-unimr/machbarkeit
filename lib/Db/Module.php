<?php

declare(strict_types=1);
// SPDX-FileCopyrightText: Nattika Jugkaeo <nattika.jugkaeo@uni-marburg.de>
// SPDX-License-Identifier: AGPL-3.0-or-later

namespace OCA\Machbarkeit\Db;

use JsonSerializable;

use OCP\AppFramework\Db\Entity;

/**
 * @method getId(): int
 * @method getName(): string
 * @method setName(string $name): void
 * @method getOntology(): string
 * @method setOntology(string $ontology): void
 * @method getUiProfile(): string
 * @method setUiProfile(string $uiProfile): void
 */
class Module extends Entity implements JsonSerializable
{
	public string $name = '';
	public string $version = '';

	public function jsonSerialize(): array
	{
		return [
			'id' => $this->id,
			'name' => $this->name,
			'version' => $this->version,
		];
	}
}
