<?php declare(strict_types=1);

namespace Fazland\Notifire\Util\Email;

use Fazland\Notifire\Exception\NoGrammarDefinedException;

/**
 * ABNF grammar definitions
 * Based upon swift mailer' Swift_Mailer_Grammar class.
 */
class Grammar
{
    /**
     * Special characters used in the syntax which need to be escaped.
     *
     * @var string[]
     */
    private static $_specials = [];

    /**
     * Tokens defined in RFC 2822 (and some related RFCs).
     *
     * @var string[]
     */
    private static $_grammar = [];

    /**
     * Gets the singleton instance.
     *
     * @return self
     */
    public static function getInstance(): self
    {
        static $instance = null;
        if (null === $instance) {
            $instance = new self();
        }

        return $instance;
    }

    /**
     * Gets the grammar defined for $name token.
     *
     * @param string $name exactly as written in the RFC
     *
     * @return string
     */
    public function getDefinition(string $name): string
    {
        if (\array_key_exists($name, self::$_grammar)) {
            return self::$_grammar[$name];
        }

        throw new NoGrammarDefinedException($name);
    }

    /**
     * Returns the tokens defined in RFC 2822 (and some related RFCs).
     *
     * @return array
     */
    public function getGrammarDefinitions(): array
    {
        return self::$_grammar;
    }

    /**
     * Returns the current special characters used in the syntax which need to be escaped.
     *
     * @return array
     */
    public function getSpecials(): array
    {
        return self::$_specials;
    }

    /**
     * Escape special characters in a string (convert to quoted-pairs).
     *
     * @param string   $token
     * @param string[] $include additional chars to escape
     * @param string[] $exclude chars from escaping
     *
     * @return string
     */
    public function escapeSpecials(string $token, array $include = [], array $exclude = []): string
    {
        foreach (\array_merge(['\\'], \array_diff(self::$_specials, $exclude), $include) as $char) {
            $token = \str_replace($char, '\\'.$char, $token);
        }

        return $token;
    }

    protected function init(): void
    {
        if (\count(self::$_specials) > 0) {
            return;
        }

        self::$_specials = [
            '(', ')', '<', '>', '[', ']',
            ':', ';', '@', ',', '.', '"',
        ];

        /*** Refer to RFC 2822 for ABNF grammar ***/

        // All basic building blocks
        self::$_grammar['NO-WS-CTL'] = '[\x01-\x08\x0B\x0C\x0E-\x19\x7F]';
        self::$_grammar['WSP'] = '[ \t]';
        self::$_grammar['CRLF'] = '(?:\r\n)';
        self::$_grammar['FWS'] = '(?:(?:'.self::$_grammar['WSP'].'*'.
            self::$_grammar['CRLF'].')?'.self::$_grammar['WSP'].')';
        self::$_grammar['text'] = '[\x00-\x08\x0B\x0C\x0E-\x7F]';
        self::$_grammar['quoted-pair'] = '(?:\\\\'.self::$_grammar['text'].')';
        self::$_grammar['ctext'] = '(?:'.self::$_grammar['NO-WS-CTL'].
            '|[\x21-\x27\x2A-\x5B\x5D-\x7E])';
        // Uses recursive PCRE (?1) -- could be a weak point??
        self::$_grammar['ccontent'] = '(?:'.self::$_grammar['ctext'].'|'.
            self::$_grammar['quoted-pair'].'|(?1))';
        self::$_grammar['comment'] = '(\((?:'.self::$_grammar['FWS'].'|'.
            self::$_grammar['ccontent'].')*'.self::$_grammar['FWS'].'?\))';
        self::$_grammar['CFWS'] = '(?:(?:'.self::$_grammar['FWS'].'?'.
            self::$_grammar['comment'].')*(?:(?:'.self::$_grammar['FWS'].'?'.
            self::$_grammar['comment'].')|'.self::$_grammar['FWS'].'))';
        self::$_grammar['qtext'] = '(?:'.self::$_grammar['NO-WS-CTL'].
            '|[\x21\x23-\x5B\x5D-\x7E])';
        self::$_grammar['qcontent'] = '(?:'.self::$_grammar['qtext'].'|'.
            self::$_grammar['quoted-pair'].')';
        self::$_grammar['quoted-string'] = '(?:'.self::$_grammar['CFWS'].'?"'.
            '('.self::$_grammar['FWS'].'?'.self::$_grammar['qcontent'].')*'.
            self::$_grammar['FWS'].'?"'.self::$_grammar['CFWS'].'?)';
        self::$_grammar['atext'] = '[a-zA-Z0-9!#\$%&\'\*\+\-\/=\?\^_`\{\}\|~]';
        self::$_grammar['atom'] = '(?:'.self::$_grammar['CFWS'].'?'.
            self::$_grammar['atext'].'+'.self::$_grammar['CFWS'].'?)';
        self::$_grammar['dot-atom-text'] = '(?:'.self::$_grammar['atext'].'+'.
            '(\.'.self::$_grammar['atext'].'+)*)';
        self::$_grammar['dot-atom'] = '(?:'.self::$_grammar['CFWS'].'?'.
            self::$_grammar['dot-atom-text'].'+'.self::$_grammar['CFWS'].'?)';
        self::$_grammar['word'] = '(?:'.self::$_grammar['atom'].'|'.
            self::$_grammar['quoted-string'].')';
        self::$_grammar['phrase'] = '(?:'.self::$_grammar['word'].'+?)';
        self::$_grammar['no-fold-quote'] = '(?:"(?:'.self::$_grammar['qtext'].
            '|'.self::$_grammar['quoted-pair'].')*")';
        self::$_grammar['dtext'] = '(?:'.self::$_grammar['NO-WS-CTL'].
            '|[\x21-\x5A\x5E-\x7E])';
        self::$_grammar['no-fold-literal'] = '(?:\[(?:'.self::$_grammar['dtext'].
            '|'.self::$_grammar['quoted-pair'].')*\])';

        // Message IDs
        self::$_grammar['id-left'] = '(?:'.self::$_grammar['dot-atom-text'].'|'.
            self::$_grammar['no-fold-quote'].')';
        self::$_grammar['id-right'] = '(?:'.self::$_grammar['dot-atom-text'].'|'.
            self::$_grammar['no-fold-literal'].')';

        // Addresses, mailboxes and paths
        self::$_grammar['local-part'] = '(?:'.self::$_grammar['dot-atom'].'|'.
            self::$_grammar['quoted-string'].')';
        self::$_grammar['dcontent'] = '(?:'.self::$_grammar['dtext'].'|'.
            self::$_grammar['quoted-pair'].')';
        self::$_grammar['domain-literal'] = '(?:'.self::$_grammar['CFWS'].'?\[('.
            self::$_grammar['FWS'].'?'.self::$_grammar['dcontent'].')*?'.
            self::$_grammar['FWS'].'?\]'.self::$_grammar['CFWS'].'?)';
        self::$_grammar['domain'] = '(?:'.self::$_grammar['dot-atom'].'|'.
            self::$_grammar['domain-literal'].')';
        self::$_grammar['addr-spec'] = '(?:'.self::$_grammar['local-part'].'@'.
            self::$_grammar['domain'].')';
    }

    /**
     * Grammar constructor
     * Can be called only from getInstance method.
     */
    private function __construct()
    {
        $this->init();
    }
}
