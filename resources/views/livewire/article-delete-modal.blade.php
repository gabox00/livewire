<div>
    <x-jet-confirmation-modal wire:model="showDeleteModel">
        <x-slot name="title">Are you sure?</x-slot>
        <x-slot name="content">Do you want to delete the article: {{$article->title}}?</x-slot>
        <x-slot name="footer">
            <div class="space-x-2">
                <x-jet-button wire:click.prevent="$set('showDeleteModel',false)">{{__('Cancel')}}</x-jet-button>
                <x-jet-danger-button wire:click.prevent="delete">{{__('Confirm')}}</x-jet-danger-button>
            </div>
        </x-slot>
    </x-jet-confirmation-modal>
</div>
