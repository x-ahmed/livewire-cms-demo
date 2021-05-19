<div class="p-6">
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
                                    Name
                                </th>
                                <th
                                    class="px-6 py-3 text-xs font-medium leading-4 tracking-wider text-left text-gray-500 uppercase bg-gray-50">
                                    Username
                                </th>
                                <th
                                    class="px-6 py-3 text-xs font-medium leading-4 tracking-wider text-left text-gray-500 uppercase bg-gray-50">
                                    Role
                                </th>
                                <th
                                    class="px-6 py-3 text-xs font-medium leading-4 tracking-wider text-left text-gray-500 uppercase bg-gray-50">
                                    Email
                                </th>
                                <th
                                    class="px-6 py-3 text-xs font-medium leading-4 tracking-wider text-center text-gray-500 uppercase bg-gray-50">
                                    Actions
                                </th>
                            </tr>
                        </thead>
                        <tbody class="min-w-full bg-white divide-y divide-gray-200">
                            @forelse ($users as $user)
                                <tr>
                                    <td class="px-6 py-4 text-sm whitespace-nowrap">
                                        {{ $user->name }}
                                    </td>
                                    <td class="px-6 py-4 text-sm whitespace-nowrap">
                                        {{ $user->username }}
                                    </td>
                                    <td class="px-6 py-4 text-sm whitespace-nowrap">
                                        {{ $user->role }}
                                    </td>
                                    <td class="px-6 py-4 text-sm whitespace-nowrap">
                                        {{ $user->email }}
                                    </td>
                                    <td class="flex items-center justify-center px-3 py-4 text-sm text-right">
                                        <x-jet-button wire:click="edit({{ (int) $user->id }})"
                                            class="mr-1">
                                            {{ __('Edit') }}
                                        </x-jet-button>
                                        <x-jet-danger-button wire:click="destroy({{ $user->id }})"
                                            class="ml-1">
                                            {{ __('Delete') }}
                                        </x-jet-danger-button>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5"
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
    @if ($users->count())
        <div class="px-3 pt-6">
            {!! $users->appends(request()->input())->links() !!}
        </div>
    @endif

    <!--  Modal Form -->
    <x-jet-dialog-modal wire:model="state.ui.isModalUp"
        wire:key="{{ $state['data']['id'] ?? 'create' }}">
        <div class="flex justify-center">
            <x-slot name="title">
                @if (!$state['data']['id'])
                    {{ __('New User') }}
                @else
                    {{ __('Update User') }}
                @endif
            </x-slot>
        </div>

        <x-slot name="content">
            <div class="mt-4">
                <x-jet-label for="name"
                    value="{{ __('Name') }}" />
                <x-jet-input id="name"
                    class="block w-full mt-1"
                    placeholder="Enter name"
                    type="text"
                    wire:model="state.data.name"
                    required />
                <x-jet-input-error for="state.data.name"
                    class="mt-2" />
            </div>
            <div class="mt-4">
                <x-jet-label for="username"
                    value="{{ __('Username') }}" />
                <x-jet-input id="username"
                    class="block w-full mt-1"
                    placeholder="Enter username"
                    type="text"
                    wire:model="state.data.username"
                    required />
                <x-jet-input-error for="state.data.username"
                    class="mt-2" />
            </div>
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
