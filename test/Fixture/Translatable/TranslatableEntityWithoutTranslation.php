<?php

declare(strict_types=1);

namespace mdeboer\DoctrineBehaviour\Test\Fixture\Translatable;

use Doctrine\ORM\Mapping\Entity;
use mdeboer\DoctrineBehaviour\Test\Fixture\Entity\AbstractEntity;
use mdeboer\DoctrineBehaviour\TranslatableInterface;
use mdeboer\DoctrineBehaviour\TranslatableTrait;

#[Entity]
class TranslatableEntityWithoutTranslation extends AbstractEntity implements TranslatableInterface
{
    use TranslatableTrait;
}
