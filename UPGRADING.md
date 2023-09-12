# Upgrading

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
