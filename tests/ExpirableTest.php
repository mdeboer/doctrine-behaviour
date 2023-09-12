<?php

namespace mdeboer\DoctrineBehaviour\Tests;

use Carbon\Carbon;
use Carbon\CarbonImmutable;
use mdeboer\DoctrineBehaviour\ExpirableTrait;
use mdeboer\DoctrineBehaviour\Test\Assertions\DateAssertions;
use mdeboer\DoctrineBehaviour\Test\Fixtures\ExpirableEntity;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(ExpirableTrait::class)]
class ExpirableTest extends TestCase
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

    public function testCanSetExpiresAtWithMutable(): void
    {
        $entity = new ExpirableEntity();
        $date = Carbon::now()->addHours(3);

        $this->assertNull($entity->getExpiresAt());

        $entity->setExpiresAt($date);

        $this->assertDateEquals($date, $entity->getExpiresAt());
    }

    public function testCanSetExpiresAtWithImmutable(): void
    {
        $entity = new ExpirableEntity();
        $date = CarbonImmutable::now()->addHours(3);

        $this->assertNull($entity->getExpiresAt());

        $entity->setExpiresAt($date);

        $this->assertDateEquals($date, $entity->getExpiresAt());
    }

    public function testCanUnsetExpiresAt(): void
    {
        $entity = new ExpirableEntity();
        $date = Carbon::now()->addHours(3);

        $this->assertNull($entity->getExpiresAt());

        $entity->setExpiresAt($date);

        $this->assertDateEquals($date, $entity->getExpiresAt());

        $entity->setExpiresAt(null);

        $this->assertNull($entity->getExpiresAt());
    }

    public function testCanSetExpiresInWithDateInterval(): void
    {
        $entity = new ExpirableEntity();
        $date = Carbon::now()->addHours(3);

        $this->assertNull($entity->getExpiresAt());

        $entity->setExpiresIn(new \DateInterval('PT3H'));

        $this->assertDateEquals($date, $entity->getExpiresAt());
    }

    public function testCanSetExpiresInWithString(): void
    {
        $entity = new ExpirableEntity();
        $date = Carbon::now()->addHours(3);

        $this->assertNull($entity->getExpiresAt());

        $entity->setExpiresIn('3 hours');

        $this->assertDateEquals($date, $entity->getExpiresAt());
    }

    public function testCanUnsetExpiresIn(): void
    {
        $entity = new ExpirableEntity();
        $date = Carbon::now()->addHours(3);

        $this->assertNull($entity->getExpiresAt());

        $entity->setExpiresIn(new \DateInterval('PT3H'));

        $this->assertDateEquals($date, $entity->getExpiresAt());

        $entity->setExpiresIn(null);

        $this->assertNull($entity->getExpiresAt());
    }

    public function testIsExpiredWithPastDate(): void
    {
        $entity = new ExpirableEntity();
        $date = Carbon::now()->subHours(2);

        $this->assertNull($entity->getExpiresAt());

        $entity->setExpiresAt($date);

        $this->assertTrue($entity->isExpired());
    }

    public function testIsExpiredWithCurrentDate(): void
    {
        $entity = new ExpirableEntity();
        $date = Carbon::now();

        $this->assertNull($entity->getExpiresAt());

        $entity->setExpiresAt($date);

        $this->assertTrue($entity->isExpired());
    }

    public function testIsNotExpiredWithFutureDate(): void
    {
        $entity = new ExpirableEntity();
        $date = Carbon::now()->addHours(3);

        $this->assertNull($entity->getExpiresAt());

        $entity->setExpiresAt($date);

        $this->assertFalse($entity->isExpired());
    }

    public function testIsNotExpiredWithNoDate(): void
    {
        $entity = new ExpirableEntity();

        $this->assertNull($entity->getExpiresAt());

        $this->assertFalse($entity->isExpired());
    }

    public function testCanExpireImmediately(): void
    {
        $entity = new ExpirableEntity();
        $date = Carbon::now();

        $this->assertNull($entity->getExpiresAt());

        $entity->expire();

        $this->assertTrue($entity->isExpired());
        $this->assertDateEquals($date, $entity->getExpiresAt());
    }

    public function testCanUnexpire(): void
    {
        $entity = new ExpirableEntity();
        $date = Carbon::now()->addHours(3);

        $entity->setExpiresAt($date);

        $this->assertDateEquals($date, $entity->getExpiresAt());

        $entity->unexpire();

        $this->assertNull($entity->getExpiresAt());
    }
}
