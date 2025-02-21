<?php

declare(strict_types=1);

namespace mdeboer\DoctrineBehaviour\Tests\Filter;

use mdeboer\DoctrineBehaviour\Filter\SoftDeleteFilter;
use mdeboer\DoctrineBehaviour\SoftDeletableTrait;
use mdeboer\DoctrineBehaviour\Test\AbstractTestCase;
use mdeboer\DoctrineBehaviour\Test\Fixture\Entity\NeutralEntity;
use mdeboer\DoctrineBehaviour\Test\Fixture\Entity\SoftDeletableEntity;
use mdeboer\DoctrineBehaviour\Test\Trait\MockedTimeTrait;
use PHPUnit\Framework\Attributes\CoversClass;

#[
    CoversClass(SoftDeletableTrait::class),
    CoversClass(SoftDeleteFilter::class)
]
class SoftDeleteFilterTest extends AbstractTestCase
{
    use MockedTimeTrait;

    public function testFilterExpiredEntities(): void
    {
        $em = $this->createEntityManager();

        // Enable filter.
        $em->getFilters()->enable('softdelete');

        // Persist entity.
        $entity = new SoftDeletableEntity();
        $em->persist($entity);

        // Persist soft-deleted entities.
        $deletedEntity = new SoftDeletableEntity();
        $deletedEntity->delete();
        $em->persist($deletedEntity);

        $pastDeletedEntity = new SoftDeletableEntity();
        $pastDeletedEntity->setDeletedAt($this->clock->now()->modify('-4 days'));
        $em->persist($pastDeletedEntity);

        $futureDeletedEntity = new SoftDeletableEntity();
        $futureDeletedEntity->setDeletedAt($this->clock->now()->modify('+4 days'));
        $em->persist($futureDeletedEntity);

        // Flush changes.
        $em->flush();

        // Query all expirable entities to see if filter is working.
        $entities = $em
            ->createQuery(sprintf('SELECT e.id FROM %s e ORDER BY e.id ASC', SoftDeletableEntity::class))
            ->getSingleColumnResult();

        static::assertSame(
            [
                $entity->id,
            ],
            $entities
        );
    }

    public function testDoesNotFilterOtherEntities(): void
    {
        $em = $this->createEntityManager();

        // Enable filter.
        $em->getFilters()->enable('softdelete');

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
            $entities
        );
    }
}
