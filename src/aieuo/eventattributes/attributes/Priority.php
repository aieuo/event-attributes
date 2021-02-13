<?php

namespace aieuo\eventattributes\attributes;

#[\Attribute]
class Priority {

    public function __construct(private int $priority) {
    }

}