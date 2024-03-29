<?php

declare(strict_types=1);

namespace mdeboer\DoctrineBehaviour;

/**
 * Expirable interface.
 */
interface ExpirableInterface
{
    /**
     * Get expiration date.
     *
     * @return \DateTimeImmutable|null
     */
    public function getExpiresAt(): ?\DateTimeImmutable;

    /**
     * Set expiration date.
     *
     * @param \DateTimeInterface|null $date
     *
     * @return $this
     */
    public function setExpiresAt(?\DateTimeInterface $date): self;

    /**
     * Expire in x years/months/days/hours ...
     *
     * @param \DateInterval|string|null $time
     *
     * @return $this
     */
    public function setExpiresIn(\DateInterval|string|null $time): self;

    /**
     * Get if expired.
     *
     * @return bool
     */
    public function isExpired(): bool;

    /**
     * Expire immediately.
     *
     * @return $this
     */
    public function expire(): self;

    /**
     * Unexpire.
     *
     * @return $this
     */
    public function unexpire(): self;
}
