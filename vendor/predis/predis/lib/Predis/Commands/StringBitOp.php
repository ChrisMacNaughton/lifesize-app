<?php

/*
 * This file is part of the Predis package.
 *
 * (c) Daniele Alessandri <suppakilla@gmail.com>
 *
 * For the full copyright and license information, please view the LICENSE
 * file that was distributed with this source code.
 */

namespace Predis\Commands;

/**
 * @link http://redis.io/commands/bitop
 * @author Daniele Alessandri <suppakilla@gmail.com>
 */
class StringBitOp extends Command implements IPrefixable
{
    /**
     * {@inheritdoc}
     */
    public function getId()
    {
        return 'BITOP';
    }

    /**
     * {@inheritdoc}
     */
    protected function filterArguments(Array $arguments)
    {
        if (count($arguments) === 3 && is_array($arguments[2])) {
            list($operation, $destination, ) = $arguments;
            $arguments = $arguments[2];
            array_unshift($arguments, $operation, $destination);
        }

        return $arguments;
    }

    /**
     * {@inheritdoc}
     */
    public function prefixKeys($prefix)
    {
        PrefixHelpers::skipFirst($this, $prefix);
    }

    /**
     * {@inheritdoc}
     */
    protected function canBeHashed()
    {
        return $this->checkSameHashForKeys(
            array_slice(($args = $this->getArguments()), 1, count($args))
        );
    }
}
