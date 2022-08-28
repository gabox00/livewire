<div>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">Formulario</h2>
    </x-slot>
    <div>
        <div class="max-w-7xl mx-auto py-10 sm:px-6 lg:px-8">
            <x-jet-form-section submit="save">
                <x-slot name="title">{{__('New Article')}}</x-slot>
                <x-slot name="description">{{__('Some description')}}</x-slot>
                <x-slot name="form">
                    <div class="col-span-6 sm:col-span-4">
                        <x-select-image :image="$image" :article="$article"/>
                        <x-jet-input-error for="image" class="mt-2"/>
                    </div>
                    <div class="col-span-6 sm:col-span-4">
                        <x-jet-label for="title" :value="__('Title')"/>
                        <x-jet-input id="title" class="mt-1 block w-full" type="text" wire:model="article.title"/>
                        <x-jet-input-error for="article.title" class="mt-2"/>
                    </div>
                    <div class="col-span-6 sm:col-span-4">
                        <x-jet-label for="slug" :value="__('Slug')"/>
                        <x-jet-input id="slug" class="mt-1 block w-full" type="text" wire:model="article.slug"/>
                        <x-jet-input-error for="article.slug" class="mt-2"/>
                    </div>
                    <div class="col-span-6 sm:col-span-4">
                        <x-jet-label for="category_id" :value="__('Categories')"/>
                        <div class="flex mt-1 space-x-2">
                            <x-select id="category_id" class="block w-full" :options="$categories" :placeholder="__('Select category')" wire:model="article.category_id"/>
                            <x-jet-secondary-button wire:click="openCategoryForm">+</x-jet-secondary-button>
                        </div>
                        <x-jet-input-error for="article.category_id" class="mt-2"/>
                    </div>
                    <div class="col-span-6 sm:col-span-4">
                        <x-jet-label for="content" :value="__('Content')"/>
                        <x-textarea id="content" class="mt-1 block w-full" wire:model="article.content"/>
                        <x-jet-input-error for="article.content" class="mt-2"/>
                    </div>
                    <x-slot name="actions">
                        @if($this->article->exists)
                            <livewire:article-delete-modal :article="$article">
                                <x-jet-danger-button class="mr-auto" wire:click="$emit('confirmArticleDeletion', '{{ $article }}')">
                                    {{__('Delete')}}
                                </x-jet-danger-button>
                            </livewire:article-delete-modal>
                        @endif
                            <x-jet-button>{{__('Save')}}</x-jet-button>
                    </x-slot>
                </x-slot>
            </x-jet-form-section>
        </div>
    </div>
    <x-jet-modal wire:model="showCategoryModal">
        <form wire:submit.prevent="saveNewCategory">
            <div class="px-6 py-4">
                <div class="text-lg">
                    {{__('New Category')}}
                </div>
                <div class="mt-4 space-y-2">
                    <x-jet-label for="new-category-name" :value="__('Name')"/>
                    <x-jet-input id="new-category-name" class="mt-1 block w-full" type="text" wire:model="newCategory.name"/>
                    <x-jet-input-error for="newCategory.name" class="mt-2"/>

                    <x-jet-label for="new-category-slug" :value="__('Slug')"/>
                    <x-jet-input id="new-category-slug" class="mt-1 block w-full" type="text" wire:model="newCategory.slug"/>
                    <x-jet-input-error for="newCategory.slug" class="mt-2"/>
                </div>
            </div>
            <div class="flex flex-row justify-end px-6 py-4 bg-gray-100 text-right space-x-2">
                <x-jet-button>{{__('Save')}}</x-jet-button>
                <x-jet-secondary-button wire:click="closeCategoryForm">{{__('Cancel')}}</x-jet-secondary-button>
            </div>
        </form>
    </x-jet-modal>
</div>
