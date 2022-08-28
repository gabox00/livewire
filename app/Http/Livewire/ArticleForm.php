<?php

namespace App\Http\Livewire;

use App\Models\Article;
use App\Models\Category;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;
use Illuminate\Validation\Rule;
use Laravel\Jetstream\Role;
use Livewire\Component;
use Livewire\WithFileUploads;

class ArticleForm extends Component
{
    use WithFileUploads;

    public Article $article;

    public $image;

    public $newCategory;

    public $showCategoryModal = false;

    public function openCategoryForm(){
        $this->newCategory = new Category();
        $this->showCategoryModal = true;
    }

    public function closeCategoryForm(){
        $this->showCategoryModal = false;
        $this->newCategory = null;
        $this->clearValidation('newCategory.*');
    }

    public function saveNewCategory(){
        $this->validateOnly('newCategory.name');
        $this->validateOnly('newCategory.slug');
        $this->newCategory->save();
        $this->article->category_id = $this->newCategory->id;
        $this->closeCategoryForm();
    }

    protected function rules(){
        return [
            'article.title' => 'required|min:5',
            'article.slug' => [
                'required',
                'alpha_dash',
                Rule::unique('articles','slug')->ignore($this->article)
            ],
            'article.content' => 'required',
            'image' => [
                Rule::requiredIf(!$this->article->image),
                Rule::when($this->image, ['image','max:2048'])
            ],
            'article.category_id' => [
                'required',
                Rule::exists('categories','id')
            ],
            'newCategory.name' => [
                Rule::unique('categories','name')->ignore($this->newCategory),
                Rule::requiredIf($this->newCategory instanceof Category),
            ],
            'newCategory.slug' => [
                Rule::unique('categories','slug')->ignore($this->newCategory),
                Rule::requiredIf($this->newCategory instanceof Category),
            ],
        ];
    }

    public function mount(Article $article){
        $this->article = $article;
    }

    public function updated($propertyName){
        $this->validateOnly($propertyName);
    }

    public function updatedArticleTitle($title){ //updated mas nombre del objeto mas el atributo a cambiar para poder cambiar las cosas reactivamente
        $this->article->slug = Str::slug($title);
    }

    public function updatedNewCategoryName($title){
        $this->newCategory->slug = Str::slug($title);
    }

    public function save(){
        $this->validate();

        if($this->image) {
            $this->article->image = $this->uploadImage();
        }

        Auth::user()->articles()->save($this->article);

        session()->flash('flash.banner', __('Article saved.'));

        $this->redirectRoute('articles.index');
    }

    public function render()
    {
        return view('livewire.article-form', [
            'categories' => Category::pluck('name','id')
        ]);
    }

    protected function uploadImage(){
        if($oldImage = $this->article->image){
            Storage::disk('public')->delete($oldImage);
        }
        return $this->image->store('/', 'public');
    }
}
