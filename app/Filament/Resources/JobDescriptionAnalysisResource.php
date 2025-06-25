<?php

namespace App\Filament\Resources;

use App\Enums\JobDescriptionAnalysisStatusEnum;
use App\Filament\Resources\JobDescriptionAnalysisResource\Pages;
use App\Models\JobDescriptionAnalysis;
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

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make()
                    ->schema([
                        Forms\Components\View::make('filament.components.job-fit-preview')
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
                            ->options(static::$descriptionTypes)
                            ->required()
                            ->default('job_description')
                            ->reactive(),
                        Forms\Components\Textarea::make('description')
                            ->label('Job Description')
                            ->maxLength(5000)
                            ->rows(8)
                            ->visible(fn(callable $get) => $get('description_type') === 'job_description')
                            ->required(fn(callable $get) => $get('description_type') === 'job_description'),
                        Forms\Components\TextInput::make('description')
                            ->label('Job Description URL')
                            ->url()
                            ->maxLength(2048)
                            ->visible(fn(callable $get) => $get('description_type') === 'url')
                            ->required(fn(callable $get) => $get('description_type') === 'url'),
                    ]),
            ])->columns(1);
    }

    public static function getResourceTableCols(): array
    {
        return [
            Tables\Columns\TextColumn::make('name')
                ->label('Job Title')
                ->searchable()
                ->sortable(),
            Tables\Columns\TextColumn::make('description_type')
                ->label('Description Type')
                ->formatStateUsing(fn(string $state): string => @static::$descriptionTypes[$state] ?? $state)
                ->sortable(),
            Tables\Columns\TextColumn::make('status')
                ->label('Status')
                ->sortable()
                ->formatStateUsing(fn(string $state) => JobDescriptionAnalysisStatusEnum::from($state)->label())
                ->color(fn(string $state) => JobDescriptionAnalysisStatusEnum::from($state)->color())
                ->icons([
                    'heroicon-o-arrow-path' => fn($state) => $state === JobDescriptionAnalysisStatusEnum::IN_PROGRESS->name,
                ])
                ->extraAttributes(['class' => 'column-animate-spin'])
                ->iconPosition('after')
                ->searchable(),
            Tables\Columns\TextColumn::make('created_at')
                ->label('Created At')
                ->dateTime()
                ->sortable(),

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
            ]);
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
