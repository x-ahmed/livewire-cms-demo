<div class="p-6">
    <div class="flex items-center justify-end px-4 py-3 text-right sm:px-6">
        <x-jet-button wire:click="create">
            {{ __('Create Page') }}
        </x-jet-button>
    </div>

    <!--  Pages Data Table -->
    <div class="flex flex-col">
        <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
            <div class="inline-block min-w-full py-2 align-middle sm:px-6 lg:px-8">
                <div class="overflow-hidden border-b border-gray-200 shadow sm:rounded-lg">
                    <table class="w-full divide-y divide-gray-200">
                        <thead>
                            <tr>
                                <th
                                    class="px-6 py-3 text-xs font-medium leading-4 tracking-wider text-left text-gray-500 uppercase bg-gray-50">
                                    Title</th>
                                <th
                                    class="px-6 py-3 text-xs font-medium leading-4 tracking-wider text-left text-gray-500 uppercase bg-gray-50">
                                    Link</th>
                                <th
                                    class="px-6 py-3 text-xs font-medium leading-4 tracking-wider text-left text-gray-500 uppercase bg-gray-50">
                                    Content</th>
                                <th
                                    class="px-6 py-3 text-xs font-medium leading-4 tracking-wider text-left text-gray-500 uppercase bg-gray-50">
                                    Actions</th>
                            </tr>
                        </thead>
                        <tbody class="min-w-full bg-white divide-y divide-gray-200">
                            @forelse ($pages as $page)
                                <tr>
                                    <td class="px-6 py-4 text-sm whitespace-nowrap">{{ $page->title }}</td>
                                    <td class="px-6 py-4 text-sm whitespace-nowrap">
                                        {{-- <a href="{{ URL::to("/{$page->slug}") }}" --}}
                                        <a href="{{ route('front-page', $page) }}"
                                            target="_blank"
                                            class="text-indigo-600 hover:text-indigo-900">
                                            {{ $page->link }}
                                        </a>
                                    </td>
                                    <td class="px-6 py-4 text-sm whitespace-nowrap">
                                        {!! Str::limit($page->body, 40, '...') !!}
                                    </td>
                                    <td class="flex items-center justify-center px-3 py-4 text-sm text-right">
                                        <x-jet-button wire:click="edit({{ (int)$page->id }})"
                                            class="mr-1">
                                            {{ __('Edit') }}
                                        </x-jet-button>
                                        <x-jet-danger-button wire:click="destroy({{ $page->id }})"
                                            class="ml-1">
                                            {{ __('Delete') }}
                                        </x-jet-danger-button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4"
                                        class="px-6 py-4 text-2xl font-extrabold text-center">
                                        👋 No pages found
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    @if ($pages->count())
        <div class="px-3 pt-6">
            {!! $pages->appends(request()->input())->links() !!}
        </div>
    @endif


    <!--  Modal Form -->
    <x-jet-dialog-modal wire:model="state.ui.isModalUp"
        wire:key="{{ $state['data']['id'] ?? 'create' }}">
        <div class="flex justify-center">
            <x-slot name="title">
                @if (!$state['data']['id'])
                    {{ __('New Page') }}
                @else
                    {{ $state['data']['title'] }}
                @endif
            </x-slot>
        </div>

        <x-slot name="content">
            <div class="mt-4">
                <x-jet-label for="title"
                    value="{{ __('Title') }}" />
                <x-jet-input id="title"
                    class="block w-full mt-1"
                    type="text"
                    wire:model.debounce.200ms="state.data.title"
                    required />
                <x-jet-input-error for="state.data.title"
                    class="mt-2" />
            </div>
            <div class="mt-4">
                <label for="slug"
                    class="block text-sm font-medium text-gray-700">
                    Slug
                </label>
                <div class="flex mt-1 rounded-md shadow-sm">
                    <span
                        class="inline-flex items-center px-3 text-sm text-gray-500 border border-r-0 border-gray-300 rounded-l-md bg-gray-50">
                        {{ config('app.url') . '/pages/' }}
                    </span>
                    <input id="slug"
                        class="flex-1 block w-full border-gray-300 rounded-none focus:ring-indigo-500 focus:border-indigo-500 rounded-r-md sm:text-sm"
                        type="text"
                        wire:model='state.data.slug'
                        required
                        disabled
                        placeholder="Page Slug" />
                </div>
                <x-jet-input-error for="state.data.slug"
                    class="mt-2" />
            </div>
            <div class="mt-4">
                <x-jet-label for="body"
                    value="{{ __('Content') }}" />
                <div class="rounded-md shadow-sm">
                    <div class="mt-1 bg-white">
                        <div class="body-content"
                            wire:ignore>
                            <trix-editor class="trix-content"
                                x-ref="trix"
                                wire:model.debounce.100000ms="state.data.body"
                                wire:key="trix-content-unique-key">
                            </trix-editor>
                        </div>
                    </div>
                </div>
                <x-jet-input-error for="state.data.body"
                    class="mt-2" />
            </div>

        </x-slot>

        <x-slot name="footer">
            <x-jet-secondary-button wire:click="cancel"
                wire:loading.attr="disabled">
                {{ __('Cancel') }}
            </x-jet-secondary-button>

            <x-jet-button class="ml-2"
                wire:click="save"
                wire:loading.attr="disabled">
                @if (!$state['data']['id'])
                    {{ __('Save Page') }}
                @else
                    {{ __('Update Page') }}
                @endif
            </x-jet-button>
        </x-slot>
    </x-jet-dialog-modal>
</div>
