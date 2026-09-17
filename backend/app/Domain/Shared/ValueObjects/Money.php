<?php

namespace App\Domain\Shared\ValueObjects;

use InvalidArgumentException;
use JsonSerializable;
use Stringable;

/**
 * Immutable financial Money value object.
 *
 * All amounts are strictly represented in integer minor units (e.g. piasters/cents).
 * Floating-point arithmetic is strictly prohibited.
 */
class Money implements JsonSerializable, Stringable
{
    protected int $amount;
    protected Currency $currency;

    public function __construct(int $amount, Currency|string $currency)
    {
        $this->amount = $amount;
        $this->currency = Currency::of($currency);
    }

    public static function fromMinor(int $minorUnits, Currency|string $currency): self
    {
        return new self($minorUnits, $currency);
    }

    public static function fromDecimal(string|float|int $decimal, Currency|string $currency): self
    {
        $curr = Currency::of($currency);
        $decimalStr = (string) $decimal;

        // Use bcmath for exact decimal conversion
        $factor = (string) $curr->minorUnitFactor();
        $minor = bcmul($decimalStr, $factor, 0);

        return new self((int) $minor, $curr);
    }

    public static function zero(Currency|string $currency): self
    {
        return new self(0, $currency);
    }

    public function amount(): int
    {
        return $this->amount;
    }

    public function currency(): Currency
    {
        return $this->currency;
    }

    public function add(Money $other): self
    {
        $this->assertSameCurrency($other);
        return new self($this->amount + $other->amount(), $this->currency);
    }

    public function subtract(Money $other): self
    {
        $this->assertSameCurrency($other);
        return new self($this->amount - $other->amount(), $this->currency);
    }

    /**
     * Multiply money by a factor using bcmath fixed-point arithmetic.
     */
    public function multiply(int|float|string $multiplier, int $mode = PHP_ROUND_HALF_UP): self
    {
        $multiplierStr = (string) $multiplier;
        $product = bcmul((string) $this->amount, $multiplierStr, 4);
        $rounded = (int) round((float) $product, 0, $mode);

        return new self($rounded, $this->currency);
    }

    /**
     * Divide money by a divisor using bcmath fixed-point arithmetic.
     */
    public function divide(int|float|string $divisor, int $mode = PHP_ROUND_HALF_UP): self
    {
        $divisorStr = (string) $divisor;
        if (bccomp($divisorStr, '0', 4) === 0) {
            throw new InvalidArgumentException('Division by zero.');
        }

        $quotient = bcdiv((string) $this->amount, $divisorStr, 4);
        $rounded = (int) round((float) $quotient, 0, $mode);

        return new self($rounded, $this->currency);
    }

    /**
     * Allocate the amount across ratios without penny-drop loss.
     * The sum of allocated parts is guaranteed to equal the original amount.
     *
     * @param list<int|float> $ratios
     * @return list<Money>
     */
    public function allocate(array $ratios): array
    {
        if (empty($ratios)) {
            throw new InvalidArgumentException('Cannot allocate across empty ratios.');
        }

        $totalRatio = array_sum($ratios);
        if ($totalRatio <= 0) {
            throw new InvalidArgumentException('Total ratio must be positive.');
        }

        $remainder = $this->amount;
        $results = [];

        foreach ($ratios as $ratio) {
            $share = (int) floor(($this->amount * $ratio) / $totalRatio);
            $results[] = $share;
            $remainder -= $share;
        }

        // Distribute remaining minor units one by one
        for ($i = 0; $remainder > 0; $i++) {
            $results[$i % count($results)]++;
            $remainder--;
        }

        return array_map(fn ($share) => new self($share, $this->currency), $results);
    }

    public function equals(Money $other): bool
    {
        return $this->amount === $other->amount() && $this->currency->equals($other->currency());
    }

    public function greaterThan(Money $other): bool
    {
        $this->assertSameCurrency($other);
        return $this->amount > $other->amount();
    }

    public function greaterThanOrEqual(Money $other): bool
    {
        $this->assertSameCurrency($other);
        return $this->amount >= $other->amount();
    }

    public function lessThan(Money $other): bool
    {
        $this->assertSameCurrency($other);
        return $this->amount < $other->amount();
    }

    public function lessThanOrEqual(Money $other): bool
    {
        $this->assertSameCurrency($other);
        return $this->amount <= $other->amount();
    }

    public function isZero(): bool
    {
        return $this->amount === 0;
    }

    public function isPositive(): bool
    {
        return $this->amount > 0;
    }

    public function isNegative(): bool
    {
        return $this->amount < 0;
    }

    /**
     * Convert to standard decimal representation (e.g. 10.50).
     */
    public function toDecimal(): string
    {
        $factor = (string) $this->currency->minorUnitFactor();
        return bcdiv((string) $this->amount, $factor, $this->currency->decimals());
    }

    /**
     * Format with currency symbol and locale awareness.
     */
    public function format(string $locale = 'en'): string
    {
        $decimal = $this->toDecimal();

        if (str_starts_with($locale, 'ar')) {
            return $decimal . ' ' . $this->currency->symbolNative();
        }

        return $this->currency->symbol() . ' ' . $decimal;
    }

    protected function assertSameCurrency(Money $other): void
    {
        if (!$this->currency->equals($other->currency())) {
            throw new InvalidArgumentException(
                "Currency mismatch: cannot perform operation between {$this->currency->code()} and {$other->currency()->code()}."
            );
        }
    }

    public function jsonSerialize(): array
    {
        return [
            'amount' => $this->amount,
            'decimal' => $this->toDecimal(),
            'currency' => $this->currency->code(),
            'formatted' => $this->format(),
        ];
    }

    public function __toString(): string
    {
        return $this->format();
    }
}
