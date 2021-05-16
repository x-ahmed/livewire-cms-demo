<div class="p-6">
    <div class="flex items-center justify-end px-4 py-3 text-right sm:px-6">
        <x-jet-button wire:click="create">
            {{ __('Create Menu') }}
        </x-jet-button>
    </div>

    <!--  Navigation Menus Data Table -->
    <div class="flex flex-col">
        <div class="-my-2 overflow-x-auto sm:-mx-6 lg:-mx-8">
            <div class="inline-block min-w-full py-2 align-middle sm:px-6 lg:px-8">
                <div class="overflow-hidden border-b border-gray-200 shadow sm:rounded-lg">
                    <table class="w-full divide-y divide-gray-200">
                        <thead>
                            <tr>
                                <th
                                    class="px-6 py-3 text-xs font-medium leading-4 tracking-wider text-left text-gray-500 uppercase bg-gray-50">
                                    Type
                                </th>
                                <th
                                    class="px-6 py-3 text-xs font-medium leading-4 tracking-wider text-left text-gray-500 uppercase bg-gray-50">
                                    Sequence
                                </th>
                                <th
                                    class="px-6 py-3 text-xs font-medium leading-4 tracking-wider text-left text-gray-500 uppercase bg-gray-50">
                                    label
                                </th>
                                <th
                                    class="px-6 py-3 text-xs font-medium leading-4 tracking-wider text-left text-gray-500 uppercase bg-gray-50">
                                    URL
                                </th>
                                <th
                                    class="px-6 py-3 text-xs font-medium leading-4 tracking-wider text-left text-gray-500 uppercase bg-gray-50">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="min-w-full bg-white divide-y divide-gray-200">
                            @forelse ($navMenus as $navMenu)
                                <tr>
                                    <td class="px-6 py-4 text-sm whitespace-nowrap">
                                        {{ $navMenu->type_full_string }}
                                    </td>
                                    <td class="px-6 py-4 text-sm whitespace-nowrap">
                                        {{ $navMenu->sequence }}
                                    </td>
                                    <td class="px-6 py-4 text-sm whitespace-nowrap">
                                        {{ \Illuminate\Support\Str::limit($navMenu->label, 40, '...') }}
                                    </td>
                                    <td class="px-6 py-4 text-sm whitespace-nowrap">
                                        <a class="text-indigo-600 hover:text-indigo-900"
                                            href="{{ url($navMenu->slug) }}">
                                            {{ \Illuminate\Support\Str::limit($navMenu->link, 40, '...') }}
                                        </a>
                                    </td>
                                    <td class="flex items-center justify-center px-3 py-4 text-sm text-right">
                                        <x-jet-button wire:click="edit({{ (int) $navMenu->id }})"
                                            class="mr-1">
                                            {{ __('Edit') }}
                                        </x-jet-button>
                                        <x-jet-danger-button wire:click="destroy({{ $navMenu->id }})"
                                            class="ml-1">
                                            {{ __('Delete') }}
                                        </x-jet-danger-button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="4"
                                        class="px-6 py-4 text-2xl font-extrabold text-center">
                                        👋 No menus found
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    @if ($navMenus->count())
        <div class="px-3 pt-6">
            {!! $navMenus->appends(request()->input())->links() !!}
        </div>
    @endif

    <!--  Modal Form -->
    <x-jet-dialog-modal wire:model="state.ui.isModalUp"
        wire:key="{{ $state['data']['id'] ?? 'create' }}">
        <div class="flex justify-center">
            <x-slot name="title">
                @if (!$state['data']['id'])
                    {{ __('New Menu') }}
                @else
                    {{ $state['data']['label'] }}
                @endif
            </x-slot>
        </div>

        <x-slot name="content">
            <div class="mt-4">
                <x-jet-label for="label"
                    value="{{ __('Label') }}" />
                <x-jet-input id="label"
                    class="block w-full mt-1"
                    placeholder="Menu Name"
                    type="text"
                    wire:model.debounce.200ms="state.data.label"
                    required />
                <x-jet-input-error for="state.data.label"
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
                        {{ config('app.url') . '/' }}
                    </span>
                    <input id="slug"
                        class="flex-1 block w-full border-gray-300 rounded-none focus:ring-indigo-500 focus:border-indigo-500 rounded-r-md sm:text-sm"
                        type="text"
                        wire:model.debounce.500ms='state.data.slug'
                        required
                        disabled
                        placeholder="Menu Slug" />
                </div>
                <x-jet-input-error for="state.data.slug"
                    class="mt-2" />
            </div>
            <div class="mt-4">
                <x-jet-label for="sequence"
                    value="{{ __('sequence') }}" />
                <x-jet-input id="sequence"
                    class="block w-full mt-1"
                    placeholder="Menu Sequence Number"
                    type="number"
                    min="1"
                    wire:model.debounce.200ms="state.data.sequence"
                    required />
                <x-jet-input-error for="state.data.sequence"
                    class="mt-2" />
            </div>
            <div class="mt-4">
                <x-jet-label for="type"
                    value="{{ __('Type') }}" />
                <select
                    class="block w-full px-4 py-3 mt-1 leading-tight text-gray-700 bg-gray-100 border border-gray-300 rounded-md shadow-sm appearance-none focus:outline-none focus:bg-white focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                    wire:model="state.data.type"
                    wire:key="{{ $state['data']['type'] }}">
                    <option value="">
                        Select Menu Type
                    </option>
                    <option value="side"
                        selected={{ $state['data']['type'] === 'side' ? 'selected' : '' }}>
                        Side Navigation Bar
                    </option>
                    <option value="top"
                        selected={{ $state['data']['type'] === 'top' ? 'selected' : '' }}>
                        Top Navigation Bar
                    </option>
                </select>
                <x-jet-input-error for="state.data.type"
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
                    {{ __('Save Menu') }}
                @else
                    {{ __('Update Menu') }}
                @endif
            </x-jet-button>
        </x-slot>
    </x-jet-dialog-modal>
</div>
