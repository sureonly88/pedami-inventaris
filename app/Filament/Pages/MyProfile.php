<?php

namespace App\Filament\Pages;

use Filament\Forms\Components\Section;
use Filament\Forms\Components\TextInput;
use Filament\Forms\Form;
use Filament\Pages\Page;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Concerns\InteractsWithForms;
use Illuminate\Support\Facades\Hash;
use Filament\Notifications\Notification;

class MyProfile extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-user-circle';
    
    protected static ?string $title = 'Profil Saya';
    
    protected static ?string $navigationLabel = 'Profil Saya';
    
    protected static ?int $navigationSort = 100;

    protected static string $view = 'filament.pages.my-profile';

    public ?array $data = [];

    public function mount(): void
    {
        $this->form->fill([
            'name' => auth()->user()->name,
            'email' => auth()->user()->email,
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Informasi Pribadi')
                    ->schema([
                        TextInput::make('name')
                            ->label('Nama Lengkap')
                            ->required(),
                        TextInput::make('email')
                            ->label('Alamat Email')
                            ->email()
                            ->required(),
                    ])->columns(2),

                Section::make('Ubah Password')
                    ->description('Kosongkan jika tidak ingin mengubah password.')
                    ->schema([
                        TextInput::make('current_password')
                            ->label('Password Saat Ini')
                            ->password()
                            ->currentPassword(),
                        TextInput::make('new_password')
                            ->label('Password Baru')
                            ->password()
                            ->minLength(8)
                            ->same('password_confirmation'),
                        TextInput::make('password_confirmation')
                            ->label('Konfirmasi Password Baru')
                            ->password(),
                    ])->columns(3),
            ])
            ->statePath('data');
    }

    public function save()
    {
        $data = $this->form->getState();

        $user = auth()->user();

        // Update basic info
        $user->name = $data['name'];
        $user->email = $data['email'];

        // Update password if provided
        if (!empty($data['new_password'])) {
            $user->password = $data['new_password']; // the mutator handles hash if 'hashed' cast is present
        }

        $user->save();

        Notification::make() 
            ->success()
            ->title('Profil berhasil diperbarui!')
            ->send();
            
        // Reset password fields
        $this->form->fill([
            'name' => $user->name,
            'email' => $user->email,
            'current_password' => null,
            'new_password' => null,
            'password_confirmation' => null,
        ]);
    }
}
