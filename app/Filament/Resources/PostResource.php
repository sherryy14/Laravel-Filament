<?php

namespace App\Filament\Resources;

use Filament\Forms;
use App\Models\Post;
use Filament\Tables;
use App\Models\Category;
use Filament\Forms\Form;
use Filament\Tables\Table;
use Illuminate\Support\Str;
use Nette\Utils\ImageColor;
use Filament\Resources\Resource;
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
use Filament\Tables\Columns\CheckboxColumn;
use Filament\Forms\Components\MarkdownEditor;
use App\Filament\Resources\PostResource\Pages;
use Illuminate\Database\Eloquent\SoftDeletingScope;
use App\Filament\Resources\PostResource\RelationManagers;
use App\Filament\Resources\CategoryResource\RelationManagers\PostsRelationManager;

class PostResource extends Resource
{
    protected static ?string $model = Post::class;

    protected static ?string $navigationIcon = 'heroicon-m-rectangle-stack';

    public static function form(Form $form): Form
    {
        return $form
            ->schema([
                Section::make('Add / Edit Post')
                    ->description('Manipulate the posts')
                    ->schema([

                        TextInput::make('title')
                            ->required()
                            ->live(onBlur:true)
                            ->afterStateUpdated(function (string $operation, string $state, Forms\Set $set){
                                if ($operation === 'create') {
                                    if($state !==''){
                                        $set('slug', Str::slug($state));
                                    }
                                }
                            })
                            ,
                        TextInput::make('slug')
                            ->unique(ignoreRecord: true)
                            ->live()
                            ->required(),
                        ColorPicker::make('color')
                            ->required(),
                        Select::make('category_id')
                            ->label('Category')
                            ->relationship('category', 'name')
                            ->searchable()
                            ->required(),
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
                        ->collapsed()
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

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('id')
                    ->sortable()
                    ->searchable()
                    ->label('Post ID')
                    ->toggleable(isToggledHiddenByDefault: true),
                ImageColumn::make('thumbnail'),
                TextColumn::make('title')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('slug')
                    ->sortable()
                    ->searchable()
                    ->toggleable(),
                TextColumn::make('category.name')
                    ->sortable()
                    ->searchable(),
                TextColumn::make('tags')
                    ->sortable()
                    ->searchable()
                    ->toggleable(),
                CheckboxColumn::make('published'),
                TextColumn::make('created_at')
                    ->label('Published On')
                    ->date('d M, Y')
                    ->toggleable(),

            ])
            ->filters([
                //
            ])
            ->actions([
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
        ];
    }

    public static function getPages(): array
    {
        return [
            'index' => Pages\ListPosts::route('/'),
            'create' => Pages\CreatePost::route('/create'),
            'edit' => Pages\EditPost::route('/{record}/edit'),
        ];
    }
}
