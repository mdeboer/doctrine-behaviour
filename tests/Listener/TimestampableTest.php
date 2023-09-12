<?php

namespace mdeboer\DoctrineBehaviour\Tests\Listener;

use Carbon\Carbon;
use Carbon\CarbonImmutable;
use Doctrine\ORM\EntityManagerInterface;
use Doctrine\ORM\Event\LoadClassMetadataEventArgs;
use Doctrine\ORM\Event\PrePersistEventArgs;
use Doctrine\ORM\Event\PreUpdateEventArgs;
use Doctrine\ORM\Events;
use mdeboer\DoctrineBehaviour\Listener\TimestampableListener;
use mdeboer\DoctrineBehaviour\Test\AbstractTestCase;
use mdeboer\DoctrineBehaviour\Test\Assertions\DateAssertions;
use mdeboer\DoctrineBehaviour\Test\Fixtures\ExpirableEntity;
use mdeboer\DoctrineBehaviour\Test\Fixtures\Timestampable\TimestampableEntity;
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
        $this->assertDateTimezoneEquals('Europe/Amsterdam', $date);

        $this->assertNull($entity->getCreatedAt());
        $this->assertNull($entity->getUpdatedAt());

        $listener->prePersist($entity, new PrePersistEventArgs($entity, $em));

        // Make sure createdAt and updatedAt are current time
        $this->assertDateEquals($date, $entity->getCreatedAt());
        $this->assertDateTimezoneEquals('UTC', $entity->getCreatedAt());

        $this->assertDateEquals($date, $entity->getUpdatedAt());
        $this->assertDateTimezoneEquals('UTC', $entity->getUpdatedAt());
    }

    public function testPrePersistWithCreationDateSet(): void
    {
        $entity = new TimestampableEntity();
        $listener = new TimestampableListener();
        $em = $this->createStub(EntityManagerInterface::class);

        $now = CarbonImmutable::now();
        $created = $now->subHours(3);
        $this->assertDateTimezoneEquals('Europe/Amsterdam', $now);
        $this->assertDateTimezoneEquals('Europe/Amsterdam', $created);

        $entity->setCreatedAt($created);

        $this->assertDateEquals($created, $entity->getCreatedAt());
        $this->assertDateTimezoneEquals('UTC', $entity->getCreatedAt());
        $this->assertNull($entity->getUpdatedAt());

        $listener->prePersist($entity, new PrePersistEventArgs($entity, $em));

        // Make sure createdAt is not modified
        $this->assertDateEquals($created, $entity->getCreatedAt());
        $this->assertDateTimezoneEquals('UTC', $entity->getCreatedAt());

        // Make sure updatedAt is current time
        $this->assertDateEquals($now, $entity->getUpdatedAt());
        $this->assertDateTimezoneEquals('UTC', $entity->getUpdatedAt());
    }

    public function testPrePersistWithBothDatesSet(): void
    {
        $entity = new TimestampableEntity();
        $listener = new TimestampableListener();
        $em = $this->createStub(EntityManagerInterface::class);

        $now = CarbonImmutable::now();
        $created = $now->subHours(3);
        $this->assertDateTimezoneEquals('Europe/Amsterdam', $now);
        $this->assertDateTimezoneEquals('Europe/Amsterdam', $created);

        $entity->setCreatedAt($created);
        $entity->setUpdatedAt($created);

        $this->assertDateEquals($created, $entity->getCreatedAt());
        $this->assertDateTimezoneEquals('UTC', $entity->getCreatedAt());
        $this->assertDateEquals($created, $entity->getUpdatedAt());
        $this->assertDateTimezoneEquals('UTC', $entity->getUpdatedAt());

        $listener->prePersist($entity, new PrePersistEventArgs($entity, $em));

        // Make sure createdAt and updatedAt are not modified
        $this->assertDateEquals($created, $entity->getCreatedAt());
        $this->assertDateTimezoneEquals('UTC', $entity->getCreatedAt());

        $this->assertDateEquals($created, $entity->getUpdatedAt());
        $this->assertDateTimezoneEquals('UTC', $entity->getUpdatedAt());
    }

    public function testPrePersistWithNonTimestampableEntity(): void
    {
        $entity = new TimestampableEntityWithoutInterfaces();
        $listener = new TimestampableListener();
        $em = $this->createStub(EntityManagerInterface::class);

        $date = Carbon::now();
        $this->assertDateTimezoneEquals('Europe/Amsterdam', $date);

        $this->assertNull($entity->getCreatedAt());
        $this->assertNull($entity->getUpdatedAt());

        $listener->prePersist($entity, new PrePersistEventArgs($entity, $em));

        $this->assertNull($entity->getCreatedAt());
        $this->assertNull($entity->getUpdatedAt());
    }

    public function testPreUpdateWithNonTimestampableEntity(): void
    {
        $entity = new TimestampableEntityWithoutInterfaces();
        $listener = new TimestampableListener();
        $em = $this->createStub(EntityManagerInterface::class);

        $date = Carbon::now();
        $this->assertDateTimezoneEquals('Europe/Amsterdam', $date);

        $this->assertNull($entity->getCreatedAt());
        $this->assertNull($entity->getUpdatedAt());

        $changeSet = [];
        $listener->preUpdate($entity, new PreUpdateEventArgs($entity, $em, $changeSet));

        $this->assertNull($entity->getCreatedAt());
        $this->assertNull($entity->getUpdatedAt());
    }

    public function testPreUpdateWithoutDatesSet(): void
    {
        $entity = new TimestampableEntity();
        $listener = new TimestampableListener();
        $em = $this->createStub(EntityManagerInterface::class);

        $now = CarbonImmutable::now();

        $this->assertNull($entity->getCreatedAt());
        $this->assertNull($entity->getUpdatedAt());

        $changeSet = [];
        $listener->preUpdate($entity, new PreUpdateEventArgs($entity, $em, $changeSet));

        // Make sure createdAt is not modified
        $this->assertNull($entity->getCreatedAt());

        // Make sure updatedAt is current time
        $this->assertDateEquals($now, $entity->getUpdatedAt());
        $this->assertDateTimezoneEquals('UTC', $entity->getUpdatedAt());
    }

    public function testPreUpdateWithCreationDateSet(): void
    {
        $entity = new TimestampableEntity();
        $listener = new TimestampableListener();
        $em = $this->createStub(EntityManagerInterface::class);

        $now = CarbonImmutable::now();
        $created = $now->subHours(3);
        $this->assertDateTimezoneEquals('Europe/Amsterdam', $now);
        $this->assertDateTimezoneEquals('Europe/Amsterdam', $created);

        $entity->setCreatedAt($created);

        $this->assertDateEquals($created, $entity->getCreatedAt());
        $this->assertDateTimezoneEquals('UTC', $entity->getCreatedAt());
        $this->assertNull($entity->getUpdatedAt());

        $changeSet = [];
        $listener->preUpdate($entity, new PreUpdateEventArgs($entity, $em, $changeSet));

        // Make sure createdAt is not modified
        $this->assertDateEquals($created, $entity->getCreatedAt());
        $this->assertDateTimezoneEquals('UTC', $entity->getCreatedAt());

        // Make sure updatedAt is current time
        $this->assertDateEquals($now, $entity->getUpdatedAt());
        $this->assertDateTimezoneEquals('UTC', $entity->getUpdatedAt());
    }

    public function testPreUpdateWithBothDatesSet(): void
    {
        $entity = new TimestampableEntity();
        $listener = new TimestampableListener();
        $em = $this->createStub(EntityManagerInterface::class);

        $now = CarbonImmutable::now();
        $created = $now->subHours(3);
        $this->assertDateTimezoneEquals('Europe/Amsterdam', $now);
        $this->assertDateTimezoneEquals('Europe/Amsterdam', $created);

        $entity->setCreatedAt($created);
        $entity->setUpdatedAt($created);

        $this->assertDateEquals($created, $entity->getCreatedAt());
        $this->assertDateTimezoneEquals('UTC', $entity->getCreatedAt());
        $this->assertDateEquals($created, $entity->getUpdatedAt());
        $this->assertDateTimezoneEquals('UTC', $entity->getUpdatedAt());

        $changeSet = [];
        $listener->preUpdate($entity, new PreUpdateEventArgs($entity, $em, $changeSet));

        // Make sure createdAt is not modified
        $this->assertDateEquals($created, $entity->getCreatedAt());
        $this->assertDateTimezoneEquals('UTC', $entity->getCreatedAt());

        // Make sure updatedAt is current time
        $this->assertDateEquals($now, $entity->getUpdatedAt());
        $this->assertDateTimezoneEquals('UTC', $entity->getUpdatedAt());
    }

    public function testLoadClassMetadata(): void
    {
        $em = $this->createEntityManager();
        $metadata = $em->getClassMetadata(TimestampableEntity::class);

        $this->assertNotNull($metadata);
        $this->assertEquals(
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
        $em = $this->createEntityManager();
        $metadata = $em->getClassMetadata(ExpirableEntity::class);

        // Save the list of configured entity listeners.
        $this->assertNotNull($metadata);

        $listeners = $metadata->entityListeners;

        // Trigger loadClassMetadata event.
        $timestampableSubscriber = new TimestampableListener();
        $timestampableSubscriber->loadClassMetadata(new LoadClassMetadataEventArgs($metadata, $em));

        // Check if the entity listeners have been changed.
        $metadata = $em->getClassMetadata(ExpirableEntity::class);

        $this->assertNotNull($metadata);
        $this->assertEquals($listeners, $metadata->entityListeners);
    }
}
