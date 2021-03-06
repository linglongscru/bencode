<?php

namespace SandFoxMe\Bencode\Util;

use SandFoxMe\Bencode\Exceptions\RuntimeException;

abstract class Util
{
    const MBSTRING_OVERLOAD_CONFLICT = 2; //  strlen

    public static function detectMbstringOverload()
    {
        // this method can be removed when minimal php version is bumped to the version with mbstring.func_overload removed

        $funcOverload = intval(ini_get('mbstring.func_overload')); // false and empty string will be 0 and the test will pass

        if ($funcOverload & self::MBSTRING_OVERLOAD_CONFLICT) {
            // @codeCoverageIgnoreStart
            // This exception is thrown on a misconfiguration that is not possible to be set dynamically
            // and therefore is excluded from testing
            throw new RuntimeException(
                sprintf('mbstring.func_overload is set to %d, func_overload level 2 has known conflicts with Bencode library', $funcOverload)
            );
            // @codeCoverageIgnoreEnd
        }
    }
}
