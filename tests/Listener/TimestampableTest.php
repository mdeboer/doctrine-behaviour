<?php

declare(strict_types=1);

namespace mdeboer\DoctrineBehaviour\Tests\Listener;

use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\PrePersistEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Events;
use mdeboer\DoctrineBehaviour\Listener\TimestampableListener;
use mdeboer\DoctrineBehaviour\Test\AbstractTestCase;
use mdeboer\DoctrineBehaviour\Test\Assertion\DateAssertions;
use mdeboer\DoctrineBehaviour\Test\Fixture\Entity\NeutralEntity;
use mdeboer\DoctrineBehaviour\Test\Fixture\Entity\TimestampableEntity;
use mdeboer\DoctrineBehaviour\Test\Fixture\Timestampable\TimestampableEntityWithoutInterfaces;
use mdeboer\DoctrineBehaviour\Test\Trait\MockedTimeTrait;
use mdeboer\DoctrineBehaviour\TimestampableTrait;
use PHPUnit\Framework\Attributes\CoversClass;

#[
    CoversClass(TimestampableTrait::class),
    CoversClass(TimestampableListener::class)
]
class TimestampableTest extends AbstractTestCase
{
    use DateAssertions;
    use MockedTimeTrait;

    public function testPrePersistWithoutDatesSet(): void
    {
        $entity = new TimestampableEntity();
        $listener = new TimestampableListener();

        $em = $this->createStub(EntityManagerInterface::class);

        $now = $this->clock->now();
        static::assertDateTimezoneEquals('Europe/Amsterdam', $now);

        static::assertNull($entity->getCreatedAt());
        static::assertNull($entity->getUpdatedAt());

        $listener->prePersist($entity, new PrePersistEventArgs($entity, $em));

        // Make sure createdAt and updatedAt are current time
        static::assertDateEquals($now, $entity->getCreatedAt());
        static::assertDateTimezoneEquals('UTC', $entity->getCreatedAt());

        static::assertDateEquals($now, $entity->getUpdatedAt());
        static::assertDateTimezoneEquals('UTC', $entity->getUpdatedAt());
    }

    public function testPrePersistWithCreationDateSet(): void
    {
        $entity = new TimestampableEntity();
        $listener = new TimestampableListener();
        $em = $this->createStub(EntityManagerInterface::class);

        $now = $this->clock->now();
        $created = $now->modify('-3 hours');
        static::assertDateTimezoneEquals('Europe/Amsterdam', $now);
        static::assertDateTimezoneEquals('Europe/Amsterdam', $created);

        $entity->setCreatedAt($created);

        static::assertDateEquals($created, $entity->getCreatedAt());
        static::assertDateTimezoneEquals('UTC', $entity->getCreatedAt());
        static::assertNull($entity->getUpdatedAt());

        $listener->prePersist($entity, new PrePersistEventArgs($entity, $em));

        // Make sure createdAt is not modified
        static::assertDateEquals($created, $entity->getCreatedAt());
        static::assertDateTimezoneEquals('UTC', $entity->getCreatedAt());

        // Make sure updatedAt is current time
        static::assertDateEquals($now, $entity->getUpdatedAt());
        static::assertDateTimezoneEquals('UTC', $entity->getUpdatedAt());
    }

    public function testPrePersistWithBothDatesSet(): void
    {
        $entity = new TimestampableEntity();
        $listener = new TimestampableListener();
        $em = $this->createStub(EntityManagerInterface::class);

        $now = $this->clock->now();
        $created = $now->modify('-3 hours');
        static::assertDateTimezoneEquals('Europe/Amsterdam', $now);
        static::assertDateTimezoneEquals('Europe/Amsterdam', $created);

        $entity->setCreatedAt($created);
        $entity->setUpdatedAt($created);

        static::assertDateEquals($created, $entity->getCreatedAt());
        static::assertDateTimezoneEquals('UTC', $entity->getCreatedAt());
        static::assertDateEquals($created, $entity->getUpdatedAt());
        static::assertDateTimezoneEquals('UTC', $entity->getUpdatedAt());

        $listener->prePersist($entity, new PrePersistEventArgs($entity, $em));

        // Make sure createdAt and updatedAt are not modified
        static::assertDateEquals($created, $entity->getCreatedAt());
        static::assertDateTimezoneEquals('UTC', $entity->getCreatedAt());

        static::assertDateEquals($created, $entity->getUpdatedAt());
        static::assertDateTimezoneEquals('UTC', $entity->getUpdatedAt());
    }

    public function testPrePersistWithNonTimestampableEntity(): void
    {
        $entity = new TimestampableEntityWithoutInterfaces();
        $listener = new TimestampableListener();
        $em = $this->createStub(EntityManagerInterface::class);

        $now = $this->clock->now();
        static::assertDateTimezoneEquals('Europe/Amsterdam', $now);

        static::assertNull($entity->getCreatedAt());
        static::assertNull($entity->getUpdatedAt());

        $listener->prePersist($entity, new PrePersistEventArgs($entity, $em));

        static::assertNull($entity->getCreatedAt());
        static::assertNull($entity->getUpdatedAt());
    }

