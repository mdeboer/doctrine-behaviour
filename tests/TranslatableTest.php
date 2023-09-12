<?php

namespace mdeboer\DoctrineBehaviour\Tests;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use mdeboer\DoctrineBehaviour\Exception\TranslationNotFoundException;
use mdeboer\DoctrineBehaviour\Test\Fixtures\Entities\TranslatableEntity;
use mdeboer\DoctrineBehaviour\Test\Fixtures\Entities\TranslatableEntityTranslation;
use mdeboer\DoctrineBehaviour\Test\Fixtures\Translatable\OtherEntityTranslation;
use mdeboer\DoctrineBehaviour\TranslatableTrait;
use mdeboer\DoctrineBehaviour\TranslationInterface;
use mdeboer\DoctrineBehaviour\TranslationTrait;
use PHPUnit\Framework\Attributes\CoversClass;
use PHPUnit\Framework\TestCase;

#[CoversClass(TranslatableTrait::class)]
#[CoversClass(TranslationTrait::class)]
class TranslatableTest extends TestCase
{
    public function testCanInitialiseEmpty(): void
    {
        $entity = new TranslatableEntity();

        $translations = $entity->getTranslations();

        static::assertInstanceOf(Collection::class, $translations);
        static::assertTrue($translations->isEmpty());
    }

    public function testCanInitialiseWithArray(): void
    {
        $englishTranslation = new TranslatableEntityTranslation('en');
        $germanTranslation = new TranslatableEntityTranslation('de');

        $entity = new TranslatableEntity([$englishTranslation, $germanTranslation]);

        $translations = $entity->getTranslations();

        static::assertInstanceOf(Collection::class, $translations);
        static::assertCount(2, $translations);

        static::assertTrue($translations->contains($englishTranslation));
        static::assertSame($entity, $englishTranslation->getTranslatable());
        static::assertArrayHasKey('en', $translations);

        static::assertTrue($translations->contains($germanTranslation));
        static::assertSame($entity, $germanTranslation->getTranslatable());
        static::assertArrayHasKey('de', $translations);
    }

    public function testCanInitialiseWithArrayCollection(): void
    {
        $englishTranslation = new TranslatableEntityTranslation('en');
        $germanTranslation = new TranslatableEntityTranslation('de');

        $entity = new TranslatableEntity(
            new ArrayCollection([$englishTranslation, $germanTranslation])
        );

        $translations = $entity->getTranslations();

        static::assertInstanceOf(Collection::class, $translations);
        static::assertCount(2, $translations);

        static::assertTrue($translations->contains($englishTranslation));
        static::assertSame($entity, $englishTranslation->getTranslatable());
        static::assertArrayHasKey('en', $translations);

        static::assertTrue($translations->contains($germanTranslation));
        static::assertSame($entity, $germanTranslation->getTranslatable());
        static::assertArrayHasKey('de', $translations);
    }

    public function testCanInitialiseWithArrayContainingDuplicates(): void
    {
        $englishTranslation1 = new TranslatableEntityTranslation('en');
        $englishTranslation2 = new TranslatableEntityTranslation('en');
        $germanTranslation = new TranslatableEntityTranslation('de');

        $entity = new TranslatableEntity(
            [
                $englishTranslation1,
                $englishTranslation2,
                $germanTranslation
            ]
        );

        $translations = $entity->getTranslations();

        static::assertInstanceOf(Collection::class, $translations);
        static::assertCount(2, $translations);

        static::assertFalse($translations->contains($englishTranslation1));
        static::assertArrayHasKey('en', $translations);
        static::assertNotSame($translations['en'], $englishTranslation1);

        static::assertTrue($translations->contains($englishTranslation2));
        static::assertSame($entity, $englishTranslation2->getTranslatable());
        static::assertSame($translations['en'], $englishTranslation2);

        static::assertTrue($translations->contains($germanTranslation));
        static::assertSame($entity, $germanTranslation->getTranslatable());
        static::assertArrayHasKey('de', $translations);
    }

