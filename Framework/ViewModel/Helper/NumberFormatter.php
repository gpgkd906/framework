<?php
declare(strict_types=1);

namespace Framework\ViewModel\Helper;

use Framework\ObjectManager\SingletonInterface;
use Closure;
use NumberFormatter as NativeNumberFormatter;

class NumberFormatter implements SingletonInterface
{
    use \Framework\ObjectManager\SingletonTrait;

    /**
     *
     * @api
     * @var mixed $locale
     * @access private
     * @link
     */
    private $locale = null;

    /**
     *
     * @api
     * @var mixed $NumberFormatters
     * @access private
     * @link
     */
    private $NumberFormatters = [
        NativeNumberFormatter::SPELLOUT => [],
        NativeNumberFormatter::CURRENCY => [],
    ];

    /**
     *
     * @api
     * @param mixed $locale
     * @return mixed $locale
     * @link
     */
    public function setLocale ($locale)
    {
        return $this->locale = $locale;
    }

    /**
     *
     * @api
     * @return mixed $locale
     * @link
     */
    public function getLocale ()
    {
        if ($this->locale === null) {
            $this->locale = setlocale(LC_ALL, 0);
        }
        return $this->locale;
    }

    public function ToWord($number, $locale = null)
    {
        if (empty($locale)) {
            $locale = $this->getLocale();
        }
        if (!isset($this->NumberFormatters[NativeNumberFormatter::SPELLOUT][$locale])) {
            $this->NumberFormatters[NativeNumberFormatter::SPELLOUT][$locale] = new NumberFormatter($locale, NativeNumberFormatter::SPELLOUT);
        }
        return $this->NumberFormatters[NativeNumberFormatter::SPELLOUT][$locale]->format($number);
    }

    public function toCurrency($number, $locale = null)
    {
        if (empty($locale)) {
            $locale = $this->getLocale();
        }
        if (!isset($this->NumberFormatters[NativeNumberFormatter::CURRENCY][$locale])) {
            $this->NumberFormatters[NativeNumberFormatter::CURRENCY][$locale] = new NumberFormatter($locale, NativeNumberFormatter::CURRENCY);
        }
        return $this->NumberFormatters[NativeNumberFormatter::CURRENCY][$locale]->format($number);
    }
}
