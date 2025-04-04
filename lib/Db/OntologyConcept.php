<?php

declare(strict_types=1);
// SPDX-FileCopyrightText: Nattika Jugkaeo <nattika.jugkaeo@uni-marburg.de>
// SPDX-License-Identifier: AGPL-3.0-or-later

namespace OCA\Machbarkeit\Db;

use JsonSerializable;

use OCP\AppFramework\Db\Entity;

class OntologyConcept extends Entity implements JsonSerializable {
	public string $display = '';
	public string $termCodes = '';
	public string $context = '';
	public string $code = '';
	public string $code_system = '';
	public bool $selectable = false;
	public bool $leaf = false;
	public bool $time_restriction_allowed = false;
	public ?string $filter_name = null;
	public ?string $filter_type = null;
	public ?string $filter_options = '';
	public ?int $module_id = null;
	public ?int $parent_id = null;

	public function jsonSerialize(): array {
		return [
			'id' => $this->id,
			'display' => $this->display,
			'termCodes' => $this->termCodes,
			'context' => $this->context,
			'code' => $this->code,
			'code_system' => $this->code_system,
			'selectable' => $this->selectable,
			'leaf' => $this->leaf,
			'time_restriction_allowed' => $this->time_restriction_allowed,
			'filter_name' => $this->filter_name,
			'filter_type' => $this->filter_type,
			'filter_options' => $this->filter_options,
			'module_id' => $this->module_id,
			'parent_id' => $this->parent_id,
		];
	}
}
