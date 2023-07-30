<?php

namespace mdeboer\DoctrineBehaviour\Tests\Fixtures\Translatable;

use mdeboer\DoctrineBehaviour\Tests\Fixtures\AbstractEntity;
use mdeboer\DoctrineBehaviour\TranslationInterface;
use mdeboer\DoctrineBehaviour\TranslationTrait;
use Doctrine\ORM\Mapping\Entity;

#[Entity]
class TranslationEntity extends AbstractEntity implements TranslationInterface
{
    use TranslationTrait;
}
