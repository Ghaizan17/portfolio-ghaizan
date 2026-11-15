<?php

use App\Http\Controllers\HomeController;
use App\Http\Controllers\JournalController;
use App\Http\Controllers\PageController;
use App\Http\Controllers\ProjectController;
use App\Http\Controllers\ResumeDownloadController;
use App\Http\Controllers\SitemapController;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Route;

Route::get('/', HomeController::class)->name('home');

Route::get('/about', [PageController::class, 'about'])->name('about');
Route::get('/skills', [PageController::class, 'skills'])->name('skills');
Route::get('/certificates', [PageController::class, 'certificates'])->name('certificates');
Route::get('/experience', [PageController::class, 'experience'])->name('experience');
Route::get('/resume', [PageController::class, 'resume'])->name('resume');
Route::get('/resume/download', ResumeDownloadController::class)->name('resume.download');
Route::get('/resume/preview', ResumeDownloadController::class)->name('resume.preview');
Route::get('/contact', [PageController::class, 'contact'])->name('contact');
Route::get('/guestbook', [PageController::class, 'guestbook'])->name('guestbook');
Route::get('/testimonials', [PageController::class, 'testimonials'])->name('testimonials');

Route::get('/projects', [ProjectController::class, 'index'])->name('projects.index');
Route::get('/projects/{project:slug}', [ProjectController::class, 'show'])->name('projects.show');

Route::get('/journal', [JournalController::class, 'index'])->name('journal.index');
Route::get('/journal/{post:slug}', [JournalController::class, 'show'])->name('journal.show');

Route::get('/sitemap.xml', SitemapController::class)->name('sitemap');

Route::get('/robots.txt', function (): Response {
    return response(
        "User-agent: *\nAllow: /\nDisallow: /admin\nDisallow: /resume/download\n\nSitemap: ".route('sitemap')."\n",
        200,
        ['Content-Type' => 'text/plain'],
    );
})->name('robots');
