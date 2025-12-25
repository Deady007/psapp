<?php

namespace App\Services;

use App\Models\Project;
use Illuminate\Support\Str;

class IssueKeyGenerator
{
    /**
     * @return array{issue_number: int, issue_key: string}
     */
    public function next(Project $project): array
    {
        return Project::query()->getConnection()->transaction(function () use ($project) {
            $lockedProject = Project::query()->lockForUpdate()->findOrFail($project->id);

            $prefix = $lockedProject->issue_prefix;

            if ($prefix === null || $prefix === '') {
                $prefixSource = $lockedProject->code ?: $lockedProject->name;
                $prefix = Str::upper(Str::slug($prefixSource, ''));

                if ($prefix === '') {
                    $prefix = 'PRJ';
                }

                $lockedProject->issue_prefix = $prefix;
            }

            $sequence = (int) $lockedProject->issue_sequence + 1;
            $lockedProject->issue_sequence = $sequence;
            $lockedProject->save();

            return [
                'issue_number' => $sequence,
                'issue_key' => sprintf('%s-%s', $prefix, str_pad((string) $sequence, 4, '0', STR_PAD_LEFT)),
            ];
        });
    }
}
