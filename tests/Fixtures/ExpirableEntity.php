<?php

namespace mdeboer\DoctrineBehaviour\Tests\Fixtures;

use mdeboer\DoctrineBehaviour\ExpirableInterface;
use mdeboer\DoctrineBehaviour\ExpirableTrait;
use Doctrine\ORM\Mapping\Entity;

#[Entity]
class ExpirableEntity extends AbstractEntity implements ExpirableInterface
{
    use ExpirableTrait;
}
