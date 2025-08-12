<?php

namespace App\Filament\Pages;

use App\Models\User;
use Filament\Pages\Page;
use Filament\Forms;
use Auth;
use Carbon\Carbon;
use Filament\Notifications\Notification;

class UserDetails extends Page implements Forms\Contracts\HasForms
{
    use Forms\Concerns\InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-user-circle';
    protected static string $view = 'filament.pages.user-details';

    public $name;
    public $email;
    public $phones;
    public $introduction;
    public User $user;
    public $formState;

    public function getTitle(): string
    {
        return __('User details');
    }

    public static function getNavigationLabel(): string
    {
        return __('User details');
    }

    public function getBreadcrumbs(): array
    {
        return [
            url('/user-profile') => __('User details'),
            'edit' => __('Edit')
        ];
    }

    protected function getHeaderActions(): array
    {
        $actions = [];
        return $actions;
    }

    public function mount(): void
    {
        $this->user = Auth::user();
        $this->formState = [
            'name' => $this->user->name,
            'email' => $this->user->email,
            'introduction' => $this->user->introduction,
            'linkedin' => $this->user->linkedin,
            'position' => $this->user->position,
            'ai_integration' => [
                'provider' => $this->user->ai_integration->provider ?? null,
                'key' => $this->user->ai_integration->key ?? null,
            ],
            'phones' => User::mapRelationToArray($this->user->phones(), ['type', 'number']),
            'addresses' => User::mapRelationToArray($this->user->addresses(), ['city', 'location']),
            'links' =>  User::mapRelationToArray($this->user->links(), ['name', 'value']),
            'skills' => User::mapRelationToArray($this->user->skills(), ['type', 'value']),
            'courses' => User::mapRelationToArray($this->user->courses(), ['instituition', 'start_date', 'end_date', 'name'], function ($row) {
                $row["start_date"] = @$row["start_date"] ? Carbon::parse($row["start_date"])->format('Y-m-d') : null;
                $row["end_date"] = @$row["end_date"] ? Carbon::parse($row["end_date"])->format('Y-m-d')  : null;
                return $row;
            }),
            'experiences' =>  User::mapRelationToArray($this->user->experiences(), ['position', 'company', 'description', 'start_date', 'end_date'], function ($row) {
                $row["start_date"] = @$row["start_date"] ? Carbon::parse($row["start_date"])->format('Y-m-d') : null;
                $row["end_date"] = @$row["end_date"] ? Carbon::parse($row["end_date"])->format('Y-m-d')  : null;
                return $row;
            }),
            'projects' => User::mapRelationToArray($this->user->projects(), ['name', 'description', 'start_date', 'end_date'], function ($row) {
                $row["start_date"] = @$row["start_date"] ? Carbon::parse($row["start_date"])->format('Y-m-d') : null;
                $row["end_date"] = @$row["end_date"] ? Carbon::parse($row["end_date"])->format('Y-m-d')  : null;
                return $row;
            }),
            'certificates' => User::mapRelationToArray($this->user->certificates(), ['name', 'description', 'date'], function ($row) {
                $row["date"] = @$row["date"] ? Carbon::parse($row["date"])->format('Y-m-d') : null;
                return $row;
            })
        ];
    }

