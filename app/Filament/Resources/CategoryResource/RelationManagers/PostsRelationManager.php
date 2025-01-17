<?php

namespace App\Filament\Resources\CategoryResource\RelationManagers;

use Filament\Forms;
use Filament\Tables;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Filament\Forms\Components\Group;
use Filament\Forms\Components\Select;
use Filament\Forms\Components\Section;
use Filament\Forms\Components\Checkbox;
use Filament\Tables\Columns\TextColumn;
use Filament\Forms\Components\TagsInput;
use Filament\Forms\Components\TextInput;
use Filament\Tables\Columns\ImageColumn;
use Filament\Forms\Components\FileUpload;
use Illuminate\Database\Eloquent\Builder;
use Filament\Forms\Components\ColorPicker;
use Filament\Forms\Components\MarkdownEditor;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use Filament\Resources\RelationManagers\RelationManager;
use Filament\Tables\Columns\CheckboxColumn;

class PostsRelationManager extends RelationManager
{
    protected static string $relationship = 'posts';

    public function form(Form $form): Form
    {
        return $form
        ->schema([
            Section::make('Add / Edit Post')
                ->description('Manipulate the posts')
                ->schema([

                    TextInput::make('title')
                        ->required(),
                    TextInput::make('slug')
                        ->unique(ignoreRecord: true)
                        ->required(),
                    ColorPicker::make('color')
                        ->required(),

                    // Select::make('category_id')->label('Category')->options(Category::all()->pluck('name', 'id'))->required(),
                    MarkdownEditor::make('content')
                        ->required()
                        ->columnSpanFull(),
                ])->columnSpan(2)->columns(2),

            Group::make()->schema([
                Section::make('Image')
                    ->collapsible()
                    ->schema([
                        FileUpload::make('thumbnail')
                            ->disk('public')
                            ->directory('thumbnail')
                            ->required()
                            ->columnSpanFull(),

                    ])->columnSpan(1)->columns(1),
                Section::make('Meta')
                    ->collapsible()
                    ->schema([
                        TagsInput::make('tags')
                            ->required(),
                        Checkbox::make('published'),
                    ])->columnSpan(1)->columns(1),
            ])
        ])->columns([
            'default' => 1,
            'md' => 2,
            'lg' => 3,
        ]);
    }

    public function table(Table $table): Table
    {
        return $table
            ->recordTitleAttribute('title')
            ->columns([
                ImageColumn::make('thumbnail'),
                TextColumn::make('title'),
                CheckboxColumn::make('published'),
            ])
            ->filters([
                //
            ])
            ->headerActions([
                Tables\Actions\CreateAction::make(),
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
                // Tables\Actions\DeleteAction::make(),
            ])
            ->bulkActions([
                Tables\Actions\BulkActionGroup::make([
                    Tables\Actions\DeleteBulkAction::make(),
                ]),
            ]);
    }
}
