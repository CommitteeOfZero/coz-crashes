<?php namespace CoZCrashes;

class Base {
    protected $c;
    public function __construct($container) {
        $this->c = $container;
    }
}