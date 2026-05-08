<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Concerns\AuthorizesOwnership;
use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\StoreEducationRequest;
use App\Http\Requests\Admin\StoreExperienceRequest;
use App\Http\Requests\Admin\StoreCourseRequest;
use App\Http\Requests\Admin\StoreSkillRequest;
use App\Http\Requests\Admin\UpdateCourseRequest;
use App\Http\Requests\Admin\UpdateEducationRequest;
use App\Http\Requests\Admin\UpdateExperienceRequest;
use App\Http\Requests\Admin\UpdateProfileRequest;
use App\Http\Requests\Admin\UpdateSkillRequest;
use App\Models\Course;
use App\Models\Education;
use App\Models\Experience;
use App\Models\Skill;
use App\Models\User;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;

class CvController extends Controller
{
    use AuthorizesOwnership;

    public function index(): View
    {
        $user = User::query()
            ->with(['profile', 'experiences.competencies', 'education', 'skills', 'courses'])
            ->whereKey(Auth::id())
            ->firstOrFail();

        return view('admin.cv.index', compact('user'));
    }

    public function updateProfile(UpdateProfileRequest $request): RedirectResponse
    {
        $user = Auth::user();
        $data = $request->safe()->except(['avatar', 'remove_avatar']);

        if ($request->boolean('remove_avatar')) {
            if ($user->profile?->avatar_path) {
                Storage::disk('public')->delete($user->profile->avatar_path);
            }
            $data['avatar_path'] = null;
        } elseif ($request->hasFile('avatar')) {
            if ($user->profile?->avatar_path) {
                Storage::disk('public')->delete($user->profile->avatar_path);
            }
            $data['avatar_path'] = $request->file('avatar')->store('avatars', 'public');
        }

        $user->profile()->updateOrCreate(
            ['user_id' => $user->id],
            $data
        );

        return redirect()->route('admin.cv.index')->with('success', 'Perfil actualizado exitosamente');
    }

    // ── EXPERIENCIA ──────────────────────────────────────────────────────────

    public function storeExperience(StoreExperienceRequest $request): RedirectResponse
    {
        DB::transaction(function () use ($request): void {
            $data = $request->validated();
            $competencies = $data['competencies'] ?? null;
            unset($data['competencies']);
            $data['show_in_web'] = $request->boolean('show_in_web');
            $data['show_in_pdf'] = $request->boolean('show_in_pdf');

            $experience = Auth::user()->experiences()->create($data);
            $this->syncCompetencies($experience, $competencies);
        });

        return redirect()->route('admin.cv.index')->with('success', 'Experiencia agregada exitosamente');
    }

    public function updateExperience(UpdateExperienceRequest $request, Experience $experience): RedirectResponse
    {
        $this->authorizeOwnership($experience);

        DB::transaction(function () use ($request, $experience): void {
            $data = $request->validated();
            $competencies = $data['competencies'] ?? null;
            unset($data['competencies']);
            $data['show_in_web'] = $request->boolean('show_in_web');
            $data['show_in_pdf'] = $request->boolean('show_in_pdf');

            $experience->update($data);
            $this->syncCompetencies($experience, $competencies);
        });

        return redirect()->route('admin.cv.index')->with('success', 'Experiencia actualizada exitosamente');
    }

    public function destroyExperience(Experience $experience): RedirectResponse
    {
        $this->authorizeOwnership($experience);
        $experience->delete();

        return redirect()->route('admin.cv.index')->with('success', 'Experiencia eliminada exitosamente');
    }

    // ── EDUCACION ────────────────────────────────────────────────────────────

    public function storeEducation(StoreEducationRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['show_in_web'] = $request->boolean('show_in_web');
        $data['show_in_pdf'] = $request->boolean('show_in_pdf');

        Auth::user()->education()->create($data);

        return redirect()->route('admin.cv.index')->with('success', 'Formacion agregada exitosamente');
    }

    public function updateEducation(UpdateEducationRequest $request, Education $education): RedirectResponse
    {
        $this->authorizeOwnership($education);

        $data = $request->validated();
        $data['show_in_web'] = $request->boolean('show_in_web');
        $data['show_in_pdf'] = $request->boolean('show_in_pdf');

        $education->update($data);

        return redirect()->route('admin.cv.index')->with('success', 'Formacion actualizada exitosamente');
    }

    public function destroyEducation(Education $education): RedirectResponse
    {
        $this->authorizeOwnership($education);
        $education->delete();

        return redirect()->route('admin.cv.index')->with('success', 'Formacion eliminada exitosamente');
    }

    // ── CURSOS Y CERTIFICACIONES ─────────────────────────────────────────────

    public function storeCourse(StoreCourseRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['show_in_web'] = $request->boolean('show_in_web');
        $data['show_in_pdf'] = $request->boolean('show_in_pdf');

        Auth::user()->courses()->create($data);

        $label = $request->input('type') === 'certification' ? 'Certificacion' : 'Curso';

        return redirect()->route('admin.cv.index')->with('success', "{$label} agregado exitosamente");
    }

    public function updateCourse(UpdateCourseRequest $request, Course $course): RedirectResponse
    {
        $this->authorizeOwnership($course);

        $data = $request->validated();
        $data['show_in_web'] = $request->boolean('show_in_web');
        $data['show_in_pdf'] = $request->boolean('show_in_pdf');

        $course->update($data);

        return redirect()->route('admin.cv.index')->with('success', 'Actualizado exitosamente');
    }

    public function destroyCourse(Course $course): RedirectResponse
    {
        $this->authorizeOwnership($course);
        $course->delete();

        return redirect()->route('admin.cv.index')->with('success', 'Eliminado exitosamente');
    }

    // ── HABILIDADES ──────────────────────────────────────────────────────────

    public function storeSkill(StoreSkillRequest $request): RedirectResponse
    {
        $data = $request->validated();
        $data['show_in_web'] = $request->boolean('show_in_web');
        $data['show_in_pdf'] = $request->boolean('show_in_pdf');

        Auth::user()->skills()->create($data);

        return redirect()->route('admin.cv.index')->with('success', 'Habilidad agregada exitosamente');
    }

    public function updateSkill(UpdateSkillRequest $request, Skill $skill): RedirectResponse
    {
        $this->authorizeOwnership($skill);

        $data = $request->validated();
        $data['show_in_web'] = $request->boolean('show_in_web');
        $data['show_in_pdf'] = $request->boolean('show_in_pdf');

        $skill->update($data);

        return redirect()->route('admin.cv.index')->with('success', 'Habilidad actualizada exitosamente');
    }

    public function destroySkill(Skill $skill): RedirectResponse
    {
        $this->authorizeOwnership($skill);
        $skill->delete();

        return redirect()->route('admin.cv.index')->with('success', 'Habilidad eliminada exitosamente');
    }

    // ── PRIVADOS ─────────────────────────────────────────────────────────────

    private function syncCompetencies(Experience $experience, ?string $csv): void
    {
        $experience->competencies()->delete();

        if (blank($csv)) {
            return;
        }

        $names = array_filter(array_map('trim', explode(',', $csv)));

        foreach ($names as $name) {
            $experience->competencies()->create(['name' => $name]);
        }
    }
}
