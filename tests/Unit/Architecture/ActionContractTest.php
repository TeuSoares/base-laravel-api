<?php

namespace Tests\Unit\Architecture;

use App\Core\Abstracts\Action;

test('all actions must implement an execute method')
    ->expect('App\Modules\*\Actions')
    ->toBeClasses()
    ->toExtend(Action::class)
    ->not->toBeAbstract()
    ->toHaveMethod('execute');
