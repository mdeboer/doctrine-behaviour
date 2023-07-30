<?php

namespace mdeboer\DoctrineBehaviour\Tests\Fixtures\Timestampable;

use mdeboer\DoctrineBehaviour\Tests\Fixtures\AbstractEntity;
use mdeboer\DoctrineBehaviour\TimestampableTrait;
use Doctrine\ORM\Mapping\Entity;

#[Entity]
class TimestampableEntityWithoutInterfaces extends AbstractEntity
{
    use TimestampableTrait;
}
