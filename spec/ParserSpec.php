<?php

namespace spec\Fungku\Postmark;

use Parsedown;
use PhpSpec\ObjectBehavior;
use Prophecy\Argument;

class ParserSpec extends ObjectBehavior
{
    function let(Parsedown $parsedown)
    {
        $this->beConstructedWith($parsedown);
    }

    function it_is_initializable()
    {
        $this->shouldHaveType('Fungku\Postmark\Parser');
    }
}
