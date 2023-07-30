# Timestampable Behaviour

1. Create your entity class implementing `mdeboer\DoctrineBehaviour\TimestampableInterface`.
2. Use the `mdeboer\DoctrineBehaviour\TimestampableTrait` trait.
3. Either add the `mdeboer\DoctrineBehaviour\Listener\TimestampableListener` entity listener to each timestampable
   entity yourself. Or register the `mdeboer\DoctrineBehaviour\Subscriber\TimestampableSubscriber` Symfony event
   subscriber to automatically apply the listener to each timestampable entity (preferred).

## Example

```php
<?php
# src/Entity/MyTimestampableEntity.php

namespace App\Entity;

use mdeboer\DoctrineBehaviour\TimestampableInterface;
use mdeboer\DoctrineBehaviour\TimestampableTrait;

class MyTimestampableEntity implements TimestampableInterface
{
    use TimestampableTrait;
    
    // ... Your entity code here.
}
```

```php
<?php
# src/MyClass.php

namespace App;

use App\Entity\MyTimestampableEntity;

class MyClass
{
    // ...
    
    function doWhatever() {
        $em = $this->getEntityManager();
        $entity = new MyTimestampableEntity();
        
        $em->persist($entity);
        $em->flush();
        
        // Entity will now have created and updated timestamps set.
    }
}
```
