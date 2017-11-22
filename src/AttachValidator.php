<?php

/**
 * Created by Larakit.
 * Link: http://github.com/larakit
 * User: Alexey Berdnikov
 * Date: 13.06.17
 * Time: 17:30
 */
namespace Larakit\Attach;

use Larakit\ValidateBuilder;

class AttachValidator extends ValidateBuilder {
    
    function build() {
        $this->to('name')
            ->ruleRequired('Вы не указали название вложения')
        ;
    }
    
}