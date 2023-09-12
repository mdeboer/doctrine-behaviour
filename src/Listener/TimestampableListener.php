<?php

namespace mdeboer\DoctrineBehaviour\Listener;

use Carbon\CarbonImmutable;
use Doctrine\ORM\Event\LoadClassMetadataEventArgs;
use Doctrine\ORM\Event\PrePersistEventArgs;
use Doctrine\ORM\Events;
use mdeboer\DoctrineBehaviour\TimestampableInterface;
use Doctrine\ORM\Event\PreUpdateEventArgs;

class TimestampableListener
{
    public function prePersist(object $entity, PrePersistEventArgs $event): void
    {
        $now = CarbonImmutable::now('UTC');

        if (!$entity instanceof TimestampableInterface) {
            return;
        }

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

        $entity->setUpdatedAt(CarbonImmutable::now('UTC'));
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
