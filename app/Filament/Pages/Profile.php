<?php

namespace App\Filament\Pages;

use App\Models\Alumni;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Actions\Action;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Hash;
use Filament\Forms\Components\Select;
use Filament\Support\Exceptions\Halt;
use Filament\Forms\Components\Section;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Components\TextInput;
use Filament\Notifications\Notification;
use Filament\Forms\Components\FileUpload;
use Filament\Forms\Components\RichEditor;
use Filament\Forms\Concerns\InteractsWithForms;

class Profile extends Page implements HasForms
{
    use InteractsWithForms;

    public ?array $data = []; 

    public ?Alumni $ignoredUser = null;

    protected static ?string $navigationIcon = 'heroicon-s-user';

    protected static ?string $title = 'Profil';

    protected static string $view = 'filament.pages.profile';

    public function mount(): void
    {
        $loggedInAlumni = filament()->auth()->user();

        if ($loggedInAlumni) {
            $this->ignoredUser = $loggedInAlumni; 
            $this->form->fill(Alumni::find($loggedInAlumni->id)->toArray() ?? []);
        }
    }
    
    public function form(Form $form): Form
    {
        return $form
            ->model(Alumni::class)
            ->schema([
                Section::make()
                ->columns([
                    'default' => 2,
                    'lg' => 12,
                ])
                ->schema([
                    TextInput::make('username')
                        ->disabled()
                        ->unique(ignorable: $this->ignoredUser )
                        ->required()
                        ->maxLength(255)
                        ->columnSpan([
                            'default' => 2,
                            'lg' => 6,
                        ]),
                    TextInput::make('password')
                        ->helperText('Ubah password anda')
                        ->password()
                        ->revealable()
                        ->afterStateHydrated(function (TextInput $component, $state) {
                            $component->state('');
                        })
                        ->dehydrateStateUsing(fn (string $state): string => Hash::make($state))
                        ->dehydrated(fn (?string $state): bool => filled($state))
                        ->required(fn (string $operation): bool => $operation == 'create')
                        ->maxLength(255)
                        ->columnSpan([
                            'default' => 2,
                            'lg' => 6,
                        ]),
                ]),
                Section::make()
                ->columns([
                    'default' => 2,
                    'lg' => 12,
                ])
                ->schema([
                    TextInput::make('name')
                        ->label('Name')
                        ->required()
                        ->helperText('Masukkan nama dan gelar anda')
                        ->hint(fn ($state, $component) => 'Sisa ' . $component->getMaxLength() - strlen($state) . ' Karakter')
                        ->maxLength(28)
                        ->columnSpan([
                            'default' => 2,
                            'lg' => 12,
                        ]),
                    TextInput::make('class')
                        ->label('Tahun Lulus')
                        ->disabled()
                        ->required()
                        ->numeric()
                        ->maxLength(255)
                        ->columnSpan([
                            'default' => 2,
                            'lg' => 6,
                        ]),
                    Select::make('major_id')
                        ->label('Jurusan')
                        ->disabled()
                        ->native(false)
                        ->relationship('majors', 'name')
                        ->required()
                        ->columnSpan([
                            'default' => 2,
                            'lg' => 6,
                        ]),
                    FileUpload::make('photo')
                        ->required()
                        ->image()
                        ->label('Foto')
                        ->directory('/alumnis-tracer')
                        ->default('/default/alumni.svg')
                        ->columnSpan([
                            'default' => 2,
                            'lg' => 12,
                        ]),
                    TextInput::make('position')
                        ->label('Pekerjaan')
                        ->maxLength(255)
                        ->columnSpan([
                            'default' => 2,
                            'lg' => 6,
                        ]),
                    TextInput::make('company')
                        ->label('Instansi')
                        ->maxLength(255)
                        ->columnSpan([
                            'default' => 2,
                            'lg' => 6,
                        ]),
                ])
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

            Alumni::updateOrCreate(['id' => $this->ignoredUser->id], $data);
        } catch (Halt $exception) {
            return;
        }
    
        Notification::make()
            ->success()
            ->title('Data tersimpan')
            ->send();
    }
}
