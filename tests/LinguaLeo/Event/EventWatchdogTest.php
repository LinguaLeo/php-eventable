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

class EventWatchdogTest extends \PHPUnit_Framework_TestCase
{
    public function testEmptyPromise()
    {
        $running = false;
        $watchdog = new EventWatchdog();
        $watchdog->deliver('something', function () use (&$running) {
            $running = true;
        });
        $this->assertTrue($watchdog->emit('something'));
        $this->assertFalse($running);
    }

    public function testWatchAfterPromise()
    {
        $sum = 0;
        $watchdog = new EventWatchdog();
        $watchdog->deliver('add', function ($a, $b) use (&$sum) {
            $sum += $a + $b;
        });
        $watchdog->postpone('add', [100, 200]);
        $this->assertTrue($watchdog->emit('add'));
        $this->assertSame(300, $sum);
    }

    public function testWatchBeforePromise()
    {
        $sum = 0;
        $watchdog = new EventWatchdog();
        $watchdog->postpone('add', [100, 200]);
        $watchdog->deliver('add', function ($a, $b) use (&$sum) {
            $sum += $a + $b;
        });
        $this->assertTrue($watchdog->emit('add'));
        $this->assertSame(300, $sum);
    }

    public function testManyWatchAfterPromise()
    {
        $sum = 0;
        $watchdog = new EventWatchdog();
        $watchdog->deliver('add', function ($a, $b) use (&$sum) {
            $sum += $a + $b;
        });
        $watchdog->postpone('add', [100, 200]);
        $watchdog->postpone('add', [300, 400]);
        $this->assertTrue($watchdog->emit('add'));
        $this->assertSame(1000, $sum);
    }

    public function testManyEmitForPromiseCall()
    {
        $sum = 0;
        $watchdog = new EventWatchdog();
        $watchdog->postpone('add', [100, 200]);
        $watchdog->deliver('add', function ($a, $b) use (&$sum) {
            $sum += $a + $b;
        });
        $this->assertTrue($watchdog->emit('add'));
        $this->assertTrue($watchdog->emit('add')); // must do no change
        $this->assertSame(300, $sum);
    }

    public function testPutArgumentsForPromise()
    {
        $sum = 0;
        $watchdog = new EventWatchdog();
        $watchdog->postpone('add', [100, 200]);
        $watchdog->deliver('add', function ($a, $b, $n) use (&$sum) {
            $sum += ($a + $b) * $n;
        });
        $this->assertTrue($watchdog->emit('add', [2]));
        $this->assertSame(600, $sum);
    }

    public function testPromiseAndListenersDefaultPriority()
    {
        $data = [];
        $watchdog = new EventWatchdog();
        $watchdog->deliver('add', function () use (&$data) {
            $data[] = 2;
        });
        $watchdog->on('add', function () use (&$data) {
            $data[] = 1;
        });
        $watchdog->postpone('add', []);
        $this->assertTrue($watchdog->emit('add'));
        $this->assertSame([1, 2], $data);
    }
}
