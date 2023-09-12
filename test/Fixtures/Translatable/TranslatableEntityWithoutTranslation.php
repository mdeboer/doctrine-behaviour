<?php

namespace mdeboer\DoctrineBehaviour\Test\Fixtures\Translatable;

use Doctrine\ORM\Mapping\Entity;
use mdeboer\DoctrineBehaviour\Test\Fixtures\Entities\AbstractEntity;
use mdeboer\DoctrineBehaviour\TranslatableInterface;
use mdeboer\DoctrineBehaviour\TranslatableTrait;

#[Entity]
class TranslatableEntityWithoutTranslation extends AbstractEntity implements TranslatableInterface
{
    use TranslatableTrait;
}
