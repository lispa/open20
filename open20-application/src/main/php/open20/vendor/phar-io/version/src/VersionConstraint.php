<?php
/*
 *
 * (l) Arne Blankerts <arne@blankerts.de>, Sebastian Heuer <sebastian@phpeople.de>, Sebastian Bergmann <sebastian@phpunit.de>
 *
 */

namespace PharIo\Version;

interface VersionConstraint {
    /**
     * @param Version $version
     *
     * @return bool
     */
    public function complies(Version $version);

    /**
     * @return string
     */
    public function asString();

}
