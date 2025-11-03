<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Configuración del Sistema') }}
            </h2>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">
                        {{ __('Configuración del Sistema') }}
                    </h3>
                    <p class="mt-1 text-sm text-gray-500">
                        {{ __('Configura la configuración global del sistema para almacenamiento de archivos y seguridad.') }}
                    </p>
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

                <form method="POST" action="{{ route('admin.configurations.update') }}" class="p-6">
                    @csrf
                    @method('PUT')

                    <!-- Global Quota Configuration -->
                    <div class="mb-8">
                        <h4 class="text-lg font-medium text-gray-900 mb-4">{{ __('Configuración de Cuota de Almacenamiento') }}</h4>
                        <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
                            <div>
                                <label for="cuota_global" class="block text-sm font-medium text-gray-700 mb-2">
                                    {{ __('Cuota Global de Almacenamiento (bytes)') }}
                                </label>
                                <div class="flex items-center space-x-3">
                                    <input type="number"
                                           id="cuota_global"
                                           name="cuota_global"
                                           value="{{ old('cuota_global', \App\Models\Configuration::getValue('cuota_global', '10485760')) }}"
                                           min="1048576"
                                           class="flex-1 border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent">
                                    <div class="text-sm text-gray-500 whitespace-nowrap">
                                        {{ \App\Models\Configuration::getValue('cuota_global', '10485760') ? number_format(\App\Models\Configuration::getValue('cuota_global', '10485760') / 1024 / 1024, 2) . ' MB' : '0 MB' }}
                                    </div>
                                </div>
                                <p class="mt-1 text-sm text-gray-500">
                                    {{ __('Cuota de almacenamiento predeterminada para todos los usuarios (mínimo 1MB)') }}
                                </p>
                                @error('cuota_global')
                                    <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                                @enderror
                            </div>

                            <div>
                                <label class="block text-sm font-medium text-gray-700 mb-2">
                                    {{ __('Configuraciones Rápidas') }}
                                </label>
                                <div class="flex gap-2">
                                    <button type="button"
                                            onclick="setQuota(1048576)"
                                            class="text-xs px-2 py-1 bg-gray-100 hover:bg-gray-200 rounded">
                                        1 MB
                                    </button>
                                    <button type="button"
                                            onclick="setQuota(5242880)"
                                            class="text-xs px-2 py-1 bg-gray-100 hover:bg-gray-200 rounded">
                                        5 MB
                                    </button>
                                    <button type="button"
                                            onclick="setQuota(10485760)"
                                            class="text-xs px-2 py-1 bg-gray-100 hover:bg-gray-200 rounded">
                                        10 MB
                                    </button>
                                    <button type="button"
                                            onclick="setQuota(52428800)"
                                            class="text-xs px-2 py-1 bg-gray-100 hover:bg-gray-200 rounded">
                                        50 MB
                                    </button>
                                    <button type="button"
                                            onclick="setQuota(104857600)"
                                            class="text-xs px-2 py-1 bg-gray-100 hover:bg-gray-200 rounded">
                                        100 MB
                                    </button>
                                    <button type="button"
                                            onclick="setQuota(1073741824)"
                                            class="text-xs px-2 py-1 bg-gray-100 hover:bg-gray-200 rounded">
                                        1 GB
                                    </button>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- File Type Restrictions -->
                    <div class="mb-8">
                        <h4 class="text-lg font-medium text-gray-900 mb-4">{{ __('Configuración de Seguridad de Archivos') }}</h4>
                        <div>
                            <label for="extensiones_prohibidas" class="block text-sm font-medium text-gray-700 mb-2">
                                {{ __('Extensiones de Archivo Prohibidas') }}
                            </label>
                            <input type="text"
                                   id="extensiones_prohibidas"
                                   name="extensiones_prohibidas"
                                   value="{{ old('extensiones_prohibidas', \App\Models\Configuration::getValue('extensiones_prohibidas', 'exe,bat,js,php,sh')) }}"
                                   class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                   placeholder="exe,bat,js,php,sh">
                            <p class="mt-1 text-sm text-gray-500">
                                {{ __('Extensiones de archivo que no están permitidas para subir. Separa múltiples extensiones con comas.') }}
                            </p>
                            <p class="mt-1 text-sm text-gray-500">
                                <strong>{{ __('Extensiones actuales:') }}</strong>
                                @php
                                    $prohibitedExtensions = explode(',', \App\Models\Configuration::getValue('extensiones_prohibidas', 'exe,bat,js,php,sh'));
                                @endphp
                                @foreach($prohibitedExtensions as $ext)
                                    <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-red-100 text-red-800 mr-1 mt-1">
                                        .{{ trim($ext) }}
                                    </span>
                                @endforeach
                            </p>
                            @error('extensiones_prohibidas')
                                <p class="mt-1 text-sm text-red-600">{{ $message }}</p>
                            @enderror
                        </div>
                    </div>

                    <!-- Security Warning -->
                    <div class="mb-8 bg-yellow-50 border border-yellow-200 rounded-md p-4">
                        <div class="flex">
                            <div class="flex-shrink-0">
                                <svg class="h-5 w-5 text-yellow-400" viewBox="0 0 20 20" fill="currentColor">
                                    <path fill-rule="evenodd" d="M8.257 3.099c.765-1.36 2.722-1.36 3.486 0l5.58 9.92c.75 1.334-.213 2.98-1.742 2.98H4.42c-1.53 0-2.493-1.646-1.743-2.98l5.58-9.92zM11 13a1 1 0 11-2 0 1 1 0 012 0zm-1-8a1 1 0 00-1 1v3a1 1 0 002 0V6a1 1 0 00-1-1z" clip-rule="evenodd"></path>
                                </svg>
                            </div>
                            <div class="ml-3">
                                <h3 class="text-sm font-medium text-yellow-800">
                                    {{ __('Aviso de Seguridad') }}
                                </h3>
                                <div class="mt-2 text-sm text-yellow-700">
                                    <p>
                                        {{ __('Los cambios a esta configuración afectarán a todos los usuarios inmediatamente.') }}
                                        {{ __('Asegúrate de probar las cargas de archivos después de hacer cambios a las restricciones de extensión.') }}
                                    </p>
                                </div>
                            </div>
                        </div>
                    </div>

                    <!-- Action Buttons -->
                    <div class="flex justify-end space-x-4">
                        <button type="button"
                                onclick="resetForm()"
                                class="bg-gray-300 hover:bg-gray-400 text-gray-800 font-bold py-2 px-4 rounded">
                            {{ __('Restablecer Cambios') }}
                        </button>
                        <button type="submit"
                                class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            {{ __('Guardar Configuración') }}
                        </button>
                    </div>
                </form>
            </div>

            <!-- Current Configuration Status -->
            <div class="mt-6 bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">
                        {{ __('Estado de Configuración Actual') }}
                    </h3>
                </div>
                <div class="p-6">
                    <dl class="grid grid-cols-1 gap-x-4 gap-y-6 sm:grid-cols-2">
                        <div>
                            <dt class="text-sm font-medium text-gray-500">{{ __('Cuota Global') }}</dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                {{ number_format(\App\Models\Configuration::getValue('cuota_global', '10485760') / 1024 / 1024, 2) }} MB
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">{{ __('Extensiones Prohibidas') }}</dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                {{ \App\Models\Configuration::getValue('extensiones_prohibidas', 'exe,bat,js,php,sh') }}
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">{{ __('Entradas de Tabla de Configuración') }}</dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                {{ $configurations->count() }}
                            </dd>
                        </div>
                        <div>
                            <dt class="text-sm font-medium text-gray-500">{{ __('Última Actualización') }}</dt>
                            <dd class="mt-1 text-sm text-gray-900">
                                {{ $configurations->max('updated_at') ? $configurations->max('updated_at')->format('Y-m-d H:i:s') : 'N/A' }}
                            </dd>
                        </div>
                    </dl>
                </div>
            </div>
        </div>
    </div>

    <script>
        function setQuota(bytes) {
            document.getElementById('cuota_global').value = bytes;
            updateQuotaDisplay(bytes);
        }

        function updateQuotaDisplay(bytes) {
            const mb = (bytes / 1024 / 1024).toFixed(2);
            // Update the display next to the input
            const quotaDiv = document.querySelector('[class*="text-gray-500 whitespace-nowrap"]');
            if (quotaDiv) {
                quotaDiv.textContent = mb + ' MB';
            }
        }

        function resetForm() {
            // Reset form to original values
            document.getElementById('cuota_global').value = '{{ \App\Models\Configuration::getValue('cuota_global', '10485760') }}';
            document.getElementById('extensiones_prohibidas').value = '{{ \App\Models\Configuration::getValue('extensiones_prohibidas', 'exe,bat,js,php,sh') }}';
        }

        // Update display when quota input changes
        document.getElementById('cuota_global').addEventListener('input', function(e) {
            updateQuotaDisplay(e.target.value);
        });
    </script>
</x-app-layout>
