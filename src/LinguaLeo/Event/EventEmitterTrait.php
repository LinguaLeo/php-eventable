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

trait EventEmitterTrait
{
    /**
     * The list of listeners
     *
     * @var array
     */
    protected $listeners = [];

    /**
     * Subscribes to an event.
     *
     * @param string $eventName
     * @param callable $callback
     * @param int $priority
     * @return void
     */
    public function on($eventName, callable $callback, $priority = 100)
    {

        $this->listeners[$eventName]['sorted'] = false;
        $this->listeners[$eventName]['priority'][] = $priority;
        $this->listeners[$eventName]['callback'][] = $callback;
    }

    /**
     * Dispatches an event.
     *
     * @param string $eventName
     * @param array $arguments
     * @return bool
     */
    public function emit($eventName, array $arguments = [])
    {
        foreach ($this->getListeners($eventName) as $listener) {
            if (call_user_func_array($listener, $arguments) === false) {
                return false;
            }
        }
        return true;
    }

    /**
     * Returns listeners for an event
     *
     * @param string $eventName
     * @return array
     */
    protected function getListeners($eventName)
    {
        if (empty($this->listeners[$eventName])) {
            return [];
        }

        if (empty($this->listeners[$eventName]['sorted'])) {
            array_multisort($this->listeners[$eventName]['priority'], SORT_NUMERIC, SORT_DESC, $this->listeners[$eventName]['callback']);
            $this->listeners[$eventName]['sorted'] = true;
        }

        return $this->listeners[$eventName]['callback'];
    }
}
