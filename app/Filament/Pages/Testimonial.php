<?php

namespace App\Filament\Pages;

use App\Models\Alumni;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Actions\Action;
use Filament\Support\Exceptions\Halt;
use Filament\Forms\Components\Section;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Components\Textarea;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use App\Models\Testimonial as TestimonialModel;
use Filament\Forms\Components\Hidden;
use Filament\Forms\Concerns\InteractsWithForms;
use Mokhosh\FilamentRating\Components\Rating;

class Testimonial extends Page implements HasForms
{
    use InteractsWithForms;

    public ?array $data = []; 

    public ?Alumni $ignoredUser = null;

    protected static ?string $navigationIcon = 'heroicon-s-chat-bubble-bottom-center-text';

    protected static ?string $title = 'Cerita Alumni';

    protected static string $view = 'filament.pages.testimonial';

    public function mount(): void
    {
        $loggedInAlumni = filament()->auth()->user();

        if ($loggedInAlumni) {
            $this->ignoredUser = $loggedInAlumni; 
            $this->form->fill(TestimonialModel::where('alumni_id', $loggedInAlumni->id)->first()?->toArray() ?? []);
        }
    }

    public function form(Form $form): Form
    {
        return $form
            ->model(TestimonialModel::class)
            ->schema([
                Section::make()
                ->columns([
                    'default' => 2,
                    'lg' => 12,
                ])
                ->schema([
                Hidden::make('show')
                    ->default(true),
                Textarea::make('content')
                    ->hint(fn ($state, $component) => 'Sisa ' . $component->getMaxLength() - strlen($state) . ' Karakter') 
                    ->maxlength(400) 
                    ->live()
                    ->label('Cerita')
                    ->rows(5)
                    ->required()
                    ->columnSpan([
                        'default' => 2,
                        'lg' => 12,
                    ]),
                Rating::make('rating')
                    ->required()
                    ->stars(5)
                    ->size('xl')
                ]),
            ])
            ->statePath('data');
    }

    protected function getFormActions(): array
    {
        return [
            Action::make('save')
                ->label('Simpan')
                ->submit('save'),
        ];
    }

    public function save(): void
    {
        try {
            $data = $this->form->getState();

            TestimonialModel::updateOrCreate(['alumni_id' => $this->ignoredUser->id], $data);
        } catch (Halt $exception) {
            return;
        }
    
        Notification::make()
            ->success()
            ->title('Data tersimpan')
            ->send();
    }
}