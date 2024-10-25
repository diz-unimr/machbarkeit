<?php

declare(strict_types=1);
// SPDX-FileCopyrightText: Nattika Jugkaeo <nattika.jugkaeo@uni-marburg.de>
// SPDX-License-Identifier: AGPL-3.0-or-later

namespace OCA\Machbarkeit\Db;

use JsonSerializable;

use OCP\AppFramework\Db\Entity;
use PHPUnit\Util\Json;

class OntologyConcept extends Entity implements JsonSerializable
{
	public string $display = '';
	public ?string $termCodes = null;
	public bool $timeRestrictionAllowed = false;
	public bool $typeQuantity = false;
	public bool $selectable = false;
	public bool $leaf = false;
	public ?int $moduleId = null;
	public ?int $parentId = null;
	public ?int $filterOptionsId = null;


	public function jsonSerialize(): array
	{
		return [
			'id' => $this->id,
			'display' => $this->display,
			'termCodes' => $this->termCodes,
			'timeRestrictionAllowed' => $this->timeRestrictionAllowed,
			'typeQuantity' => $this->typeQuantity,
			'selectable' => $this->selectable,
			'leaf' => $this->leaf,
			'filterOptionsId' => $this->filterOptionsId,
			'moduleId' => $this->moduleId,
			'parentId' => $this->parentId,
		];
	}
}
