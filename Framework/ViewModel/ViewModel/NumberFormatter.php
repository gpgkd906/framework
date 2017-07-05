<?php

namespace Framework\ViewModel\ViewModel;

use Framework\ObjectManager\SingletonInterface;
use Closure;
use NumberFormatter;

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
        NumberFormatter::SPELLOUT => [],
        NumberFormatter::CURRENCY => [],
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
        if (!isset($this->NumberFormatters[NumberFormatter::SPELLOUT][$locale])) {
            $this->NumberFormatters[NumberFormatter::SPELLOUT][$locale] = new NumberFormatter($locale, NumberFormatter::SPELLOUT);
        }
        return $this->NumberFormatters[NumberFormatter::SPELLOUT][$locale]->format($number);
    }

    public function toCurrency($number, $locale = null)
    {
        if (empty($locale)) {
            $locale = $this->getLocale();
        }
        if (!isset($this->NumberFormatters[NumberFormatter::CURRENCY][$locale])) {
            $this->NumberFormatters[NumberFormatter::CURRENCY][$locale] = new NumberFormatter($locale, NumberFormatter::CURRENCY);
        }
        return $this->NumberFormatters[NumberFormatter::CURRENCY][$locale]->format($number);        
    }
}