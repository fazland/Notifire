<?php declare(strict_types=1);

namespace Fazland\Notifire\Util\Email;

class AddressParser
{
    /**
     * Parse a RFC 2822 compliant address and return
     * its address and personal parts.
     * Returns null if address is invalid.
     *
     * @param string $address
     *
     * @return array|null
     */
    public static function parse(string $address)
    {
        static $regex = null;
        if (null === $regex) {
            $grammar = Grammar::getInstance();

            $addrSpec = $grammar->getDefinition('addr-spec');
            $cfws = $grammar->getDefinition('CFWS');
            $phrase = $grammar->getDefinition('phrase');

            $regex = '/(?<address>'.$addrSpec.')|(?:(?<name>'.$phrase.')(?:'.$cfws.'?)<(?<addressalt>'.$addrSpec.')>(?:'.$cfws.'?))/';
        }

        if (! preg_match($regex, $address, $match)) {
            return null;
        }

        $addr = isset($match['addressalt']) ? $match['addressalt'] : $match['address'];

        $name = $match['name'] ?? '';
        $name = trim($name, " \t\n\r\0\x0B\"");

        return [
            'address' => $addr,
            'personal' => $name,
        ];
    }
}