    public function testPreUpdateWithNonTimestampableEntity(): void
    {
        $entity = new TimestampableEntityWithoutInterfaces();
        $listener = new TimestampableListener();
        $em = $this->createStub(EntityManagerInterface::class);

        $now = $this->clock->now();
        static::assertDateTimezoneEquals('Europe/Amsterdam', $now);

        static::assertNull($entity->getCreatedAt());
        static::assertNull($entity->getUpdatedAt());

        $changeSet = [];
        $listener->preUpdate($entity, new PreUpdateEventArgs($entity, $em, $changeSet));

        static::assertNull($entity->getCreatedAt());
        static::assertNull($entity->getUpdatedAt());
    }

    public function testPreUpdateWithoutDatesSet(): void
    {
        $entity = new TimestampableEntity();
        $listener = new TimestampableListener();
        $em = $this->createStub(EntityManagerInterface::class);

        $now = $this->clock->now();

        static::assertNull($entity->getCreatedAt());
        static::assertNull($entity->getUpdatedAt());

        $changeSet = [];
        $listener->preUpdate($entity, new PreUpdateEventArgs($entity, $em, $changeSet));

        // Make sure createdAt is not modified
        static::assertNull($entity->getCreatedAt());

        // Make sure updatedAt is current time
        static::assertDateEquals($now, $entity->getUpdatedAt());
        static::assertDateTimezoneEquals('UTC', $entity->getUpdatedAt());
    }

    public function testPreUpdateWithCreationDateSet(): void
    {
        $entity = new TimestampableEntity();
        $listener = new TimestampableListener();
        $em = $this->createStub(EntityManagerInterface::class);

        $now = $this->clock->now();
        $created = $now->modify('-3 hours');
        static::assertDateTimezoneEquals('Europe/Amsterdam', $now);
        static::assertDateTimezoneEquals('Europe/Amsterdam', $created);

        $entity->setCreatedAt($created);

        static::assertDateEquals($created, $entity->getCreatedAt());
        static::assertDateTimezoneEquals('UTC', $entity->getCreatedAt());
        static::assertNull($entity->getUpdatedAt());

        $changeSet = [];
        $listener->preUpdate($entity, new PreUpdateEventArgs($entity, $em, $changeSet));

        // Make sure createdAt is not modified
        static::assertDateEquals($created, $entity->getCreatedAt());
        static::assertDateTimezoneEquals('UTC', $entity->getCreatedAt());

        // Make sure updatedAt is current time
        static::assertDateEquals($now, $entity->getUpdatedAt());
        static::assertDateTimezoneEquals('UTC', $entity->getUpdatedAt());
    }

    public function testPreUpdateWithBothDatesSet(): void
    {
        $entity = new TimestampableEntity();
        $listener = new TimestampableListener();
        $em = $this->createStub(EntityManagerInterface::class);

        $now = $this->clock->now();
        $created = $now->modify('-3 hours');
        static::assertDateTimezoneEquals('Europe/Amsterdam', $now);
        static::assertDateTimezoneEquals('Europe/Amsterdam', $created);

        $entity->setCreatedAt($created);
        $entity->setUpdatedAt($created);

        static::assertDateEquals($created, $entity->getCreatedAt());
        static::assertDateTimezoneEquals('UTC', $entity->getCreatedAt());
        static::assertDateEquals($created, $entity->getUpdatedAt());
        static::assertDateTimezoneEquals('UTC', $entity->getUpdatedAt());

        $changeSet = [];
        $listener->preUpdate($entity, new PreUpdateEventArgs($entity, $em, $changeSet));

        // Make sure createdAt is not modified
        static::assertDateEquals($created, $entity->getCreatedAt());
        static::assertDateTimezoneEquals('UTC', $entity->getCreatedAt());

        // Make sure updatedAt is current time
        static::assertDateEquals($now, $entity->getUpdatedAt());
        static::assertDateTimezoneEquals('UTC', $entity->getUpdatedAt());
    }

    public function testLoadClassMetadata(): void
    {
        $em = $this->createEntityManager(eventListeners: [
            [
                [Events::loadClassMetadata],
                new TimestampableListener(),
            ],
        ]);

        $metadata = $em->getClassMetadata(TimestampableEntity::class);

        static::assertNotNull($metadata);
        static::assertEquals(
            [
                Events::prePersist => [
                    [
                        'class' => TimestampableListener::class,
                        'method' => Events::prePersist,
                    ],
                ],
                Events::preUpdate => [
                    [
                        'class' => TimestampableListener::class,
                        'method' => Events::preUpdate,
                    ],
                ],
            ],
            $metadata->entityListeners,
        );
    }

    public function testLoadClassMetadataOfNonTimestampableEntity(): void
    {
        $em = $this->createEntityManager(eventListeners: [
            [
                [Events::loadClassMetadata],
                new TimestampableListener(),
            ],
        ]);

        // Make sure no entity listeners were added to non-timestampable entities.
        $metadata = $em->getClassMetadata(NeutralEntity::class);

        static::assertNotNull($metadata);
        static::assertEmpty($metadata->entityListeners);
    }
}
