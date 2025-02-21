<?php

declare(strict_types=1);

namespace mdeboer\DoctrineBehaviour\Test\Fixture\Entity;

use Doctrine\ORM\Mapping\Entity;
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