    public function testCanInitialiseWithArrayCollectionContainingDuplicates(): void
    {
        $englishTranslation1 = new TranslatableEntityTranslation('en');
        $englishTranslation2 = new TranslatableEntityTranslation('en');
        $germanTranslation = new TranslatableEntityTranslation('de');

        $entity = new TranslatableEntity(
            new ArrayCollection(
                [
                    $englishTranslation1,
                    $englishTranslation2,
                    $germanTranslation
                ]
            )
        );

        $translations = $entity->getTranslations();

        static::assertInstanceOf(Collection::class, $translations);
        static::assertCount(2, $translations);

        static::assertFalse($translations->contains($englishTranslation1));
        static::assertArrayHasKey('en', $translations);
        static::assertNotSame($translations['en'], $englishTranslation1);

        static::assertTrue($translations->contains($englishTranslation2));
        static::assertSame($entity, $englishTranslation2->getTranslatable());
        static::assertSame($translations['en'], $englishTranslation2);

        static::assertTrue($translations->contains($germanTranslation));
        static::assertSame($entity, $germanTranslation->getTranslatable());
        static::assertArrayHasKey('de', $translations);
    }

    public function testThrowsExceptionOnInitWithInvalidTranslation(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid translation.');

        new TranslatableEntity(
            [
                new TranslatableEntityTranslation('en'),
                new \stdClass()
            ]
        );
    }

    public function testThrowsExceptionOnInitWithTranslationOfOtherEntity(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid translation.');

        new TranslatableEntity(
            [
                new TranslatableEntityTranslation('nl'),
                new OtherEntityTranslation('en')
            ]
        );
    }

    public function testCanClone(): void
    {
        $entity = new TranslatableEntity(
            [
                new TranslatableEntityTranslation('en'),
                new TranslatableEntityTranslation('nl')
            ]
        );

        $translations = $entity->getTranslations();

        static::assertCount(2, $translations);
        static::assertArrayHasKey('en', $translations);
        static::assertArrayHasKey('nl', $translations);

        /** @var TranslationInterface $translation */
        foreach ($translations as $translation) {
            static::assertSame($entity, $translation->getTranslatable());
        }

        $clone = clone $entity;

        $translations = $clone->getTranslations();

        static::assertCount(2, $translations);
        static::assertArrayHasKey('en', $translations);
        static::assertArrayHasKey('nl', $translations);

        // Make sure that translatable has been set to the cloned entity of each translation.
        /** @var TranslationInterface $translation */
        foreach ($translations as $translation) {
            $translatable = $translation->getTranslatable();
            static::assertSame($clone, $translatable);
            static::assertNotSame($entity, $translatable);
        }
    }

    public function testCanAddTranslation(): void
    {
        $entity = new TranslatableEntity();
        $translation = new TranslatableEntityTranslation('en');

        $entity->addTranslation($translation);

        $translations = $entity->getTranslations();

        static::assertCount(1, $translations);
        static::assertTrue($translations->contains($translation));
        static::assertSame($entity, $translation->getTranslatable());
    }

    public function testThrowsExceptionOnAddWithTranslationOfOtherEntity(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid translation.');

        $entity = new TranslatableEntity();

        $entity->addTranslation(new OtherEntityTranslation('en'));
    }

    public function testCanRemoveTranslation(): void
    {
        $translationToRemove = new TranslatableEntityTranslation('en');
        $entity = new TranslatableEntity(
            [
                $translationToRemove,
                new TranslatableEntityTranslation('nl')
            ]
        );

        $translations = $entity->getTranslations();

        static::assertInstanceOf(Collection::class, $translations);
        static::assertCount(2, $translations);
        static::assertTrue($translations->contains($translationToRemove));
        static::assertArrayHasKey('en', $translations);
        static::assertArrayHasKey('nl', $translations);

        // Remove translation
        $entity->removeTranslation($translationToRemove);

        $translations = $entity->getTranslations();

        static::assertCount(1, $translations);
        static::assertFalse($translations->contains($translationToRemove));
        static::assertArrayNotHasKey('en', $translations);
    }

    public function testHasTranslation(): void
    {
        $entity = new TranslatableEntity(
            [
                new TranslatableEntityTranslation('en'),
                new TranslatableEntityTranslation('nl')
            ]
        );

        $translations = $entity->getTranslations();

        static::assertInstanceOf(Collection::class, $translations);
        static::assertCount(2, $translations);

        static::assertTrue($entity->hasTranslation('en'));
        static::assertTrue($entity->hasTranslation('nl'));
        static::assertFalse($entity->hasTranslation(''));
        static::assertFalse($entity->hasTranslation('de'));
        static::assertFalse($entity->hasTranslation('en_US'));
    }

    public function testHasTranslationThrowsOnInvalidLocale(): void
    {
        $entity = new TranslatableEntity();

        // Generate random string longer than maximum allowed size.
        $locale = bin2hex(\random_bytes((\INTL_MAX_LOCALE_LEN / 2) + 10));

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid locale.');

        $entity->hasTranslation($locale);
    }

