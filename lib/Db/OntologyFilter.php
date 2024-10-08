<?php

declare(strict_types=1);
// SPDX-FileCopyrightText: Nattika Jugkaeo <nattika.jugkaeo@uni-marburg.de>
// SPDX-License-Identifier: AGPL-3.0-or-later

namespace OCA\Machbarkeit\Db;

use JsonSerializable;

use OCP\AppFramework\Db\Entity;

class OntologyFilters extends Entity implements JsonSerializable
{
    public ?int $moduleId = null;
    public ?int $conceptId = null;
    public ?string $code = null;
    public ?string $display = null;
    public ?string $system = null;
    public ?string $version = null;
    public bool $allowedUnits = false;
    public ?bool $selectableConcepts = null;
    public ?bool $timeRestrictionAllowed = null;
    public ?bool $optional = null;
    public ?string $type = null;

    public function jsonSerialize(): array
    {
        return [
            'id' => $this->id,
            'moduleId' => $this->moduleId,
            'conceptId' => $this->conceptId,
            'code' => $this->code,
            'display' => $this->display,
            'system' => $this->system,
            'version' => $this->version,
            'allowedUnits' => $this->allowedUnits,
            'selectableConcepts' => $this->selectableConcepts,
            'timeRestrictionAllowed' => $this->timeRestrictionAllowed,
            'optional' => $this->optional,
            'type' => $this->type,
        ];
    }
}
