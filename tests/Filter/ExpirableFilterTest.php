<?php

declare(strict_types=1);

namespace mdeboer\DoctrineBehaviour\Tests\Filter;

use mdeboer\DoctrineBehaviour\ExpirableTrait;
use mdeboer\DoctrineBehaviour\Filter\ExpirableFilter;
use mdeboer\DoctrineBehaviour\Test\AbstractTestCase;
use mdeboer\DoctrineBehaviour\Test\Fixture\Entity\ExpirableEntity;
use mdeboer\DoctrineBehaviour\Test\Fixture\Entity\NeutralEntity;
use mdeboer\DoctrineBehaviour\Test\Trait\MockedTimeTrait;
use PHPUnit\Framework\Attributes\CoversClass;

#[
    CoversClass(ExpirableTrait::class),
    CoversClass(ExpirableFilter::class)
]
class ExpirableFilterTest extends AbstractTestCase
{
    use MockedTimeTrait;

    public function testFilterExpiredEntities(): void
    {
        $em = $this->createEntityManager();

        // Enable filter.
        $em->getFilters()->enable('expirable');

        // Persist non-expired entity.
        $validEntity = new ExpirableEntity();
        $em->persist($validEntity);

        // Persist entity that is soon to expire.
        $expiringEntity = new ExpirableEntity();
        $expiringEntity->setExpiresIn('2 hours');
        $em->persist($expiringEntity);

        // Persist expired entity.
        $expiredEntity = new ExpirableEntity();
        $expiredEntity->expire();
        $em->persist($expiredEntity);

        // Flush changes.
        $em->flush();

        // Query all expirable entities to see if filter is working.
        $entities = $em
            ->createQuery(sprintf('SELECT e.id FROM %s e ORDER BY e.id ASC', ExpirableEntity::class))
            ->getSingleColumnResult();

        static::assertSame(
            [
                $validEntity->id,
                $expiringEntity->id,
            ],
            $entities,
        );
    }

    public function testDoesNotFilterOtherEntities(): void
    {
        $em = $this->createEntityManager();

        // Enable filter.
        $em->getFilters()->enable('expirable');

        // Persist other entity
        $entity = new NeutralEntity();
        $em->persist($entity);

        // Flush changes.
        $em->flush();

        // Query all expirable entities to see if filter is working.
        $entities = $em
            ->createQuery(sprintf('SELECT e.id FROM %s e ORDER BY e.id ASC', NeutralEntity::class))
            ->getSingleColumnResult();

        static::assertSame(
            [
                $entity->id,
            ],
            $entities,
        );
    }
}
