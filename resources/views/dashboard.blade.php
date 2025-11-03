<x-app-layout>
    <x-slot name="header">
        <h2 class="font-semibold text-xl text-gray-800 leading-tight">
            {{ __('Dashboard de Almacenamiento Seguro') }}
        </h2>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <!-- Success/Error Messages -->
            @if (session('success'))
                <div class="mb-4 bg-green-100 border border-green-400 text-green-700 px-4 py-3 rounded" role="alert">
                    {{ session('success') }}
                </div>
            @endif

            @if (session('error'))
                <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded" role="alert">
                    {{ session('error') }}
                </div>
            @endif

            <div class="grid grid-cols-1 lg:grid-cols-3 gap-6">
                <!-- Storage Quota Card -->
                <div class="lg:col-span-3">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Información de Almacenamiento</h3>
                            <div class="space-y-2">
                                <div class="flex justify-between">
                                    <span class="text-sm font-medium text-gray-500">Usado:</span>
                                    <span class="text-sm font-bold text-gray-900">{{ $storageInfo['used_formatted'] }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-sm font-medium text-gray-500">Cuota Total:</span>
                                    <span class="text-sm font-bold text-gray-900">{{ $storageInfo['quota_formatted'] }}</span>
                                </div>
                                <div class="flex justify-between">
                                    <span class="text-sm font-medium text-gray-500">Restante:</span>
                                    <span class="text-sm font-bold text-green-600">{{ $storageInfo['remaining_formatted'] }}</span>
                                </div>
                            </div>
                            <!-- Progress Bar -->
                            <div class="mt-4">
                                <div class="w-full bg-gray-200 rounded-full h-2">
                                    <div class="bg-blue-600 h-2 rounded-full" style="width: {{ $storageInfo['percentage'] }}%"></div>
                                </div>
                                <p class="text-xs text-gray-500 mt-1">{{ $storageInfo['percentage'] }}% usado</p>
                            </div>
                        </div>
                    </div>
                </div>

                <!-- File Upload Section -->
                <div class="lg:col-span-2">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Subir Archivo</h3>
                            <form id="fileUploadForm" enctype="multipart/form-data">
                                @csrf
                                <div class="mb-4">
                                    <label for="file" class="block text-sm font-medium text-gray-700 mb-2">
                                        Seleccionar archivo
                                    </label>
                                    <input type="file"
                                           id="file"
                                           name="file"
                                           accept="*"
                                           class="block w-full text-sm text-gray-500 file:mr-4 file:py-2 file:px-4 file:rounded-full file:border-0 file:text-sm file:font-semibold file:bg-blue-50 file:text-blue-700 hover:file:bg-blue-100">
                                    <p class="text-xs text-gray-500 mt-1">Tamaño máximo: 10MB</p>
                                </div>
                                <button type="submit"
                                        class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                                    Subir Archivo
                                </button>
                            </form>
                            <div id="uploadStatus" class="mt-4 hidden"></div>
                        </div>
                    </div>
                </div>

                <!-- Admin Quick Access -->
                @if(auth()->user()->isAdmin())
                <div class="lg:col-span-1">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Panel de Administración</h3>
                            <div class="space-y-2">
                                <a href="{{ route('admin.dashboard') }}"
                                   class="block w-full bg-green-600 hover:bg-green-700 text-white text-center py-2 px-4 rounded">
                                    Dashboard Admin
                                </a>
                                <a href="{{ route('admin.users') }}"
                                   class="block w-full bg-gray-600 hover:bg-gray-700 text-white text-center py-2 px-4 rounded">
                                    Gestionar Usuarios
                                </a>
                                <a href="{{ route('admin.groups') }}"
                                   class="block w-full bg-gray-600 hover:bg-gray-700 text-white text-center py-2 px-4 rounded">
                                    Gestionar Grupos
                                </a>
                                <a href="{{ route('admin.configurations') }}"
                                   class="block w-full bg-gray-600 hover:bg-gray-700 text-white text-center py-2 px-4 rounded">
                                    Configuraciones
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
                @endif

                <!-- Files List -->
                <div class="lg:col-span-3">
                    <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                        <div class="p-6">
                            <h3 class="text-lg font-medium text-gray-900 mb-4">Mis Archivos</h3>
                            <div id="filesList">
                                @if($files->count() > 0)
                                    <div class="overflow-x-auto">
                                        <table class="min-w-full divide-y divide-gray-200">
                                            <thead class="bg-gray-50">
                                                <tr>
                                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Nombre</th>
                                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Tamaño</th>
                                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Fecha</th>
                                                    <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">Acciones</th>
                                                </tr>
                                            </thead>
                                            <tbody class="bg-white divide-y divide-gray-200">
                                                @foreach($files as $file)
                                                <tr>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                                        {{ $file->original_name }}
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                        {{ number_format($file->size / 1024, 2) }} KB
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                        {{ $file->created_at->format('Y-m-d H:i') }}
                                                    </td>
                                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                                        <button onclick="deleteFile({{ $file->id }})"
                                                                class="text-red-600 hover:text-red-900">
                                                            Eliminar
                                                        </button>
                                                    </td>
                                                </tr>
                                                @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                @else
                                    <p class="text-gray-500 text-center py-4">No tienes archivos subidos</p>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <!-- JavaScript for file management -->
    <script src="{{ asset('js/file-upload.js') }}?v={{ filemtime(public_path('js/file-upload.js')) }}"></script>
</x-app-layout>
