<?php

namespace App\Policies;

use App\Models\DocumentFolder;
use App\Models\User;

class DocumentFolderPolicy
{
    /**
     * Determine whether the user can view any models.
     */
    public function viewAny(User $user): bool
    {
        return $user->can('documents.view');
    }

    /**
     * Determine whether the user can view the model.
     */
    public function view(User $user, DocumentFolder $documentFolder): bool
    {
        return $user->can('documents.view')
            || $documentFolder->owner_id === $user->id;
    }

    /**
     * Determine whether the user can create models.
     */
    public function create(User $user): bool
    {
        return $user->can('documents.create');
    }

    /**
     * Determine whether the user can update the model.
     */
    public function update(User $user, DocumentFolder $documentFolder): bool
    {
        return $user->can('documents.edit')
            || $documentFolder->owner_id === $user->id;
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, DocumentFolder $documentFolder): bool
    {
        return $user->can('documents.delete')
            || $documentFolder->owner_id === $user->id;
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, DocumentFolder $documentFolder): bool
    {
        return $this->delete($user, $documentFolder);
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, DocumentFolder $documentFolder): bool
    {
        return $this->delete($user, $documentFolder);
    }
}
