<?php declare(strict_types=1);
/*
 *
 * (l) Sebastian Bergmann <sebastian@phpunit.de>
 *
 */

namespace PHPUnit\Runner;

use SebastianBergmann\Version as VersionId;

/**
 * This class defines the current version of PHPUnit.
 */
class Version
{
    private static $pharVersion;
    private static $version;

    /**
     * Returns the current version of PHPUnit.
     *
     * @return string
     */
    public static function id()
    {
        if (self::$pharVersion !== null) {
            return self::$pharVersion;
        }

        if (self::$version === null) {
            $version       = new VersionId('6.5.7', \dirname(\dirname(__DIR__)));
            self::$version = $version->getVersion();
        }

        return self::$version;
    }

    /**
     * @return string
     */
    public static function series()
    {
        if (\strpos(self::id(), '-')) {
            $version = \explode('-', self::id())[0];
        } else {
            $version = self::id();
        }

        return \implode('.', \array_slice(\explode('.', $version), 0, 2));
    }

    /**
     * @return string
     */
    public static function getVersionString()
    {
        return 'PHPUnit ' . self::id() . ' by Sebastian Bergmann and contributors.';
    }

    /**
     * @return string
     */
    public static function getReleaseChannel()
    {
        if (\strpos(self::$pharVersion, '-') !== false) {
            return '-nightly';
        }

        return '';
    }
}
