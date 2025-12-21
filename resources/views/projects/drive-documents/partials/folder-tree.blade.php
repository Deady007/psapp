@php
    $children = $foldersByParent->get($parentId, collect());
@endphp

@if ($children->isNotEmpty())
    <ul class="list-unstyled pl-3">
        @foreach ($children as $child)
            <li class="mb-2">
                <a href="{{ route('projects.drive-documents.folders.show', [$project, $child]) }}" class="d-flex align-items-center text-reset">
                    <i class="far fa-folder mr-2"></i>
                    <span @class(['font-weight-bold' => $currentFolder?->id === $child->id])>{{ $child->name }}</span>
                </a>
                @include('projects.drive-documents.partials.folder-tree', [
                    'project' => $project,
                    'foldersByParent' => $foldersByParent,
                    'currentFolder' => $currentFolder,
                    'parentId' => $child->id,
                ])
            </li>
        @endforeach
    </ul>
@endif
