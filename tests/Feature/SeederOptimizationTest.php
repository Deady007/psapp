<?php

use App\Models\Customer;
use App\Models\Document;
use App\Models\DocumentFolder;
use App\Models\Project;
use App\Models\ProjectRequirement;
use Database\Seeders\DemoCustomerProjectSeeder;
use Database\Seeders\DocumentFolderSeeder;
use Database\Seeders\DocumentSeeder;
use Database\Seeders\ProjectRequirementSeeder;

it('seeds demo customer data idempotently', function () {
    $this->seed(DemoCustomerProjectSeeder::class);
    $this->seed(DemoCustomerProjectSeeder::class);

    $customer = Customer::query()->where('name', 'Demo Customer')->firstOrFail();

    expect($customer->contacts()->count())->toBe(3);
    expect($customer->contacts()->pluck('name')->all())->toEqualCanonicalizing([
        'Project Lead',
        'QA Contact',
        'IT Admin',
    ]);
    expect($customer->projects()->count())->toBe(1);
    expect($customer->projects()->value('status'))->toBe('active');
});

it('keeps document folder structure stable when re-seeding', function () {
    $this->seed(DocumentFolderSeeder::class);
    $this->seed(DocumentFolderSeeder::class);

    $project = Project::query()->firstOrFail();

    expect(DocumentFolder::query()->where('project_id', $project->id)->where('kind', 'root')->count())->toBe(1);
    expect(DocumentFolder::query()->where('project_id', $project->id)->where('kind', 'trash')->count())->toBe(1);

    $root = DocumentFolder::query()
        ->where('project_id', $project->id)
        ->where('kind', 'root')
        ->firstOrFail();

    expect(DocumentFolder::query()->where('project_id', $project->id)->where('parent_id', $root->id)->count())
        ->toBe(2);
});

it('does not duplicate documents when re-seeding', function () {
    $this->seed(DocumentSeeder::class);
    $this->seed(DocumentSeeder::class);

    $project = Project::query()->firstOrFail();

    expect(Document::query()->where('project_id', $project->id)->count())->toBe(1);
});

it('seeds project requirements only up to the target count', function () {
    $this->seed(ProjectRequirementSeeder::class);
    $this->seed(ProjectRequirementSeeder::class);

    $project = Project::query()->firstOrFail();

    expect(ProjectRequirement::query()->where('project_id', $project->id)->count())->toBe(3);
});
