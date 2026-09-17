<?php

namespace Tests\Feature;

use App\Domain\Shared\ValueObjects\AuditContext;
use App\Domain\Shared\ValueObjects\BusinessDay;
use App\Domain\Shared\ValueObjects\CanonicalId;
use App\Domain\Shared\ValueObjects\Currency;
use App\Domain\Shared\ValueObjects\Locale;
use App\Domain\Shared\ValueObjects\Money;
use Carbon\CarbonImmutable;
use InvalidArgumentException;
use Tests\TestCase;

class ValueObjectsTest extends TestCase
{
    public function test_canonical_id_generation_and_validation(): void
    {
        $id1 = CanonicalId::uuid7();
        $id2 = CanonicalId::uuid7();

        $this->assertTrue(CanonicalId::isValidUuid($id1->value()));
        $this->assertTrue(CanonicalId::isValidUuid($id2->value()));
        $this->assertNotEquals($id1->value(), $id2->value());

        // Sortability of time-ordered UUIDv7
        $this->assertLessThanOrEqual(0, strcmp($id1->value(), $id2->value()));

        // ULID
        $ulid = CanonicalId::ulid();
        $this->assertTrue(CanonicalId::isValidUlid($ulid->value()));

        // Prefixed ID
        $prefixed = CanonicalId::prefixed('bill');
        $this->assertStringStartsWith('bill_', $prefixed->value());
        $this->assertTrue(CanonicalId::isValidUuid(substr($prefixed->value(), 5)));
    }

    public function test_currency_metadata_and_validation(): void
    {
        $egp = new Currency('EGP');
        $this->assertEquals('EGP', $egp->code());
        $this->assertEquals(2, $egp->decimals());
        $this->assertEquals(100, $egp->minorUnitFactor());
        $this->assertEquals('E£', $egp->symbol());
        $this->assertEquals('ج.م', $egp->symbolNative());

        $kwd = new Currency('KWD');
        $this->assertEquals(3, $kwd->decimals());
        $this->assertEquals(1000, $kwd->minorUnitFactor());

        $this->assertTrue(Currency::isValid('SAR'));
        $this->assertTrue(Currency::isValid('AED'));
        $this->assertTrue(Currency::isValid('USD'));
        $this->assertFalse(Currency::isValid('INVALID_CURRENCY'));

        $this->expectException(InvalidArgumentException::class);
        new Currency('XYZ');
    }

    public function test_money_immutable_arithmetic_without_floats(): void
    {
        // 150.75 EGP = 15075 minor units
        $m1 = Money::fromDecimal('150.75', 'EGP');
        $m2 = Money::fromDecimal('49.25', 'EGP');

        $this->assertEquals(15075, $m1->amount());
        $this->assertEquals(4925, $m2->amount());

        // Addition
        $sum = $m1->add($m2);
        $this->assertEquals(20000, $sum->amount());
        $this->assertEquals('200.00', $sum->toDecimal());

        // Subtraction
        $diff = $m1->subtract($m2);
        $this->assertEquals(10150, $diff->amount());
        $this->assertEquals('101.50', $diff->toDecimal());

        // Multiplication (e.g. 14% tax)
        $tax = $m1->multiply('0.14');
        $this->assertEquals(2111, $tax->amount()); // 15075 * 0.14 = 2110.5 -> 2111 rounded
        $this->assertEquals('21.11', $tax->toDecimal());

        // Division
        $split = $sum->divide(4);
        $this->assertEquals(5000, $split->amount());
        $this->assertEquals('50.00', $split->toDecimal());
    }

    public function test_money_currency_mismatch_guards(): void
    {
        $egp = Money::fromDecimal('100.00', 'EGP');
        $usd = Money::fromDecimal('100.00', 'USD');

        $this->expectException(InvalidArgumentException::class);
        $egp->add($usd);
    }

    public function test_money_allocate_preserves_exact_sum_without_penny_drop(): void
    {
        // 100.00 EGP (10000 minor units) split across 3 equal shares
        $money = Money::fromDecimal('100.00', 'EGP');
        $shares = $money->allocate([1, 1, 1]);

        $this->assertCount(3, $shares);
        // Total must strictly equal original 10000 minor units (3334 + 3333 + 3333 = 10000)
        $totalAllocated = $shares[0]->amount() + $shares[1]->amount() + $shares[2]->amount();
        $this->assertEquals(10000, $totalAllocated);
        $this->assertEquals(3334, $shares[0]->amount());
        $this->assertEquals(3333, $shares[1]->amount());
        $this->assertEquals(3333, $shares[2]->amount());
    }

