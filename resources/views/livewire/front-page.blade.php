<div class="divide-y divide-gray-800"
    x-data="{show: false}">
    <nav class="flex items-center px-3 py-2 bg-gray-900 shadow-lg">
        <div>
            <button
                class="items-center block h-8 mr-3 text-gray-400 hover:text-gray-200 focus:outline-none xs:hidden sm:hidden"
                @click="show = !show">
                <svg class="w-8 fill-current"
                    viewBox="0 0 24 24">
                    <path x-show="!show"
                        fill-rule="evenodd"
                        d="M4 5h16a1 1 0 0 1 0 2H4a1 1 0 1 1 0-2zm0 6h16a1 1 0 0 1 0 2H4a1 1 0 0 1 0-2zm0 6h16a1 1 0 0 1 0 2H4a1 1 0 0 1 0-2z" />
                    <path x-show="show"
                        fill-rule="evenodd"
                        d="M18.278 16.864a1 1 0 0 1-1.414 1.414l-4.829-4.828-4.828 4.828a1 1 0 0 1-1.414-1.414l4.828-4.829-4.828-4.828a1 1 0 0 1 1.414-1.414l4.829 4.828 4.828-4.828a1 1 0 1 1 1.414 1.414l-4.828 4.829 4.828 4.828z" />
                </svg>
            </button>
        </div>
        <div class="flex items-center w-full h-12">
            <a href="{{ url('/') }}"
                class="flex items-center w-full">
                <x-jet-application-mark class="block w-auto mr-3 h-9" />
                <div class="flex items-baseline text-gray-200">
                    <p class="mr-3 text-2xl font-bold">Laravel</p>
                    <p class="font-thin text-1xl">Jetstream</p>
                </div>
            </a>
        </div>
        <div class="flex justify-end sm:w-8/12">
            {{-- top navigation links --}}
            <ul class="hidden text-xs text-gray-200 sm:text-left sm:flex">
                @foreach ($navbarLinks as $navbarLink)
                    <a href="{{ url("/{$navbarLink->slug}") }}"
                        class="cursor-pointer hover:underline">
                        <li class="px-4 py-2">
                            {{ $navbarLink->label }}
                        </li>
                    </a>
                @endforeach
            </ul>
        </div>
    </nav>
    <div class="sm:flex sm:min-h-screen">
        <aside class="text-gray-700 bg-gray-900 divide-y divide-gray-700 divide-dashed sm:w-4/12 md:w-3/12 lg:w-2/12">
            {{-- Desktop Web View --}}
            <ul class="hidden text-xs text-gray-200 sm:block sm:text-left">
                @foreach ($sidebarLinks as $sidebarLink)
                    <a href="{{ url("/{$sidebarLink->slug}") }}"
                        class="cursor-pointer hover:underline">
                        <li class="px-4 py-2 hover:bg-gray-800">
                            {{ $sidebarLink->label }}
                        </li>
                    </a>
                @endforeach
            </ul>

            {{-- Mobile Web View --}}
            <div :class="show? 'block': 'hidden'"
                class="block pb-3 divide-y divide-gray-800 sm:hidden xs-hidden">
                <ul class="text-xs text-gray-200 sm:hidden xs:hidden">
                    @foreach ($sidebarLinks as $sidebarLink)
                        <a href="{{ url("/{$sidebarLink->slug}") }}"
                            class="cursor-pointer hover:underline">
                            <li class="px-4 py-2 hover:bg-gray-800">
                                {{ $sidebarLink->label }}
                            </li>
                        </a>
                    @endforeach
                </ul>

                {{-- Top Navigation Mobile Web View --}}
                <ul class="text-xs text-gray-200 sm:hidden xs:hidden">
                    @foreach ($navbarLinks as $navbarLink)
                        <a href="{{ url("/{$navbarLink->slug}") }}"
                            class="cursor-pointer hover:underline">
                            <li class="px-4 py-2 hover:bg-gray-800">
                                {{ $navbarLink->label }}
                            </li>
                        </a>
                    @endforeach
                </ul>

            </div>
        </aside>
        <main class="min-h-screen p-12 bg-gray-100 sm:w-8/12 md:w-9/12 lg:w-10/12">
            <section class="text-gray-900 divide-y divide-gray-300">
                <h1 class="text-3xl font-bold">{{ $state['data']['title'] }}</h1>
                <article class="pt-5 text-sm">
                    {!! $state['data']['content'] !!}
                </article>
            </section>
        </main>
    </div>
</div>
