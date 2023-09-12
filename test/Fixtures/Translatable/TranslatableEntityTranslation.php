<?php

namespace mdeboer\DoctrineBehaviour\Test\Fixtures\Translatable;

use Doctrine\ORM\Mapping\Entity;
use mdeboer\DoctrineBehaviour\Test\Fixtures\AbstractEntity;
use mdeboer\DoctrineBehaviour\TranslationInterface;
use mdeboer\DoctrineBehaviour\TranslationTrait;

#[Entity]
class TranslatableEntityTranslation extends AbstractEntity implements TranslationInterface
{
    use TranslationTrait;

    public function __construct(
        ?string $locale = null,
        public ?string $name = null
    ) {
        if ($locale !== null) {
            $this->setLocale($locale);
        }
    }
}
