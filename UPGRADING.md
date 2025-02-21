# Upgrading

## 4.x - 5.0.0

It is unlikely that you will need to do anything, things should still work. But it is important to note that this
library no longer depends on [Carbon](https://carbon.nesbot.com) but now works
with [PSR-20 Clock](https://www.php-fig.org/psr/psr-20/) provided by
the [Symfony Clock](https://symfony.com/doc/current/components/clock.html) component.

### Time mocking with Carbon

In case you relied on the time mocking features of [Carbon](https://carbon.nesbot.com) in your application, make sure to
use initialise a [Carbon WrapperClock](https://github.com/briannesbitt/Carbon/blob/3.x/src/Carbon/WrapperClock.php)
instance and set it as the global clock using `Clock::set()`.

### Timestampable listener

The `mdeboer\DoctrineBehaviour\Listener\TimestampableListener` can now optionally be constructed with a clock instance.
When set, entities will be timestamped using this clock. By default, this will use the global clock set.

## 3.0.0 - 4.0.0

### Timestampable

If you are _not_ using the event subscriber `mdeboer\DoctrineBehaviour\Subscriber\TimestampableSubscriber`, you don't
need to do anything. Though you might want to consider [using the new event listener](docs/Timestampable.md) instead to
automatically add the entity listener to each timestampable class. See
the [Timestampable documentation](docs/Timestampable.md) for more information.

To upgrade, remove the use of the event subscriber and replace it by using the event listener. For examples, see below.

#### Symfony

```diff
# config/services.yaml
services:
    # ...
-    mdeboer\DoctrineBehaviour\Subscriber\TimestampableSubscriber:
-        tags:
-            - { name: 'doctrine.event_subscriber' }
+    mdeboer\DoctrineBehaviour\Listener\TimestampableListener:
+        tags:
+            - { name: 'doctrine.event_listener', event: 'loadClassMetadata' }
```

#### Other

```diff
<?php

- use mdeboer\DoctrineBehaviour\Subscriber\TimestampableSubscriber;
+ use mdeboer\DoctrineBehaviour\Listener\TimestampableListener;

// Some code to get an instance of your entity manager.
$em = $this->getEntityManager();

- // Register the event subscriber.
- $em
-     ->getEventManager()
-     ->addEventSubscriber(
-         new TimestampableSubscriber()
-     );

+ // Register the loadClassMetadata event.
+ $em
+     ->getEventManager()
+     ->addEventListener(
+         ['loadClassMetadata'],
+         new TimestampableListener()
+     );
```

### Translatable

If you are _not_ using the event subscriber `mdeboer\DoctrineBehaviour\Subscriber\TranslatableSubscriber`, you don't
need to do anything. Though you might want to consider [using the new event listener](docs/Translatable.md) instead to
automatically configure the mapping for all translatable entities and their translations. See
the [Translatable documentation](docs/Translatable.md) for more information.

To upgrade, remove the use of the event subscriber and replace it by using the event listener. For examples, see below.

#### Symfony

```diff
# config/services.yaml
services:
    # ...
-    mdeboer\DoctrineBehaviour\Subscriber\TranslatableSubscriber:
-        tags:
-            - { name: 'doctrine.event_subscriber' }
+    mdeboer\DoctrineBehaviour\Listener\TranslatableListener:
+        tags:
+            - { name: 'doctrine.event_listener', event: 'loadClassMetadata' }
```

#### Other

```diff
<?php

- use mdeboer\DoctrineBehaviour\Subscriber\TranslatableSubscriber;
+ use mdeboer\DoctrineBehaviour\Listener\TranslatableListener;

// Some code to get an instance of your entity manager.
$em = $this->getEntityManager();

- // Register the event subscriber.
- $em
-     ->getEventManager()
-     ->addEventSubscriber(
-         new TranslatableSubscriber()
-     );

+ // Register the loadClassMetadata event.
+ $em
+     ->getEventManager()
+     ->addEventListener(
+         ['loadClassMetadata'],
+         new TranslatableListener()
+     );
```
