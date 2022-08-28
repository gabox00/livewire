<div class="relative">
    @if($image instanceof Livewire\TemporaryUploadedFile)
        <x-jet-danger-button wire:click="$set('image')" class="absolute bottom-2 right-2">
            {{__('Change image')}}
        </x-jet-danger-button>
        <img src="{{ $image->temporaryUrl() }}" class="border-2 rounded" alt="">
    @elseif($article->image)
        <x-jet-label for="image" :value="__('Change image')" class='absolute bottom-2 right-2 inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring focus:ring-gray-300 disabled:opacity-25 transition cursor-pointer'/>
        <img src="{{ asset('storage/'.$article->image) }}" alt="{{$article->name}}">
    @else
        <div class="h-32 bg-gray-50 border-2 border-dashed rounded flex items-center justify-center">
            <x-jet-label for="image" :value="__('Select image')" class='inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 active:bg-gray-900 focus:outline-none focus:border-gray-900 focus:ring focus:ring-gray-300 disabled:opacity-25 transition cursor-pointer'/>
        </div>
    @endif
    <x-jet-input id="image" class="mt-2 block w-full hidden" type="file" wire:model="image"/>
</div>
