<?php

namespace App\Filament\Parent\Pages;

use Filament\Pages\Page;
use Illuminate\Support\Facades\Auth;

class MyChild extends Page
{
    protected static ?string $navigationIcon = 'heroicon-m-user';
    protected static string $view = 'filament.parent.pages.my-child';
    protected static ?string $navigationLabel = 'My Child';
    protected static ?string $title = 'Child Profile';
    protected static ?int $navigationSort = 2;

    public function getChildren()
    {
        $guardian = Auth::user()->guardian;
        
        if (!$guardian) {
            return collect();
        }

        return $guardian->students()->with([
            'enrollments' => function($query) {
                $query->where('status', 'ACTIVE')->with('package');
            }
        ])->get();
    }

    public static function canAccess(): bool
    {
        return Auth::check() && Auth::user()->isParent();
    }
}
