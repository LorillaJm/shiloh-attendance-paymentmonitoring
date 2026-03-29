<?php

namespace App\Filament\Resources\UserResource\Pages;

use App\Filament\Resources\UserResource;
use Filament\Actions;
use Filament\Resources\Pages\EditRecord;
use Illuminate\Database\Eloquent\Model;

class EditUser extends EditRecord
{
    protected static string $resource = UserResource::class;

    protected function getHeaderActions(): array
    {
        return [
            Actions\DeleteAction::make()
                ->successNotificationTitle('User deleted successfully'),
        ];
    }

    protected function getRedirectUrl(): string
    {
        return $this->getResource()::getUrl('index');
    }

    protected function getSavedNotificationTitle(): ?string
    {
        return 'User updated successfully';
    }
    
    /**
     * Mutate form data before filling the form.
     * This ensures role field is properly handled.
     */
    protected function mutateFormDataBeforeFill(array $data): array
    {
        // Check if user is trying to edit their own record
        if (auth()->id() === $this->record->id) {
            // Store original role to prevent changes
            $this->originalRole = $data['role'] ?? null;
        }
        
        return $data;
    }
    
    /**
     * Mutate form data before saving.
     * Prevent users from changing their own role.
     */
    protected function mutateFormDataBeforeSave(array $data): array
    {
        // If user is editing their own record, restore original role
        if (auth()->id() === $this->record->id && isset($this->originalRole)) {
            $data['role'] = $this->originalRole;
        }
        
        // Additional authorization check using policy
        if (isset($data['role']) && $data['role'] !== $this->record->role) {
            $this->authorize('assignRole', $this->record);
        }
        
        return $data;
    }
}
