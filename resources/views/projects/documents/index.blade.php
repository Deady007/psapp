<x-app-layout>
    <x-slot name="header">
        <div class="row mb-2">
            <div class="col-sm-7">
                <h1 class="m-0">{{ __('Documents') }}</h1>
                <div class="text-muted">{{ $project->name }}</div>
            </div>
            <div class="col-sm-5 text-sm-right mt-3 mt-sm-0">
                <a href="{{ route('projects.documents.create', $project) }}" class="btn btn-primary mr-2">
                    <i class="fas fa-upload mr-1"></i>
                    {{ __('Upload Document') }}
                </a>
                <a href="{{ route('projects.show', $project) }}" class="btn btn-outline-secondary">
                    {{ __('Back to Project') }}
                </a>
            </div>
        </div>
    </x-slot>

    @include('projects.partials.modules-nav', ['project' => $project])

    <div class="card">
        <div class="card-header">
            <h3 class="card-title">{{ __('Document list') }}</h3>
            <div class="card-tools text-muted">
                {{ __('Showing') }} {{ $documents->firstItem() ?? 0 }}-{{ $documents->lastItem() ?? 0 }} {{ __('of') }} {{ $documents->total() }}
            </div>
        </div>

        @if ($documents->count() === 0)
            <div class="card-body">
                <p class="text-muted mb-0">{{ __('No documents found.') }}</p>
            </div>
        @else
            <div class="card-body table-responsive p-0">
                <table class="table table-hover text-nowrap">
                    <thead>
                        <tr>
                            <th>{{ __('Category') }}</th>
                            <th>{{ __('File') }}</th>
                            <th>{{ __('Notes') }}</th>
                            <th>{{ __('Uploaded By') }}</th>
                            <th>{{ __('Collected At') }}</th>
                            <th class="text-right">{{ __('Actions') }}</th>
                        </tr>
                    </thead>
                    <tbody>
                        @foreach ($documents as $document)
                            <tr>
                                <td>{{ $document->category }}</td>
                                <td>
                                    <a href="{{ route('projects.documents.download', [$project, $document]) }}" class="font-weight-bold">
                                        {{ $document->original_name }}
                                    </a>
                                    @if ($document->size)
                                        <div class="text-muted small">{{ number_format($document->size / 1024, 1) }} KB</div>
                                    @endif
                                </td>
                                <td>{{ $document->notes ?: '-' }}</td>
                                <td>{{ $document->uploadedBy?->name ?? '-' }}</td>
                                <td>{{ $document->collected_at?->toDateString() ?: '-' }}</td>
                                <td class="text-right">
                                    <a href="{{ route('projects.documents.edit', [$project, $document]) }}" class="btn btn-sm btn-outline-secondary">
                                        {{ __('Edit') }}
                                    </a>
                                    <form method="POST" action="{{ route('projects.documents.destroy', [$project, $document]) }}" class="d-inline" onsubmit="return confirm('{{ __('Delete this document?') }}')">
                                        @csrf
                                        @method('DELETE')
                                        <button type="submit" class="btn btn-sm btn-outline-danger">
                                            {{ __('Delete') }}
                                        </button>
                                    </form>
                                </td>
                            </tr>
                        @endforeach
                    </tbody>
                </table>
            </div>

            <div class="card-footer">
                {{ $documents->links() }}
            </div>
        @endif
    </div>
</x-app-layout>
