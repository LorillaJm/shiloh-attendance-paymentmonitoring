<x-filament-panels::page>
    @php
        $children = $this->getChildren();
    @endphp

    @if($children->isEmpty())
        <div class="flex items-center justify-center" style="min-height: 60vh;">
            <div class="text-center" style="max-width: 400px;">
                <div style="width: 80px; height: 80px; margin: 0 auto 1.5rem; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 20px; display: flex; align-items: center; justify-content: center;">
                    <svg style="width: 40px; height: 40px; color: white;" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                        <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M16 7a4 4 0 11-8 0 4 4 0 018 0zM12 14a7 7 0 00-7 7h14a7 7 0 00-7-7z"></path>
                    </svg>
                </div>
                <h3 style="font-size: 1.5rem; font-weight: 700; color: var(--text-primary); margin-bottom: 0.75rem;">No Children Linked</h3>
                <p style="font-size: 0.875rem; color: var(--text-secondary);">Please contact the administrator to link your children to your account.</p>
            </div>
        </div>
    @else
        <div class="grid grid-cols-1 gap-6 md:grid-cols-2 lg:grid-cols-3">
            @foreach($children as $child)
                <div style="background: white; border-radius: 20px; padding: 2rem; box-shadow: 0 1px 3px 0 rgba(0, 0, 0, 0.1); border: 1px solid #f3f4f6; transition: all 0.2s;" class="dark:bg-gray-800 dark:border-gray-700">
                    {{-- Student Avatar --}}
                    <div style="width: 100px; height: 100px; margin: 0 auto 1.5rem; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); border-radius: 50%; display: flex; align-items: center; justify-content: center; font-size: 2.5rem; font-weight: 700; color: white;">
                        {{ substr($child->first_name, 0, 1) }}
                    </div>

                    {{-- Student Info --}}
                    <div style="text-align: center; margin-bottom: 1.5rem;">
                        <h3 style="font-size: 1.5rem; font-weight: 700; color: #1f2937; margin-bottom: 0.5rem;" class="dark:text-white">
                            {{ $child->full_name }}
                        </h3>
                        <p style="font-size: 0.875rem; color: #6b7280; margin-bottom: 0.25rem;" class="dark:text-gray-400">
                            {{ $child->student_no }}
                        </p>
                        <span style="display: inline-block; padding: 0.375rem 0.75rem; background: {{ $child->status === 'ACTIVE' ? '#d1fae5' : '#fee2e2' }}; color: {{ $child->status === 'ACTIVE' ? '#065f46' : '#991b1b' }}; border-radius: 9999px; font-size: 0.75rem; font-weight: 600; text-transform: uppercase; margin-top: 0.5rem;">
                            {{ $child->status }}
                        </span>
                    </div>

                    {{-- Enrollment Info --}}
                    @php
                        $activeEnrollment = $child->enrollments->first();
                    @endphp

                    @if($activeEnrollment && $activeEnrollment->package)
                        <div style="background: #f9fafb; border-radius: 12px; padding: 1rem; margin-bottom: 1rem;" class="dark:bg-gray-700">
                            <p style="font-size: 0.75rem; font-weight: 600; color: #6b7280; text-transform: uppercase; letter-spacing: 0.05em; margin-bottom: 0.5rem;" class="dark:text-gray-400">
                                Program
                            </p>
                            <p style="font-size: 0.875rem; font-weight: 600; color: #1f2937;" class="dark:text-white">
                                {{ $activeEnrollment->package->name }}
                            </p>
                        </div>

                        {{-- Stats Grid --}}
                        <div style="display: grid; grid-template-columns: repeat(2, 1fr); gap: 0.75rem;">
                            {{-- Total Sessions --}}
                            <div style="background: #f9fafb; border-radius: 12px; padding: 0.875rem; text-align: center;" class="dark:bg-gray-700">
                                <p style="font-size: 1.5rem; font-weight: 700; color: #1f2937; margin-bottom: 0.25rem;" class="dark:text-white">
                                    {{ $activeEnrollment->package->total_sessions ?? 0 }}
                                </p>
                                <p style="font-size: 0.75rem; color: #6b7280;" class="dark:text-gray-400">
                                    Total Sessions
                                </p>
                            </div>

                            {{-- Balance --}}
                            <div style="background: #f9fafb; border-radius: 12px; padding: 0.875rem; text-align: center;" class="dark:bg-gray-700">
                                <p style="font-size: 1.125rem; font-weight: 700; color: #1f2937; margin-bottom: 0.25rem;" class="dark:text-white">
                                    ₱{{ number_format($activeEnrollment->remaining_balance_computed, 0) }}
                                </p>
                                <p style="font-size: 0.75rem; color: #6b7280;" class="dark:text-gray-400">
                                    Balance
                                </p>
                            </div>
                        </div>
                    @else
                        <div style="background: #f9fafb; border-radius: 12px; padding: 1.5rem; text-align: center;" class="dark:bg-gray-700">
                            <p style="font-size: 0.875rem; color: #6b7280;" class="dark:text-gray-400">
                                No active enrollment
                            </p>
                        </div>
                    @endif

                    {{-- Quick Actions --}}
                    <div style="margin-top: 1.5rem; display: flex; gap: 0.5rem;">
                        <a href="{{ route('filament.parent.pages.my-children-attendance') }}" style="flex: 1; padding: 0.625rem; background: linear-gradient(135deg, #667eea 0%, #764ba2 100%); color: white; border-radius: 10px; font-size: 0.875rem; font-weight: 600; text-align: center; text-decoration: none; transition: transform 0.2s;">
                            View Attendance
                        </a>
                    </div>
                </div>
            @endforeach
        </div>
    @endif
</x-filament-panels::page>
