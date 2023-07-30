<?php

namespace mdeboer\DoctrineBehaviour\Tests\Fixtures;

use mdeboer\DoctrineBehaviour\SoftDeletableInterface;
use mdeboer\DoctrineBehaviour\SoftDeletableTrait;
use Doctrine\ORM\Mapping\Entity;

#[Entity]
class SoftDeletableEntity extends AbstractEntity implements SoftDeletableInterface
{
    use SoftDeletableTrait;
}
