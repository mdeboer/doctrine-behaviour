<?php

namespace mdeboer\DoctrineBehaviour\Tests\Fixtures\Translatable;

use mdeboer\DoctrineBehaviour\Tests\Fixtures\AbstractEntity;
use mdeboer\DoctrineBehaviour\TranslationInterface;
use mdeboer\DoctrineBehaviour\TranslationTrait;
use Doctrine\ORM\Mapping\Entity;

#[Entity]
class OtherEntityTranslation extends AbstractEntity implements TranslationInterface
{
    use TranslationTrait;

    public function __construct(
        ?string $locale = null
    ) {
        if ($locale !== null) {
            $this->setLocale($locale);
        }
    }
}
