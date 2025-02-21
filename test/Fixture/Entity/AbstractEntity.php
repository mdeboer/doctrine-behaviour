<?php

declare(strict_types=1);

namespace mdeboer\DoctrineBehaviour\Test\Fixture\Entity;

use Doctrine\ORM\Mapping\Column;
use Doctrine\ORM\Mapping\GeneratedValue;
use Doctrine\ORM\Mapping\Id;
use Doctrine\ORM\Mapping\MappedSuperclass;

#[MappedSuperclass]
abstract class AbstractEntity
{
    #[Id]
    #[Column]
    #[GeneratedValue]
    public ?int $id = null;
}
