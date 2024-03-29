<?php

namespace mdeboer\DoctrineBehaviour\Test\Fixtures\Entities;

use Doctrine\ORM\Mapping\Entity;
use mdeboer\DoctrineBehaviour\Test\Fixtures\Entities\AbstractEntity;
use mdeboer\DoctrineBehaviour\TranslatableInterface;
use mdeboer\DoctrineBehaviour\TranslatableTrait;

#[Entity]
class TranslatableEntity extends AbstractEntity implements TranslatableInterface
{
    use TranslatableTrait;

    public function __construct(iterable $translations = [])
    {
        $this->initTranslations($translations);
    }

    public function __clone(): void
    {
        $this->cloneTranslations();
    }
}
