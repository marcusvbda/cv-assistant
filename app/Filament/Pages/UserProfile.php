<?php

namespace App\Filament\Pages;

use Filament\Pages\Page;
use Filament\Forms;
use Auth;
use Filament\Notifications\Notification;

class UserProfile extends Page implements Forms\Contracts\HasForms
{
    use Forms\Concerns\InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';
    protected static ?string $navigationLabel = 'User Profile';
    protected static string $view = 'filament.pages.user-profile';
    protected static ?string $title = 'Edit User Profile';

    public $name;
    public $email;
    public $phones;
    public $introduction;
    public $links;
    public $position;
    public $skills;
    public $location;

    public function getBreadcrumbs(): array
    {
        return [
            url('/user-profile') => 'User Profile',
            'edit' => 'Edit',
        ];
    }

    public function mount(): void
    {
        $user = Auth::user();
        $this->name = $user->name;
        $this->email = $user->email;
        $this->introduction = $user->introduction;
        $this->location = $user->location;
        $this->position = $user->position;
        $this->phones = $user->phones()->get()->map(fn($x) => ['value' => $x->value])->toArray();
        $this->links = $user->links()->get()->map(fn($x) => ['value' => $x->value, 'name' => $x->name])->toArray();
        $this->skills = $user->skills()->get()->map(fn($x) => ['value' => $x->value, 'type' => $x->type])->toArray();
    }

    protected function getFormSchema(): array
    {
        return [
            Forms\Components\TextInput::make('name')->required(),
            Forms\Components\TextInput::make('position'),
            Forms\Components\TextInput::make('email')->email()->required()->disabled()->label('E-mail'),
            Forms\Components\TextInput::make('location'),
            Forms\Components\Textarea::make('introduction'),
            Forms\Components\Repeater::make('phones')->collapsible()->simple(
                Forms\Components\TextInput::make('value')->required()
            )->reorderableWithDragAndDrop(false),
            Forms\Components\Repeater::make('links')->schema([
                Forms\Components\TextInput::make('name')->required(),
                Forms\Components\TextInput::make('value')->label("Url")->required(),
            ])->columns(2)->reorderableWithDragAndDrop(false),
            Forms\Components\Repeater::make('skills')->schema([
                Forms\Components\TextInput::make('type')->required(),
                Forms\Components\TagsInput::make('value')->label("Skills")->required()->placeholder("Add a skill"),
            ])->columns(2)->reorderableWithDragAndDrop(false)
        ];
    }

    public function submit()
    {
        $data = $this->form->getState();
        $user = Auth::user();
        $user->phones()->delete();
        foreach ($this->phones as $phone) {
            $user->phones()->create([
                'value' => $phone['value'],
            ]);
        }

        $user->links()->delete();
        foreach ($this->links as $link) {
            $user->links()->create([
                'name' => $link['name'],
                'value' => $link['value'],
            ]);
        }
        $user->update($data);

        $user->skills()->delete();
        foreach ($this->skills as $skill) {
            $user->skills()->create([
                'type' => $skill['type'],
                'value' => $skill['value'],
            ]);
        }

        Notification::make()
            ->title('Saved successfully')
            ->success()
            ->send();
    }
}
