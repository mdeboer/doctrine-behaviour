<?php

namespace mdeboer\DoctrineBehaviour\Test\Fixtures\Translatable;

use Doctrine\ORM\Mapping\Entity;
use mdeboer\DoctrineBehaviour\Test\Fixtures\Entities\AbstractEntity;
use mdeboer\DoctrineBehaviour\TranslationInterface;
use mdeboer\DoctrineBehaviour\TranslationTrait;

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
