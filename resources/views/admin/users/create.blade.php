<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Crear Nuevo Usuario') }}
            </h2>
            <a href="{{ route('admin.users') }}"
               class="bg-gray-500 hover:bg-gray-600 text-white font-bold py-2 px-4 rounded">
                {{ __('Volver a Usuarios') }}
            </a>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-2xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">
                        {{ __('Información del Nuevo Usuario') }}
                    </h3>
                </div>

                @if(session('success'))
                    <div class="bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded m-4">
                        {{ session('success') }}
                    </div>
                @endif

                @if(session('error'))
                    <div class="bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded m-4">
                        {{ session('error') }}
                    </div>
                @endif

                @if($errors->any())
                    <div class="m-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                        <strong>Errores encontrados:</strong>
                        <ul class="list-disc ml-4">
                            @foreach($errors->all() as $error)
                                <li>{{ $error }}</li>
                            @endforeach
                        </ul>
                    </div>
                @endif

                <form method="POST" action="{{ route('admin.users.store') }}" class="p-6">
                    @csrf

                    <div class="grid grid-cols-1 gap-6">
                        <div>
                            <label for="name" class="block text-sm font-medium text-gray-700">
                                {{ __('Nombre Completo') }}
                            </label>
                            <input type="text" name="name" id="name" required
                                   value="{{ old('name') }}"
                                   class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        </div>

                        <div>
                            <label for="email" class="block text-sm font-medium text-gray-700">
                                {{ __('Email') }}
                            </label>
                            <input type="email" name="email" id="email" required
                                   value="{{ old('email') }}"
                                   class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                        </div>

                        <div>
                            <label for="password" class="block text-sm font-medium text-gray-700">
                                {{ __('Contraseña') }}
                            </label>
                            <input type="password" name="password" id="password" required
                                   class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                            <p class="mt-1 text-sm text-gray-500">
                                {{ __('Mínimo 8 caracteres') }}
                            </p>
                        </div>

                        <div>
                            <label for="role" class="block text-sm font-medium text-gray-700">
                                {{ __('Rol') }}
                            </label>
                            <select name="role" id="role" required
                                    class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                <option value="usuario" {{ old('role') === 'usuario' ? 'selected' : '' }}>
                                    {{ __('Usuario') }}
                                </option>
                                <option value="admin" {{ old('role') === 'admin' ? 'selected' : '' }}>
                                    {{ __('Administrador') }}
                                </option>
                            </select>
                        </div>

                        <div>
                            <label for="group_id" class="block text-sm font-medium text-gray-700">
                                {{ __('Grupo (Opcional)') }}
                            </label>
                            <select name="group_id" id="group_id"
                                    class="mt-1 block w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-blue-500 focus:border-blue-500">
                                <option value="">{{ __('Sin Grupo') }}</option>
                                @foreach($groups as $group)
                                    <option value="{{ $group->id }}"
                                            {{ old('group_id') == $group->id ? 'selected' : '' }}>
                                        {{ $group->name }}
                                    </option>
                                @endforeach
                            </select>
                        </div>
                    </div>

                    <div class="mt-8 flex justify-end space-x-4">
                        <a href="{{ route('admin.users') }}"
                           class="bg-gray-300 hover:bg-gray-400 text-black font-bold py-2 px-4 rounded">
                            {{ __('Cancelar') }}
                        </a>
                        <button type="submit"
                                class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            {{ __('Crear Usuario') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-app-layout>
