<x-app-layout>
    <x-slot name="header">
        <div class="row mb-2">
            <div class="col-sm-7">
                <h1 class="m-0">{{ __('Drive Documents') }}</h1>
                <div class="text-muted">{{ $project->name }}</div>
                <nav aria-label="{{ __('Breadcrumb') }}">
                    <ol class="breadcrumb bg-transparent p-0 mb-0">
                        <li class="breadcrumb-item">
                            <a href="{{ route('projects.drive-documents.index', $project) }}">{{ __('Drive Documents') }}</a>
                        </li>
                        @foreach ($breadcrumbs as $crumb)
                            <li class="breadcrumb-item @if ($loop->last) active @endif">
                                @if ($loop->last)
                                    {{ $crumb->name }}
                                @else
                                    <a href="{{ route('projects.drive-documents.folders.show', [$project, $crumb]) }}">{{ $crumb->name }}</a>
                                @endif
                            </li>
                        @endforeach
                    </ol>
                </nav>
            </div>
            <div class="col-sm-5 mt-3 mt-sm-0">
                <div class="d-flex flex-wrap justify-content-sm-end align-items-center">
                    @can('create', \App\Models\DocumentFolder::class)
                        <button
                            type="button"
                            class="btn btn-outline-primary mr-2 mb-2 mb-sm-0"
                            data-toggle="modal"
                            data-target="#drive-folder-modal"
                        >
                            <i class="far fa-folder mr-1"></i>
                            {{ __('New Folder') }}
                        </button>
                    @endcan
                    @can('create', \App\Models\Document::class)
                        <button
                            type="button"
                            class="btn btn-primary mr-2 mb-2 mb-sm-0"
                            data-toggle="modal"
                            data-target="#drive-upload-modal"
                        >
                            <i class="fas fa-upload mr-1"></i>
                            {{ __('Upload File') }}
                        </button>
                    @endcan
                    <a href="{{ route('projects.show', $project) }}" class="btn btn-outline-secondary mb-2 mb-sm-0">
                        {{ __('Back to Project') }}
                    </a>
                </div>
            </div>
        </div>
    </x-slot>

    @include('projects.partials.modules-nav', ['project' => $project])

    @if (! $driveReady)
        <div class="alert alert-warning">
            {{ __('Project Drive folders are being prepared. Please refresh in a moment to start uploading.') }}
        </div>
    @endif

    @php
        $isRoot = $rootFolder && $folder && $folder->is($rootFolder);
        $currentRoute = $isRoot || ! $folder
            ? route('projects.drive-documents.index', $project)
            : route('projects.drive-documents.folders.show', [$project, $folder]);
        $selectedFolder = old('folder_id', $isRoot ? null : $folder?->id);
        $parentId = old('parent_id', $folder?->id ?? $rootFolder?->id);
        $currentFolderId = $folder?->id ?? $rootFolder?->id;
        $childFolders = $currentFolderId
            ? $foldersByParent->get($currentFolderId, collect())->sortBy('name')
            : collect();
        $hasFilters = collect($filters)
            ->filter(fn (string $value) => $value !== '')
            ->isNotEmpty();
    @endphp

    <div class="row">
        <div class="col-12">
            <div class="card mb-3 collapse @if ($hasFilters) show @endif" id="drive-filters">
                <div class="card-body py-2">
                    <form method="GET" action="{{ $currentRoute }}" class="d-flex flex-wrap align-items-end">
                        <div class="form-group mr-3 mb-2">
                            <x-input-label for="filter_source" :value="__('Source')" />
                            <x-text-input id="filter_source" name="source" type="text" :value="$filters['source']" class="form-control-sm" />
                        </div>
                        <div class="form-group mr-3 mb-2">
                            <x-input-label for="filter_received_from" :value="__('Received From')" />
                            <x-text-input id="filter_received_from" name="received_from" type="text" :value="$filters['received_from']" class="form-control-sm" />
                        </div>
                        <div class="form-group mr-3 mb-2">
                            <x-input-label for="filter_mime_type" :value="__('Type')" />
                            <x-text-input id="filter_mime_type" name="mime_type" type="text" :value="$filters['mime_type']" class="form-control-sm" />
                        </div>
                        <div class="form-group mr-3 mb-2">
                            <x-input-label for="filter_received_at_from" :value="__('Received From Date')" />
                            <x-text-input id="filter_received_at_from" name="received_at_from" type="text" data-datepicker="1" :value="$filters['received_at_from']" class="form-control-sm" />
                        </div>
                        <div class="form-group mr-3 mb-2">
                            <x-input-label for="filter_received_at_to" :value="__('Received To Date')" />
                            <x-text-input id="filter_received_at_to" name="received_at_to" type="text" data-datepicker="1" :value="$filters['received_at_to']" class="form-control-sm" />
                        </div>
                        <div class="form-group mr-3 mb-2">
                            <button type="submit" class="btn btn-primary btn-sm mr-2">{{ __('Apply Filters') }}</button>
                            <a href="{{ $currentRoute }}" class="btn btn-outline-secondary btn-sm">{{ __('Reset') }}</a>
                        </div>
                    </form>
                </div>
            </div>

            <div class="card">
                <div class="card-header">
                    <h3 class="card-title">{{ __('File Manager') }}</h3>
                    <div class="card-tools d-flex align-items-center">
                        <button
                            class="btn btn-outline-secondary btn-sm mr-2"
                            type="button"
                            data-toggle="collapse"
                            data-target="#drive-filters"
                            aria-expanded="{{ $hasFilters ? 'true' : 'false' }}"
                            aria-controls="drive-filters"
                        >
                            <i class="fas fa-filter mr-1"></i>
                            {{ __('Filters') }}
                        </button>
                        <span class="text-muted">
                            {{ __('Showing') }} {{ $documents->firstItem() ?? 0 }}-{{ $documents->lastItem() ?? 0 }} {{ __('of') }} {{ $documents->total() }}
                        </span>
                    </div>
                </div>

                @php
                    $hasItems = $childFolders->isNotEmpty() || $documents->count() > 0;
                @endphp

                @if (! $hasItems)
                    <div class="card-body">
                        <p class="text-muted mb-0">{{ __('No folders or files found.') }}</p>
                    </div>
                @else
                    <div class="list-group list-group-flush">
                        @foreach ($childFolders as $child)
                            <div class="list-group-item">
                                <div class="d-flex align-items-center">
                                    <i class="far fa-folder text-warning mr-3"></i>
                                    <a href="{{ route('projects.drive-documents.folders.show', [$project, $child]) }}" class="text-reset flex-grow-1 font-weight-bold text-truncate">
                                        {{ $child->name }}
                                    </a>
                                    <div class="dropdown">
                                        <button
                                            class="btn btn-sm btn-link text-muted"
                                            type="button"
                                            id="folder-menu-{{ $child->id }}"
                                            data-toggle="dropdown"
                                            aria-haspopup="true"
                                            aria-expanded="false"
                                        >
                                            <i class="fas fa-ellipsis-v"></i>
                                        </button>
                                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="folder-menu-{{ $child->id }}">
                                            <a class="dropdown-item" href="{{ route('projects.drive-documents.folders.show', [$project, $child]) }}">
                                                {{ __('Open') }}
                                            </a>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        @endforeach

                        @foreach ($documents as $document)
                            <div class="list-group-item">
                                <div class="d-flex align-items-center">
                                    <i class="far fa-file-alt text-info mr-3"></i>
                                    <div class="flex-grow-1 font-weight-bold text-truncate">
                                        {{ $document->name }}
                                    </div>
                                    <div class="dropdown">
                                        <button
                                            class="btn btn-sm btn-link text-muted"
                                            type="button"
                                            id="document-menu-{{ $document->id }}"
                                            data-toggle="dropdown"
                                            aria-haspopup="true"
                                            aria-expanded="false"
                                        >
                                            <i class="fas fa-ellipsis-v"></i>
                                        </button>
                                        <div class="dropdown-menu dropdown-menu-right" aria-labelledby="document-menu-{{ $document->id }}">
                                            <button class="dropdown-item" type="button" data-toggle="collapse" data-target="#rename-{{ $document->id }}">
                                                {{ __('Rename') }}
                                            </button>
                                            <button class="dropdown-item" type="button" data-toggle="collapse" data-target="#move-{{ $document->id }}">
                                                {{ __('Move') }}
                                            </button>
                                            <button class="dropdown-item" type="button" data-toggle="collapse" data-target="#copy-{{ $document->id }}">
                                                {{ __('Copy') }}
                                            </button>
                                            <div class="dropdown-divider"></div>
                                            <form method="POST" action="{{ route('projects.drive-documents.destroy', [$project, $document]) }}" data-confirm="{{ __('Move this document to trash?') }}" data-confirm-button="{{ __('Yes, move to trash') }}" data-cancel-button="{{ __('Cancel') }}">
                                                @csrf
                                                @method('DELETE')
                                                <button type="submit" class="dropdown-item text-danger">
                                                    {{ __('Delete') }}
                                                </button>
                                            </form>
                                        </div>
                                    </div>
                                </div>

                                <div class="collapse mt-2" id="rename-{{ $document->id }}">
                                    <form method="POST" action="{{ route('projects.drive-documents.rename', [$project, $document]) }}" class="d-flex flex-wrap align-items-end">
                                        @csrf
                                        @method('PATCH')
                                        <label for="rename-name-{{ $document->id }}" class="sr-only">{{ __('Name') }}</label>
                                        <input id="rename-name-{{ $document->id }}" name="name" type="text" class="form-control form-control-sm mr-2 mb-2" value="{{ $document->name }}" required>
                                        <button type="submit" class="btn btn-sm btn-primary mb-2">{{ __('Save') }}</button>
                                    </form>
                                </div>

                                <div class="collapse mt-2" id="move-{{ $document->id }}">
                                    <form method="POST" action="{{ route('projects.drive-documents.move', [$project, $document]) }}" class="d-flex flex-wrap align-items-end">
                                        @csrf
                                        @method('PATCH')
                                        <label for="move-folder-{{ $document->id }}" class="sr-only">{{ __('Destination') }}</label>
                                        <select id="move-folder-{{ $document->id }}" name="destination_folder_id" class="form-control form-control-sm mr-2 mb-2">
                                            <option value="">{{ __('Project Root') }}</option>
                                            @foreach ($folderOptions as $folderId => $folderName)
                                                <option value="{{ $folderId }}">{{ $folderName }}</option>
                                            @endforeach
                                        </select>
                                        <button type="submit" class="btn btn-sm btn-primary mb-2">{{ __('Move') }}</button>
                                    </form>
                                </div>

                                <div class="collapse mt-2" id="copy-{{ $document->id }}">
                                    <form method="POST" action="{{ route('projects.drive-documents.copy', [$project, $document]) }}" class="d-flex flex-wrap align-items-end">
                                        @csrf
                                        <label for="copy-name-{{ $document->id }}" class="sr-only">{{ __('Copy name') }}</label>
                                        <input id="copy-name-{{ $document->id }}" name="name" type="text" class="form-control form-control-sm mr-2 mb-2" placeholder="{{ __('Copy name (optional)') }}">
                                        <label for="copy-folder-{{ $document->id }}" class="sr-only">{{ __('Destination') }}</label>
                                        <select id="copy-folder-{{ $document->id }}" name="destination_folder_id" class="form-control form-control-sm mr-2 mb-2">
                                            <option value="">{{ __('Project Root') }}</option>
                                            @foreach ($folderOptions as $folderId => $folderName)
                                                <option value="{{ $folderId }}">{{ $folderName }}</option>
                                            @endforeach
                                        </select>
                                        <button type="submit" class="btn btn-sm btn-primary mb-2">{{ __('Copy') }}</button>
                                    </form>
                                </div>
                            </div>
                        @endforeach
                    </div>
                @endif

                @if ($documents->hasPages())
                    <div class="card-footer">
                        {{ $documents->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    @can('create', \App\Models\DocumentFolder::class)
        <div class="modal fade" id="drive-folder-modal" tabindex="-1" role="dialog" aria-labelledby="drive-folder-modal-title" aria-hidden="true">
            <div class="modal-dialog" role="document">
                <div class="modal-content">
                    <form method="POST" action="{{ route('projects.drive-documents.folders.store', $project) }}">
                        @csrf
                        <div class="modal-header bg-primary">
                            <h5 class="modal-title" id="drive-folder-modal-title">{{ __('Create Folder') }}</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="{{ __('Close') }}">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            @if ($driveReady && $rootFolder)
                                <div class="form-group">
                                    <x-input-label for="modal_parent_id" :value="__('Parent Folder')" />
                                    <select id="modal_parent_id" name="parent_id" class="form-control" data-control="select2">
                                        <option value="{{ $rootFolder->id }}" @selected((string) $parentId === (string) $rootFolder->id)>{{ __('Project Root') }}</option>
                                        @foreach ($folderOptions as $folderId => $folderName)
                                            <option value="{{ $folderId }}" @selected((string) $parentId === (string) $folderId)>{{ $folderName }}</option>
                                        @endforeach
                                    </select>
                                    <x-input-error class="mt-2" :messages="$errors->get('parent_id')" />
                                </div>
                                <div class="form-group">
                                    <x-input-label for="modal_folder_name" :value="__('Folder name')" />
                                    <x-text-input id="modal_folder_name" name="name" type="text" :value="old('name')" required />
                                    <x-input-error class="mt-2" :messages="$errors->get('name')" />
                                </div>
                            @else
                                <p class="text-muted mb-0">{{ __('Folders will appear once Drive setup finishes.') }}</p>
                            @endif
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">
                                {{ __('Cancel') }}
                            </button>
                            @if ($driveReady && $rootFolder)
                                <x-primary-button>{{ __('Create Folder') }}</x-primary-button>
                            @endif
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endcan

    @can('create', \App\Models\Document::class)
        <div class="modal fade" id="drive-upload-modal" tabindex="-1" role="dialog" aria-labelledby="drive-upload-modal-title" aria-hidden="true">
            <div class="modal-dialog modal-lg" role="document">
                <div class="modal-content">
                    <form method="POST" action="{{ route('projects.drive-documents.upload', $project) }}" enctype="multipart/form-data" id="document-upload-form">
                        @csrf
                        <div class="modal-header bg-primary">
                            <h5 class="modal-title" id="drive-upload-modal-title">{{ __('Upload File') }}</h5>
                            <button type="button" class="close" data-dismiss="modal" aria-label="{{ __('Close') }}">
                                <span aria-hidden="true">&times;</span>
                            </button>
                        </div>
                        <div class="modal-body">
                            @if (! $driveReady)
                                <div class="alert alert-warning">
                                    {{ __('Project Drive folders are being prepared. Please refresh in a moment to start uploading.') }}
                                </div>
                            @endif
                            <div class="form-group">
                                <x-input-label for="folder_id" :value="__('Folder')" />
                                <select id="folder_id" name="folder_id" class="form-control" data-control="select2" @disabled(! $driveReady)>
                                    <option value="" @selected($selectedFolder === null || $selectedFolder === '')>{{ __('Project Root') }}</option>
                                    @foreach ($folderOptions as $folderId => $folderName)
                                        <option value="{{ $folderId }}" @selected((string) $selectedFolder === (string) $folderId)>{{ $folderName }}</option>
                                    @endforeach
                                </select>
                                <x-input-error class="mt-2" :messages="$errors->get('folder_id')" />
                            </div>

                            <div class="row">
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <x-input-label for="source" :value="__('Source')" />
                                        <x-text-input id="source" name="source" type="text" :value="old('source')" required />
                                        <x-input-error class="mt-2" :messages="$errors->get('source')" />
                                    </div>
                                </div>
                                <div class="col-md-6">
                                    <div class="form-group">
                                        <x-input-label for="received_from" :value="__('Received From')" />
                                        <x-text-input id="received_from" name="received_from" type="text" :value="old('received_from')" required />
                                        <x-input-error class="mt-2" :messages="$errors->get('received_from')" />
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <x-input-label for="received_at" :value="__('Received At')" />
                                <x-text-input id="received_at" name="received_at" type="text" data-datepicker="1" :value="old('received_at')" />
                                <x-input-error class="mt-2" :messages="$errors->get('received_at')" />
                            </div>

                            <div class="form-group">
                                <x-input-label for="file" :value="__('Document File')" />
                                <div id="upload-dropzone" class="border rounded p-3 text-center text-muted">
                                    <div class="font-weight-bold">{{ __('Drag and drop a file here') }}</div>
                                    <div class="small">{{ __('or click to browse') }}</div>
                                </div>
                                <input id="file" name="file" type="file" class="d-none" required @disabled(! $driveReady)>
                                <div id="upload-filename" class="small text-muted mt-2"></div>
                                <x-input-error class="mt-2" :messages="$errors->get('file')" />
                            </div>
                        </div>
                        <div class="modal-footer">
                            <button type="button" class="btn btn-outline-secondary" data-dismiss="modal">
                                {{ __('Cancel') }}
                            </button>
                            <x-primary-button :disabled="! $driveReady">{{ __('Upload') }}</x-primary-button>
                        </div>
                    </form>
                </div>
            </div>
        </div>
    @endcan

    @push('css')
        <style>
            #drive-folder-modal,
            #drive-upload-modal {
                z-index: 2050;
            }

            .drive-modal-backdrop {
                z-index: 2040 !important;
            }
        </style>
    @endpush

    @section('js')
        @parent
        <script>
            document.addEventListener('DOMContentLoaded', () => {
                const dropzone = document.getElementById('upload-dropzone');
                const fileInput = document.getElementById('file');
                const fileName = document.getElementById('upload-filename');
                const hasJQuery = window.$ && window.$.fn && typeof window.$.fn.modal === 'function';

                if (dropzone && fileInput && fileName) {
                    const highlight = () => dropzone.classList.add('bg-light');
                    const unhighlight = () => dropzone.classList.remove('bg-light');

                    dropzone.addEventListener('click', () => fileInput.click());
                    dropzone.addEventListener('dragenter', (event) => {
                        event.preventDefault();
                        highlight();
                    });
                    dropzone.addEventListener('dragover', (event) => {
                        event.preventDefault();
                        highlight();
                    });
                    dropzone.addEventListener('dragleave', (event) => {
                        event.preventDefault();
                        unhighlight();
                    });
                    dropzone.addEventListener('drop', (event) => {
                        event.preventDefault();
                        unhighlight();
                        if (event.dataTransfer.files.length > 0) {
                            fileInput.files = event.dataTransfer.files;
                            fileName.textContent = event.dataTransfer.files[0].name;
                        }
                    });

                    fileInput.addEventListener('change', () => {
                        const file = fileInput.files[0];
                        fileName.textContent = file ? file.name : '';
                    });
                }

                const showFolderModal = @json($errors->has('name') || $errors->has('parent_id'));
                const showUploadModal = @json($errors->has('folder_id') || $errors->has('source') || $errors->has('received_from') || $errors->has('received_at') || $errors->has('file'));

                if (showFolderModal && window.$) {
                    $('#drive-folder-modal').modal('show');
                }

                if (showUploadModal && window.$) {
                    $('#drive-upload-modal').modal('show');
                }

                if (hasJQuery) {
                    const $folderModal = $('#drive-folder-modal');
                    const $uploadModal = $('#drive-upload-modal');

                    if ($folderModal.length) {
                        $folderModal.appendTo('body');
                        $folderModal.on('shown.bs.modal', () => {
                            $('.modal-backdrop').addClass('drive-modal-backdrop');
                        });
                    }

                    if ($uploadModal.length) {
                        $uploadModal.appendTo('body');
                        $uploadModal.on('shown.bs.modal', () => {
                            $('.modal-backdrop').addClass('drive-modal-backdrop');
                        });
                    }
                }
            });
        </script>
    @endsection
</x-app-layout>
