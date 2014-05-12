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

class EventWatchdogTraitTest extends \PHPUnit_Framework_TestCase
{
    use EventWatchdogTrait;

    public function testEmptyPromise()
    {
        $running = false;
        $this->promise('something', function () use (&$running) {
            $running = true;
        });
        $this->assertTrue($this->emit('something'));
        $this->assertFalse($running);
    }

    public function testWatchAfterPromise()
    {
        $sum = 0;
        $this->promise('add', function ($a, $b) use (&$sum) {
            $sum += $a + $b;
        });
        $this->watch('add', [100, 200]);
        $this->assertTrue($this->emit('add'));
        $this->assertSame(300, $sum);
    }

    public function testWatchBeforePromise()
    {
        $sum = 0;
        $this->watch('add', [100, 200]);
        $this->promise('add', function ($a, $b) use (&$sum) {
            $sum += $a + $b;
        });
        $this->assertTrue($this->emit('add'));
        $this->assertSame(300, $sum);
    }

    public function testManyWatchAfterPromise()
    {
        $sum = 0;
        $this->promise('add', function ($a, $b) use (&$sum) {
            $sum += $a + $b;
        });
        $this->watch('add', [100, 200]);
        $this->watch('add', [300, 400]);
        $this->assertTrue($this->emit('add'));
        $this->assertSame(1000, $sum);
    }

    public function testManyEmitForPromiseCall()
    {
        $sum = 0;
        $this->watch('add', [100, 200]);
        $this->promise('add', function ($a, $b) use (&$sum) {
            $sum += $a + $b;
        });
        $this->assertTrue($this->emit('add'));
        $this->assertTrue($this->emit('add')); // must do no change
        $this->assertSame(300, $sum);
    }

    public function testPutArgumentsForPromise()
    {
        $sum = 0;
        $this->watch('add', [100, 200]);
        $this->promise('add', function ($a, $b, $n) use (&$sum) {
            $sum += ($a + $b) * $n;
        });
        $this->assertTrue($this->emit('add', [2]));
        $this->assertSame(600, $sum);
    }
}
