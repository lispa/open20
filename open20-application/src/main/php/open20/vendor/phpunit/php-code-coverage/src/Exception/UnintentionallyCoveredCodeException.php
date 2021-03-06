<?php
/*
 *
 * (l) Sebastian Bergmann <sebastian@phpunit.de>
 *
 */

namespace SebastianBergmann\CodeCoverage;

/**
 * Exception that is raised when code is unintentionally covered.
 */
class UnintentionallyCoveredCodeException extends RuntimeException
{
    /**
     * @var array
     */
    private $unintentionallyCoveredUnits = [];

    /**
     * @param array $unintentionallyCoveredUnits
     */
    public function __construct(array $unintentionallyCoveredUnits)
    {
        $this->unintentionallyCoveredUnits = $unintentionallyCoveredUnits;

        parent::__construct($this->toString());
    }

    /**
     * @return array
     */
    public function getUnintentionallyCoveredUnits()
    {
        return $this->unintentionallyCoveredUnits;
    }

    /**
     * @return string
     */
    private function toString()
    {
        $message = '';

        foreach ($this->unintentionallyCoveredUnits as $unit) {
            $message .= '- ' . $unit . "\n";
        }

        return $message;
    }
}
