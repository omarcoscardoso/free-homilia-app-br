<div class="flex flex-col md:flex-row h-screen bg-gray-100 dark:bg-gray-900 transition-colors duration-300">
    {{-- Lado Esquerdo: Formulário - Fixo --}}
    <div class="flex-none w-full md:w-1/3 bg-white dark:bg-gray-800 shadow-lg md:shadow-none p-6 transition-colors duration-300 md:sticky md:top-0 md:h-screen">    
    <!-- <div class="flex-none w-full md:w-1/3 bg-white dark:bg-gray-800 shadow-lg md:shadow-none p-6 transition-colors duration-300 overflow-y-auto"> -->
        <div class="w-full max-w-sm flex flex-col items-center space-y-0 mx-auto">
            {{-- Logo --}}
            <div class="mb-8">
                <a href="{{ route('gemini-homilia.create') }}">
                    <img class="w-auto h-auto max-h-[89px]" src="https://storage.googleapis.com/homilia/logo_homilia.png" alt="HomiliA Logo">
                </a>
            </div>

            {{-- Formulário --}}
            <form wire:submit.prevent="generateHomilia" class="w-full flex flex-col items-center space-y-4">
                @csrf
                <x-forms.input-text
                    name="text_homilia"
                    wire:model="text_homilia"
                    placeholder="Digite o tema ou o texto base do seu sermão."
                    class="bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100"
                    wire:loading.attr="disabled"
                    wire:target="generateHomilia"
                />

                <x-forms.input-text
                    name="verso_homilia"
                    wire:model="verso_homilia" 
                    placeholder="Referência, (ex: João 3:16 ou Rm 8 -Opcional)"
                    class="bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100"
                    wire:loading.attr="disabled"
                    wire:target="generateHomilia"
                />

                <x-forms.input-text
                    name="qt_divisao_homilia"
                    type="number"
                    wire:model="qt_divisao_homilia"
                    placeholder="Quantidade de divisões (ex: 3, 5 - Opcional)"
                    class="bg-white dark:bg-gray-700 text-gray-900 dark:text-gray-100"
                    wire:loading.attr="disabled"
                    wire:target="generateHomilia"
                />

                <!-- <button
                    type="submit"
                    class="w-full bg-blue-600 text-white py-3 px-6 rounded-full hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:ring-offset-2 transition duration-200 ease-in-out shadow-md"
                    wire:loading.attr="disabled"
                    wire:target="generateHomilia"
                    wire:loading.class="opacity-70 cursor-not-allowed"
                >
                    Escrever esboço
                </button> -->
                <button 
                    type="submit" 
                    class="w-full bg-blue-600 text-white py-3 px-6 rounded-full 
                           hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-blue-500 
                           focus:ring-offset-2 transition duration-300 ease-in-out shadow-md
                           transform hover:scale-105 active:scale-95" 
                    wire:loading.attr="disabled" 
                    wire:target="generateHomilia" 
                    wire:loading.class="opacity-70 cursor-not-allowed transform-none"
                >
                    Escrever esboço
                </button>
            </form>
        </div>
    </div>

    <div class="flex-1 p-6 bg-gray-50 dark:bg-gray-950 overflow-y-auto transition-colors duration-300 relative">
    <div
        class="absolute inset-0 bg-white bg-opacity-70 flex items-center justify-center text-blak text-2xl font-semibold z-10 transition-opacity duration-300
               opacity-0 pointer-events-none" 
        wire:loading.class="opacity-100 pointer-events-auto"
        wire:loading.class.remove="opacity-0 pointer-events-none"
        wire:target="generateHomilia"
    >
        <div role="status" class="w-full max-w-lg p-4 border border-gray-200 rounded-lg shadow-sm animate-pulse dark:border-gray-700 md:p-6 bg-white dark:bg-gray-800">
            <div class="flex items-center justify-center h-48 mb-4 bg-gray-300 rounded-lg dark:bg-gray-700">
                <svg class="w-10 h-10 text-gray-200 dark:text-gray-600 animate-spin" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 16 20">
                    <path d="M14.066 0H7v5a2 2 0 0 1-2 2H0v11a1.97 1.97 0 0 0 1.934 2h12.132A1.97 1.97 0 0 0 16 18V2a1.97 1.97 0 0 0-1.934-2ZM10.5 6a1.5 1.5 0 1 1 0 2.999A1.5 1.5 0 0 1 10.5 6Zm2.221 10.515a1 1 0 0 1-.858.485h-8a1 1 0 0 1-.9-1.43L5.6 10.039a.978.978 0 0 1 .936-.57 1 1 0 0 1 .9.632l1.181 2.981.541-1a.945.945 0 0 1 .883-.522 1 1 0 0 1 .879.529l1.832 3.438a1 1 0 0 1-.031.988Z"/>
                    <path d="M5 5V.13a2.96 2.96 0 0 0-1.293.749L.879 3.707A2.98 2.98 0 0 0 .13 5H5Z"/>
                </svg>
            </div>
            <div class="h-2.5 bg-gray-200 rounded-full dark:bg-gray-700 w-48 mb-4"></div>
            <div class="h-2 bg-gray-200 rounded-full dark:bg-gray-700 mb-2.5"></div>
            <div class="h-2 bg-gray-200 rounded-full dark:bg-gray-700 mb-2.5"></div>
            <div class="h-2 bg-gray-200 rounded-full dark:bg-gray-700"></div>
            <div class="flex items-center mt-4">
               <svg class="w-10 h-10 me-3 text-gray-200 dark:text-gray-700" aria-hidden="true" xmlns="http://www.w3.org/2000/svg" fill="currentColor" viewBox="0 0 20 20">
                    <path d="M10 0a10 10 0 1 0 10 10A10.011 10.011 0 0 0 10 0Zm0 5a3 3 0 1 1 0 6 3 3 0 0 1 0-6Zm0 13a8.949 8.949 0 0 1-4.951-1.488A3.987 3.987 0 0 1 9 13h2a3.987 3.987 0 0 1 3.951 3.512A8.949 8.949 0 0 1 10 18Z"/>
                </svg>
                <div>
                    <div class="h-2.5 bg-gray-200 rounded-full dark:bg-gray-700 w-32 mb-2"></div>
                    <div class="w-48 h-2 bg-gray-200 rounded-full dark:bg-gray-700"></div>
                </div>
            </div>
            <p class="mt-4 text-gray-320 dark:text-gray-500">Aguarde, gerando seu esboço...</p>
        </div>

    </div>

    @if($message)
        <x-homilia-result :message="$message" />
    @else
        <div class="flex items-center justify-center text-gray-500 dark:text-gray-400 text-xl font-medium h-full transition-colors duration-300">
            Seu esboço aparecerá aqui.
        </div>
    @endif
</div>
</div>
