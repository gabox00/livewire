<?php

namespace Tests\Feature\Livewire;

use App\Models\Category;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Livewire\Livewire;
use Tests\TestCase;
use App\Models\Article;

class ArticleFormTest extends TestCase
{
    use RefreshDatabase;

    /** @test */
    public function guests_cannot_create_or_update_articles(){
        $this->get(route('article.create'))
            ->assertRedirect('login');

        $article = Article::factory()->create();

        $this->get(route('article.edit', $article))
            ->assertRedirect('login');
    }

    /** @test */
    public function article_form_renders_properly(){
        $user = User::factory()->create();

        $this->actingAs($user)->get(route('article.create'))
            ->assertSeeLivewire('article-form')
            ->assertDontSeeText(__('Delete'));

        $article = Article::factory()->create();

        $this->actingAs($user)->get(route('article.edit', $article))
            ->assertSeeLivewire('article-form')
            ->assertSeeText(__('Delete'));;
    }

    /** @test */
    public function blade_template_is_wired_properly(){
        Livewire::test('article-form')
            ->assertSeeHtml('wire:submit.prevent="save"')
            ->assertSeeHtml('wire:model="article.title"')
            ->assertSeeHtml('wire:model="article.slug"')
            ->assertSeeHtml('wire:model="article.content"');
    }

    /** @test */
    public function can_create_new_article(){

        Storage::fake('public');

        $image = UploadedFile::fake()->image('post-image.png');

        $user = User::factory()->create();

        $category = Category::factory()->create();

        Livewire::actingAs($user)->test('article-form')
            ->set('image', $image)
            ->set('article.title', 'New article')
            ->set('article.slug', 'new-article')
            ->set('article.content', 'Article content')
            ->set('article.category_id', $category->id)
            ->call('save')
            ->assertSessionHas('flash.banner')
            ->assertRedirect(route('articles.index'));

        $this->assertDatabaseHas('articles', [
            'image' => Storage::disk('public')->files()[0],
           'title' => 'New article',
           'slug' => 'new-article',
           'content' => 'Article content',
           'category_id' => $category->id,
           'user_id' => $user->id,
        ]);
    }

    /** @test */
    public function can_update_articles(){
        Storage::fake('public');
        $image = UploadedFile::fake()->image('post-image.png');

        Article::whereNotNull('id')->delete();

        $article = Article::factory()->create();

        $user = User::factory()->create();
        Livewire::actingAs($user)->test('article-form', ['article' => $article])
            ->assertSet('article.title', $article->title)
            ->assertSet('article.slug', $article->slug)
            ->assertSet('article.content', $article->content)
            ->assertSet('article.category_id', $article->category->id)
            ->set('image', $image)
            ->set('article.title', 'Updated title')
            ->set('article.slug', 'updated-slug')
            ->call('save')
            ->assertSessionHas('flash.banner')
            ->assertRedirect(route('articles.index'));

        $this->assertDatabaseCount('articles', 1);

        $this->assertDatabaseHas('articles', [
            'title' => 'Updated title',
            'slug' => 'updated-slug'
        ]);
    }

    /** @test */
    public function can_update_articles_image(){

        Storage::fake('public');
        $oldImage = UploadedFile::fake()->image('old-image.png');
        $oldImagePath = $oldImage->store('/','public');
        $newImage = UploadedFile::fake()->image('new-image.png');

        Article::whereNotNull('id')->delete();

        $article = Article::factory()->create([
            'image' => $oldImagePath
        ]);

        $user = User::factory()->create();

        Livewire::actingAs($user)->test('article-form', ['article' => $article])
            ->set('image', $newImage)
            ->call('save')
            ->assertSessionHas('flash.banner')
            ->assertRedirect(route('articles.index'));

        Storage::disk('public')
            ->assertExists($article->fresh()->image)
            ->assertMissing($oldImagePath);
    }

