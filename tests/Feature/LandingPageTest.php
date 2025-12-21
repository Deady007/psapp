<?php

test('landing page can be rendered', function () {
    $response = $this->get('/');

    $response
        ->assertSuccessful()
        ->assertSeeText('Operational clarity for project teams.');
});
