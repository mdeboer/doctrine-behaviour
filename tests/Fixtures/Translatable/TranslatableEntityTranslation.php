<?php

namespace mdeboer\DoctrineBehaviour\Tests\Fixtures\Translatable;

use mdeboer\DoctrineBehaviour\Tests\Fixtures\AbstractEntity;
use mdeboer\DoctrineBehaviour\TranslationInterface;
use mdeboer\DoctrineBehaviour\TranslationTrait;
use Doctrine\ORM\Mapping\Entity;

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
