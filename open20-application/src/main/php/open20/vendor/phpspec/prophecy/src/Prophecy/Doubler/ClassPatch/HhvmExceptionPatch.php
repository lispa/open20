<?php

/*
 * (l) Konstantin Kudryashov <ever.zet@gmail.com>
 *     Marcello Duarte <marcello.duarte@gmail.com>
 *
 */

namespace Prophecy\Doubler\ClassPatch;

use Prophecy\Doubler\Generator\Node\ClassNode;

/**
 * Exception patch for HHVM to remove the stubs from special methods
 *
 */
class HhvmExceptionPatch implements ClassPatchInterface
{
    /**
     * Supports exceptions on HHVM.
     *
     * @param ClassNode $node
     *
     * @return bool
     */
    public function supports(ClassNode $node)
    {
        if (!defined('HHVM_VERSION')) {
            return false;
        }

        return 'Exception' === $node->getParentClass() || is_subclass_of($node->getParentClass(), 'Exception');
    }

    /**
     * Removes special exception static methods from the doubled methods.
     *
     * @param ClassNode $node
     *
     * @return void
     */
    public function apply(ClassNode $node)
    {
        if ($node->hasMethod('setTraceOptions')) {
            $node->getMethod('setTraceOptions')->useParentCode();
        }
        if ($node->hasMethod('getTraceOptions')) {
            $node->getMethod('getTraceOptions')->useParentCode();
        }
    }

    /**
     * {@inheritdoc}
     */
    public function getPriority()
    {
        return -50;
    }
}
