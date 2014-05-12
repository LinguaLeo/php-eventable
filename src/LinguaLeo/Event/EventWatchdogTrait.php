<?php

/**
 * The MIT License (MIT)
 *
 * Copyright (c) 2014 LinguaLeo
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in all
 * copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN THE
 * SOFTWARE.
 */

namespace LinguaLeo\Event;

trait EventWatchdogTrait
{
    use EventEmitterTrait;

    /**
     * Set an event in the queue
     *
     * @param string $eventName
     * @param array $values
     */
    public function watch($eventName, array $values = [])
    {
        $this->listeners[$eventName]['watchdog'][] = $values;
    }

    /**
     * Promises a handler for emitted values
     *
     * @param string $eventName
     * @param callable $callback
     */
    public function promise($eventName, callable $callback, $priority = 999)
    {
        $this->on($eventName, function () use ($eventName, $callback) {
            if (empty($this->listeners[$eventName]['watchdog'])) {
                return;
            }
            $arguments = func_get_args();
            while ($values = array_shift($this->listeners[$eventName]['watchdog'])) {
                call_user_func_array($callback, array_merge($values, $arguments));
            }
        }, $priority);
    }
}
