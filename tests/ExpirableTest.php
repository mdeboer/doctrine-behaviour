<?php

declare(strict_types=1);

namespace mdeboer\DoctrineBehaviour\Tests;

use mdeboer\DoctrineBehaviour\ExpirableTrait;
use mdeboer\DoctrineBehaviour\Test\AbstractTestCase;
use mdeboer\DoctrineBehaviour\Test\Assertion\DateAssertions;
use mdeboer\DoctrineBehaviour\Test\Fixture\Entity\ExpirableEntity;
use mdeboer\DoctrineBehaviour\Test\Trait\MockedTimeTrait;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(ExpirableTrait::class)]
class ExpirableTest extends AbstractTestCase
{
    use DateAssertions;
    use MockedTimeTrait;

    public function testCanSetExpiresAtWithMutable(): void
    {
        $entity = new ExpirableEntity();
        $date = \DateTime::createFromImmutable($this->clock->now()->modify('+3 hours'));

        static::assertNull($entity->getExpiresAt());

        $entity->setExpiresAt($date);

        static::assertDateEquals($date, $entity->getExpiresAt());
    }

    public function testCanSetExpiresAtWithImmutable(): void
    {
        $entity = new ExpirableEntity();
        $date = $this->clock->now()->modify('+3 hours');

        static::assertNull($entity->getExpiresAt());

        $entity->setExpiresAt($date);

        static::assertDateEquals($date, $entity->getExpiresAt());
    }

    public function testCanUnsetExpiresAt(): void
    {
        $entity = new ExpirableEntity();
        $date = $this->clock->now()->modify('+3 hours');

        static::assertNull($entity->getExpiresAt());

        $entity->setExpiresAt($date);

        static::assertDateEquals($date, $entity->getExpiresAt());

        $entity->setExpiresAt(null);

        static::assertNull($entity->getExpiresAt());
    }

    public function testCanSetExpiresInWithDateInterval(): void
    {
        $entity = new ExpirableEntity();
        $date = $this->clock->now()->modify('+3 hours');

        static::assertNull($entity->getExpiresAt());

        $entity->setExpiresIn(new \DateInterval('PT3H'));

        static::assertDateEquals($date, $entity->getExpiresAt());
    }

    public function testCanSetExpiresInWithString(): void
    {
        $entity = new ExpirableEntity();
        $date = $this->clock->now()->modify('+3 hours');

        static::assertNull($entity->getExpiresAt());

        $entity->setExpiresIn('3 hours');

        static::assertDateEquals($date, $entity->getExpiresAt());
    }

    public function testCanUnsetExpiresIn(): void
    {
        $entity = new ExpirableEntity();
        $date = $this->clock->now()->modify('+3 hours');

        static::assertNull($entity->getExpiresAt());

        $entity->setExpiresIn(new \DateInterval('PT3H'));

        static::assertDateEquals($date, $entity->getExpiresAt());

        $entity->setExpiresIn(null);

        static::assertNull($entity->getExpiresAt());
    }

    public function testIsExpiredWithPastDate(): void
    {
        $entity = new ExpirableEntity();
        $date = $this->clock->now()->modify('-2 hours');

        static::assertNull($entity->getExpiresAt());

        $entity->setExpiresAt($date);

        static::assertTrue($entity->isExpired());
    }

    public function testIsExpiredWithCurrentDate(): void
    {
        $entity = new ExpirableEntity();
        $date = $this->clock->now();

        static::assertNull($entity->getExpiresAt());

        $entity->setExpiresAt($date);

        static::assertTrue($entity->isExpired());
    }

    public function testIsNotExpiredWithFutureDate(): void
    {
        $entity = new ExpirableEntity();
        $date = $this->clock->now()->modify('+3 hours');

        static::assertNull($entity->getExpiresAt());

        $entity->setExpiresAt($date);

        static::assertFalse($entity->isExpired());
    }

    public function testIsNotExpiredWithNoDate(): void
    {
        $entity = new ExpirableEntity();

        static::assertNull($entity->getExpiresAt());

        static::assertFalse($entity->isExpired());
    }

    public function testCanExpireImmediately(): void
    {
        $entity = new ExpirableEntity();
        $date = $this->clock->now();

        static::assertNull($entity->getExpiresAt());

        $entity->expire();

        static::assertTrue($entity->isExpired());
        static::assertDateEquals($date, $entity->getExpiresAt());
    }

    public function testCanUnexpire(): void
    {
        $entity = new ExpirableEntity();
        $date = $this->clock->now()->modify('+3 hours');

        $entity->setExpiresAt($date);

        static::assertDateEquals($date, $entity->getExpiresAt());

        $entity->unexpire();

        static::assertNull($entity->getExpiresAt());
    }
}
