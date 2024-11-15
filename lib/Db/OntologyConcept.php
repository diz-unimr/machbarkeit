<?php

declare(strict_types=1);
// SPDX-FileCopyrightText: Nattika Jugkaeo <nattika.jugkaeo@uni-marburg.de>
// SPDX-License-Identifier: AGPL-3.0-or-later

namespace OCA\Machbarkeit\Db;

use JsonSerializable;

use OCP\AppFramework\Db\Entity;

class OntologyConcept extends Entity implements JsonSerializable {
	public string $display = '';
	public string $code = '';
	public string $codeSystem = '';
	public bool $selectable = false;
	public bool $leaf = false;
	public bool $timeRestrictionAllowed = false;
	public ?string $filterName = null;
	public ?string $filterType = null;
	public ?string $filterOptions = '';
	public ?int $moduleId = null;
	public ?int $parentId = null;

	public function jsonSerialize(): array {
		return [
			'id' => $this->id,
			'display' => $this->display,
			'code' => $this->code,
			'codeSystem' => $this->codeSystem,
			'selectable' => $this->selectable,
			'leaf' => $this->leaf,
			'timeRestrictionAllowed' => $this->timeRestrictionAllowed,
			'filterName' => $this->filterName,
			'filterType' => $this->filterType,
			'filterOptions' => $this->filterOptions,
			'moduleId' => $this->moduleId,
			'parentId' => $this->parentId,
		];
	}
}
