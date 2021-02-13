<?php

namespace aieuo\eventattributes\attributes;

#[\Attribute]
class HandleCancelled {

    public function __construct(private bool $handle = true) {
    }

}