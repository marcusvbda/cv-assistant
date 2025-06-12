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
    public $addresses;

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
        $this->addresses = $user->addresses()->get()->map(fn($x) => ['value' => $x->value])->toArray();
        $this->phones = $user->phones()->get()->map(fn($x) => ['value' => $x->value])->toArray();
        $this->links = $user->links()->get()->map(fn($x) => ['value' => $x->value, 'name' => $x->name])->toArray();
        $this->skills = $user->skills()->get()->map(fn($x) => ['value' => $x->value, 'type' => $x->type])->toArray();
    }

    protected function getFormSchema(): array
    {
        return [
            Forms\Components\Tabs::make('User Details')->tabs([
                Forms\Components\Tabs\Tab::make('General')->schema([
                    Forms\Components\TextInput::make('name')->required(),
                    Forms\Components\TextInput::make('position'),
                    Forms\Components\Textarea::make('introduction'),
                ]),
                Forms\Components\Tabs\Tab::make('Addresses')->schema([
                    Forms\Components\Repeater::make('addresses')->hiddenLabel()->simple(
                        Forms\Components\TextInput::make('value')->required()
                    )->reorderableWithDragAndDrop(false),
                ]),
                Forms\Components\Tabs\Tab::make('Contact')->schema([
                    Forms\Components\TextInput::make('email')->email()->required()->disabled()->label('E-mail'),
                    Forms\Components\Repeater::make('phones')->simple(
                        Forms\Components\TextInput::make('value')->required()
                    )->reorderableWithDragAndDrop(false),
                ]),
                Forms\Components\Tabs\Tab::make('Websites')->schema([
                    Forms\Components\Repeater::make('links')->hiddenLabel()->schema([
                        Forms\Components\TextInput::make('name')->required(),
                        Forms\Components\TextInput::make('value')->label("Url")->required(),
                    ])->columns(2)->reorderableWithDragAndDrop(false),
                ]),
                Forms\Components\Tabs\Tab::make('Skills')->schema([
                    Forms\Components\Repeater::make('skills')->hiddenLabel()->schema([
                        Forms\Components\TextInput::make('type')->required(),
                        Forms\Components\TagsInput::make('value')->label("Skills")->required()->placeholder("Add a skill"),
                    ])->columns(2)->reorderableWithDragAndDrop(false)
                ])
            ]),
        ];
    }

    public function submit()
    {
        $data = $this->form->getState();
        $user = Auth::user();
        $this->syncHasMany($user, 'phones', ['value']);
        $this->syncHasMany($user, 'links', ['name', 'value']);
        $this->syncHasMany($user, 'skills', ['type', 'value']);
        $this->syncHasMany($user, 'addresses', ['value']);
        $user->update($data);

        Notification::make()
            ->title('Saved successfully')
            ->success()
            ->send();
    }

    private function syncHasMany($user, string $relation, array $fields): void
    {
        $user->$relation()->delete();

        $items = $this->$relation ?? [];

        if (count($items)) {
            $data = array_map(fn($item) => collect($item)->only($fields)->toArray(), $items);
            $user->$relation()->createMany($data);
        }
    }
}
