<?php

namespace App\Filament\Pages;

use App\Services\AIService;
use Filament\Pages\Page;
use Filament\Forms;
use Auth;
use Carbon\Carbon;
use Filament\Notifications\Notification;

class UserProfile extends Page implements Forms\Contracts\HasForms
{
    use Forms\Concerns\InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-user-circle';
    protected static ?string $navigationLabel = 'User Profile';
    protected static string $view = 'filament.pages.user-profile';
    protected static ?string $title = 'Edit User Profile';

    public $name;
    public $email;
    public $phones;
    public $introduction;
    public $links;
    public $position;
    public $ai_integration;
    public $integrations;
    public $skills;
    public $experiences;
    public $certificates;
    public $projects;
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
        $this->ai_integration = [
            'provider' => $user->ai_integration->provider ?? null,
            'key' => $user->ai_integration->key ?? null,
        ];
        $this->phones = $this->mapRelationToArray($user->phones(), ['type', 'number']);
        $this->addresses = $this->mapRelationToArray($user->addresses(), ['city', 'location']);
        $this->links = $this->mapRelationToArray($user->links(), ['name', 'value']);
        $this->skills = $this->mapRelationToArray($user->skills(), ['type', 'value']);
        $this->courses = $this->mapRelationToArray($user->courses(), ['instituition', 'start_date', 'end_date', 'name'], function ($row) {
            $row["start_date"] = @$row["start_date"] ? Carbon::parse($row["start_date"])->format('Y-m-d') : null;
            $row["end_date"] = @$row["end_date"] ? Carbon::parse($row["end_date"])->format('Y-m-d')  : null;
            return $row;
        });
        $this->experiences = $this->mapRelationToArray($user->experiences(), ['position', 'company', 'description', 'start_date', 'end_date'], function ($row) {
            $row["start_date"] = @$row["start_date"] ? Carbon::parse($row["start_date"])->format('Y-m-d') : null;
            $row["end_date"] = @$row["end_date"] ? Carbon::parse($row["end_date"])->format('Y-m-d')  : null;
            return $row;
        });
        $this->projects = $this->mapRelationToArray($user->projects(), ['name', 'description', 'start_date', 'end_date'], function ($row) {
            $row["start_date"] = @$row["start_date"] ? Carbon::parse($row["start_date"])->format('Y-m-d') : null;
            $row["end_date"] = @$row["end_date"] ? Carbon::parse($row["end_date"])->format('Y-m-d')  : null;
            return $row;
        });
        $this->certificates = $this->mapRelationToArray($user->certificates(), ['name', 'description', 'date'], function ($row) {
            $row["date"] = @$row["date"] ? Carbon::parse($row["date"])->format('Y-m-d') : null;
            return $row;
        });
    }

    private function mapRelationToArray($relation, $fieds, $callback = null): array
    {
        return $relation->get()->map(function ($item) use ($fieds, $callback) {
            $values = collect($item)->only($fieds)->toArray();
            return is_callable($callback) ? $callback($values) : $values;
        })->toArray();
    }

    protected function getFormSchema(): array
    {
        return [
            Forms\Components\Tabs::make('User Details')->tabs([
                Forms\Components\Tabs\Tab::make('General')->schema([
                    Forms\Components\TextInput::make('name')->required(),
                    Forms\Components\TextInput::make('position'),
                    Forms\Components\Textarea::make('introduction')->rows(5),
                    Forms\Components\Actions::make([
                        Forms\Components\Actions\Action::make('fillIntruductionWithAI')
                            ->label('Fill with AI')
                            ->icon('heroicon-m-sparkles')
                            ->action(function () {
                                $state = $this->form->getState();
                                unset($state["ai_integration"]);
                                unset($state["introduction"]);
                                $service = AIService::make()->user('You write polished, concise first-person resume summaries without mention companies name. Reply ONLY with the summary in english with around 130 words, no extra text.')
                                    ->user(json_encode($state));
                                $this->introduction = $service->generate();
                            })
                            ->disabled(fn() => empty(data_get($this->ai_integration ?? [], "key")))
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
                Forms\Components\Tabs\Tab::make('Experience')->schema([
                    Forms\Components\Repeater::make('experiences')->hiddenLabel()->schema([
                        Forms\Components\Section::make()->schema([
                            Forms\Components\TextInput::make('position')->required(),
                            Forms\Components\TextInput::make('company')->required(),
                            Forms\Components\DatePicker::make('start_date')->required(),
                            Forms\Components\DatePicker::make('end_date'),
                        ])->columns(2),
                        Forms\Components\Textarea::make('description')->rows(5)->required(),
                        Forms\Components\Actions::make([
                            Forms\Components\Actions\Action::make('fillExperienceWithAI')
                                ->label('Fill with AI')
                                ->icon('heroicon-m-sparkles')
                                ->action(function (array $arguments, Forms\Set $set, Forms\Get $get) {
                                    $service = AIService::make()->user('You write polished, concise description of a job experience. Reply ONLY with the desciption in english, no extra text.')
                                        ->user(json_encode([
                                            'position' => $get('position'),
                                            'company' => $get('company')
                                        ]));
                                    $set('description', $service->generate());
                                })
                                ->disabled(fn() => empty(data_get($this->ai_integration ?? [], "key")))
                        ])

                    ])->reorderableWithDragAndDrop(false),
                ]),
                Forms\Components\Tabs\Tab::make('Projects')->schema([
                    Forms\Components\Repeater::make('projects')->hiddenLabel()->schema([
                        Forms\Components\Section::make()->schema([
                            Forms\Components\TextInput::make('name')->required(),
                            Forms\Components\DatePicker::make('start_date')->required(),
                            Forms\Components\DatePicker::make('end_date'),
                        ])->columns(2),
                        Forms\Components\Textarea::make('description')->rows(5)->required(),
                    ])->reorderableWithDragAndDrop(false),
                ]),
                Forms\Components\Tabs\Tab::make('Certificates')->schema([
                    Forms\Components\Repeater::make('certificates')->hiddenLabel()->schema([
                        Forms\Components\Section::make()->schema([
                            Forms\Components\TextInput::make('name')->required(),
                            Forms\Components\DatePicker::make('date')->required(),
                        ])->columns(2),
                        Forms\Components\Textarea::make('description')->rows(5)->required(),
                    ])->reorderableWithDragAndDrop(false),
                ]),
                Forms\Components\Tabs\Tab::make('Settings')->schema([
                    Forms\Components\Tabs::make('settings_items')->hiddenLabel()->tabs([
                        Forms\Components\Tabs\Tab::make('AI Integration')->schema([
                            Forms\Components\Select::make('ai_integration.provider')
                                ->label('Provider')
                                ->options([
                                    'groq' => 'GROQ',
                                ])
                                ->required(),
                            Forms\Components\TextInput::make('ai_integration.key')
                                ->label('API Key')
                                ->required()
                            // ->password(),
                        ])->columns(2),
                    ])
                ])
            ]),
        ];
    }

    public function submit()
    {
        $data = $this->form->getState();

        $ai_integration = data_get($data, 'ai_integration');
        $provider = data_get($ai_integration, 'provider');
        $key = data_get($ai_integration, 'key');

        if ($provider && $key) {
            try {
                $testKey = uniqid();
                $service = AIService::make()
                    ->setProvider($provider)
                    ->setKey($key)
                    ->user('Say just "ok"')
                    ->user("teste[$testKey]");

                $response = $service->generate($key, $provider);
                if (trim(strtolower($response)) !== 'ok') {
                    throw new \Exception('Invalid response from AI service');
                }
            } catch (\Throwable $e) {
                Notification::make()
                    ->title('Invalid API Key')
                    ->body('The AI Integration Key appears to be invalid: ' . $e->getMessage())
                    ->danger()
                    ->send();
                return;
            }
        }

        $user = Auth::user();
        $this->syncHasMany($user, 'phones', ['type', 'number']);
        $this->syncHasMany($user, 'links', ['name', 'value']);
        $this->syncHasMany($user, 'skills', ['type', 'value']);
        $this->syncHasMany($user, 'addresses', ['location', 'city']);
        $this->syncHasMany($user, 'courses', ['name', 'instituition', 'start_date', 'end_date']);
        $this->syncHasMany($user, 'experiences', ['position', 'company', 'description', 'start_date', 'end_date']);
        $this->syncHasMany($user, 'projects', ['name', 'description', 'start_date', 'end_date']);
        $this->syncHasMany($user, 'certificates', ['name', 'description', 'date']);
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
