<?php

namespace App\Filament\Pages;

use App\Services\AIService;
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
    public $courses;

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
        $this->position = $user->position;
        $this->phones = $this->mapRelationToArray($user->phones(), ['type', 'number']);
        $this->addresses = $this->mapRelationToArray($user->addresses(), ['city', 'location']);
        $this->links = $this->mapRelationToArray($user->links(), ['name', 'value']);
        $this->skills = $this->mapRelationToArray($user->skills(), ['type', 'value']);
        $this->courses = $this->mapRelationToArray($user->courses(), ['instituition', 'start_date', 'end_date', 'name']);
    }

    private function mapRelationToArray($relation, $fieds): array
    {
        return $relation->get()->map(function ($item) use ($fieds) {
            return collect($item)->only($fieds)->toArray();
        })->toArray();
    }

    protected function getFormSchema(): array
    {
        return [
            Forms\Components\Tabs::make('User Details')->tabs([
                Forms\Components\Tabs\Tab::make('General')->schema([
                    Forms\Components\TextInput::make('name')->required(),
                    Forms\Components\TextInput::make('position'),
                    Forms\Components\Textarea::make('introduction'),
                    Forms\Components\Actions::make([
                        Forms\Components\Actions\Action::make('fillIntruductionWithAI')
                            ->label('Fill with AI')
                            ->icon('heroicon-m-sparkles')
                            ->action(function () {
                                $state = $this->form->getState();
                                unset($state["introduction"]);
                                $service = AIService::make()->system('You write polished, concise first-person resume summaries. Reply ONLY with the summary, no extra text.')
                                    ->user('You write polished, concise first-person resume summaries.')
                                    ->user(json_encode($state));
                                $this->introduction = $service->generate();
                            })
                    ])
                ]),
                Forms\Components\Tabs\Tab::make('Addresses')->schema([
                    Forms\Components\Repeater::make('addresses')->hiddenLabel()->schema([
                        Forms\Components\TextInput::make('location')->required(),
                        Forms\Components\TextInput::make('city')->required(),
                    ])->columns(2)->reorderableWithDragAndDrop(false),
                ]),
                Forms\Components\Tabs\Tab::make('Contact')->schema([
                    Forms\Components\TextInput::make('email')->email()->required()->disabled()->label('E-mail'),
                    Forms\Components\Repeater::make('phones')->schema([
                        Forms\Components\TextInput::make('type')->required(),
                        Forms\Components\TextInput::make('number')->required(),
                    ])->columns(2)->reorderableWithDragAndDrop(false),
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
                ]),
                Forms\Components\Tabs\Tab::make('Education')->schema([
                    Forms\Components\Repeater::make('courses')->hiddenLabel()->schema([
                        Forms\Components\TextInput::make('name')->required(),
                        Forms\Components\TextInput::make('instituition')->required(),
                        Forms\Components\DatePicker::make('start_date')->required(),
                        Forms\Components\DatePicker::make('end_date'),
                    ])->columns(2)->reorderableWithDragAndDrop(false),
                ]),
            ]),
        ];
    }

    public function submit()
    {
        $data = $this->form->getState();
        $user = Auth::user();
        $this->syncHasMany($user, 'phones', ['type', 'number']);
        $this->syncHasMany($user, 'links', ['name', 'value']);
        $this->syncHasMany($user, 'skills', ['type', 'value']);
        $this->syncHasMany($user, 'addresses', ['location', 'city']);
        $this->syncHasMany($user, 'courses', ['name', 'instituition', 'start_date', 'end_date']);
        unset($data["email"]);
        $user->update($data);

        Notification::make()
            ->title('Saved successfully')
            ->success()
            ->send();
    }

    private function syncHasMany($user, string $relation, array $fields): void
    {
        $user->$relation()->delete();

        $items = data_get($this->form->getState(), $relation, []);

        if (!count($items)) {
            return;
        }

        $isMorph = method_exists($user, $relation) && method_exists($user->$relation(), 'getMorphType');
        $morphType = $isMorph ? $user->$relation()->getMorphType() : null;
        $morphId = $isMorph ? $user->$relation()->getForeignKeyName() : null;

        $data = array_map(function ($item) use ($fields, $isMorph, $morphType, $morphId, $user) {
            $base = collect($item)->only($fields)->toArray();

            if ($isMorph) {
                $base[$morphType] = get_class($user);
                $base[$morphId] = $user->getKey();
            }

            return $base;
        }, $items);

        $user->$relation()->createMany($data);
    }
}
