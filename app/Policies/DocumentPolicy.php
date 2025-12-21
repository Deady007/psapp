<?php

namespace App\Policies;

use App\Models\Document;
use App\Models\User;

class DocumentPolicy
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
    public function view(User $user, Document $document): bool
    {
        return $user->can('documents.view')
            || $this->ownsDocument($user, $document);
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
    public function update(User $user, Document $document): bool
    {
        return $user->can('documents.edit')
            || $this->ownsDocument($user, $document);
    }

    /**
     * Determine whether the user can delete the model.
     */
    public function delete(User $user, Document $document): bool
    {
        return $user->can('documents.delete')
            || $this->ownsDocument($user, $document);
    }

    /**
     * Determine whether the user can restore the model.
     */
    public function restore(User $user, Document $document): bool
    {
        return $this->delete($user, $document);
    }

    /**
     * Determine whether the user can permanently delete the model.
     */
    public function forceDelete(User $user, Document $document): bool
    {
        return $this->delete($user, $document);
    }

    public function rename(User $user, Document $document): bool
    {
        return $this->update($user, $document);
    }

    public function move(User $user, Document $document): bool
    {
        return $this->update($user, $document);
    }

    public function copy(User $user, Document $document): bool
    {
        return $user->can('documents.create')
            || $this->ownsDocument($user, $document);
    }

    private function ownsDocument(User $user, Document $document): bool
    {
        return $document->uploaded_by === $user->id
            || $document->folder?->owner_id === $user->id;
    }
}
