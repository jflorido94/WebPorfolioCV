<!DOCTYPE html>
<html>
<head>
    <meta charset="utf-8">
    <title>CV - {{ $user->name }}</title>
    @vite(['resources/css/app.css'])
    <style>
        @media print {
            body { margin: 0; padding: 10mm; }
            .page-break { page-break-after: always; }
        }
    </style>
</head>
<body class="p-8 max-w-4xl mx-auto bg-white text-gray-900">
    <div class="mb-8">
        <h1 class="text-3xl font-bold">{{ $user->name }}</h1>
        <p class="text-lg text-purple-600 font-semibold">{{ $user->profile?->title }}</p>
        <p class="text-gray-600">{{ $user->profile?->location }}</p>
    </div>

    @if($user->profile?->bio)
    <section class="mb-6">
        <h2 class="text-xl font-bold border-b-2 border-purple-600 pb-2 mb-4">Sobre mí</h2>
        <p style="white-space: pre-line">{{ $user->profile->bio }}</p>
    </section>
    @endif

    @if($user->experiences->where('show_in_pdf', true)->isNotEmpty())
    <section class="mb-6">
        <h2 class="text-xl font-bold border-b-2 border-purple-600 pb-2 mb-4">Experiencia</h2>
        @foreach($user->experiences->where('show_in_pdf', true) as $exp)
        <div class="mb-4">
            <h3 class="font-bold">{{ $exp->role }} - {{ $exp->company }}</h3>
            <p class="text-sm text-gray-600">{{ $exp->period }}</p>
            @if($exp->description)
            <p class="text-sm">{{ $exp->description }}</p>
            @endif
        </div>
        @endforeach
    </section>
    @endif

    @if($user->education->where('show_in_pdf', true)->isNotEmpty())
    <section class="mb-6">
        <h2 class="text-xl font-bold border-b-2 border-purple-600 pb-2 mb-4">Educación</h2>
        @foreach($user->education->where('show_in_pdf', true) as $edu)
        <div class="mb-2">
            <h3 class="font-bold">{{ $edu->title }}</h3>
            <p class="text-sm text-gray-600">{{ $edu->institution }} {{ $edu->year ? '(' . $edu->year . ')' : '' }}</p>
        </div>
        @endforeach
    </section>
    @endif

    @if($user->skills->where('show_in_pdf', true)->isNotEmpty())
    <section>
        <h2 class="text-xl font-bold border-b-2 border-purple-600 pb-2 mb-4">Competencias</h2>
        <div class="text-sm">
            @foreach($user->skills->where('show_in_pdf', true) as $skill)
            <span class="inline-block mr-3 mb-2">{{ $skill->name }}</span>
            @endforeach
        </div>
    </section>
    @endif
</body>
</html>
