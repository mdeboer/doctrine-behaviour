<?php

declare(strict_types=1);

namespace mdeboer\DoctrineBehaviour;

use Doctrine\Common\Collections\ArrayCollection;
use Doctrine\Common\Collections\Collection;
use Doctrine\Common\Collections\Criteria;
use Doctrine\Common\Collections\Selectable;
use mdeboer\DoctrineBehaviour\Exception\TranslationNotFoundException;

/**
 * Translatable trait.
 *
 * @template T of TranslationInterface
 *
 * @implements TranslatableInterface<T>
 */
trait TranslatableTrait
{
    /** @var Collection<string, T> */
    protected Collection $translations;

    /** @var \WeakReference<T>|null */
    protected ?\WeakReference $currentTranslation = null;

    /**
     * Get translations.
     *
     * @return Collection<string, T>
     */
    public function getTranslations(): Collection
    {
        return $this->translations;
    }

    /**
     * Set translations.
     *
     * @param iterable<array-key, T> $translations
     *
     * @return $this
     */
    public function setTranslations(iterable $translations): self
    {
        $translationClass = static::class . 'Translation';

        $existingLocales = $this->translations->getKeys();
        $setLocales = [];

        // Process set locales
        foreach ($translations as $translation) {
            // Update of an existing translation, no need to replace.
            if ($this->translations->contains($translation)) {
                $setLocales[] = $translation->getLocale();
                continue;
            }

            if ($translation instanceof $translationClass === false) {
                throw new \InvalidArgumentException('Invalid translation.');
            }

            // Canonicalize locale.
            $locale = \Locale::canonicalize($translation->getLocale());

            // Replace a translation (e.g. when same locale is in $translations twice)
            $existingTranslation = $this->translations->get($locale) ?? null;

            if ($existingTranslation !== null && $existingTranslation !== $translation) {
                $existingTranslation->setTranslatable(null);
                $this->translations->removeElement($existingTranslation);
            }

            // Set translatable.
            $translation->setTranslatable($this);

            $this->translations->set($locale, $translation);
            $setLocales[] = $locale;
        }

        // Clean up removed locales
        $removedLocales = array_diff($existingLocales, $setLocales);

        foreach ($removedLocales as $removedLocale) {
            $translation = $this->translations->remove($removedLocale);

            if ($translation instanceof TranslationInterface) {
                $translation->setTranslatable(null);
            }
        }

        return $this;
    }

    /**
     * Add translation.
     *
     * @param T $translation
     *
     * @return $this
     */
    public function addTranslation(TranslationInterface $translation): self
    {
        if ($this->translations->contains($translation) === false) {
            $translationClass = static::class . 'Translation';

            if ($translation instanceof $translationClass === false) {
                throw new \InvalidArgumentException('Invalid translation.');
            }

            $translation->setTranslatable($this);

            $this->translations->set($translation->getLocale(), $translation);
        }

        return $this;
    }

    /**
     * Remove translation
     *
     * @param T $translation
     *
     * @return $this
     */
    public function removeTranslation(TranslationInterface $translation): self
    {
        if ($this->translations->contains($translation)) {
            $translation->setTranslatable(null);

            $this->translations->removeElement($translation);
        }

        return $this;
    }

    /**
     * Check if translation exists.
     *
     * @param string $locale
     *
     * @return bool
     */
    public function hasTranslation(string $locale): bool
    {
        if (empty($locale)) {
            return false;
        }

        $locale = \Locale::canonicalize($locale);

        if ($locale === null) {
            throw new \InvalidArgumentException('Invalid locale.');
        }

        return $this->translations->containsKey($locale);
    }

    /**
     * Get or create translation.
     *
     * @param string|string[] $locale Translation locale, if a string and the translation is missing it will be
     *                                created. When an array, it will return the preferred translation based on
     *                                the order of the array or throw an exception; no translation will be created
     *                                in this case.
     *
     * @throws TranslationNotFoundException When locale parameter is an array and none of the translations for these
     *                                      locales could be found.
     * @return T
     */
    public function translate(string|array $locale): TranslationInterface
    {
        if (is_array($locale)) {
            // Clean up locales.
            $locale = array_unique($locale);

            foreach ($locale as &$l) {
                $l = \Locale::canonicalize($l);

                if ($l === null) {
                    throw new \InvalidArgumentException('Invalid locale.');
                }
            }
            unset($l);

            // Find translation in Selectable collection.
            if ($this->translations instanceof Selectable) {
                $translations = $this->translations
                    ->matching(
                        Criteria::create()
                            ->where(
                                count($locale) > 1
                                    ? Criteria::expr()->in('locale', $locale)
                                    : Criteria::expr()->eq('locale', $locale[0])
                            )
                    );

                foreach ($locale as $l) {
                    foreach ($translations as $translation) {
                        if ($translation->getLocale() === $l) {
                            $this->currentTranslation = \WeakReference::create($translation);

                            return $translation;
                        }
                    }
                }

                throw new TranslationNotFoundException();
            }

            // Find translation in regular collection.
            foreach ($locale as $l) {
                if ($this->translations->containsKey($locale)) {
                    $translation = $this->translations->get($locale);

                    $this->currentTranslation = \WeakReference::create($translation);

                    return $translation;
                }
            }

            throw new TranslationNotFoundException();
        }

        $locale = \Locale::canonicalize($locale);

        if ($locale === null) {
            throw new \InvalidArgumentException('Invalid locale.');
        }

        /** @var T|null $currentTranslation */
        $currentTranslation = $this->currentTranslation?->get();

        if ($currentTranslation instanceof TranslationInterface && $currentTranslation->getLocale() === $locale) {
            return $currentTranslation;
        }

        if (!$translation = $this->translations->get($locale)) {
            $translationClass = static::class . 'Translation';

            $translation = new $translationClass();
            $translation->setLocale($locale);

            $this->addTranslation($translation);
        }

        $this->currentTranslation = \WeakReference::create($translation);

        return $translation;
    }

    /**
     * Initialise translatable entity.
     * Call this method in __construct!
     *
     * @param T[]|ArrayCollection<array-key, T> $translations
     *
     * @return void
     */
    protected function initTranslations(iterable $translations = []): void
    {
        $indexedTranslations = new ArrayCollection();
        $translationClass = static::class . 'Translation';

        foreach ($translations as $translation) {
            if ($translation instanceof TranslationInterface === false) {
                throw new \InvalidArgumentException('Invalid translation.');
            }

            if ($translation instanceof $translationClass === false) {
                throw new \InvalidArgumentException('Invalid translation.');
            }

            $translation->setTranslatable($this);
            $indexedTranslations[$translation->getLocale()] = $translation;
        }

        $this->translations = $indexedTranslations;
    }

    /**
     * Clone translations.
     * Call this method in __clone (optional)!
     *
     * @return void
     */
    protected function cloneTranslations(): void
    {
        $translations = [];

        /** @var TranslationInterface $translation */
        foreach ($this->translations as $locale => $translation) {
            $clonedTranslation = clone $translation;
            $clonedTranslation->setTranslatable($this);

            $translations[$locale] = $clonedTranslation;
        }

        $this->translations = new ArrayCollection($translations);
    }
}
