<?php

namespace mdeboer\DoctrineBehaviour\Tests\Listener;

use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\PrePersistEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Events;
use mdeboer\DoctrineBehaviour\Listener\TimestampableListener;
use mdeboer\DoctrineBehaviour\Test\AbstractTestCase;
use mdeboer\DoctrineBehaviour\Test\Assertions\DateAssertions;
use mdeboer\DoctrineBehaviour\Test\Fixtures\Entities\NeutralEntity;
use mdeboer\DoctrineBehaviour\Test\Fixtures\Entities\TimestampableEntity;
use mdeboer\DoctrineBehaviour\Test\Fixtures\Timestampable\TimestampableEntityWithoutInterfaces;
use mdeboer\DoctrineBehaviour\TimestampableTrait;
use PHPUnit\Framework\Attributes\CoversClass;

#[
    CoversClass(TimestampableTrait::class),
    CoversClass(TimestampableListener::class)
]
class TimestampableTest extends AbstractTestCase
{
    use DateAssertions;

    protected function setUp(): void
    {
        parent::setUp();

        // Freeze time
        $now = Carbon::now('Europe/Amsterdam');

        Carbon::setTestNowAndTimezone($now);
        CarbonImmutable::setTestNowAndTimezone($now);
    }

    protected function tearDown(): void
    {
        parent::tearDown();

        // Unfreeze time
        Carbon::setTestNowAndTimezone();
        CarbonImmutable::setTestNowAndTimezone();
    }

    public function testPrePersistWithoutDatesSet(): void
    {
        $entity = new TimestampableEntity();
        $listener = new TimestampableListener();

        $em = $this->createStub(EntityManagerInterface::class);

        $date = Carbon::now();
        static::assertDateTimezoneEquals('Europe/Amsterdam', $date);

        static::assertNull($entity->getCreatedAt());
        static::assertNull($entity->getUpdatedAt());

        $listener->prePersist($entity, new PrePersistEventArgs($entity, $em));

        // Make sure createdAt and updatedAt are current time
        static::assertDateEquals($date, $entity->getCreatedAt());
        static::assertDateTimezoneEquals('UTC', $entity->getCreatedAt());

        static::assertDateEquals($date, $entity->getUpdatedAt());
        static::assertDateTimezoneEquals('UTC', $entity->getUpdatedAt());
    }

    public function testPrePersistWithCreationDateSet(): void
    {
        $entity = new TimestampableEntity();
        $listener = new TimestampableListener();
        $em = $this->createStub(EntityManagerInterface::class);

        $now = CarbonImmutable::now();
        $created = $now->subHours(3);
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

        $now = CarbonImmutable::now();
        $created = $now->subHours(3);
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

        $date = Carbon::now();
        static::assertDateTimezoneEquals('Europe/Amsterdam', $date);

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

        $date = Carbon::now();
        static::assertDateTimezoneEquals('Europe/Amsterdam', $date);

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

        $now = CarbonImmutable::now();

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

        $now = CarbonImmutable::now();
        $created = $now->subHours(3);
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

        $now = CarbonImmutable::now();
        $created = $now->subHours(3);
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
                new TimestampableListener()
            ]
        ]);

        $metadata = $em->getClassMetadata(TimestampableEntity::class);

        static::assertNotNull($metadata);
        static::assertEquals(
            [
                Events::prePersist => [
                    [
                        'class' => TimestampableListener::class,
                        'method' => Events::prePersist
                    ]
                ],
                Events::preUpdate => [
                    [
                        'class' => TimestampableListener::class,
                        'method' => Events::preUpdate
                    ]
                ]
            ],
            $metadata->entityListeners
        );
    }

    public function testLoadClassMetadataOfNonTimestampableEntity(): void
    {
        $em = $this->createEntityManager(eventListeners: [
            [
                [Events::loadClassMetadata],
                new TimestampableListener()
            ]
        ]);

        // Make sure no entity listeners were added to non-timestampable entities.
        $metadata = $em->getClassMetadata(NeutralEntity::class);

        static::assertNotNull($metadata);
        static::assertEmpty($metadata->entityListeners);
    }
}
