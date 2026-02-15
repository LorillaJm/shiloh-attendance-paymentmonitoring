<?php

namespace App\Filament\Widgets;

use App\Models\PaymentSchedule;
use Filament\Tables;
use Filament\Tables\Table;
use Filament\Widgets\TableWidget as BaseWidget;
use Illuminate\Database\Eloquent\Builder;

class OverdueTable extends BaseWidget
{
    protected static ?int $sort = 5;
    
    protected static ?string $heading = 'Overdue Payments';
    
    protected int | string | array $columnSpan = 'full';
    
    protected static ?string $pollingInterval = '20s';

    public function table(Table $table): Table
    {
        $today = now('Asia/Manila');

        return $table
            ->query(
                PaymentSchedule::query()
                    ->with(['enrollment.student', 'enrollment.package'])
                    ->where('status', 'UNPAID')
                    ->whereNotNull('due_date')
                    ->where('due_date', '<', $today->format('Y-m-d'))
                    ->orderBy('due_date', 'asc')
                    ->limit(20)
            )
            ->columns([
                Tables\Columns\TextColumn::make('enrollment.student.student_no')
                    ->label('Student No')
                    ->searchable()
                    ->sortable()
                    ->weight('semibold')
                    ->color('primary'),

                Tables\Columns\TextColumn::make('enrollment.student.full_name')
                    ->label('Student Name')
                    ->searchable(['first_name', 'last_name'])
                    ->weight('medium'),

                Tables\Columns\TextColumn::make('enrollment.package.name')
                    ->label('Program')
                    ->badge()
                    ->color('info'),

                Tables\Columns\TextColumn::make('installment_no')
                    ->label('Payment #')
                    ->formatStateUsing(fn ($state) => $state == 0 ? 'Down Payment' : "Payment #{$state}")
                    ->badge()
                    ->color('warning'),

                Tables\Columns\TextColumn::make('due_date')
                    ->label('Due Date')
                    ->date('M d, Y')
                    ->sortable()
                    ->description(fn ($record) => $record->due_date->diffForHumans())
                    ->color('danger')
                    ->weight('semibold'),

                Tables\Columns\TextColumn::make('amount_due')
                    ->label('Amount')
                    ->money('PHP')
                    ->sortable()
                    ->weight('semibold')
                    ->color('danger')
                    ->alignEnd(),

                Tables\Columns\TextColumn::make('enrollment.student.guardian_contact')
                    ->label('Contact')
                    ->icon('heroicon-o-phone')
                    ->copyable()
                    ->toggleable(),
            ])
            ->actions([
                Tables\Actions\Action::make('view_student')
                    ->label('Student')
                    ->icon('heroicon-o-user')
                    ->url(fn ($record) => route('filament.admin.resources.students.edit', $record->enrollment->student_id))
                    ->openUrlInNewTab(),
                    
                Tables\Actions\Action::make('view_enrollment')
                    ->label('Enrollment')
                    ->icon('heroicon-o-academic-cap')
                    ->url(fn ($record) => route('filament.admin.resources.enrollments.view', $record->enrollment_id))
                    ->openUrlInNewTab(),
            ])
            ->paginated(false)
            ->heading('Overdue Payments (Top 20 - Oldest First)')
            ->description('Past due date - requires immediate attention');
    }
}
