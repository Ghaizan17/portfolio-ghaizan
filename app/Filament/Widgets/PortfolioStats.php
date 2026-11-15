<?php

namespace App\Filament\Widgets;

use App\Models\Certificate;
use App\Models\Experience;
use App\Models\GuestbookEntry;
use App\Models\Message;
use App\Models\Post;
use App\Models\Project;
use Filament\Widgets\StatsOverviewWidget;
use Filament\Widgets\StatsOverviewWidget\Stat;

class PortfolioStats extends StatsOverviewWidget
{
    protected static ?int $sort = 1;

    protected function getStats(): array
    {
        return [
            Stat::make('Projects', (string) Project::query()->count())
                ->description(Project::query()->where('is_published', true)->count().' published')
                ->descriptionIcon('heroicon-m-rectangle-stack')
                ->color('primary'),
            Stat::make('Certificates', (string) Certificate::query()->count())
                ->description(Certificate::query()->where('is_published', true)->count().' published')
                ->descriptionIcon('heroicon-m-trophy')
                ->color('warning'),
            Stat::make('Experience', (string) Experience::query()->count())
                ->description(Experience::query()->where('is_published', true)->count().' published')
                ->descriptionIcon('heroicon-m-briefcase')
                ->color('success'),
            Stat::make('Journal', (string) Post::query()->count())
                ->description(Post::query()->where('is_published', true)->count().' published')
                ->descriptionIcon('heroicon-m-newspaper')
                ->color('info'),
            Stat::make('Unread messages', (string) Message::query()->unread()->active()->count())
                ->description('Archived: '.Message::query()->where('is_archived', true)->count())
                ->descriptionIcon('heroicon-m-envelope')
                ->color('danger'),
            Stat::make('Guestbook pending', (string) GuestbookEntry::query()->where('status', GuestbookEntry::STATUS_PENDING)->count())
                ->description(GuestbookEntry::query()->where('status', GuestbookEntry::STATUS_APPROVED)->count().' approved')
                ->descriptionIcon('heroicon-m-chat-bubble-left-ellipsis')
                ->color('gray'),
        ];
    }
}
