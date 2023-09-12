<?php

namespace mdeboer\DoctrineBehaviour\Tests;

use Carbon\Carbon;
use Carbon\CarbonImmutable;
use mdeboer\DoctrineBehaviour\SoftDeletableTrait;
use mdeboer\DoctrineBehaviour\Test\Assertions\DateAssertions;
use mdeboer\DoctrineBehaviour\Test\Fixtures\Entities\SoftDeletableEntity;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(SoftDeletableTrait::class)]
class SoftDeletableTest extends TestCase
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

    public function testCanSetDeletedAtWithMutable(): void
    {
        $entity = new SoftDeletableEntity();
        $date = Carbon::now();
        static::assertDateTimezoneEquals('Europe/Amsterdam', $date);

        static::assertNull($entity->getDeletedAt());

        $entity->setDeletedAt($date);

        static::assertDateEquals($date, $entity->getDeletedAt());
        static::assertDateTimezoneEquals('UTC', $entity->getDeletedAt());
    }

    public function testCanSetDeletedAtWithImmutable(): void
    {
        $entity = new SoftDeletableEntity();
        $date = CarbonImmutable::now();
        static::assertDateTimezoneEquals('Europe/Amsterdam', $date);

        static::assertNull($entity->getDeletedAt());

        $entity->setDeletedAt($date);

        static::assertDateEquals($date, $entity->getDeletedAt());
        static::assertDateTimezoneEquals('UTC', $entity->getDeletedAt());
    }

    public function testCanUnsetDeletedAt(): void
    {
        $entity = new SoftDeletableEntity();
        $date = Carbon::now();
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
        $date = CarbonImmutable::now()->subHours(2);
        static::assertDateTimezoneEquals('Europe/Amsterdam', $date);

        static::assertNull($entity->getDeletedAt());

        $entity->setDeletedAt($date);

        static::assertTrue($entity->isDeleted());
    }

    public function testIsDeletedWithCurrentDate(): void
    {
        $entity = new SoftDeletableEntity();
        $date = CarbonImmutable::now();
        static::assertDateTimezoneEquals('Europe/Amsterdam', $date);

        static::assertNull($entity->getDeletedAt());

        $entity->setDeletedAt($date);

        static::assertTrue($entity->isDeleted());
    }

    public function testIsDeletedWithFutureDate(): void
    {
        $entity = new SoftDeletableEntity();
        $date = CarbonImmutable::now()->addHours(3);

        static::assertNull($entity->getDeletedAt());

        $entity->setDeletedAt($date);

        static::assertTrue($entity->isDeleted());
    }

    public function testCanDeleteImmediately(): void
    {
        $entity = new SoftDeletableEntity();
        $date = Carbon::now();
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
        $date = Carbon::now();
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
