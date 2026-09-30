<x-app-layout>
    <x-slot:title>
        Cards
    </x-slot>

    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('card.title') }}
        </h2>
    </x-slot>

    <div class="py-8">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">

                <div class="p-6 text-gray-900 flex items-center justify-between">
                    <h1 class="text-2xl font-semibold">
                        {{ __('card.featured') }}
                    </h1>


                    <!-- Language Select -->
                    <select
                        class="border border-gray-300 rounded-md pr-7 py-2 bg-white text-sm focus:outline-none focus:ring-1 focus:ring-blue-500"
                        name="locale" onchange="window.location.href='{{ url('cards/lang') }}/' + this.value">
                        <option @selected($currentLocale == 'en') value="en">English</option>
                        <option @selected($currentLocale == 'hin') value="hin">Hindi</option>
                        <option @selected($currentLocale == 'ben') value="ben">Bengali</option>
                    </select>


                </div>

                <div class="p-6 grid grid-cols-2 gap-4">

                    <!-- Card 1 -->
                    <div>
                        <a href="#"
                            class="bg-neutral-primary-soft flex flex-col w-full h-full p-6 border border-default rounded-base shadow-xs hover:bg-neutral-secondary-medium">

                            <h5 class="mb-3 text-2xl font-semibold tracking-tight text-heading leading-8">
                                {{ __('card.card-1.heading') }}
                            </h5>

                            <p class="text-body">
                                {{ __('card.card-1.content') }}
                            </p>

                        </a>
                    </div>


                    <!-- Card 2 -->
                    <div>
                        <a href="#"
                            class="bg-neutral-primary-soft flex flex-col w-full h-full p-6 border border-default rounded-base shadow-xs hover:bg-neutral-secondary-medium">

                            <h5 class="mb-3 text-2xl font-semibold tracking-tight text-heading leading-8">
                                {{ __('card.card-2.heading') }}
                            </h5>

                            <p class="text-body">
                                {{ __('card.card-2.content') }}
                            </p>

                        </a>
                    </div>


                    <!-- Card 3 -->
                    <div>
                        <a href="#"
                            class="bg-neutral-primary-soft flex flex-col w-full h-full p-6 border border-default rounded-base shadow-xs hover:bg-neutral-secondary-medium">

                            <h5 class="mb-3 text-2xl font-semibold tracking-tight text-heading leading-8">
                                {{ __('card.card-3.heading') }}
                            </h5>

                            <p class="text-body">
                                {{ __('card.card-3.content') }}
                            </p>

                        </a>
                    </div>

                </div>

            </div>
        </div>
    </div>

</x-app-layout>
