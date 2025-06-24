<?php

namespace App\Filament\Pages;

use App\Filament\Widgets\AlertNoAiIntegration;
use App\Models\User;
use App\Services\AIService;
use Filament\Pages\Page;
use Filament\Actions;
use Filament\Forms;
use Auth;
use Carbon\Carbon;
use Filament\Notifications\Notification;

class UserDetails extends Page implements Forms\Contracts\HasForms
{
    use Forms\Concerns\InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-user-circle';
    protected static ?string $navigationLabel = 'User Details';
    protected static string $view = 'filament.pages.user-details';
    protected static ?string $title = 'Edit User Details';

    public $name;
    public $email;
    public $phones;
    public $introduction;
    public User $user;
    public $formState;

    public function getBreadcrumbs(): array
    {
        return [
            url('/user-profile') => 'User Details',
            'edit' => 'Edit',
        ];
    }

    protected function getHeaderActions(): array
    {
        $actions = [];
        if ($this->user->hasAiIntegration()) {
            $actions[] = Actions\Action::make('improveWithAI')
                ->label('Improve with AI')
                ->icon('heroicon-m-sparkles')
                ->action(fn() => $this->processAIImprovement());
        }

        return $actions;
    }

    public function getHeaderWidgets(): array
    {
        return [
            AlertNoAiIntegration::class,
        ];
    }

    public function processAIImprovement()
    {
        $state = $this->formState;
        unset($state["name"]);
        unset($state["ai_integration"]);
        unset($state["email"]);

        $format = collect($state)->map(fn($value) => gettype(($value)))->toArray();
        $service = AIService::make()
            ->user('Improve or fix grammatically this dataset values (keep the JSON format) to create a polished, concise first-person resume summary optimized for ATS.')
            ->user('Required fields: introduction, addresses (location, city), phones (type, number), links (name, value), skills (type, value (array of string)), courses (name, instituition, start_date, end_date), experiences (position, company, description, start_date, end_date), projects (name, description, start_date, end_date), certificates (name, description, date).')
            ->user('if a field is not present, do not add it (except skills), just improve the existing ones.')
            ->user(json_encode($state));

        $suggestion = $service->json($format)->generate();

        $this->formState = array_merge($this->formState, $suggestion);

        Notification::make()
            ->title('Suggestion from AI Service filled, please review and save if you like it.')
            ->success()
            ->send();
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
            'phones' => $this->mapRelationToArray($this->user->phones(), ['type', 'number']),
            'addresses' => $this->mapRelationToArray($this->user->addresses(), ['city', 'location']),
            'links' =>  $this->mapRelationToArray($this->user->links(), ['name', 'value']),
            'skills' => $this->mapRelationToArray($this->user->skills(), ['type', 'value']),
            'courses' => $this->mapRelationToArray($this->user->courses(), ['instituition', 'start_date', 'end_date', 'name'], function ($row) {
                $row["start_date"] = @$row["start_date"] ? Carbon::parse($row["start_date"])->format('Y-m-d') : null;
                $row["end_date"] = @$row["end_date"] ? Carbon::parse($row["end_date"])->format('Y-m-d')  : null;
                return $row;
            }),
            'experiences' =>  $this->mapRelationToArray($this->user->experiences(), ['position', 'company', 'description', 'start_date', 'end_date'], function ($row) {
                $row["start_date"] = @$row["start_date"] ? Carbon::parse($row["start_date"])->format('Y-m-d') : null;
                $row["end_date"] = @$row["end_date"] ? Carbon::parse($row["end_date"])->format('Y-m-d')  : null;
                return $row;
            }),
            'projects' => $this->mapRelationToArray($this->user->projects(), ['name', 'description', 'start_date', 'end_date'], function ($row) {
                $row["start_date"] = @$row["start_date"] ? Carbon::parse($row["start_date"])->format('Y-m-d') : null;
                $row["end_date"] = @$row["end_date"] ? Carbon::parse($row["end_date"])->format('Y-m-d')  : null;
                return $row;
            }),
            'certificates' => $this->mapRelationToArray($this->user->certificates(), ['name', 'description', 'date'], function ($row) {
                $row["date"] = @$row["date"] ? Carbon::parse($row["date"])->format('Y-m-d') : null;
                return $row;
            })
        ];
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
            Forms\Components\Tabs::make('User Details')->statePath('formState')->tabs([
                Forms\Components\Tabs\Tab::make('General')->schema([
                    Forms\Components\TextInput::make('name')->required(),
                    Forms\Components\TextInput::make('position'),
                    Forms\Components\TextInput::make('linkedin')->url(),
                    Forms\Components\Textarea::make('introduction')->rows(5),
                    Forms\Components\Actions::make([
                        Forms\Components\Actions\Action::make('fillIntruductionWithAI')
                            ->label('Fill with AI')
                            ->icon('heroicon-m-sparkles')
                            ->action(function () {
                                $state = $this->formState;
                                unset($state["ai_integration"]);
                                unset($state["introduction"]);
                                $service = AIService::make()->user('You write polished, concise first-person resume summaries otimized to ATS without mention companies name. Reply ONLY with the summary in english with around 130 words, no extra text.')
                                    ->user(json_encode($state));
                                $this->formState["introduction"] = $service->generate();
                            })
                            ->disabled(fn() => !$this->user->hasAiIntegration())
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
                                    $service = AIService::make()->user('You write polished, concise description of the job experience otimized to ATS. Reply ONLY with the desciption in english, no extra text.')
                                        ->user(json_encode([
                                            'position' => $get('position'),
                                            'company' => $get('company')
                                        ]));
                                    $set('description', $service->generate());
                                })
                                ->disabled(fn() => !$this->user->hasAiIntegration())
                        ])

                    ])->reorderableWithDragAndDrop(false),
                ]),
                Forms\Components\Tabs\Tab::make('Projects')->schema([
                    Forms\Components\Repeater::make('projects')->hiddenLabel()->schema([
                        Forms\Components\Grid::make()->schema([
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
                                ]),
                            Forms\Components\TextInput::make('ai_integration.key')
                                ->label('API Key')
                                ->password()
                                ->revealable()
                        ])->columns(2),
                    ])
                ])
            ]),
        ];
    }

    public function submit()
    {
        $ai_integration = data_get($this->formState, 'ai_integration');
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
