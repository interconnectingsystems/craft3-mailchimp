<?php


namespace white\craft\mailchimp\client\middleware;


interface MiddlewareInterface
{
    public function __invoke(callable $handler) : callable;
}