    protected function getFormSchema(): array
    {
        return [
            Forms\Components\Tabs::make('User details')->statePath('formState')->tabs([
                Forms\Components\Tabs\Tab::make(__('General'))->schema([
                    Forms\Components\TextInput::make('name')->label(__("Name"))->required(),
                    Forms\Components\TextInput::make('position')->label(__("Position"))->required(),
                    Forms\Components\TextInput::make('linkedin')->url(),
                    Forms\Components\Textarea::make('introduction')->label(__("Introduction"))->rows(5),
                ]),
                Forms\Components\Tabs\Tab::make(__('Addresses'))->schema([
                    Forms\Components\Repeater::make('addresses')->hiddenLabel()
                        ->addActionLabel(__('Add address'))
                        ->schema([
                            Forms\Components\TextInput::make('location')->label(__("Location"))->required(),
                            Forms\Components\TextInput::make('city')->label(__("City"))->required(),
                        ])->columns(2)->reorderableWithDragAndDrop(false)
                ]),
                Forms\Components\Tabs\Tab::make(__('Contacts'))->schema([
                    Forms\Components\TextInput::make('email')->email()->required()->disabled()->label('E-mail'),
                    Forms\Components\Repeater::make('phones')->label(__("Phones"))->addActionLabel(__('Add phone'))->schema([
                        Forms\Components\TextInput::make('type')->label(__("Type"))->required(),
                        Forms\Components\TextInput::make('number')->label(__("Number"))->required(),
                    ])->columns(2)->reorderableWithDragAndDrop(false),
                ]),
                Forms\Components\Tabs\Tab::make('Websites')->schema([
                    Forms\Components\Repeater::make('links')->hiddenLabel()->schema([
                        Forms\Components\TextInput::make('name')->label(__("Name"))->required(),
                        Forms\Components\TextInput::make('value')->label("Url")->required(),
                    ])->columns(2)->reorderableWithDragAndDrop(false),
                ]),
                Forms\Components\Tabs\Tab::make('Skills')->label(__("Skills"))->schema([
                    Forms\Components\Repeater::make('skills')->addActionLabel(__('Add skill'))->hiddenLabel()->schema([
                        Forms\Components\TextInput::make('type')->label(__("Type"))->required(),
                        Forms\Components\TagsInput::make('value')->label(__("Skills"))->required()
                    ])->columns(2)->reorderableWithDragAndDrop(false)
                ]),
                Forms\Components\Tabs\Tab::make('Education')->label(__("Education"))->schema([
                    Forms\Components\Repeater::make('courses')->hiddenLabel()->addActionLabel(__('Add course'))->schema([
                        Forms\Components\TextInput::make('name')->label(__("Name"))->required(),
                        Forms\Components\TextInput::make('instituition')->label(__("Instituition"))->required(),
                        Forms\Components\DatePicker::make('start_date')->label(__("Start date"))->required(),
                        Forms\Components\DatePicker::make('end_date')->label(__("End date")),
                    ])->columns(2)->reorderableWithDragAndDrop(false),
                ]),
                Forms\Components\Tabs\Tab::make(__('Experience'))->schema([
                    Forms\Components\Repeater::make('experiences')->hiddenLabel()->addActionLabel(__('Add experience'))->schema([
                        Forms\Components\Section::make()->schema([
                            Forms\Components\TextInput::make('position')->label(__("Position"))->required(),
                            Forms\Components\TextInput::make('company')->label("Company")->required(),
                            Forms\Components\DatePicker::make('start_date')->label(__("Start date"))->required(),
                            Forms\Components\DatePicker::make('end_date')->label(__("End date")),
                        ])->columns(2),
                        Forms\Components\Textarea::make('description')->label(__("Description"))->rows(5)->required(),
                    ])->reorderableWithDragAndDrop(false),
                ]),
                Forms\Components\Tabs\Tab::make(__("Projects"))->schema([
                    Forms\Components\Repeater::make('projects')->addActionLabel(__('Add project'))->hiddenLabel()->schema([
                        Forms\Components\Grid::make()->schema([
                            Forms\Components\TextInput::make('name')->label(__("Name"))->required(),
                            Forms\Components\DatePicker::make('start_date')->label(__("Start date"))->required(),
                            Forms\Components\DatePicker::make('end_date')->label(__("End date")),
                        ])->columns(2),
                        Forms\Components\Textarea::make('description')->label(__("Description"))->rows(5)->required(),
                    ])->reorderableWithDragAndDrop(false),
                ]),
                Forms\Components\Tabs\Tab::make(__("Certificates"))->schema([
                    Forms\Components\Repeater::make('certificates')->addActionLabel(__('Add certificate'))->hiddenLabel()->schema([
                        Forms\Components\Section::make()->schema([
                            Forms\Components\TextInput::make('name')->label(__("Name"))->required(),
                            Forms\Components\DatePicker::make('date')->label(__("Date"))->required(),
                        ])->columns(2),
                        Forms\Components\Textarea::make('description')->label(__("Description"))->rows(5)->required(),
                    ])->reorderableWithDragAndDrop(false),
                ])
            ]),
        ];
    }

    public function submit()
    {
        $ai_integration = data_get($this->formState, 'ai_integration');

        $this->user = Auth::user();
        $this->syncHasMany('phones', ['type', 'number']);
        $this->syncHasMany('links', ['name', 'value']);
        $this->syncHasMany('skills', ['type', 'value']);
        $this->syncHasMany('addresses', ['location', 'city']);
        $this->syncHasMany('courses', ['name', 'instituition', 'start_date', 'end_date']);
        $this->syncHasMany('experiences', ['position', 'company', 'description', 'start_date', 'end_date']);
        $this->syncHasMany('projects', ['name', 'description', 'start_date', 'end_date']);
        $this->syncHasMany('certificates', ['name', 'description', 'date']);
        unset($this->formState["email"]);
        $this->user->update($this->formState);

        Notification::make()
            ->title('Saved successfully')
            ->success()
            ->send();

        return redirect(static::getUrl());
    }

    private function syncHasMany(string $relation, array $fields): void
    {
        $this->user->$relation()->delete();
        $items = data_get($this->formState, $relation, []);
        if (!count($items)) {
            return;
        }

        $isMorph = method_exists($this->user, $relation) && method_exists($this->user->$relation(), 'getMorphType');
        $morphType = $isMorph ? $this->user->$relation()->getMorphType() : null;
        $morphId = $isMorph ? $this->user->$relation()->getForeignKeyName() : null;

        $data = array_map(function ($item) use ($fields, $isMorph, $morphType, $morphId) {
            $base = collect($item)->only($fields)->toArray();

            if ($isMorph) {
                $base[$morphType] = get_class($this->user);
                $base[$morphId] = $this->user->getKey();
            }

            return $base;
        }, $items);
        $this->user->$relation()->createMany(array_values($data));
    }
}
