<?php

declare(strict_types=1);

namespace mdeboer\DoctrineBehaviour\Test\Fixture\Translatable;

use Doctrine\ORM\Mapping\Entity;
use mdeboer\DoctrineBehaviour\Test\Fixture\Entity\AbstractEntity;
use mdeboer\DoctrineBehaviour\TranslationInterface;
use mdeboer\DoctrineBehaviour\TranslationTrait;

#[Entity]
class OtherEntityTranslation extends AbstractEntity implements TranslationInterface
{
    use TranslationTrait;

    public function __construct(
        ?string $locale = null,
    ) {
        if ($locale !== null) {
            $this->setLocale($locale);
        }
    }
}
