# Translatable Behaviour

1. Create your entity class implementing `mdeboer\DoctrineBehaviour\TranslatableInterface`.
2. Create your translation class implementing `mdeboer\DoctrineBehaviour\TranslationInterface`.
3. Use the `mdeboer\DoctrineBehaviour\TranslatableTrait` trait on the translatable entity.
4. Use the `mdeboer\DoctrineBehaviour\TranslationTrait` trait on the translation entity.

### Registering the events

#### Symfony

1. Load the `mdeboer\DoctrineBehaviour\Listener\TranslatableListener` as service in your Symfony config.
2. Tag the service with the `doctrine.event_listener` tag with the `event` option set to `loadClassMetadata`:

```yaml
# config/services.yaml
services:
    # ...
    mdeboer\DoctrineBehaviour\Listener\TranslatableListener:
        tags:
            - { name: 'doctrine.event_listener', event: 'loadClassMetadata' }
```

#### Other

1. Register the `loadClassMetadata` event in the `EventManager`:

```php
<?php

use mdeboer\DoctrineBehaviour\Listener\TranslatableListener;

// Some code to get an instance of your entity manager.
$em = $this->getEntityManager();

// Register the loadClassMetadata event.
$em
    ->getEventManager()
    ->addEventListener(
        ['loadClassMetadata'],
        new TranslatableListener()
    );
```

## Example

```php
<?php
# src/Entity/MyTranslatableEntity.php

namespace App\Entity;

use mdeboer\DoctrineBehaviour\TranslatableInterface;
use mdeboer\DoctrineBehaviour\TranslatableTrait;

class MyTranslatableEntity implements TranslatableInterface
{
    use TranslatableTrait;
    
    private string $bar = '';
    
    public function __construct() {
        // Initialise the translations collection.
        $this->initTranslations();
    }
    
    public function __clone() {
        // Make sure that the translations are cloned properly.
        $this->cloneTranslations();
    }
    
    // ... Your entity code here with non-translatable properties.
    public function getBar() {
        return $this->bar;
    }
    
    public function setBar(string $value): void {
        $this->bar = $value;
    }
}
```

```php
<?php
# src/Entity/MyTranslatableEntityTranslation.php

namespace App\Entity;

use mdeboer\DoctrineBehaviour\TranslationInterface;
use mdeboer\DoctrineBehaviour\TranslationTrait;

class MyTranslatableEntityTranslation implements TranslationInterface
{
    use TranslationTrait;
    
    // ... Your entity code here with all translatable properties.
    private string $foo = '';
    
    public function getFoo() {
        return $this->foo;
    }
    
    public function setFoo(string $value): void {
        $this->foo = $value;
    }
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
        $entity = new MyTranslatableEntity();
        
        $entity->setBar('baz');
        
        $englishEntity = $entity->translate('en');
        $englishEntity->setFoo('bar');
        // ...
        
        // Translations are saved too (cascaded).
        $em->persist($entity);
        $em->flush();
        
        $entity->getBar() // baz
        $entity->hasTranslation('en'); // true
        $entity->translate('en')->getFoo() // bar
    }
}
```
