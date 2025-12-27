<?php

test('landing page can be rendered', function () {
    $response = $this->get('/');

    $response
        ->assertSuccessful()
        ->assertSeeText('Plan, track, and deliver with confidence.');
});
