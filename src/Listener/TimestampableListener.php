<?php

declare(strict_types=1);

namespace mdeboer\DoctrineBehaviour\Listener;

use Doctrine\ORM\Event\LoadClassMetadataEventArgs;
use Doctrine\ORM\Event\PrePersistEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Events;
use mdeboer\DoctrineBehaviour\TimestampableInterface;
use Psr\Clock\ClockInterface;
use Symfony\Component\Clock\Clock;

class TimestampableListener
{
    private readonly ClockInterface $clock;

    public function __construct(
        ?ClockInterface $clock = null
    ) {
        $this->clock = $clock ?? Clock::get();
    }

    public function prePersist(object $entity, PrePersistEventArgs $event): void
    {
        if (!$entity instanceof TimestampableInterface) {
            return;
        }

        $now = $this->clock->now();

        if ($entity->getCreatedAt() === null) {
            $entity->setCreatedAt($now);
        }

        if ($entity->getUpdatedAt() === null) {
            $entity->setUpdatedAt($now);
        }
    }

    public function preUpdate(object $entity, PreUpdateEventArgs $event): void
    {
        if (!$entity instanceof TimestampableInterface) {
            return;
        }

        $entity->setUpdatedAt($this->clock->now());
    }

    public function loadClassMetadata(LoadClassMetadataEventArgs $eventArgs): void
    {
        $classMetadata = $eventArgs->getClassMetadata();

        if ($classMetadata->reflClass->implementsInterface(TimestampableInterface::class) === false) {
            return;
        }

        $entityListeners = $classMetadata->entityListeners;

        // Return if entity listener has already been configured.
        foreach ($entityListeners as $listeners) {
            foreach ($listeners as $listener) {
                if ($listener['class'] === TimestampableListener::class) {
                    return;
                }
            }
        }

        $classMetadata->addEntityListener(
            Events::prePersist,
            static::class,
            'prePersist'
        );

        $classMetadata->addEntityListener(
            Events::preUpdate,
            static::class,
            'preUpdate'
        );
    }
}
