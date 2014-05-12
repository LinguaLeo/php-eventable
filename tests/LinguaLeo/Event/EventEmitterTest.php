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

class EventEmitterTest extends \PHPUnit_Framework_TestCase
{
    public function testEmptyEmit()
    {
        $emitter = new EventEmitter();
        $this->assertTrue($emitter->emit('something'));
    }

    public function testOneListener()
    {
        $data = false;

        $emitter = new EventEmitter();
        $emitter->on('something', function () use (&$data) {
            $data = true;
        });

        $this->assertTrue($emitter->emit('something'));
        $this->assertTrue($data);
    }

    public function testTwoListeners()
    {
        $data = [];

        $emitter = new EventEmitter();
        $emitter->on('something', function () use (&$data) {
            $data[] = 1;
        });
        $emitter->on('something', function () use (&$data) {
            $data[] = 2;
        });

        $this->assertTrue($emitter->emit('something'));
        $this->assertSame([1, 2], $data);
    }

    public function testListenerWithArguments()
    {
        $sum = 0;

        $emitter = new EventEmitter();
        $emitter->on('add', function ($a, $b) use (&$sum) {
            $sum = $a + $b;
        });

        $this->assertTrue($emitter->emit('add', [100, 200]));
        $this->assertSame(300, $sum);
    }

    public function testCancelListeners()
    {
        $sum = 0;

        $emitter = new EventEmitter();
        $emitter->on('add', function ($a, $b) use (&$sum) {
            $sum = $a + $b;
            return false;
        });
        $emitter->on('add', function () {
            throw new \RuntimeException('Not yet implemented');
        });

        $this->assertFalse($emitter->emit('add', [100, 200]));
        $this->assertSame(300, $sum);
    }

    public function testPriorityListeners()
    {
        $sum = 0;

        $emitter = new EventEmitter();
        $emitter->on('add', function () use (&$sum) {
            $sum <<= 1;
        }, -999); // low priority
        $emitter->on('add', function ($a, $b) use (&$sum) {
            $sum = $a + $b;
        });

        $this->assertTrue($emitter->emit('add', [100, 200]));
        $this->assertSame(600, $sum);
    }
}
