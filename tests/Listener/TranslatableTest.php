<?php

namespace mdeboer\DoctrineBehaviour\Tests\Listener;

use Doctrine\ORM\Mapping\ClassMetadataInfo;
use Doctrine\ORM\Mapping\MappingException;
use mdeboer\DoctrineBehaviour\Listener\TranslatableListener;
use mdeboer\DoctrineBehaviour\Test\AbstractTestCase;
use mdeboer\DoctrineBehaviour\Test\Fixtures\Entities\TranslatableEntity;
use mdeboer\DoctrineBehaviour\Test\Fixtures\Entities\TranslatableEntityTranslation;
use mdeboer\DoctrineBehaviour\Test\Fixtures\Translatable\OtherEntityTranslation;
use mdeboer\DoctrineBehaviour\Test\Fixtures\Translatable\TranslatableEntityWithoutTranslation;
use mdeboer\DoctrineBehaviour\Test\Fixtures\Translatable\TranslationEntity;
use PHPUnit\Framework\Attributes\CoversClass;

#[CoversClass(TranslatableListener::class)]
class TranslatableTest extends AbstractTestCase
{
    public function testLoadClassMetadataForTranslatable(): void
    {
        $entityClass = TranslatableEntity::class;

        $em = $this->createEntityManager();

        // Check if the association has been mapped.
        $metadata = $em->getClassMetadata($entityClass);

        static::assertNotNull($metadata);

        static::assertTrue($metadata->hasAssociation('translations'));
        static::assertEquals(
            [
                'fieldName' => 'translations',
                'targetEntity' => TranslatableEntityTranslation::class,
                'mappedBy' => 'translatable',
                'fetch' => ClassMetadataInfo::FETCH_EXTRA_LAZY,
                'indexBy' => 'locale',
                'cascade' => ['persist'],
                'orphanRemoval' => true,
                'type' => ClassMetadataInfo::ONE_TO_MANY,
                'inversedBy' => null,
                'isOwningSide' => false,
                'sourceEntity' => $entityClass,
                'isCascadeRemove' => true,
                'isCascadePersist' => true,
                'isCascadeRefresh' => false,
                'isCascadeMerge' => false,
                'isCascadeDetach' => false
            ],
            $metadata->getAssociationMapping('translations')
        );
    }

    public function testLoadClassMetadataForTranslatableWithoutTranslationClass(): void
    {
        $entityClass = TranslatableEntityWithoutTranslation::class;
        $em = $this->createEntityManager();

        // Expect exception
        $this->expectException(MappingException::class);
        $this->expectExceptionMessage("Translation class {$entityClass}Translation not found.");

        $em->getClassMetadata($entityClass);
    }

    public function testLoadClassMetadataForTranslation(): void
    {
        $entityClass = TranslatableEntityTranslation::class;

        $em = $this->createEntityManager();

        // Check if the association has been mapped.
        $metadata = $em->getClassMetadata($entityClass);

        static::assertNotNull($metadata);
        static::assertTrue($metadata->hasAssociation('translatable'));

        static::assertEquals(
            [
                'fieldName' => 'translatable',
                'targetEntity' => TranslatableEntity::class,
                'mappedBy' => null,
                'fetch' => ClassMetadataInfo::FETCH_LAZY,
                'cascade' => [],
                'orphanRemoval' => false,
                'type' => ClassMetadataInfo::MANY_TO_ONE,
                'inversedBy' => 'translations',
                'isOwningSide' => true,
                'sourceEntity' => $entityClass,
                'isCascadeRemove' => false,
                'isCascadePersist' => false,
                'isCascadeRefresh' => false,
                'isCascadeMerge' => false,
                'isCascadeDetach' => false,
                'joinColumns' => [
                    [
                        'name' => 'translatable_id',
                        'referencedColumnName' => 'id',
                        'onDelete' => 'CASCADE',
                        'nullable' => false
                    ]
                ],
                'joinColumnFieldNames' => [
                    'translatable_id' => 'translatable_id'
                ],
                'targetToSourceKeyColumns' => [
                    'id' => 'translatable_id'
                ],
                'sourceToTargetKeyColumns' => [
                    'translatable_id' => 'id'
                ]
            ],
            $metadata->getAssociationMapping('translatable')
        );

        // Check locale field
        static::assertTrue($metadata->hasField('locale'));
        static::assertEquals(
            [
                'fieldName' => 'locale',
                'type' => 'string',
                'length' => 12,
                'columnName' => 'locale'
            ],
            $metadata->getFieldMapping('locale')
        );

        // Check unique constraints
        // FIXME: Unique constraint currently breaks due to a wrong order in the unit of work, see for example: https://github.com/doctrine/orm/issues/6776
        static::assertEmpty($metadata->table['uniqueConstraints'] ?? []);

        static::assertNotEquals(
            [
                "{$metadata->getTableName()}_uniq_trans" => [
                    'columns' => [
                        'translatable_id',
                        'locale'
                    ]
                ]
            ],
            $metadata->table['uniqueConstraints'] ?? []
        );
    }

    public function testLoadClassMetadataForTranslationWithoutTranslatableClass(): void
    {
        $entityClass = OtherEntityTranslation::class;
        $entityTranslatableClass = \preg_replace('/Translation$/', '', $entityClass);

        $em = $this->createEntityManager();

        // Expect exception
        $this->expectException(MappingException::class);
        $this->expectExceptionMessage("Translatable class {$entityTranslatableClass} not found.");

        $em->getClassMetadata($entityClass);
    }

    public function testLoadClassMetadataForTranslationWithWrongName(): void
    {
        $entityClass = TranslationEntity::class;

        $em = $this->createEntityManager();

        // Expect exception
        $this->expectException(MappingException::class);
        $this->expectExceptionMessage("Translation class name should be {$entityClass}Translation.");

        $em->getClassMetadata($entityClass);
    }
}
