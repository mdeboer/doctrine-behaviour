<?php

namespace mdeboer\DoctrineBehaviour\Tests;

use Carbon\Carbon;
use Carbon\CarbonImmutable;
use mdeboer\DoctrineBehaviour\Test\Assertions\DateAssertions;
use mdeboer\DoctrineBehaviour\Test\Fixtures\Entities\TimestampableEntity;
use mdeboer\DoctrineBehaviour\TimestampableTrait;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(TimestampableTrait::class)]
class TimestampableTest extends TestCase
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

    public function testCanSetCreatedAtWithMutable(): void
    {
        $entity = new TimestampableEntity();
        $date = Carbon::now()->addHours(3);
        static::assertDateTimezoneEquals('Europe/Amsterdam', $date);

        static::assertNull($entity->getCreatedAt());
        static::assertNull($entity->getUpdatedAt());

        $entity->setCreatedAt($date);

        static::assertDateEquals($date, $entity->getCreatedAt());
        static::assertDateTimezoneEquals('UTC', $entity->getCreatedAt());
        static::assertNull($entity->getUpdatedAt());
    }

    public function testCanSetCreatedAtWithImmutable(): void
    {
        $entity = new TimestampableEntity();
        $date = CarbonImmutable::now()->addHours(3);
        static::assertDateTimezoneEquals('Europe/Amsterdam', $date);

        static::assertNull($entity->getCreatedAt());
        static::assertNull($entity->getUpdatedAt());

        $entity->setCreatedAt($date);

        static::assertDateEquals($date, $entity->getCreatedAt());
        static::assertDateTimezoneEquals('UTC', $entity->getCreatedAt());
        static::assertNull($entity->getUpdatedAt());
    }

    public function testCanSetUpdatedAtWithMutable(): void
    {
        $entity = new TimestampableEntity();
        $date = Carbon::now()->addHours(3);
        static::assertDateTimezoneEquals('Europe/Amsterdam', $date);

        static::assertNull($entity->getCreatedAt());
        static::assertNull($entity->getUpdatedAt());

        $entity->setUpdatedAt($date);

        static::assertNull($entity->getCreatedAt());
        static::assertDateEquals($date, $entity->getUpdatedAt());
        static::assertDateTimezoneEquals('UTC', $entity->getUpdatedAt());
    }

    public function testCanSetUpdatedAtWithImmutable(): void
    {
        $entity = new TimestampableEntity();
        $date = CarbonImmutable::now()->addHours(3);
        static::assertDateTimezoneEquals('Europe/Amsterdam', $date);

        static::assertNull($entity->getCreatedAt());
        static::assertNull($entity->getUpdatedAt());

        $entity->setUpdatedAt($date);

        static::assertNull($entity->getCreatedAt());
        static::assertDateEquals($date, $entity->getUpdatedAt());
        static::assertDateTimezoneEquals('UTC', $entity->getUpdatedAt());
    }
}
