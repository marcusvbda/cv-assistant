<?php

namespace App\Filament\Resources;

use App\Enums\JobDescriptionAnalysisStatusEnum;
use App\Enums\JobDescriptionAnalysisTypeEnum;
use App\Filament\Resources\JobDescriptionAnalysisResource\Pages;
use App\Models\JobDescriptionAnalysis;
use App\Tables\Columns\ProgressColumn;
use Filament\Forms;
use Filament\Forms\Components\Section;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Table;
use Auth;

class JobDescriptionAnalysisResource extends Resource
{
    protected static ?string $model = JobDescriptionAnalysis::class;
    protected static ?string $navigationIcon = 'heroicon-o-document-text';
    protected int|string|array $columnSpan = 'full';
    public static array $descriptionTypes = [
        'job_description' => 'Job Description content',
        'url' => 'Job Description URL',
    ];

    public static function getModelLabel(): string
    {
        return __("Job Description Analyse");
    }

    public static function getPluralModelLabel(): string
    {
        return __("Job Description Analyses");
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()
                    ->schema([
                        Forms\Components\View::make('components.job-fit-preview')
                            ->visibleOn('view')
                            ->viewData(fn($record) => [
                                'item' => $record,
                                'user' => Auth::user(),
                            ]),
                        Forms\Components\TextInput::make('name')
                            ->required()
                            ->maxLength(255),
                        Forms\Components\Select::make('description_type')
                            ->label('Description Type')
                            ->options(function () {
                                $values = JobDescriptionAnalysisTypeEnum::values();
                                if (Auth::user()->hasAiIntegration()) return $values;
                                unset($values['JOB_DESCRIPTION_URL']);
                                return $values;
                            })
                            ->required()
                            ->default('job_description')
                            ->reactive(),
                        Forms\Components\Textarea::make('description')
                            ->label('Job Description')
                            ->maxLength(5000)
                            ->rows(8)
                            ->visible(fn(callable $get) => $get('description_type') === JobDescriptionAnalysisTypeEnum::JOB_DESCRIPTION->name)
                            ->required(fn(callable $get) => $get('description_type') === JobDescriptionAnalysisTypeEnum::JOB_DESCRIPTION->name),
                        Forms\Components\TextInput::make('description')
                            ->label('Job Description URL')
                            ->url()
                            ->maxLength(2048)
                            ->visible(fn(callable $get) => $get('description_type') === JobDescriptionAnalysisTypeEnum::JOB_DESCRIPTION_URL->name)
                            ->required(fn(callable $get) => $get('description_type') === JobDescriptionAnalysisTypeEnum::JOB_DESCRIPTION_URL->name),
                    ]),
            ])->columns(1);
    }

    protected function getTablePollingInterval(): ?int
    {
        return 2000;
    }

    public static function getResourceTableCols($sortable = true): array
    {
        return [
            Tables\Columns\TextColumn::make('name')
                ->label('Job Title')
                ->searchable()
                ->sortable($sortable),
            Tables\Columns\TextColumn::make('status')
                ->label('Fit Calculation Status')
                ->sortable($sortable)
                ->formatStateUsing(fn(string $state) => JobDescriptionAnalysisStatusEnum::from($state)->label())
                ->color(fn(string $state) => JobDescriptionAnalysisStatusEnum::from($state)->color())
                ->icons([
                    'heroicon-o-arrow-path' => fn($state) => $state === JobDescriptionAnalysisStatusEnum::IN_PROGRESS->name,
                ])
                ->extraAttributes(['class' => 'column-animate-spin'])
                ->iconPosition('after')
                ->searchable(),
            ProgressColumn::make('progress')->getStateUsing(function ($record) {
                if ($record->status !== JobDescriptionAnalysisStatusEnum::COMPLETED->name) {
                    return JobDescriptionAnalysisStatusEnum::from($record->status)->description();
                }

                $details = $record?->jobApplyDetail()?->first();
                $percentageFit = is_numeric($details?->percentage_fit) ? (int) $details->percentage_fit : 0;
                $routeResume = route('download.pdf', ['jobApplyDetail' => $details->id, 'type' => 'resume']);
                $routeCoverLetter = route('download.pdf', ['jobApplyDetail' => $details->id, 'type' => 'cover_letter']);
                return [
                    "percentage" => $percentageFit,
                    "links" => <<<HTML
                        <div class="text-sm text-gray-600 flex flex-col md:flex-row gap-4">
                            <a class="text-primary-600" target="_blank" href="$routeResume">Resume</a>
                            <a class="text-primary-600" target="_blank" href="$routeCoverLetter">Cover letter</a>
                        </div>
                    HTML
                ];
            })->label('Percentage Fit')->disabledClick(),
            Tables\Columns\TextColumn::make('updated_at')
                ->label('Updated At')
                ->dateTime()
                ->sortable($sortable),
            Tables\Columns\TextColumn::make('created_at')
                ->label('Created At')
                ->dateTime()
                ->sortable($sortable),
        ];
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns(static::getResourceTableCols())
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\ViewAction::make(),
                Tables\Actions\EditAction::make(),
                Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ])->poll("5s")
            ->defaultSort('id', 'desc');
    }

    public static function getRelations(): array
    {
        return [
            //
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListJobDescriptionAnalyses::route('/'),
            'create' => Pages\CreateJobDescriptionAnalysis::route('/create'),
            'edit' => Pages\EditJobDescriptionAnalysis::route('/{record}/edit'),
        ];
    }
}
