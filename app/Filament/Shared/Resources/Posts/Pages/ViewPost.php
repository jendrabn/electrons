<?php

namespace App\Filament\Shared\Resources\Posts\Pages;

use App\Enums\Status;
use App\Filament\Admin\Resources\Posts\PostResource;
use Filament\Actions\Action;
use Filament\Actions\ViewAction;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Textarea;
use Filament\Resources\Pages\ViewRecord;
use Filament\Schemas\Components\Utilities\Get;

class ViewPost extends ViewRecord
{
    protected static string $resource = PostResource::class;

    protected function getHeaderActions(): array
    {
        return [
            ViewAction::make('backToList')
                ->label('Back to List')
                ->icon('heroicon-o-arrow-left')
                ->color('gray')
                ->url($this->getResource()::getUrl('index')),
            Action::make('changeStatus')
                ->label('Change Status')
                ->icon('heroicon-o-cog')
                ->color('info')
                ->schema([
                    Select::make('status')
                        ->label('Status')
                        ->required()
                        ->options(Status::class)
                        ->enum(Status::class)
                        ->live(),
                    Textarea::make('rejected_reason')
                        ->label('Rejection Reason')
                        ->visible(fn(Get $get) => $get('status') === Status::REJECTED)
                        ->dehydrated(fn(Get $get) => $get('status') === Status::REJECTED)
                        ->required(fn(Get $get) => $get('status') === Status::REJECTED)
                ])
                ->action(function (array $data) {
                    $data['status'] = $data['status']->value;

                    if ($data['status'] !== Status::REJECTED->value) {
                        $data['rejected_reason'] = null;
                    }

                    if ($data['status'] === Status::PUBLISHED->value) {
                        $data['published_at'] = now();
                    }

                    $this->record->update($data);

                    $this->refreshFormData([
                        'status',
                        'rejected_reason',
                        'published_at'
                    ]);
                })
                ->successNotificationTitle('Status changed successfully')
                ->visible(fn() => auth()->user()->isAdmin()),

            Action::make('viewOnSite')
                ->label('View on Site')
                ->icon('heroicon-o-arrow-top-right-on-square')
                ->color('success')
                ->url(fn() => route('posts.show', $this->record->slug))
                ->openUrlInNewTab()
                ->visible(fn() => $this->record->status === Status::PUBLISHED->value),
        ];
    }

    protected function getHeaderWidgets(): array
    {
        return [
            // You can add custom widgets here if needed
        ];
    }

    public function getTitle(): string
    {
        return 'View Post';
    }

    protected function getHeaderTabsColor(): string
    {
        return 'primary';
    }

    public static function getNavigationLabel(): string
    {
        return 'View Post';
    }
}
