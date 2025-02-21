<?php

declare(strict_types=1);

namespace mdeboer\DoctrineBehaviour\Filter;

use Doctrine\ORM\Mapping\ClassMetadata;
use Doctrine\ORM\Query\Filter\SQLFilter;
use mdeboer\DoctrineBehaviour\SoftDeletableInterface;

/**
 * Soft-delete SQL filter.
 *
 * Filters all soft-deleted entities from query results. Entities are considered soft-deleted if deleted_at is not
 * null and regardless whether the time is in the past or in the future.
 */
class SoftDeleteFilter extends SQLFilter
{
    public function addFilterConstraint(ClassMetadata $targetEntity, $targetTableAlias): string
    {
        if (!$targetEntity->getReflectionClass()->implementsInterface(SoftDeletableInterface::class)) {
            return '';
        }

        // Get connection and database platform
        $connection = $this->getConnection();
        $platform = $connection->getDatabasePlatform();

        // Get quoted column name
        $column = sprintf("%s.%s", $targetTableAlias, $targetEntity->getColumnName('deletedAt'));

        // Return constraint where deletedAt is NULL
        return sprintf('%s IS NULL', $platform->quoteIdentifier($column));
    }
}
