<?php

namespace Lemmon\Router;

interface RouterInterface
{
    CONST METHOD_GET    =    0b1;
    CONST METHOD_POST   =   0b10;
    CONST METHOD_PUT    =  0b100;
    CONST METHOD_DELETE = 0b1000;


    public function match(...$args);
}