<?php

use Ahtinurme\OctaneCheck;
use Spatie\Health\Enums\Status;

it('will fail when Octane is not installed', function () {
    $result = OctaneCheck::new()->run();

    expect($result)
        ->status->toBe(Status::failed())
        ->notificationMessage->toBe('Octane does not seem to be installed correctly.');
});
