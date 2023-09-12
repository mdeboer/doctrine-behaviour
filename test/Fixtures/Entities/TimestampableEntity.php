<?php

namespace mdeboer\DoctrineBehaviour\Test\Fixtures\Entities;

use Doctrine\ORM\Mapping\Entity;
use mdeboer\DoctrineBehaviour\TimestampableInterface;
use mdeboer\DoctrineBehaviour\TimestampableTrait;

#[Entity]
class TimestampableEntity extends AbstractEntity implements TimestampableInterface
{
    use TimestampableTrait;
}
