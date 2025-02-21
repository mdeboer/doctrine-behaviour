<?php

declare(strict_types=1);

namespace mdeboer\DoctrineBehaviour\Tests;

use mdeboer\DoctrineBehaviour\SoftDeletableTrait;
use mdeboer\DoctrineBehaviour\Test\Assertion\DateAssertions;
use mdeboer\DoctrineBehaviour\Test\Fixture\Entity\SoftDeletableEntity;
use mdeboer\DoctrineBehaviour\Test\Trait\MockedTimeTrait;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(SoftDeletableTrait::class)]
class SoftDeletableTest extends TestCase
{
    use DateAssertions;
    use MockedTimeTrait;

    public function testCanSetDeletedAtWithMutable(): void
    {
        $entity = new SoftDeletableEntity();
        $date = \DateTime::createFromImmutable($this->clock->now());
        static::assertDateTimezoneEquals('Europe/Amsterdam', $date);

        static::assertNull($entity->getDeletedAt());

        $entity->setDeletedAt($date);

        static::assertDateEquals($date, $entity->getDeletedAt());
        static::assertDateTimezoneEquals('UTC', $entity->getDeletedAt());
    }

    public function testCanSetDeletedAtWithImmutable(): void
    {
        $entity = new SoftDeletableEntity();
        $date = $this->clock->now();
        static::assertDateTimezoneEquals('Europe/Amsterdam', $date);

        static::assertNull($entity->getDeletedAt());

        $entity->setDeletedAt($date);

        static::assertDateEquals($date, $entity->getDeletedAt());
        static::assertDateTimezoneEquals('UTC', $entity->getDeletedAt());
    }

    public function testCanUnsetDeletedAt(): void
    {
        $entity = new SoftDeletableEntity();
        $date = $this->clock->now();
        static::assertDateTimezoneEquals('Europe/Amsterdam', $date);

        static::assertNull($entity->getDeletedAt());

        $entity->setDeletedAt($date);

        static::assertDateEquals($date, $entity->getDeletedAt());
        static::assertDateTimezoneEquals('UTC', $entity->getDeletedAt());

        $entity->setDeletedAt(null);

        static::assertNull($entity->getDeletedAt());
    }

    public function testIsDeletedWithPastDate(): void
    {
        $entity = new SoftDeletableEntity();
        $date = $this->clock->now()->modify('-2 hours');
        static::assertDateTimezoneEquals('Europe/Amsterdam', $date);

        static::assertNull($entity->getDeletedAt());

        $entity->setDeletedAt($date);

        static::assertTrue($entity->isDeleted());
    }

    public function testIsDeletedWithCurrentDate(): void
    {
        $entity = new SoftDeletableEntity();
        $date = $this->clock->now();
        static::assertDateTimezoneEquals('Europe/Amsterdam', $date);

        static::assertNull($entity->getDeletedAt());

        $entity->setDeletedAt($date);

        static::assertTrue($entity->isDeleted());
    }

    public function testIsDeletedWithFutureDate(): void
    {
        $entity = new SoftDeletableEntity();
        $date = $this->clock->now()->modify('+3 hours');

        static::assertNull($entity->getDeletedAt());

        $entity->setDeletedAt($date);

        static::assertTrue($entity->isDeleted());
    }

    public function testCanDeleteImmediately(): void
    {
        $entity = new SoftDeletableEntity();
        $date = $this->clock->now();
        static::assertDateTimezoneEquals('Europe/Amsterdam', $date);

        static::assertNull($entity->getDeletedAt());

        $entity->delete();

        static::assertTrue($entity->isDeleted());
        static::assertDateEquals($date, $entity->getDeletedAt());
        static::assertDateTimezoneEquals('UTC', $entity->getDeletedAt());
    }

    public function testCanRecoverImmediately(): void
    {
        $entity = new SoftDeletableEntity();
        $date = $this->clock->now();
        static::assertDateTimezoneEquals('Europe/Amsterdam', $date);

        static::assertNull($entity->getDeletedAt());

        $entity->setDeletedAt($date);

        static::assertDateEquals($date, $entity->getDeletedAt());
        static::assertDateTimezoneEquals('UTC', $entity->getDeletedAt());

        $entity->recover();

        static::assertNull($entity->getDeletedAt());
        static::assertFalse($entity->isDeleted());
    }
}
