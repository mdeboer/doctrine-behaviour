<?php

declare(strict_types=1);

namespace mdeboer\DoctrineBehaviour\Test\Fixture\Entity;

use Doctrine\ORM\Mapping\Entity;
use mdeboer\DoctrineBehaviour\TimestampableInterface;
use mdeboer\DoctrineBehaviour\TimestampableTrait;

#[Entity]
class TimestampableEntity extends AbstractEntity implements TimestampableInterface
{
    use TimestampableTrait;
}
