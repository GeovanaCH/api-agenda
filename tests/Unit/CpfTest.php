<?php

namespace Tests\Unit;

use App\Support\Cpf;
use PHPUnit\Framework\Attributes\DataProvider;
use PHPUnit\Framework\TestCase;

class CpfTest extends TestCase
{
    public function test_it_cleans_non_digits(): void
    {
        $this->assertSame('52998224725', Cpf::clean('529.982.247-25'));
    }

    #[DataProvider('validCpfs')]
    public function test_it_accepts_valid_cpfs(string $cpf): void
    {
        $this->assertTrue(Cpf::isValid($cpf));
    }

    #[DataProvider('invalidCpfs')]
    public function test_it_rejects_invalid_cpfs(string $cpf): void
    {
        $this->assertFalse(Cpf::isValid($cpf));
    }

    /**
     * @return array<string, array{0: string}>
     */
    public static function validCpfs(): array
    {
        return [
            'plain' => ['52998224725'],
            'formatted' => ['529.982.247-25'],
            'another' => ['39053344705'],
        ];
    }

    /**
     * @return array<string, array{0: string}>
     */
    public static function invalidCpfs(): array
    {
        return [
            'repeated' => ['11111111111'],
            'wrong_check' => ['52998224726'],
            'too_short' => ['123'],
            'empty' => [''],
        ];
    }
}
