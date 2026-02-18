<x-filament-widgets::widget>
    <x-filament::section>
        <div class="space-y-4">
            <h2 class="text-xl font-bold">Welcome to Shiloh Parent Portal</h2>
            
            @forelse($this->getStudents() as $student)
                <div class="border rounded-lg p-4 bg-gray-50">
                    <h3 class="font-semibold text-lg">{{ $student->full_name }}</h3>
                    <p class="text-sm text-gray-600">Student No: {{ $student->student_no }}</p>
                    
                    <div class="mt-3 grid grid-cols-3 gap-4">
                        <div>
                            <p class="text-xs text-gray-500">Active Enrollments</p>
                            <p class="text-2xl font-bold text-blue-600">{{ $student->enrollments->count() }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500">Sessions (Last 7 days)</p>
                            <p class="text-2xl font-bold text-green-600">{{ $student->sessionOccurrences->count() }}</p>
                        </div>
                        <div>
                            <p class="text-xs text-gray-500">Age</p>
                            <p class="text-2xl font-bold text-purple-600">{{ $student->age ?? 'N/A' }}</p>
                        </div>
                    </div>
                </div>
            @empty
                <p class="text-gray-500">No students found for your account.</p>
            @endforelse
        </div>
    </x-filament::section>
</x-filament-widgets::widget>
