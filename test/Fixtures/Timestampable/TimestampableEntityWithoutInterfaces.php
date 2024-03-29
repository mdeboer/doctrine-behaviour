<?php

namespace mdeboer\DoctrineBehaviour\Test\Fixtures\Timestampable;

use Doctrine\ORM\Mapping\Entity;
use mdeboer\DoctrineBehaviour\Test\Fixtures\Entities\AbstractEntity;
use mdeboer\DoctrineBehaviour\TimestampableTrait;

#[Entity]
class TimestampableEntityWithoutInterfaces extends AbstractEntity
{
    use TimestampableTrait;
}
