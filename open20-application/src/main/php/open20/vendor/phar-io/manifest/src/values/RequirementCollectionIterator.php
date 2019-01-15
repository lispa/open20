<?php
/*
 *
 * (l) Arne Blankerts <arne@blankerts.de>, Sebastian Heuer <sebastian@phpeople.de>, Sebastian Bergmann <sebastian@phpunit.de>
 *
 */

namespace PharIo\Manifest;

class RequirementCollectionIterator implements \Iterator {
    /**
     * @var Requirement[]
     */
    private $requirements = [];

    /**
     * @var int
     */
    private $position;

    public function __construct(RequirementCollection $requirements) {
        $this->requirements = $requirements->getRequirements();
    }

    public function rewind() {
        $this->position = 0;
    }

    /**
     * @return bool
     */
    public function valid() {
        return $this->position < count($this->requirements);
    }

    /**
     * @return int
     */
    public function key() {
        return $this->position;
    }

    /**
     * @return Requirement
     */
    public function current() {
        return $this->requirements[$this->position];
    }

    public function next() {
        $this->position++;
    }
}