    public function testCanSetTranslationsWhenEmpty(): void
    {
        $entity = new TranslatableEntity();

        $translations = $entity->getTranslations();

        static::assertInstanceOf(Collection::class, $translations);
        static::assertCount(0, $translations);

        $entity->setTranslations(
            [
                new TranslatableEntityTranslation('en'),
                new TranslatableEntityTranslation('nl')
            ]
        );

        $translations = $entity->getTranslations();

        static::assertInstanceOf(Collection::class, $translations);
        static::assertCount(2, $translations);

        static::assertArrayHasKey('en', $translations);
        static::assertArrayHasKey('nl', $translations);
        static::assertSame($entity, $translations['en']->getTranslatable());
        static::assertSame($entity, $translations['nl']->getTranslatable());
    }

    public function testSetTranslationsRemovesOtherTranslations(): void
    {
        $entity = new TranslatableEntity(
            [
                new TranslatableEntityTranslation('de')
            ]
        );

        $translations = $entity->getTranslations();

        // Make sure we have the DE translation.
        static::assertInstanceOf(Collection::class, $translations);
        static::assertCount(1, $translations);
        static::assertArrayHasKey('de', $translations);

        $entity->setTranslations(
            [
                new TranslatableEntityTranslation('en'),
                new TranslatableEntityTranslation('nl')
            ]
        );

        $translations = $entity->getTranslations();

        static::assertInstanceOf(Collection::class, $translations);
        static::assertCount(2, $translations);

        // Make sure we have EN and NL translations and that the DE translation is removed.
        static::assertArrayHasKey('en', $translations);
        static::assertArrayHasKey('nl', $translations);
        static::assertArrayNotHasKey('de', $translations);
    }

    public function testSetTranslationsReplacesExistingTranslationsOfNewInstance(): void
    {
        $existingTranslation = new TranslatableEntityTranslation('en');

        $entity = new TranslatableEntity(
            [
                $existingTranslation
            ]
        );

        $translations = $entity->getTranslations();

        static::assertInstanceOf(Collection::class, $translations);
        static::assertCount(1, $translations);
        static::assertArrayHasKey('en', $translations);
        static::assertSame($existingTranslation, $translations['en']);

        $entity->setTranslations(
            [
                new TranslatableEntityTranslation('en'),
                new TranslatableEntityTranslation('nl')
            ]
        );

        $translations = $entity->getTranslations();

        static::assertInstanceOf(Collection::class, $translations);
        static::assertCount(2, $translations);

        static::assertArrayHasKey('en', $translations);
        static::assertArrayHasKey('nl', $translations);
        static::assertNotSame($existingTranslation, $translations['en']);
        static::assertNull($existingTranslation->getTranslatable());
    }

    public function testSetTranslationsIgnoresExistingTranslationsOfSameInstance(): void
    {
        $existingTranslation = new TranslatableEntityTranslation('en');

        $entity = new TranslatableEntity(
            [
                $existingTranslation
            ]
        );

        $translations = $entity->getTranslations();

        static::assertInstanceOf(Collection::class, $translations);
        static::assertCount(1, $translations);
        static::assertArrayHasKey('en', $translations);
        static::assertSame($existingTranslation, $translations['en']);

        $existingTranslation->id = 33;

        $entity->setTranslations(
            [
                $existingTranslation
            ]
        );

        $translations = $entity->getTranslations();

        static::assertInstanceOf(Collection::class, $translations);
        static::assertCount(1, $translations);

        static::assertArrayHasKey('en', $translations);
        static::assertSame($existingTranslation, $translations['en']);
        static::assertSame($entity, $existingTranslation->getTranslatable());
    }

    public function testThrowsExceptionOnSetTranslationsWithTranslationOfOtherEntity(): void
    {
        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid translation.');

        $entity = new TranslatableEntity();

        $entity->setTranslations(
            [
                new TranslatableEntityTranslation('nl'),
                new OtherEntityTranslation('en')
            ]
        );
    }

    public function testTranslateEmptyWithNewLocale(): void
    {
        $entity = new TranslatableEntity();

        $translations = $entity->getTranslations();

        static::assertInstanceOf(Collection::class, $translations);
        static::assertCount(0, $translations);

        // Call translate with new locale.
        $newTranslation = $entity->translate('en');

        $translations = $entity->getTranslations();

        static::assertInstanceOf(Collection::class, $translations);
        static::assertCount(1, $translations);
        static::assertArrayHasKey('en', $translations);
        static::assertSame($newTranslation, $translations['en']);
    }