    public function test_money_comparisons_and_formatting(): void
    {
        $m100 = Money::fromDecimal('100.00', 'EGP');
        $m50 = Money::fromDecimal('50.00', 'EGP');
        $mZero = Money::zero('EGP');

        $this->assertTrue($m100->greaterThan($m50));
        $this->assertTrue($m50->lessThan($m100));
        $this->assertTrue($mZero->isZero());
        $this->assertTrue($m100->isPositive());

        // Formatting
        $this->assertEquals('E£ 100.00', $m100->format('en'));
        $this->assertEquals('100.00 ج.م', $m100->format('ar'));
    }

    public function test_business_day_handles_midnight_crossover(): void
    {
        $turnover = '04:00:00';
        $timezone = 'Africa/Cairo';

        // Transaction at 01:30 AM on September 18
        // Since it is before 04:00 AM cutoff, it belongs to September 17 business day!
        $lateNightSale = CarbonImmutable::parse('2026-09-18 01:30:00', $timezone);
        $bDayLate = BusinessDay::forDateTime($lateNightSale, $timezone, $turnover);

        $this->assertEquals('2026-09-17', $bDayLate->toDateString());
        $this->assertEquals('2026-09-17T04:00:00+03:00', $bDayLate->startsAt()->toIso8601String());
        $this->assertEquals('2026-09-18T03:59:59+03:00', $bDayLate->endsAt()->toIso8601String());
        $this->assertTrue($bDayLate->contains($lateNightSale));

        // Transaction at 04:15 AM on September 18
        // This is after 04:00 AM, so it belongs to September 18 business day!
        $earlyMorningSale = CarbonImmutable::parse('2026-09-18 04:15:00', $timezone);
        $bDayMorning = BusinessDay::forDateTime($earlyMorningSale, $timezone, $turnover);

        $this->assertEquals('2026-09-18', $bDayMorning->toDateString());
        $this->assertTrue($bDayMorning->contains($earlyMorningSale));
        $this->assertFalse($bDayLate->contains($earlyMorningSale));
    }

    public function test_locale_and_numeral_shaping(): void
    {
        $ar = Locale::arabic();
        $this->assertEquals('ar', $ar->code());
        $this->assertEquals('rtl', $ar->direction());
        $this->assertTrue($ar->isRtl());

        $en = Locale::english();
        $this->assertEquals('en', $en->code());
        $this->assertEquals('ltr', $en->direction());
        $this->assertTrue($en->isLtr());

        // Numeral conversion
        $western = 'Price: 125.50 EGP';
        $eastern = Locale::toEasternNumerals($western);
        $this->assertEquals('Price: ١٢٥.٥٠ EGP', $eastern);

        $backToWestern = Locale::toWesternNumerals($eastern);
        $this->assertEquals($western, $backToWestern);
    }

    public function test_audit_context_redaction_and_serialization(): void
    {
        $audit = AuditContext::create(
            organizationId: '018f1234-5678-7000-8000-000000000001',
            actionKey: 'staff.pin.update',
            targetType: 'staff_identity',
            targetId: '018f1234-5678-7000-8000-000000000002',
            actorId: '018f1234-5678-7000-8000-000000000003',
            actorType: AuditContext::ACTOR_STAFF,
            branchId: '018f1234-5678-7000-8000-000000000004',
            reason: 'Supervisor override for forgotten PIN',
            payload: [
                'staff_code' => 'STF-01',
                'pin' => '1234',
                'pin_hash' => '$2y$12$securehashvalue',
                'router_secret' => 'superSecretPassword',
                'device_token' => 'bnd_token12345',
                'settings' => [
                    'password' => 'secret123',
                    'language' => 'ar',
                ],
                'note' => 'PIN successfully reset by branch manager',
            ]
        );

        $json = $audit->jsonSerialize();

        $this->assertTrue(CanonicalId::isValidUuid($json['event_id']));
        $this->assertEquals('staff.pin.update', $json['action_key']);
        $this->assertEquals('Supervisor override for forgotten PIN', $json['reason']);

        // Sensitive credentials must be redacted automatically
        $this->assertEquals('[REDACTED]', $json['payload']['pin']);
        $this->assertEquals('[REDACTED]', $json['payload']['pin_hash']);
        $this->assertEquals('[REDACTED]', $json['payload']['router_secret']);
        $this->assertEquals('[REDACTED]', $json['payload']['device_token']);
        $this->assertEquals('[REDACTED]', $json['payload']['settings']['password']);

        // Non-sensitive data remains intact
        $this->assertEquals('STF-01', $json['payload']['staff_code']);
        $this->assertEquals('ar', $json['payload']['settings']['language']);
        $this->assertEquals('PIN successfully reset by branch manager', $json['payload']['note']);
    }
}
