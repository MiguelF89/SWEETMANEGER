<!DOCTYPE html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">
    <head>
        <meta charset="utf-8">
        <meta name="viewport" content="width=device-width, initial-scale=1">

        <title>Sweet Manager</title>


        <link rel="preconnect" href="https://fonts.bunny.net">
        <link href="https://fonts.bunny.net/css?family=figtree:400,600&display=swap" rel="stylesheet" />
        
        @vite(['resources/css/app.css', 'resources/js/app.js'] ) 
    </head>
    <body class="antialiased">
        <div class="relative min-h-screen bg-gray-100 dark:bg-gray-900 selection:bg-blue-500 selection:text-white">
            

            @if (Route::has('login'))
                <div class="fixed top-0 right-0 p-6 text-right z-10">
                    @auth

                    <a href="{{ url('/dashboard') }}" 
                           class="inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-150 ease-in-out">
                            <svg class="w-5 h-5 mr-2" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M3 12l2-2m0 0l7-7 7 7M5 10v10a1 1 0 001 1h3m10-11l2 2m-2-2v10a1 1 0 01-1 1h-3m-6 0a1 1 0 001-1v-4a1 1 0 011-1h2a1 1 0 011 1v4a1 1 0 001 1m-6 0h6"></path></svg>
                            Dashboard
                        </a>
                    @else

                        <a href="{{ route('login' ) }}" 
                           class="font-semibold text-gray-600 hover:text-blue-600 dark:text-gray-400 dark:hover:text-blue-400 focus:outline focus:outline-2 focus:rounded-sm focus:outline-blue-500 transition duration-150 ease-in-out px-3 py-2">
                            Log in
                        </a>

                        @if (Route::has('register'))
                      
                            <a href="{{ route('register') }}" 
                               class="ml-4 inline-flex items-center px-4 py-2 border border-transparent text-sm font-medium rounded-md shadow-sm text-white bg-blue-600 hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 transition duration-150 ease-in-out">
                                Register
                            </a>
                        @endif
                    @endauth
                </div>
            @endif

           
            <div class="flex flex-col items-center justify-center min-h-screen pt-16 pb-16">
                <div class="max-w-4xl mx-auto px-6 lg:px-8 text-center">
                    
                    
                    <h1 class="text-6xl font-extrabold text-gray-900 dark:text-white mb-4">
                        Sweet Manager
                    </h1>
                    
                    
                    <p class="text-xl text-gray-600 dark:text-gray-400 mb-12">
                        Gerencie suas instituições, produtos e vendas com eficiência.
                    </p>

                    
                    <div class="grid grid-cols-1 md:grid-cols-3 gap-8">
                        
                  
                        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6 transition duration-300 ease-in-out transform hover:scale-[1.02] border-t-4 border-blue-500">
                            <div class="flex flex-col items-center">
                                <svg class="w-12 h-12 text-blue-500 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M11 16l-4-4m0 0l4-4m-4 4h14m-5 4v1a3 3 0 01-3 3H6a3 3 0 01-3-3v-1m18-8v-1a3 3 0 00-3-3h-4"></path></svg>
                                <h2 class="text-2xl font-semibold text-gray-900 dark:text-white mb-2">Acesso</h2>
                                <p class="text-gray-600 dark:text-gray-400 mb-4">
                                    Já possui uma conta? Faça login para acessar o sistema.
                                </p>
                                <a href="{{ route('login' ) }}" 
                                   class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent text-base font-medium rounded-md text-white bg-blue-600 hover:bg-blue-700">
                                    Fazer Login
                                </a>
                            </div>
                        </div>

                   
                        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6 transition duration-300 ease-in-out transform hover:scale-[1.02] border-t-4 border-green-500">
                            <div class="flex flex-col items-center">
                                <svg class="w-12 h-12 text-green-500 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M18 9v3m0 0v3m0-3h3m-3 0h-3m-2-5a4 4 0 11-8 0 4 4 0 018 0zM3 20a6 6 0 0112 0v1H3v-1z"></path></svg>
                                <h2 class="text-2xl font-semibold text-gray-900 dark:text-white mb-2">Novo Usuário</h2>
                                <p class="text-gray-600 dark:text-gray-400 mb-4">
                                    Crie sua conta e comece a gerenciar agora mesmo.
                                </p>
                                <a href="{{ route('register' ) }}" 
                                   class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent text-base font-medium rounded-md text-white bg-green-600 hover:bg-green-700">
                                    Registrar
                                </a>
                            </div>
                        </div>

                   
                        <div class="bg-white dark:bg-gray-800 overflow-hidden shadow-xl sm:rounded-lg p-6 transition duration-300 ease-in-out transform hover:scale-[1.02] border-t-4 border-orange-500">
                            <div class="flex flex-col items-center">
                                <svg class="w-12 h-12 text-orange-500 mb-4" fill="none" stroke="currentColor" viewBox="0 0 24 24" xmlns="http://www.w3.org/2000/svg"><path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"></path></svg>
                                <h2 class="text-2xl font-semibold text-gray-900 dark:text-white mb-2">Visão Geral</h2>
                                <p class="text-gray-600 dark:text-gray-400 mb-4">
                                    Acesse o painel de controle após o login.
                                </p>
                                @auth
                                    <a href="{{ url('/dashboard' ) }}" 
                                       class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent text-base font-medium rounded-md text-white bg-orange-600 hover:bg-orange-700">
                                        Ir para Dashboard
                                    </a>
                                @else
                                    <button disabled 
                                       class="w-full inline-flex justify-center items-center px-4 py-2 border border-transparent text-base font-medium rounded-md text-white bg-gray-400 cursor-not-allowed">
                                        Acesso Restrito
                                    </button>
                                @endauth
                            </div>
                        </div>
                        
                    </div>
                </div>
            </div>
        </div>
    </body>
</html>
