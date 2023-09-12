# Soft-deletable Behaviour

## Usage

1. Create your entity class implementing `mdeboer\DoctrineBehaviour\SoftDeletableInterface`.
2. Use the `mdeboer\DoctrineBehaviour\SoftDeletableTrait` trait.

### Registering the filter

#### Symfony

1. Configure the `expirable` filter in your Doctrine config:

```yaml
doctrine:
    orm:
        filters:
            softdelete:
                class: 'mdeboer\DoctrineBehaviour\Filter\SoftDeleteFilter'
                enabled: true
```

#### Other

1. Configure the `softdelete` filter in your Doctrine config (
   see [documentation](https://www.doctrine-project.org/projects/doctrine-orm/en/latest/reference/filters.html)):

```php
<?php

use mdeboer\DoctrineBehaviour\Filter\SoftDeleteFilter;

// Your ORM configuration
$config = ...;

// Add filter
$config->addFilter('softdelete', SoftDeleteFilter::class);

// Create your Entity Manager
$em = new EntityManager(...);

// Enable expirable filter
$em->getFilters()->enable('softdelete');
```

## Example

```php
<?php
# src/Entity/MySoftDeletableEntity.php

namespace App\Entity;

use mdeboer\DoctrineBehaviour\SoftDeletableInterface;
use mdeboer\DoctrineBehaviour\SoftDeletableTrait;

class MySoftDeletableEntity implements SoftDeletableInterface
{
    use SoftDeletableTrait;
    
    // ... Your entity code here.
}
```

```php
<?php
# src/MyClass.php

namespace App;

use App\Entity\MySoftDeletableEntity;

class MyClass
{
    // ...
    
    function doWhatever() {
        $em = $this->getEntityManager();
        $entity = new MySoftDeletableEntity();
        
        // Soft-delete the entity, will not _actually_ delete it.
        $entity->delete();
        
        $em->persist($entity);
        $em->flush();
    }
}
```
