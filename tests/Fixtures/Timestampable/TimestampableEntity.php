<?php

namespace mdeboer\DoctrineBehaviour\Tests\Fixtures\Timestampable;

use mdeboer\DoctrineBehaviour\Tests\Fixtures\AbstractEntity;
use mdeboer\DoctrineBehaviour\TimestampableInterface;
use mdeboer\DoctrineBehaviour\TimestampableTrait;
use Doctrine\ORM\Mapping\Entity;

#[Entity]
class TimestampableEntity extends AbstractEntity implements TimestampableInterface
{
    use TimestampableTrait;
}
