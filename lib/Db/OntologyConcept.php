<?php

declare(strict_types=1);
// SPDX-FileCopyrightText: Nattika Jugkaeo <nattika.jugkaeo@uni-marburg.de>
// SPDX-License-Identifier: AGPL-3.0-or-later

namespace OCA\Machbarkeit\Db;

use JsonSerializable;

use OCP\AppFramework\Db\Entity;

class OntologyConcept extends Entity implements JsonSerializable {
	public string $display = '';
	public ?int $moduleId = null;
	public ?int $parentId = null;
	public bool $selectable = false;
	public bool $leaf = false;

	public function jsonSerialize(): array {
		return [
			'id' => $this->id,
			'display' => $this->display,
			'moduleId' => $this->moduleId,
			'parentId' => $this->parentId,
			'selectable' => $this->selectable,
			'leaf' => $this->leaf
		];
	}
}
