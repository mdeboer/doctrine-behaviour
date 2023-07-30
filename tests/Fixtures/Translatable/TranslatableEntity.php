<?php

namespace mdeboer\DoctrineBehaviour\Tests\Fixtures\Translatable;

use mdeboer\DoctrineBehaviour\Tests\Fixtures\AbstractEntity;
use mdeboer\DoctrineBehaviour\TranslatableInterface;
use mdeboer\DoctrineBehaviour\TranslatableTrait;
use Doctrine\ORM\Mapping\Entity;

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
