<?php

use App\Http\Controllers\Admin\CvController as AdminCvController;
use App\Http\Controllers\Admin\MetricsController;
use App\Http\Controllers\Admin\PostController;
use App\Http\Controllers\Admin\SettingsController;
use App\Http\Controllers\Public\BlogController;
use App\Http\Controllers\Public\CvController;
use App\Http\Controllers\Public\HomeController;
use App\Http\Controllers\SitemapController;
use Illuminate\Support\Facades\Route;

Route::middleware('record.visit')->group(function () {
    Route::get('/', HomeController::class)->name('home');
    Route::get('/cv', [CvController::class, 'show'])->name('cv.show');
});
Route::get('/cv/print', [CvController::class, 'print'])->name('cv.print');
Route::get('/cv/download-pdf', [CvController::class, 'downloadPdf'])->name('cv.download-pdf');
Route::get('/blog', [BlogController::class, 'index'])->name('blog.index');
Route::get('/blog/{slug}', [BlogController::class, 'show'])->name('blog.show');
Route::get('/sitemap.xml', [SitemapController::class, 'index'])->name('sitemap');

// Breeze auth controllers redirect to 'dashboard' after login/register; alias it to home.
Route::redirect('/dashboard', '/')->name('dashboard');

Route::middleware(['auth', 'verified'])->group(function () {
    Route::redirect('/admin', '/admin/posts')->name('admin.dashboard');

    Route::middleware('admin')->group(function () {
        // CV
        Route::get('/admin/cv', [AdminCvController::class, 'index'])->name('admin.cv.index');
        Route::put('/admin/cv/profile', [AdminCvController::class, 'updateProfile'])->name('admin.cv.profile.update');

        // Experiencia
        Route::post('/admin/cv/experience', [AdminCvController::class, 'storeExperience'])->name('admin.cv.experience.store');
        Route::put('/admin/cv/experience/{experience}', [AdminCvController::class, 'updateExperience'])->name('admin.cv.experience.update');
        Route::delete('/admin/cv/experience/{experience}', [AdminCvController::class, 'destroyExperience'])->name('admin.cv.experience.destroy');

        // Educacion
        Route::post('/admin/cv/education', [AdminCvController::class, 'storeEducation'])->name('admin.cv.education.store');
        Route::put('/admin/cv/education/{education}', [AdminCvController::class, 'updateEducation'])->name('admin.cv.education.update');
        Route::delete('/admin/cv/education/{education}', [AdminCvController::class, 'destroyEducation'])->name('admin.cv.education.destroy');

        // Cursos y Certificaciones
        Route::post('/admin/cv/course', [AdminCvController::class, 'storeCourse'])->name('admin.cv.course.store');
        Route::put('/admin/cv/course/{course}', [AdminCvController::class, 'updateCourse'])->name('admin.cv.course.update');
        Route::delete('/admin/cv/course/{course}', [AdminCvController::class, 'destroyCourse'])->name('admin.cv.course.destroy');

        // Habilidades
        Route::post('/admin/cv/skill', [AdminCvController::class, 'storeSkill'])->name('admin.cv.skill.store');
        Route::put('/admin/cv/skill/{skill}', [AdminCvController::class, 'updateSkill'])->name('admin.cv.skill.update');
        Route::delete('/admin/cv/skill/{skill}', [AdminCvController::class, 'destroySkill'])->name('admin.cv.skill.destroy');

        // Posts
        Route::resource('admin/posts', PostController::class, ['as' => 'admin']);

        // Metricas
        Route::get('/admin/metrics', [MetricsController::class, 'index'])->name('admin.metrics.index');

        // Configuracion de cuenta
        Route::get('/admin/settings', [SettingsController::class, 'index'])->name('admin.settings.index');
        Route::put('/admin/settings/email', [SettingsController::class, 'updateEmail'])->name('admin.settings.email.update');
        Route::put('/admin/settings/password', [SettingsController::class, 'updatePassword'])->name('admin.settings.password.update');
    });
});

require __DIR__.'/auth.php';
