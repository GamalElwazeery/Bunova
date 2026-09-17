<?php

namespace App\Domain\Shared\ValueObjects;

use InvalidArgumentException;
use Stringable;

class Locale implements Stringable
{
    public const AR = 'ar';
    public const AR_EG = 'ar-EG';
    public const AR_SA = 'ar-SA';
    public const EN = 'en';
    public const EN_US = 'en-US';

    protected const EASTERN_NUMERALS = ['٠', '١', '٢', '٣', '٤', '٥', '٦', '٧', '٨', '٩'];
    protected const WESTERN_NUMERALS = ['0', '1', '2', '3', '4', '5', '6', '7', '8', '9'];

    protected string $code;
    protected string $language;
    protected ?string $region;
    protected string $direction;

    public function __construct(string $code)
    {
        $normalized = str_replace('_', '-', trim($code));
        $parts = explode('-', $normalized);
        $lang = strtolower($parts[0]);

        if (!in_array($lang, ['ar', 'en'], true)) {
            throw new InvalidArgumentException("Unsupported locale language '{$lang}'. Only 'ar' and 'en' are supported.");
        }

        $this->code = $normalized;
        $this->language = $lang;
        $this->region = isset($parts[1]) ? strtoupper($parts[1]) : null;
        $this->direction = $lang === 'ar' ? 'rtl' : 'ltr';
    }

    public static function of(Locale|string $locale): self
    {
        return $locale instanceof self ? $locale : new self($locale);
    }

    public static function arabic(): self
    {
        return new self(self::AR);
    }

    public static function english(): self
    {
        return new self(self::EN);
    }

    public function code(): string
    {
        return $this->code;
    }

    public function language(): string
    {
        return $this->language;
    }

    public function region(): ?string
    {
        return $this->region;
    }

    public function direction(): string
    {
        return $this->direction;
    }

    public function isRtl(): bool
    {
        return $this->direction === 'rtl';
    }

    public function isLtr(): bool
    {
        return $this->direction === 'ltr';
    }

    /**
     * Convert Western Arabic digits (0-9) to Eastern Arabic digits (٠-٩).
     */
    public static function toEasternNumerals(string|int|float $value): string
    {
        return str_replace(self::WESTERN_NUMERALS, self::EASTERN_NUMERALS, (string) $value);
    }

    /**
     * Convert Eastern Arabic digits (٠-٩) to Western Arabic digits (0-9).
     */
    public static function toWesternNumerals(string $value): string
    {
        return str_replace(self::EASTERN_NUMERALS, self::WESTERN_NUMERALS, $value);
    }

    public function equals(Locale|string $other): bool
    {
        $otherCode = $other instanceof self ? $other->code() : str_replace('_', '-', trim($other));
        return strtolower($this->code) === strtolower($otherCode);
    }

    public function __toString(): string
    {
        return $this->code;
    }
}
