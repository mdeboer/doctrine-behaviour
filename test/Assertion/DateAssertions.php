<?php

declare(strict_types=1);

namespace mdeboer\DoctrineBehaviour\Test\Assertion;

trait DateAssertions
{
    /**
     * Assert date equals.
     *
     * Before comparing both dates are converted to UTC and then compared.
     *
     * @param \DateTimeInterface      $expected
     * @param \DateTimeInterface|null $actual
     *
     * @return void
     */
    public function assertDateEquals(
        \DateTimeInterface $expected,
        ?\DateTimeInterface $actual
    ): void {
        $tz = new \DateTimeZone('UTC');

        $expected = \DateTimeImmutable::createFromInterface($expected)->setTimezone($tz);

        if ($actual !== null) {
            $actual = \DateTimeImmutable::createFromInterface($actual)->setTimezone($tz);
        }

        static::assertEquals(
            $expected->format(\DateTimeInterface::ATOM),
            $actual?->format(\DateTimeInterface::ATOM)
        );
    }

    /**
     * Assert date timezone equals.
     *
     * @param string|\DateTimeZone                  $expected
     * @param \DateTimeZone|\DateTimeInterface|null $actual
     *
     * @throws \DateInvalidTimeZoneException
     *
     * @return void
     */
    public function assertDateTimezoneEquals(
        string|\DateTimeZone $expected,
        \DateTimeZone|\DateTimeInterface|null $actual
    ): void {
        if (is_string($expected)) {
            $expected = new \DateTimeZone($expected);
        }

        if ($actual instanceof \DateTimeInterface) {
            $actual = $actual->getTimezone();
        }

        static::assertEquals($expected->getName(), $actual->getName());
    }
}
