<?php

namespace App\Http\Livewire;

use Illuminate\Support\Facades\Storage;
use Livewire\Component;

class ArticleDeleteModal extends Component
{
    protected $listeners = ['confirmArticleDeletion'];

    public $article;
    public $showDeleteModel = false;

    public function confirmArticleDeletion($article){
        $article = json_decode($article);
        if ($this->article->id === $article->id)
            $this->showDeleteModel = true;
    }

    public function delete(){
        Storage::disk('public')->delete($this->article->image);

        $this->article->delete();

        session()->flash('flash.bannerStyle', 'danger');
        session()->flash('flash.banner', __('Article deleted.'));

        $this->redirect(route('articles.index'));
    }

    public function render()
    {
        return view('livewire.article-delete-modal');
    }
}
