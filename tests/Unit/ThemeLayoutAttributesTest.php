<?php

uses(Tests\TestCase::class);

it('renders theme attributes on the guest layout body', function () {
    $html = view('layouts.guest', ['slot' => ''])->render();

    expect($html)
        ->toContain('data-theme="softui"')
        ->toContain('data-density="comfortable"')
        ->toContain('data-motion="1"')
        ->toContain('data-theme-switcher')
        ->toContain('value="softui"');
});

it('renders theme attributes on the kanban layout body', function () {
    $html = view('components.kanban-layout', ['slot' => ''])->render();

    expect($html)
        ->toContain('data-theme="softui"')
        ->toContain('data-density="comfortable"')
        ->toContain('data-motion="1"')
        ->toContain('data-theme-switcher')
        ->toContain('value="softui"');
});

it('defines ember and gold theme colors for gradients', function () {
    $css = file_get_contents(base_path('resources/css/app.css'));

    expect($css)
        ->toContain('--color-ember-dark')
        ->toContain('--color-ember')
        ->toContain('--color-ember-bright')
        ->toContain('--color-gold');
});
