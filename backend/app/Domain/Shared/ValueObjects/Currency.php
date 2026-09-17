<?php

namespace App\Domain\Shared\ValueObjects;

use InvalidArgumentException;
use Stringable;

class Currency implements Stringable
{
    public const EGP = 'EGP';
    public const SAR = 'SAR';
    public const AED = 'AED';
    public const USD = 'USD';
    public const EUR = 'EUR';
    public const KWD = 'KWD';
    public const BHD = 'BHD';

    protected static array $definitions = [
        self::EGP => [
            'name' => 'Egyptian Pound',
            'name_ar' => 'جنيه مصري',
            'decimals' => 2,
            'symbol' => 'E£',
            'symbol_native' => 'ج.م',
        ],
        self::SAR => [
            'name' => 'Saudi Riyal',
            'name_ar' => 'ريال سعودي',
            'decimals' => 2,
            'symbol' => 'SAR',
            'symbol_native' => 'ر.س',
        ],
        self::AED => [
            'name' => 'UAE Dirham',
            'name_ar' => 'درهم إماراتي',
            'decimals' => 2,
            'symbol' => 'AED',
            'symbol_native' => 'د.إ',
        ],
        self::USD => [
            'name' => 'US Dollar',
            'name_ar' => 'دولار أمريكي',
            'decimals' => 2,
            'symbol' => '$',
            'symbol_native' => '$',
        ],
        self::EUR => [
            'name' => 'Euro',
            'name_ar' => 'يورو',
            'decimals' => 2,
            'symbol' => '€',
            'symbol_native' => '€',
        ],
        self::KWD => [
            'name' => 'Kuwaiti Dinar',
            'name_ar' => 'دينار كويتي',
            'decimals' => 3,
            'symbol' => 'KD',
            'symbol_native' => 'د.ك',
        ],
        self::BHD => [
            'name' => 'Bahraini Dinar',
            'name_ar' => 'دينار بحريني',
            'decimals' => 3,
            'symbol' => 'BD',
            'symbol_native' => 'د.ب',
        ],
    ];

    protected string $code;
    protected int $decimals;
    protected string $symbol;
    protected string $symbolNative;
    protected string $name;
    protected string $nameAr;

    public function __construct(string $code)
    {
        $upper = strtoupper(trim($code));
        if (!isset(self::$definitions[$upper])) {
            throw new InvalidArgumentException("Unsupported or invalid currency code '{$code}'.");
        }

        $meta = self::$definitions[$upper];
        $this->code = $upper;
        $this->decimals = $meta['decimals'];
        $this->symbol = $meta['symbol'];
        $this->symbolNative = $meta['symbol_native'];
        $this->name = $meta['name'];
        $this->nameAr = $meta['name_ar'];
    }

    public static function of(Currency|string $currency): self
    {
        return $currency instanceof self ? $currency : new self($currency);
    }

    public static function isValid(string $code): bool
    {
        return isset(self::$definitions[strtoupper(trim($code))]);
    }

    public static function supportedCodes(): array
    {
        return array_keys(self::$definitions);
    }

    public function code(): string
    {
        return $this->code;
    }

    public function decimals(): int
    {
        return $this->decimals;
    }

    public function minorUnitFactor(): int
    {
        return (int) (10 ** $this->decimals);
    }

    public function symbol(): string
    {
        return $this->symbol;
    }

    public function symbolNative(): string
    {
        return $this->symbolNative;
    }

    public function name(string $locale = 'en'): string
    {
        return str_starts_with($locale, 'ar') ? $this->nameAr : $this->name;
    }

    public function equals(Currency|string $other): bool
    {
        $otherCode = $other instanceof self ? $other->code() : strtoupper(trim($other));
        return $this->code === $otherCode;
    }

    public function __toString(): string
    {
        return $this->code;
    }
}
