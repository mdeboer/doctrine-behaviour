# Timestampable Behaviour

## Usage

1. Create your entity class implementing `mdeboer\DoctrineBehaviour\TimestampableInterface`.
2. Use the `mdeboer\DoctrineBehaviour\TimestampableTrait` trait.

### Registering the events

#### Symfony

1. Load `mdeboer\DoctrineBehaviour\Listener\TimestampableListener` as service in your Symfony config.
2. Tag the service with the `doctrine.event_listener` tag with the `event` option set to `loadClassMetadata`:

```yaml
# config/services.yaml
services:
    # ...
    mdeboer\DoctrineBehaviour\Listener\TimestampableListener:
        tags:
            - { name: 'doctrine.event_listener', event: 'loadClassMetadata' }
```

#### Other

1. Register the `loadClassMetadata` event in the `EventManager`:

```php
<?php

use mdeboer\DoctrineBehaviour\Listener\TimestampableListener;

// Some code to get an instance of your entity manager.
$em = $this->getEntityManager();

// Register the loadClassMetadata event.
$em
    ->getEventManager()
    ->addEventListener(
        ['loadClassMetadata'],
        new TimestampableListener()
    );
```

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
