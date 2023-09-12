<?php

namespace mdeboer\DoctrineBehaviour\Test\Fixtures\Entities;

use Doctrine\ORM\Mapping\Entity;
use mdeboer\DoctrineBehaviour\ExpirableInterface;
use mdeboer\DoctrineBehaviour\ExpirableTrait;

#[Entity]
class ExpirableEntity extends AbstractEntity implements ExpirableInterface
{
    use ExpirableTrait;
}
