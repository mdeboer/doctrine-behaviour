<?php

namespace mdeboer\DoctrineBehaviour\Tests\Fixtures\Translatable;

use mdeboer\DoctrineBehaviour\Tests\Fixtures\AbstractEntity;
use mdeboer\DoctrineBehaviour\TranslatableInterface;
use mdeboer\DoctrineBehaviour\TranslatableTrait;
use Doctrine\ORM\Mapping\Entity;

#[Entity]
class TranslatableEntityWithoutTranslation extends AbstractEntity implements TranslatableInterface
{
    use TranslatableTrait;
}
