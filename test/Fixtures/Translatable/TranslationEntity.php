<?php

namespace mdeboer\DoctrineBehaviour\Test\Fixtures\Translatable;

use Doctrine\ORM\Mapping\Entity;
use mdeboer\DoctrineBehaviour\Test\Fixtures\AbstractEntity;
use mdeboer\DoctrineBehaviour\TranslationInterface;
use mdeboer\DoctrineBehaviour\TranslationTrait;

#[Entity]
class TranslationEntity extends AbstractEntity implements TranslationInterface
{
    use TranslationTrait;
}
