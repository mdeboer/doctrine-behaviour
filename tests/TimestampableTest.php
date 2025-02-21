<?php

declare(strict_types=1);

namespace mdeboer\DoctrineBehaviour\Tests;

use mdeboer\DoctrineBehaviour\Test\Assertion\DateAssertions;
use mdeboer\DoctrineBehaviour\Test\Fixture\Entity\TimestampableEntity;
use mdeboer\DoctrineBehaviour\Test\Trait\MockedTimeTrait;
use mdeboer\DoctrineBehaviour\TimestampableTrait;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(TimestampableTrait::class)]
class TimestampableTest extends TestCase
{
    use DateAssertions;
    use MockedTimeTrait;

    public function testCanSetCreatedAtWithMutable(): void
    {
        $entity = new TimestampableEntity();
        $date = \DateTime::createFromImmutable($this->clock->now()->modify('+3 hours'));
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
        $date = $this->clock->now()->modify('+3 hours');
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
        $date = \DateTime::createFromImmutable($this->clock->now()->modify('+3 hours'));
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
        $date = $this->clock->now()->modify('+3 hours');
        static::assertDateTimezoneEquals('Europe/Amsterdam', $date);

        static::assertNull($entity->getCreatedAt());
        static::assertNull($entity->getUpdatedAt());

        $entity->setUpdatedAt($date);

        static::assertNull($entity->getCreatedAt());
        static::assertDateEquals($date, $entity->getUpdatedAt());
        static::assertDateTimezoneEquals('UTC', $entity->getUpdatedAt());
    }
}
