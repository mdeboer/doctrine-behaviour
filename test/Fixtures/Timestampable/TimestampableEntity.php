<?php

namespace mdeboer\DoctrineBehaviour\Test\Fixtures\Timestampable;

use Doctrine\ORM\Mapping\Entity;
use mdeboer\DoctrineBehaviour\Test\Fixtures\AbstractEntity;
use mdeboer\DoctrineBehaviour\TimestampableInterface;
use mdeboer\DoctrineBehaviour\TimestampableTrait;

#[Entity]
class TimestampableEntity extends AbstractEntity implements TimestampableInterface
{
    use TimestampableTrait;
}