    public function testTranslateWithNewLocale(): void
    {
        $entity = new TranslatableEntity(
            [
                new TranslatableEntityTranslation('de')
            ]
        );

        $translations = $entity->getTranslations();

        static::assertInstanceOf(Collection::class, $translations);
        static::assertCount(1, $translations);
        static::assertArrayHasKey('de', $translations);

        // Call translate with new locale.
        $newTranslation = $entity->translate('en');

        $translations = $entity->getTranslations();

        static::assertInstanceOf(Collection::class, $translations);
        static::assertCount(2, $translations);
        static::assertArrayHasKey('de', $translations);
        static::assertArrayHasKey('en', $translations);
        static::assertSame($newTranslation, $translations['en']);
    }

    public function testTranslateWithExistingLocale(): void
    {
        $entity = new TranslatableEntity();

        $translations = $entity->getTranslations();

        static::assertInstanceOf(Collection::class, $translations);
        static::assertCount(0, $translations);

        // Call translate with new locale.
        $newTranslation = $entity->translate('en');

        $translations = $entity->getTranslations();

        static::assertInstanceOf(Collection::class, $translations);
        static::assertCount(1, $translations);
        static::assertArrayHasKey('en', $translations);
        static::assertSame($newTranslation, $translations['en']);

        static::assertSame($entity->translate('en'), $newTranslation);
    }

    public function testTranslateThrowsOnInvalidLocale(): void
    {
        $entity = new TranslatableEntity();

        // Generate random string longer than maximum allowed size.
        $locale = bin2hex(\random_bytes((\INTL_MAX_LOCALE_LEN / 2) + 10));

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid locale.');

        $entity->translate($locale);
    }

    public function testTranslateReturnsFirstTranslationInArray(): void
    {
        $entity = new TranslatableEntity(
            [
                new TranslatableEntityTranslation('en'),
                new TranslatableEntityTranslation('nl'),
                new TranslatableEntityTranslation('de')
            ]
        );

        $translations = $entity->getTranslations();

        static::assertInstanceOf(Collection::class, $translations);
        static::assertCount(3, $translations);
        static::assertArrayHasKey('en', $translations);
        static::assertArrayHasKey('nl', $translations);
        static::assertArrayHasKey('de', $translations);

        $translation = $entity->translate(['nl', 'de']);

        static::assertSame($translation, $translations['nl']);
        static::assertSame('nl', $translation->getLocale());
        static::assertSame($entity, $translation->getTranslatable());
    }

    public function testTranslateReturnsFallbackTranslationInArray(): void
    {
        $entity = new TranslatableEntity(
            [
                new TranslatableEntityTranslation('en'),
                new TranslatableEntityTranslation('nl'),
                new TranslatableEntityTranslation('de')
            ]
        );

        $translations = $entity->getTranslations();

        static::assertInstanceOf(Collection::class, $translations);
        static::assertCount(3, $translations);
        static::assertArrayHasKey('en', $translations);
        static::assertArrayHasKey('nl', $translations);
        static::assertArrayHasKey('de', $translations);

        $translation = $entity->translate(['it', 'fr', 'de', 'nl']);

        static::assertSame($translation, $translations['de']);
        static::assertSame('de', $translation->getLocale());
        static::assertSame($entity, $translation->getTranslatable());
    }

    public function testTranslateThrowsWhenTranslationNotFoundInArray(): void
    {
        $entity = new TranslatableEntity(
            [
                new TranslatableEntityTranslation('en'),
                new TranslatableEntityTranslation('nl'),
                new TranslatableEntityTranslation('de')
            ]
        );

        $translations = $entity->getTranslations();

        static::assertInstanceOf(Collection::class, $translations);
        static::assertCount(3, $translations);
        static::assertArrayHasKey('en', $translations);
        static::assertArrayHasKey('nl', $translations);
        static::assertArrayHasKey('de', $translations);

        $this->expectException(TranslationNotFoundException::class);

        $entity->translate(['it', 'fr']);
    }

    public function testThrowsExceptionOnSetInvalidLocaleOnTranslation(): void
    {
        $translation = new TranslatableEntityTranslation();

        // Generate random string longer than maximum allowed size.
        $locale = bin2hex(\random_bytes((\INTL_MAX_LOCALE_LEN / 2) + 10));

        $this->expectException(\InvalidArgumentException::class);
        $this->expectExceptionMessage('Invalid locale.');

        $translation->setLocale($locale);
    }
}
