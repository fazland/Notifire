<?php declare(strict_types=1);

namespace Fazland\Notifire\Tests\Util\Email;

use Fazland\Notifire\Util\Email\AddressParser;
use PHPUnit\Framework\TestCase;

class AddressParserTest extends TestCase
{
    public function addressDataProvider(): iterable
    {
        return [
            [['address' => 'pete@silly.example', 'personal' => 'Pete'], 'Pete <pete@silly.example>'],
            [['address' => 'joe@where.test', 'personal' => null], 'joe@where.test'],
            [['address' => 'smith@home.example', 'personal' => 'Mary Smith: Personal Account'], '"Mary Smith: Personal Account" <smith@home.example>'],
            [['address' => 'pete(his account)@silly.test(his host)', 'personal' => 'Pete(A wonderful \) chap)'], 'Pete(A wonderful \) chap) <pete(his account)@silly.test(his host)>'],
//            [['address' => 'jdoe@test   . example', 'personal' => null], 'jdoe@test   . example'],
//            [['address' => 'jdoe@machine(comment).  example', 'personal' => 'John Doe'], 'John Doe <jdoe@machine(comment).  example>'],
        ];
    }

    /**
     * @dataProvider addressDataProvider
     */
    public function testParse(array $expected, string $address): void
    {
        self::assertEquals($expected, AddressParser::parse($address));
    }
}
