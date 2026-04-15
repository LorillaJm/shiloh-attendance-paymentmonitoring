@php
    $logoPath = public_path('images/logo.png');
    $logoBase64 = '';
    if (file_exists($logoPath)) {
        $logoBase64 = 'data:image/png;base64,' . base64_encode(file_get_contents($logoPath));
    }
@endphp
<div class="report-header">
    @if($logoBase64)
        <img src="{{ $logoBase64 }}" class="report-logo" alt="Logo">
    @endif
    <h1>Shiloh's Learning and Development Center</h1>
    <h2>{{ $reportTitle ?? 'Report' }}</h2>
    @if(isset($reportSubtitle))
        <p>{{ $reportSubtitle }}</p>
    @endif
    <p>Generated: {{ now()->format('F d, Y h:i A') }}</p>
</div>