    /** @test */
    public function title_is_required(){
        Livewire::test('article-form')
            ->set('article.title', '')
            ->set('article.content', 'Article content')
            ->call('save')
            ->assertHasErrors(['article.title' => 'required'])
            ->assertSeeHtml(__('validation.required', ['attribute' => 'title']));
    }

    /** @test */
    public function image_is_required(){
        Livewire::test('article-form')
            ->set('article.title', 'Article title')
            ->set('article.content', 'Article content')
            ->call('save')
            ->assertHasErrors(['image' => 'required'])
            ->assertSeeHtml(__('validation.required', ['attribute' => 'image']));
    }

    /** @test */
    public function image_field_must_be_of_type_image(){
        Livewire::test('article-form')
            ->set('image', 'string-not-allowed')
            ->call('save')
            ->assertHasErrors(['image' => 'image'])
            ->assertSeeHtml(__('validation.image', ['attribute' => 'image']));
    }

    /** @test */
    public function image_field_must_be_2MB_max(){

        Storage::fake('public');

        $image = UploadedFile::fake()->image('post-image.png')->size(3000);

        Livewire::test('article-form')
            ->set('image', $image)
            ->call('save')
            ->assertHasErrors(['image' => 'max'])
            ->assertSeeHtml(__('validation.max.file',
                [
                    'attribute' => 'image',
                    'max' => '2048'
                ]
            ));
    }

    /** @test */
    public function title_must_be_5_characters_min(){
        Livewire::test('article-form')
            ->set('article.title', 'New')
            ->set('article.content', 'Article content')
            ->call('save')
            ->assertHasErrors(['article.title' => 'min'])
            ->assertSeeHtml(__('validation.min.string', [
                'attribute' => 'title',
                'min' => 5
            ]));
    }

    /** @test */
    public function slug_is_required(){
        Livewire::test('article-form')
            ->set('article.title', 'New Article')
            ->set('article.slug', '')
            ->set('article.content', 'Article content')
            ->call('save')
            ->assertHasErrors(['article.slug' => 'required'])
            ->assertSeeHtml(__('validation.required', ['attribute' => 'slug']));
    }

    /** @test */
    public function category_is_required(){
        Livewire::test('article-form')
            ->set('article.title', 'New Article')
            ->set('article.slug', 'new-article')
            ->set('article.content', 'Article content')
            ->set('article.category_id', null)
            ->call('save')
            ->assertHasErrors(['article.category_id' => 'required'])
            ->assertSeeHtml(__('validation.required', ['attribute' => 'category id']));
    }

    /** @test */
    public function category_must_be_exist_in_database(){
        Category::whereNotNull('id')->delete();

        Livewire::test('article-form')
            ->set('article.title', 'New Article')
            ->set('article.slug', 'new-article')
            ->set('article.content', 'Article content')
            ->set('article.category_id', 1)
            ->call('save')
            ->dump()
            ->assertHasErrors(['article.category_id' => 'exists'])
            ->assertSeeHtml(__('validation.exists', ['attribute' => 'category id']));
    }

    /** @test */
    public function can_create_new_category(){
        Category::whereNotNull('id')->delete();

        Livewire::test('article-form')
            ->call('openCategoryForm')
            ->set('newCategory.name', 'Laravel')
            ->assertSet('newCategory.slug', 'laravel')
            ->call('saveNewCategory')
            ->assertSet('article.category_id', Category::first()->id)
            ->assertSet('showCategoryModal', false);

        $this->assertDatabaseCount('categories',1);
    }

    /** @test */
    public function can_category_name_is_required(){
        Livewire::test('article-form')
            ->call('openCategoryForm')
            ->set('newCategory.slug', 'laravel')
            ->call('saveNewCategory')
            ->dump()
            ->assertHasErrors(['newCategory.name' => 'required'])
            ->assertSeeHtml(__('validation.required', ['attribute' => 'name']));
    }

