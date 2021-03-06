<?php

namespace EnumBundle\Enum;

use EnumBundle\Exception\InvalidTranslatePatternException;
use Symfony\Component\Translation\TranslatorInterface;

/**
 * @author Yann Eugoné <yann.eugone@gmail.com>
 */
abstract class AbstractTranslatedEnum implements EnumInterface
{
    /**
     * @var TranslatorInterface
     */
    private $translator;

    /**
     * @var string
     */
    private $transPattern;

    /**
     * @var string
     */
    private $transDomain = 'messages';

    /**
     * @param TranslatorInterface $translator
     * @param string              $transPattern
     */
    public function __construct(TranslatorInterface $translator, $transPattern)
    {
        if (false === strpos($transPattern, '%s')) {
            throw new InvalidTranslatePatternException($transPattern);
        }

        $this->translator = $translator;
        $this->transPattern = $transPattern;
    }

    /**
     * @return array
     */
    public function getChoices()
    {
        return array_combine(
            $this->getValues(),
            array_map(
                function ($value) {
                    return $this->translator->trans(
                        sprintf($this->transPattern, $value),
                        [],
                        $this->transDomain
                    );
                },
                $this->getValues()
            )
        );
    }

    /**
     * @param string $transDomain
     */
    public function setTransDomain($transDomain)
    {
        $this->transDomain = $transDomain;
    }

    /**
     * @return array
     */
    abstract protected function getValues();
}
