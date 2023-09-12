# Expirable Behaviour

## Usage

1. Create your entity class implementing `mdeboer\DoctrineBehaviour\ExpirableInterface`.
2. Use the `mdeboer\DoctrineBehaviour\ExpirableTrait` trait.

### Registering the filter

#### Symfony

1. Configure the `expirable` filter in your Doctrine config:

```yaml
doctrine:
    orm:
        filters:
            expirable:
                class: 'mdeboer\DoctrineBehaviour\Filter\ExpirableFilter'
                enabled: true
```

#### Other

1. Configure the `expirable` filter in your Doctrine config (
   see [documentation](https://www.doctrine-project.org/projects/doctrine-orm/en/latest/reference/filters.html)):

```php
<?php

use mdeboer\DoctrineBehaviour\Filter\ExpirableFilter;

// Your ORM configuration
$config = ...;

// Add filter
$config->addFilter('expirable', ExpirableFilter::class);

// Create your Entity Manager
$em = new EntityManager(...);

// Enable expirable filter
$em->getFilters()->enable('expirable');
```

## Example

```php
<?php
# src/Entity/MyExpirableEntity.php

namespace App\Entity;

use mdeboer\DoctrineBehaviour\ExpirableInterface;
use mdeboer\DoctrineBehaviour\ExpirableTrait;

class MyExpirableEntity implements ExpirableInterface
{
    use ExpirableTrait;
    
    // ... Your entity code here.
}
```

```php
<?php
# src/MyClass.php

namespace App;

use App\Entity\MyExpirableEntity;

class MyClass
{
    // ...
    
    function doWhatever() {        
        $em = $this->getEntityManager();
        $entity = new MyExpirableEntity();
        
        // Let the entity expire in 1 hour from now.
        $entity->setExpiresIn('1h');
        
        $em->persist($entity);
        $em->flush();
    }
}
```
