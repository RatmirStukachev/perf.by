<?php

namespace App\Filament\Resources;

use App\Enums\PageContentEnum;
use App\Filament\Resources\PageContentResource\Forms\AboutSliderForm;
use App\Filament\Resources\PageContentResource\Forms\CallBackForm;
use App\Filament\Resources\PageContentResource\Forms\CartDeliveryForm;
use App\Filament\Resources\PageContentResource\Forms\MainAdviseForm;
use App\Filament\Resources\PageContentResource\Forms\MainArrivalsForm;
use App\Filament\Resources\PageContentResource\Forms\MainBlocksForm;
use App\Filament\Resources\PageContentResource\Forms\MainBrandsForm;
use App\Filament\Resources\PageContentResource\Forms\MainDirectForm;
use App\Filament\Resources\PageContentResource\Forms\MainFormForm;
use App\Filament\Resources\PageContentResource\Forms\MainNewsForm;
use App\Filament\Resources\PageContentResource\Forms\MainPopularForm;
use App\Filament\Resources\PageContentResource\Forms\MainRequestForm;
use App\Filament\Resources\PageContentResource\Forms\MainSecondForm;
use App\Filament\Resources\PageContentResource\Forms\MainSlideForm;
use App\Filament\Resources\PageContentResource\Forms\MainTechForm;
use App\Filament\Resources\PageContentResource\Forms\ProductDeliveryForm;
use App\Filament\Resources\PageContentResource\Pages;
use App\Filament\Resources\PageContentResource\RelationManagers;
use App\Models\PageContent;
use Filament\Forms;
use Filament\Forms\Form;
use Filament\Resources\Resource;
use Filament\Tables;
use Filament\Tables\Columns\TextColumn;
use Filament\Tables\Table;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletingScope;


class PageContentResource extends Resource
{
    protected static ?string $model = PageContent::class;
    protected static ?string $navigationGroup = 'Контент';
    protected static ?string $pluralModelLabel = 'Блоки контента страниц';
    protected static ?string $label = 'Блок контента страницы';
    protected static ?string $navigationLabel = 'Блоки контента страниц';

    protected static ?string $navigationIcon = 'heroicon-o-rectangle-stack';

    public static function getNavigationLabel(): string 
    {
        return static::$navigationLabel;
    }

    public static function getModelLabel(): string
    {
        return static::$label;
    }

    public static function getPluralModelLabel(): string
    {
        return static::$pluralModelLabel;
    }

    public static function getNavigationGroup(): ?string
    {
        return static::$navigationGroup;
    }

    public static function form(Form $form): Form
    {
        return $form
            ->schema(function (PageContent $pageContent) {
                self::$label = PageContentEnum::valueOne($pageContent->key);

                return match ($pageContent->key) {
                    PageContentEnum::main_arrivals->name => MainArrivalsForm::get(),
                    PageContentEnum::main_second->name => MainSecondForm::get(),
                    PageContentEnum::main_popular->name => MainPopularForm::get(),
                    PageContentEnum::main_slide->name => MainSlideForm::get(),
                    PageContentEnum::main_brands->name => MainBrandsForm::get(),
                    PageContentEnum::main_news->name => MainNewsForm::get(),
                    PageContentEnum::product_delivery->name => ProductDeliveryForm::get(),
                    PageContentEnum::cart_delivery->name => CartDeliveryForm::get(),
                    PageContentEnum::callback_form->name => CallBackForm::get(),
                };
            })->columns(1);
    }

    public static function table(Table $table): Table
    {
        return $table
            ->columns([
                TextColumn::make('key')
                    ->formatStateUsing(fn($state) => PageContentEnum::valueOne($state))
                    ->searchable(query: function (Builder $query, string $search): Builder {
                        $keyQuery = $query->where('key', 'like', "%{$search}%");
                        
                        $enumCases = PageContentEnum::cases();
                        $matchingKeys = [];
                        
                        foreach ($enumCases as $case) {
                            if (mb_stripos($case->value, $search) !== false) {
                                $matchingKeys[] = $case->name;
                            }
                        }
                        
                        if (!empty($matchingKeys)) {
                            $keyQuery->orWhereIn('key', $matchingKeys);
                        }
                        
                        return $keyQuery;
                    })
                    ->label('Раздел'),
            ])
            ->filters([
                //
            ])
            ->actions([
                Tables\Actions\EditAction::make(),
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
            'index' => Pages\ListPageContents::route('/'),
//            'create' => Pages\CreatePageContent::route('/create'),
            'edit' => Pages\EditPageContent::route('/{record}/edit'),
        ];
    }

    public static function canDelete(Model $record): bool
    {
        return false;
    }

    public static function canCreate(): bool
    {
        return false;
    }
}
