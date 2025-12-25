<?php

uses(Tests\TestCase::class);

it('renders theme attributes on the guest layout body', function () {
    $html = view('layouts.guest', ['slot' => ''])->render();

    expect($html)
        ->toContain('data-theme="glass"')
        ->toContain('data-density="comfortable"')
        ->toContain('data-motion="1"');
});

it('renders theme attributes on the kanban layout body', function () {
    $html = view('components.kanban-layout', ['slot' => ''])->render();

    expect($html)
        ->toContain('data-theme="glass"')
        ->toContain('data-density="comfortable"')
        ->toContain('data-motion="1"');
});
