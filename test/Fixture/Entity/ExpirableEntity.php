<?php

declare(strict_types=1);

namespace mdeboer\DoctrineBehaviour\Test\Fixture\Entity;

use Doctrine\ORM\Mapping\Entity;
use mdeboer\DoctrineBehaviour\ExpirableInterface;
use mdeboer\DoctrineBehaviour\ExpirableTrait;

#[Entity]
class ExpirableEntity extends AbstractEntity implements ExpirableInterface
{
    use ExpirableTrait;
}
