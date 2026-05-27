<!DOCTYPE html>
<html lang="es">
<head>
    <meta charset="utf-8">
    <title>CV - {{ $user->name }}</title>
    <style>
        * { box-sizing: border-box; margin: 0; padding: 0; }

        body {
            font-family: 'Helvetica Neue', Arial, sans-serif;
            font-size: 10pt;
            line-height: 1.5;
            color: #1a1a18;
            background: #ffffff;
            max-width: 210mm;
            margin: 0 auto;
            padding: 14mm 18mm;
        }

        @media print {
            body { padding: 0; }
            @page { margin: 14mm 18mm; size: A4; }
        }

        .cv-header { border-bottom: 2pt solid #533AB7; padding-bottom: 9pt; margin-bottom: 13pt; }
        .cv-name { font-size: 20pt; font-weight: 700; color: #1a1a18; letter-spacing: -0.02em; }
        .cv-title { font-size: 12pt; font-weight: 600; color: #533AB7; margin-top: 2pt; }
        .cv-contact { margin-top: 6pt; font-size: 8.5pt; color: #555; }

        .cv-section { margin-bottom: 12pt; }
        .cv-section-title {
            font-size: 9pt; font-weight: 700;
            text-transform: uppercase; letter-spacing: 0.08em;
            color: #533AB7;
            border-bottom: 0.75pt solid #EEEDFE;
            padding-bottom: 2.5pt; margin-bottom: 7pt;
        }

        .cv-entry { margin-bottom: 9pt; }
        .cv-entry-role { font-size: 10.5pt; font-weight: 700; }
        .cv-entry-company { font-size: 10pt; font-weight: 600; color: #533AB7; }
        .cv-entry-sep { color: #888; font-weight: 400; }
        .cv-entry-meta { font-size: 8.5pt; color: #666; margin-bottom: 3pt; margin-top: 1pt; }
        .cv-entry-desc { font-size: 9.5pt; color: #333; line-height: 1.55; white-space: pre-line; }
        .cv-entry-tags { margin-top: 3pt; font-size: 8.5pt; color: #555; }
        .cv-entry-tags-label { font-weight: 700; }

        .cv-edu-entry { margin-bottom: 5pt; }
        .cv-edu-title { font-size: 10pt; font-weight: 700; }
        .cv-edu-institution { font-size: 9.5pt; font-weight: 600; color: #533AB7; }
        .cv-edu-type { font-weight: 400; color: #888; }
        .cv-edu-meta { font-size: 8.5pt; color: #666; }

        .cv-skills-group { margin-bottom: 4pt; }
        .cv-skills-category { font-size: 9pt; font-weight: 700; color: #444; }
        .cv-skills-list { font-size: 9.5pt; color: #333; }

        .cv-lang-entry { display: inline; margin-right: 14pt; font-size: 9.5pt; }
        .cv-lang-name { font-weight: 700; }
        .cv-lang-level { color: #666; }
    </style>
</head>
<body>

    {{-- ── CABECERA ── --}}
    <header class="cv-header">
        <div class="cv-name">{{ $user->name }}</div>
        <div class="cv-title">{{ $user->profile?->title }}</div>
        <div class="cv-contact">
            @php
                $contactParts = array_filter([
                    $user->profile?->contact_email,
                    $user->profile?->phone,
                    $user->profile?->location,
                    $user->profile?->linkedin_url ? str_replace(['https://', 'http://'], '', $user->profile->linkedin_url) : null,
                    $user->profile?->github_url   ? str_replace(['https://', 'http://'], '', $user->profile->github_url)   : null,
                ]);
            @endphp
            {{ implode('  |  ', $contactParts) }}
        </div>
    </header>

    {{-- ── PERFIL PROFESIONAL ── --}}
    @if($user->profile?->bio)
    <section class="cv-section">
        <h2 class="cv-section-title">Perfil Profesional</h2>
        <p class="cv-entry-desc">{{ $user->profile->bio }}</p>
    </section>
    @endif

    {{-- ── EXPERIENCIA PROFESIONAL ── --}}
    @php $pdfExperiences = $user->experiences->where('show_in_pdf', true); @endphp
    @if($pdfExperiences->isNotEmpty())
    <section class="cv-section">
        <h2 class="cv-section-title">Experiencia Profesional</h2>
        @foreach($pdfExperiences as $exp)
        <div class="cv-entry">
            <div>
                <span class="cv-entry-role">{{ $exp->role }}</span>
                <span class="cv-entry-sep"> &mdash; </span>
                <span class="cv-entry-company">{{ $exp->company }}</span>
            </div>
            <div class="cv-entry-meta">
                @php
                    $metaParts = array_filter([$exp->period, $exp->location]);
                @endphp
                {{ implode('  &bull;  ', $metaParts) }}
            </div>
            @if($exp->description)
            <div class="cv-entry-desc">{{ $exp->description }}</div>
            @endif
            @if($exp->competencies->isNotEmpty())
            <div class="cv-entry-tags">
                <span class="cv-entry-tags-label">Tecnologias:</span>
                {{ $exp->competencies->pluck('name')->implode(', ') }}
            </div>
            @endif
        </div>
        @endforeach
    </section>
    @endif

    {{-- ── FORMACION ACADEMICA ── --}}
    @php $pdfEducation = $user->education->where('show_in_pdf', true); @endphp
    @if($pdfEducation->isNotEmpty())
    <section class="cv-section">
        <h2 class="cv-section-title">Formacion Academica</h2>
        @foreach($pdfEducation as $edu)
        <div class="cv-edu-entry">
            <div class="cv-edu-title">{{ $edu->title }}</div>
            <div class="cv-edu-institution">{{ $edu->institution }}</div>
            @php
                $eduMeta = array_filter([$edu->location, $edu->year ? (string)$edu->year : null]);
            @endphp
            @if($eduMeta)
            <div class="cv-edu-meta">{{ implode(' | ', $eduMeta) }}</div>
            @endif
        </div>
        @endforeach
    </section>
    @endif

    {{-- ── FORMACION COMPLEMENTARIA ── --}}
    @php
        $pdfCerts   = $user->courses->where('show_in_pdf', true)->where('type', 'certification');
        $pdfCourses = $user->courses->where('show_in_pdf', true)->where('type', 'course');
    @endphp
    @if($pdfCerts->isNotEmpty() || $pdfCourses->isNotEmpty())
    <section class="cv-section">
        <h2 class="cv-section-title">Formacion Complementaria</h2>
        @foreach($pdfCerts as $cert)
        <div class="cv-edu-entry">
            <div class="cv-edu-title">
                {{ $cert->title }}
                <span class="cv-edu-type">(Certificacion)</span>
            </div>
            <div class="cv-edu-institution">{{ $cert->institution }}</div>
            @if($cert->year)
            <div class="cv-edu-meta">{{ $cert->year }}</div>
            @endif
        </div>
        @endforeach
        @foreach($pdfCourses as $course)
        <div class="cv-edu-entry">
            <div class="cv-edu-title">
                {{ $course->title }}
                <span class="cv-edu-type">(Curso)</span>
            </div>
            <div class="cv-edu-institution">{{ $course->institution }}</div>
            @if($course->year)
            <div class="cv-edu-meta">{{ $course->year }}</div>
            @endif
        </div>
        @endforeach
    </section>
    @endif

    {{-- ── COMPETENCIAS TECNICAS ── --}}
    @php $pdfSkills = $user->skills->where('show_in_pdf', true)->groupBy('category'); @endphp
    @if($pdfSkills->isNotEmpty())
    <section class="cv-section">
        <h2 class="cv-section-title">Competencias Tecnicas</h2>
        @foreach($pdfSkills as $category => $categorySkills)
        <div class="cv-skills-group">
            <span class="cv-skills-category">{{ $category }}:</span>
            <span class="cv-skills-list"> {{ $categorySkills->pluck('name')->implode(', ') }}</span>
        </div>
        @endforeach
    </section>
    @endif

    {{-- ── IDIOMAS ── --}}
    @php $pdfLanguages = $user->languages->where('show_in_pdf', true); @endphp
    @if($pdfLanguages->isNotEmpty())
    <section class="cv-section">
        <h2 class="cv-section-title">Idiomas</h2>
        <div>
            @foreach($pdfLanguages as $lang)
            <span class="cv-lang-entry">
                <span class="cv-lang-name">{{ $lang->name }}</span>:
                <span class="cv-lang-level">{{ $lang->level }}</span>
            </span>
            @endforeach
        </div>
    </section>
    @endif

</body>
</html>
