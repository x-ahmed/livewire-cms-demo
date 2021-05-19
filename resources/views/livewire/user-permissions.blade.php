<div class="p-6">
    <div class="flex items-center justify-end px-4 py-3 text-right sm:px-6">
        <x-jet-button wire:click="create">
            {{ __('Create Permission')  }}
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
                                    Role
                                </th>
                                <th
                                    class="px-6 py-3 text-xs font-medium leading-4 tracking-wider text-left text-gray-500 uppercase bg-gray-50">
                                    Route
                                </th>
                                <th
                                    class="px-6 py-3 text-xs font-medium leading-4 tracking-wider text-center text-gray-500 uppercase bg-gray-50">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="min-w-full bg-white divide-y divide-gray-200">
                            @forelse ($userPermissions as $permission)
                                <tr>
                                    <td class="px-6 py-4 text-sm whitespace-nowrap">
                                        {{ $permission->role }}
                                    </td>
                                    <td class="px-6 py-4 text-sm whitespace-nowrap">
                                        {{ $permission->route_name }}
                                    </td>
                                    <td class="flex items-center justify-center px-3 py-4 text-sm text-right">
                                        <x-jet-button wire:click="edit({{ (int) $permission->id }})"
                                            class="mr-1">
                                            {{ __('Edit') }}
                                        </x-jet-button>
                                        <x-jet-danger-button wire:click="destroy({{ $permission->id }})"
                                            class="ml-1">
                                            {{ __('Delete') }}
                                        </x-jet-danger-button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="3"
                                        class="px-6 py-4 text-2xl font-extrabold text-center">
                                        👋 No items found
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
    @if ($userPermissions->count())
        <div class="px-3 pt-6">
            {!! $userPermissions->appends(request()->input())->links() !!}
        </div>
    @endif

    <!--  Modal Form -->
    <x-jet-dialog-modal wire:model="state.ui.isModalUp"
        wire:key="{{ $state['data']['id'] ?? 'create' }}">
        <div class="flex justify-center">
            <x-slot name="title">
                @if (!$state['data']['id'])
                    {{ __('New User Permission') }}
                @else
                    {{ __('Update User Permission') }}
                @endif
            </x-slot>
        </div>

        <x-slot name="content">
            <div class="mt-4">
                <x-jet-label for="role"
                    value="{{ __('Role') }}" />
                <select
                    class="block w-full px-4 py-3 mt-1 leading-tight text-gray-700 bg-gray-100 border border-gray-300 rounded-md shadow-sm appearance-none focus:outline-none focus:bg-white focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                    id="role"
                    wire:model="state.data.role"
                    wire:key="{{ $state['data']['role'] }}">
                    <option value="">
                        Select Type
                    </option>
                    @foreach ($roles as $role)
                        <option value="{{ $role }}"
                            selected={{ $state['data']['role'] === $role ? 'selected' : '' }}>
                            {{ \Illuminate\Support\Str::ucfirst($role) }}
                        </option>
                    @endforeach
                </select>
                <x-jet-input-error for="state.data.role"
                    class="mt-2" />
            </div>
            <div class="mt-4">
                <x-jet-label for="route"
                    value="{{ __('Route') }}" />
                <select
                    class="block w-full px-4 py-3 mt-1 leading-tight text-gray-700 bg-gray-100 border border-gray-300 rounded-md shadow-sm appearance-none focus:outline-none focus:bg-white focus:border-indigo-300 focus:ring focus:ring-indigo-200 focus:ring-opacity-50"
                    wire:model="state.data.route"
                    wire:key="{{ $state['data']['route'] }}">
                    <option value="">
                        Select route
                    </option>
                    @foreach ($routes as $lroute)
                        <option value="{{ $lroute }}"
                            selected={{ $state['data']['route'] === $lroute ? 'selected' : '' }}>
                            {{ \Illuminate\Support\Str::ucfirst($lroute) }}
                        </option>
                    @endforeach
                </select>
                <x-jet-input-error for="state.data.route"
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
                    {{ __('Save') }}
                @else
                    {{ __('Update') }}
                @endif
            </x-jet-button>
        </x-slot>
    </x-jet-dialog-modal>
</div>