    /** @test */
    public function can_category_slug_is_required(){
        Category::whereNotNull('id')->delete();

        Livewire::test('article-form')
            ->call('openCategoryForm')
            ->set('newCategory.name', 'Laravel')
            ->set('newCategory.slug', null)
            ->call('saveNewCategory')
            ->assertHasErrors(['newCategory.slug' => 'required'])
            ->assertSeeHtml(__('validation.required', ['attribute' => 'slug']));
    }

    /** @test */
    public function new_category_name_must_be_unique(){
        $category = Category::factory()->create();

        Livewire::test('article-form')
            ->call('openCategoryForm')
            ->set('newCategory.name', $category->name)
            ->set('newCategory.slug', 'laravel')
            ->call('saveNewCategory')
            ->assertHasErrors(['newCategory.name' => 'unique'])
            ->assertSeeHtml(__('validation.unique', ['attribute' => 'name']));
    }

    /** @test */
    public function new_category_slug_must_be_unique(){
        Category::whereNotNull('id')->delete();

        $category = Category::factory()->create();

        Livewire::test('article-form')
            ->call('openCategoryForm')
            ->set('newCategory.name', 'Laravel')
            ->set('newCategory.slug', $category->slug)
            ->call('saveNewCategory')
            ->assertHasErrors(['newCategory.slug' => 'unique'])
            ->assertSeeHtml(__('validation.unique', ['attribute' => 'slug']));
    }

    /** @test */
    public function slug_must_be_unique(){
        $article = Article::factory()->create();

        Livewire::test('article-form')
            ->set('article.title', 'New Article')
            ->set('article.slug', $article->slug)
            ->set('article.content', 'Article content')
            ->call('save')
            ->assertHasErrors(['article.slug' => 'unique'])
            ->assertSeeHtml(__('validation.unique', ['attribute' => 'slug']));
    }

    /** @test */
    public function unique_rule_should_be_ignored_when_updating_the_same_slug(){
        $article = Article::factory()->create();
        $user = User::factory()->create();

        Livewire::actingAs($user)->test('article-form', ['article' => $article])
            ->set('article.title', 'New Article')
            ->set('article.slug', $article->slug)
            ->set('article.content', 'Article content')
            ->call('save')
            ->assertHasNoErrors(['article.slug' => 'unique']);
    }

    /** @test */
    public function slug_is_generated_automatically(){
        Livewire::test('article-form')
            ->set('article.title', 'New Article')
            ->assertSet('article.slug', 'new-article');
    }

    /** @test */
    public function slug_must_only_contain_letters_numbers_dashes_and_underscores(){
        Livewire::test('article-form')
            ->set('article.title', 'New article')
            ->set('article.slug', 'new-article$%')
            ->set('article.content', 'Article content')
            ->call('save')
            ->assertHasErrors(['article.slug' => 'alpha_dash'])
            ->assertSeeHtml(__('validation.alpha_dash', ['attribute' => 'slug']));
    }

    /** @test */
    public function content_is_required(){
        Livewire::test('article-form')
            ->set('article.title', 'New article')
            ->set('article.content', '')
            ->call('save')
            ->assertHasErrors(['article.content' => 'required'])
            ->assertSeeHtml(__('validation.required', ['attribute' => 'content']));
    }

    /** @test */
    public function real_time_validation_works_for_title(){
        Livewire::test('article-form')
            ->set('article.title', '')
            ->assertHasErrors(['article.title' => 'required'])
            ->set('article.title', 'New')
            ->assertHasErrors(['article.title' => 'min'])
            ->set('article.title', 'New Article')
            ->assertHasNoErrors('article.title');
    }

    /** @test */
    public function real_time_validation_works_for_content(){
        Livewire::test('article-form')
            ->set('article.content', '')
            ->assertHasErrors(['article.content' => 'required'])
            ->set('article.content', 'Article Content')
            ->assertHasNoErrors('article.content');
    }
}
