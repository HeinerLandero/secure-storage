<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Gestión de Usuarios') }}
            </h2>
            <div class="flex space-x-2">
                <button type="button"
                        class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded"
                        onclick="openCreateUserModal()">
                    {{ __('Agregar Nuevo Usuario') }}
                </button>
            </div>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">
                        {{ __('Usuarios') }}
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

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    {{ __('Nombre') }}
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    {{ __('Email') }}
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    {{ __('Rol') }}
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    {{ __('Grupo') }}
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    {{ __('Creado') }}
                                </th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    {{ __('Acciones') }}
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($users as $user)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm font-medium text-gray-900">
                                        {{ $user->name }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $user->email }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <span class="inline-flex px-2 py-1 text-xs font-semibold rounded-full {{ $user->role === 'admin' ? 'bg-red-100 text-red-800' : 'bg-blue-100 text-blue-800' }}">
                                            {{ $user->role === 'admin' ? __('Administrador') : __('Usuario') }}
                                        </span>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $user->group->name ?? __('Sin Grupo') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $user->created_at->format('Y-m-d') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex justify-end space-x-2 gap-2">
                                            <button type="button"
                                                    class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded text-sm font-medium transition-colors duration-200"
                                                    data-user-id="{{ $user->id }}"
                                                    data-user-name="{{ htmlspecialchars($user->name) }}"
                                                    data-user-email="{{ htmlspecialchars($user->email) }}"
                                                    data-user-role="{{ $user->role }}"
                                                    data-user-group="{{ $user->group_id }}"
                                                    onclick="openEditUserModal(this)">
                                                {{ __('Editar') }}
                                            </button>
                                            @if($user->id !== Auth::id())
                                                <form method="POST" action="{{ route('admin.users.destroy', $user) }}"
                                                      onsubmit="return confirm('¿Estás seguro de que deseas eliminar este usuario?')" class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded text-sm font-medium transition-colors duration-200">
                                                        {{ __('Eliminar') }}
                                                    </button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">
                                        {{ __('No se encontraron usuarios.') }}
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($users->hasPages())
                    <div class="px-6 py-3 border-t border-gray-200">
                        {{ $users->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Create User Modal -->
    <div id="createUserModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <h3 class="text-lg font-medium text-gray-900 mb-4">{{ __('Crear Nuevo Usuario') }}</h3>
                <form method="POST" action="{{ route('admin.users.store') }}" id="createUserForm" onsubmit="return validateCreateUser()">
                    @csrf
                    @if($errors->any())
                        <div class="mb-4 bg-red-100 border border-red-400 text-red-700 px-4 py-3 rounded">
                            <strong>Errores:</strong>
                            <ul class="list-disc ml-4">
                                @foreach($errors->all() as $error)
                                    <li>{{ $error }}</li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('Nombre') }}</label>
                        <input type="text" name="name" id="name" required class="w-full border border-gray-300 rounded-md px-3 py-2">
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('Email') }}</label>
                        <input type="email" name="email" id="email" required class="w-full border border-gray-300 rounded-md px-3 py-2">
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('Password') }}</label>
                        <input type="password" name="password" id="password" required class="w-full border border-gray-300 rounded-md px-3 py-2">
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('Role') }}</label>
                        <select name="role" id="role" required class="w-full border border-gray-300 rounded-md px-3 py-2">
                            <option value="usuario">{{ __('User') }}</option>
                            <option value="admin">{{ __('Administrator') }}</option>
                        </select>
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('Group') }}</label>
                        <select name="group_id" id="group_id" class="w-full border border-gray-300 rounded-md px-3 py-2">
                            <option value="">{{ __('No Group') }}</option>
                            @foreach($groups as $group)
                                <option value="{{ $group->id }}">{{ $group->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex justify-end space-x-3">
                        <button type="button" onclick="closeCreateUserModal()"
                                class="bg-gray-300 hover:bg-gray-400 text-black font-bold py-2 px-4 rounded">
                            {{ __('Cancelar') }}
                        </button>
                        <button type="submit" id="submitBtn"
                                class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            {{ __('Crear Usuario') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit User Modal -->
    <div id="editUserModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <h3 class="text-lg font-medium text-gray-900 mb-4">{{ __('Editar Usuario') }}</h3>
                <form method="POST" id="editUserForm" action="">
                    @csrf
                    @method('PUT')
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('Nombre') }}</label>
                        <input type="text" name="name" id="editName" required class="w-full border border-gray-300 rounded-md px-3 py-2">
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('Email') }}</label>
                        <input type="email" name="email" id="editEmail" required class="w-full border border-gray-300 rounded-md px-3 py-2">
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('Password (leave blank to keep current)') }}</label>
                        <input type="password" name="password" class="w-full border border-gray-300 rounded-md px-3 py-2">
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('Role') }}</label>
                        <select name="role" id="editRole" required class="w-full border border-gray-300 rounded-md px-3 py-2">
                            <option value="usuario">{{ __('User') }}</option>
                            <option value="admin">{{ __('Administrator') }}</option>
                        </select>
                    </div>
                    <div class="mb-4">
                        <label class="block text-sm font-medium text-gray-700 mb-2">{{ __('Group') }}</label>
                        <select name="group_id" id="editGroup" class="w-full border border-gray-300 rounded-md px-3 py-2">
                            <option value="">{{ __('No Group') }}</option>
                            @foreach($groups as $group)
                                <option value="{{ $group->id }}">{{ $group->name }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="flex justify-end space-x-3">
                        <button type="button" onclick="closeEditUserModal()"
                                class="bg-gray-300 hover:bg-gray-400 text-black font-bold py-2 px-4 rounded">
                            {{ __('Cancelar') }}
                        </button>
                        <button type="submit"
                                class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            {{ __('Actualizar Usuario') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function openCreateUserModal() {
            document.getElementById('createUserModal').classList.remove('hidden');
        }

        function closeCreateUserModal() {
            document.getElementById('createUserModal').classList.add('hidden');
        }

        function openEditUserModal(button) {
            const userId = button.dataset.userId;
            const name = button.dataset.userName;
            const email = button.dataset.userEmail;
            const role = button.dataset.userRole;
            const groupId = button.dataset.userGroup;

            document.getElementById('editName').value = name;
            document.getElementById('editEmail').value = email;
            document.getElementById('editRole').value = role;
            document.getElementById('editGroup').value = (groupId && groupId !== 'null') ? groupId : '';
            document.getElementById('editUserForm').action = '/admin/users/' + userId;
            document.getElementById('editUserModal').classList.remove('hidden');
        }

        function closeEditUserModal() {
            document.getElementById('editUserModal').classList.add('hidden');
        }

        // Close modals when clicking outside
        window.onclick = function(event) {
            const createModal = document.getElementById('createUserModal');
            const editModal = document.getElementById('editUserModal');
            if (event.target == createModal) {
                closeCreateUserModal();
            }
            if (event.target == editModal) {
                closeEditUserModal();
            }
        }

        // Validate form before submission
        function validateCreateUser() {
            const form = document.getElementById('createUserForm');
            const formData = new FormData(form);
            const submitBtn = document.getElementById('submitBtn');

            // Disable button and show loading
            submitBtn.disabled = true;
            submitBtn.textContent = 'Creando...';

            // Log form data for debugging
            console.log('Form data:', Object.fromEntries(formData));

            return true;
        }
    </script>
</x-app-layout>
