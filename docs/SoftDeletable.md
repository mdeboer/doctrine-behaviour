# Soft-deletable Behaviour

1. Create your entity class implementing `mdeboer\DoctrineBehaviour\SoftDeletableInterface`.
2. Use the `mdeboer\DoctrineBehaviour\SoftDeletableTrait` trait.
3. Optionally set up the `mdeboer\DoctrineBehaviour\Filter\SoftDeleteFilter` filter (please see
   the [Doctrine documentation](https://www.doctrine-project.org/projects/doctrine-orm/en/2.11/reference/filters.html)
   on filters).

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
