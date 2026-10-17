<?php

namespace Tests\Unit;

use App\Support\FinancialAmount;
use PHPUnit\Framework\TestCase;

class FinancialAmountTest extends TestCase
{
    public function test_profit_is_calculated_from_the_unit_price_difference_before_rounding(): void
    {
        $this->assertSame('0.01', FinancialAmount::profit('0.5', '0.02', '0.01'));
    }

    public function test_profit_calculation_preserves_losses(): void
    {
        $this->assertSame('-0.50', FinancialAmount::profit('2', '0.50', '0.75'));
    }
}
