<?php

namespace App\Model;

use App\Entity\Property;

final class InterventionSearch
{
    public ?\DateTimeImmutable $from = null;
    public ?\DateTimeImmutable $to = null;
    public ?Property $property = null;
    public ?bool $conform = null; // null = tous
}
