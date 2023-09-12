<?php

namespace mdeboer\DoctrineBehaviour\Test\Fixtures\Entities;

use Doctrine\ORM\Mapping\Entity;
use mdeboer\DoctrineBehaviour\SoftDeletableInterface;
use mdeboer\DoctrineBehaviour\SoftDeletableTrait;

#[Entity]
class SoftDeletableEntity extends AbstractEntity implements SoftDeletableInterface
{
    use SoftDeletableTrait;
}
