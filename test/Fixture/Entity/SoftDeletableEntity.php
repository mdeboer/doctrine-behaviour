<?php

declare(strict_types=1);

namespace mdeboer\DoctrineBehaviour\Test\Fixture\Entity;

use Doctrine\ORM\Mapping\Entity;
use mdeboer\DoctrineBehaviour\SoftDeletableInterface;
use mdeboer\DoctrineBehaviour\SoftDeletableTrait;

#[Entity]
class SoftDeletableEntity extends AbstractEntity implements SoftDeletableInterface
{
    use SoftDeletableTrait;
}
