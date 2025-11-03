<x-app-layout>
    <x-slot name="header">
        <div class="flex justify-between items-center">
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">
                {{ __('Group Management') }}
            </h2>
            <button type="button"
                    class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded"
                    onclick="openCreateGroupModal()">
                {{ __('Crear grupo') }}
            </button>
        </div>
    </x-slot>

    <div class="py-12">
        <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
            <div class="bg-white overflow-hidden shadow-xl sm:rounded-lg">
                <div class="px-6 py-4 border-b border-gray-200">
                    <h3 class="text-lg leading-6 font-medium text-gray-900">
                        {{ __('User Groups') }}
                    </h3>
                    <p class="mt-1 text-sm text-gray-500">
                        {{ __('Manage user groups with specific storage quotas and permissions.') }}
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

                <div class="overflow-x-auto">
                    <table class="min-w-full divide-y divide-gray-200">
                        <thead class="bg-gray-50">
                            <tr>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    {{ __('Group Name') }}
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    {{ __('Description') }}
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    {{ __('Storage Quota') }}
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    {{ __('Users Count') }}
                                </th>
                                <th class="px-6 py-3 text-left text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    {{ __('Created') }}
                                </th>
                                <th class="px-6 py-3 text-right text-xs font-medium text-gray-500 uppercase tracking-wider">
                                    {{ __('Actions') }}
                                </th>
                            </tr>
                        </thead>
                        <tbody class="bg-white divide-y divide-gray-200">
                            @forelse($groups as $group)
                                <tr>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm font-medium text-gray-900">{{ $group->name }}</div>
                                    </td>
                                    <td class="px-6 py-4">
                                        <div class="text-sm text-gray-500">
                                            {{ $group->description ?: __('No description provided') }}
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="text-sm text-gray-900">
                                            @if($group->quota > 0)
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-blue-100 text-blue-800">
                                                    {{ number_format($group->quota / 1024 / 1024, 2) }} MB
                                                </span>
                                            @else
                                                <span class="inline-flex items-center px-2.5 py-0.5 rounded-full text-xs font-medium bg-gray-100 text-gray-800">
                                                    {{ __('No Limit') }}
                                                </span>
                                            @endif
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap">
                                        <div class="flex items-center">
                                            <div class="text-sm text-gray-900 mr-2">{{ $group->users_count }}</div>
                                            <div class="text-sm text-gray-500">{{ $group->users_count == 1 ? __('user') : __('users') }}</div>
                                        </div>
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-sm text-gray-500">
                                        {{ $group->created_at->format('Y-m-d') }}
                                    </td>
                                    <td class="px-6 py-4 whitespace-nowrap text-right text-sm font-medium">
                                        <div class="flex justify-end space-x-2">
                                            <button type="button"
                                                    class="bg-blue-600 hover:bg-blue-700 text-white px-3 py-1 rounded text-sm font-medium transition-colors duration-200"
                                                    onclick="openEditGroupModal(this.dataset.groupId, this.dataset.groupName, this.dataset.groupDescription, this.dataset.groupQuota)"
                                                    data-group-id="{{ $group->id }}"
                                                    data-group-name="{{ htmlspecialchars($group->name) }}"
                                                    data-group-description="{{ htmlspecialchars($group->description ?? '') }}"
                                                    data-group-quota="{{ $group->quota }}">
                                                {{ __('Editar') }}
                                            </button>
                                            @if($group->users_count == 0)
                                                <form method="POST" action="{{ route('admin.groups.destroy', $group) }}"
                                                      onsubmit="return confirm('¿Estás seguro de que deseas eliminar este grupo?')" class="inline">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="bg-red-600 hover:bg-red-700 text-white px-3 py-1 rounded text-sm font-medium transition-colors duration-200">
                                                        {{ __('Eliminar') }}
                                                    </button>
                                                </form>
                                            @else
                                                <span class="bg-gray-300 text-gray-500 px-3 py-1 rounded text-sm cursor-not-allowed" title="No se puede eliminar grupo con usuarios asignados">
                                                    {{ __('Eliminar') }}
                                                </span>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="px-6 py-4 text-center text-sm text-gray-500">
                                        {{ __('No groups found.') }}
                                        <a href="#" onclick="openCreateGroupModal()" class="text-blue-600 hover:text-blue-900 ml-1">
                                            {{ __('Create the first group') }}
                                        </a>
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if($groups->hasPages())
                    <div class="px-6 py-3 border-t border-gray-200">
                        {{ $groups->links() }}
                    </div>
                @endif
            </div>
        </div>
    </div>

    <!-- Create Group Modal -->
    <div id="createGroupModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <h3 class="text-lg font-medium text-gray-900 mb-4">{{ __('Crear grupo') }}</h3>
                <form method="POST" action="{{ route('admin.groups.store') }}">
                    @csrf
                    <div class="mb-4">
                        <label for="group_name" class="block text-sm font-medium text-gray-700 mb-2">{{ __('Group Name') }}</label>
                        <input type="text" name="name" id="group_name" required
                               class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               placeholder="{{ __('Enter group name') }}">
                    </div>
                    <div class="mb-4">
                        <label for="group_description" class="block text-sm font-medium text-gray-700 mb-2">{{ __('Description') }}</label>
                        <textarea name="description" id="group_description" rows="3"
                                  class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                  placeholder="{{ __('Enter group description (optional)') }}"></textarea>
                    </div>
                    <div class="mb-4">
                        <label for="group_quota" class="block text-sm font-medium text-gray-700 mb-2">{{ __('Storage Quota (bytes)') }}</label>
                        <div class="flex items-center space-x-3">
                            <input type="number" name="quota" id="group_quota" min="0" required
                                   class="flex-1 border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                   placeholder="10485760">
                            <div class="text-sm text-gray-500 whitespace-nowrap" id="quota-display-create">
                                10.00 MB
                            </div>
                        </div>
                        <p class="mt-1 text-sm text-gray-500">
                            {{ __('Set to 0 for no limit') }}
                        </p>
                        <div class="mt-2 grid grid-cols-3 gap-2">
                            <button type="button" onclick="setCreateQuota(1048576)"
                                    class="text-xs px-2 py-1 bg-gray-100 hover:bg-gray-200 rounded">
                                1 MB
                            </button>
                            <button type="button" onclick="setCreateQuota(10485760)"
                                    class="text-xs px-2 py-1 bg-gray-100 hover:bg-gray-200 rounded">
                                10 MB
                            </button>
                            <button type="button" onclick="setCreateQuota(104857600)"
                                    class="text-xs px-2 py-1 bg-gray-100 hover:bg-gray-200 rounded">
                                100 MB
                            </button>
                        </div>
                    </div>
                    <div class="flex justify-end space-x-3">
                        <button type="button" onclick="closeCreateGroupModal()"
                                class="bg-gray-300 hover:bg-gray-400 text-black font-bold py-2 px-4 rounded">
                            {{ __('Cancel') }}
                        </button>
                        <button type="submit"
                                class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            {{ __('Create Group') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <!-- Edit Group Modal -->
    <div id="editGroupModal" class="fixed inset-0 bg-gray-600 bg-opacity-50 hidden overflow-y-auto h-full w-full z-50">
        <div class="relative top-20 mx-auto p-5 border w-96 shadow-lg rounded-md bg-white">
            <div class="mt-3">
                <h3 class="text-lg font-medium text-gray-900 mb-4">{{ __('Edit Group') }}</h3>
                <form method="POST" id="editGroupForm" action="">
                    @csrf
                    @method('PUT')
                    <div class="mb-4">
                        <label for="edit_group_name" class="block text-sm font-medium text-gray-700 mb-2">{{ __('Group Name') }}</label>
                        <input type="text" name="name" id="edit_group_name" required
                               class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                               placeholder="{{ __('Enter group name') }}">
                    </div>
                    <div class="mb-4">
                        <label for="edit_group_description" class="block text-sm font-medium text-gray-700 mb-2">{{ __('Description') }}</label>
                        <textarea name="description" id="edit_group_description" rows="3"
                                  class="w-full border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                  placeholder="{{ __('Enter group description (optional)') }}"></textarea>
                    </div>
                    <div class="mb-4">
                        <label for="edit_group_quota" class="block text-sm font-medium text-gray-700 mb-2">{{ __('Storage Quota (bytes)') }}</label>
                        <div class="flex items-center space-x-3">
                            <input type="number" name="quota" id="edit_group_quota" min="0" required
                                   class="flex-1 border border-gray-300 rounded-md px-3 py-2 focus:outline-none focus:ring-2 focus:ring-blue-500 focus:border-transparent"
                                   placeholder="10485760">
                            <div class="text-sm text-gray-500 whitespace-nowrap" id="quota-display-edit">
                                10.00 MB
                            </div>
                        </div>
                        <p class="mt-1 text-sm text-gray-500">
                            {{ __('Set to 0 for no limit') }}
                        </p>
                        <div class="mt-2 grid grid-cols-3 gap-2">
                            <button type="button" onclick="setEditQuota(1048576)"
                                    class="text-xs px-2 py-1 bg-gray-100 hover:bg-gray-200 rounded">
                                1 MB
                            </button>
                            <button type="button" onclick="setEditQuota(10485760)"
                                    class="text-xs px-2 py-1 bg-gray-100 hover:bg-gray-200 rounded">
                                10 MB
                            </button>
                            <button type="button" onclick="setEditQuota(104857600)"
                                    class="text-xs px-2 py-1 bg-gray-100 hover:bg-gray-200 rounded">
                                100 MB
                            </button>
                        </div>
                    </div>
                    <div class="flex justify-end space-x-3">
                        <button type="button" onclick="closeEditGroupModal()"
                                class="bg-gray-300 hover:bg-gray-400 text-black font-bold py-2 px-4 rounded">
                            {{ __('Cancel') }}
                        </button>
                        <button type="submit"
                                class="bg-blue-600 hover:bg-blue-700 text-white font-bold py-2 px-4 rounded">
                            {{ __('Update Group') }}
                        </button>
                    </div>
                </form>
            </div>
        </div>
    </div>

    <script>
        function formatBytes(bytes) {
            return (bytes / 1024 / 1024).toFixed(2) + ' MB';
        }

        function openCreateGroupModal() {
            document.getElementById('createGroupModal').classList.remove('hidden');
            // Set default quota to 10MB
            document.getElementById('group_quota').value = 10485760;
            document.getElementById('quota-display-create').textContent = formatBytes(10485760);
        }

        function closeCreateGroupModal() {
            document.getElementById('createGroupModal').classList.add('hidden');
            // Clear form
            document.querySelector('#createGroupModal form').reset();
            document.getElementById('quota-display-create').textContent = '0 MB';
        }

        function openEditGroupModal(button) {
            const groupId = button.dataset.groupId;
            const name = button.dataset.groupName;
            const description = button.dataset.groupDescription;
            const quota = button.dataset.groupQuota;

            document.getElementById('edit_group_name').value = name;
            document.getElementById('edit_group_description').value = description || '';
            document.getElementById('edit_group_quota').value = quota;
            document.getElementById('editGroupForm').action = '/admin/groups/' + groupId;
            document.getElementById('quota-display-edit').textContent = quota > 0 ? formatBytes(quota) : 'Sin límite';
            document.getElementById('editGroupModal').classList.remove('hidden');
        }

        function closeEditGroupModal() {
            document.getElementById('editGroupModal').classList.add('hidden');
        }

        function setCreateQuota(bytes) {
            document.getElementById('group_quota').value = bytes;
            document.getElementById('quota-display-create').textContent = formatBytes(bytes);
        }

        function setEditQuota(bytes) {
            document.getElementById('edit_group_quota').value = bytes;
            document.getElementById('quota-display-edit').textContent = formatBytes(bytes);
        }

        // Update quota display when input changes
        document.addEventListener('DOMContentLoaded', function() {
            const createQuotaInput = document.getElementById('group_quota');
            const editQuotaInput = document.getElementById('edit_group_quota');

            if (createQuotaInput) {
                createQuotaInput.addEventListener('input', function(e) {
                    const quota = parseInt(e.target.value) || 0;
                    document.getElementById('quota-display-create').textContent = quota > 0 ? formatBytes(quota) : 'No Limit';
                });
            }

            if (editQuotaInput) {
                editQuotaInput.addEventListener('input', function(e) {
                    const quota = parseInt(e.target.value) || 0;
                    document.getElementById('quota-display-edit').textContent = quota > 0 ? formatBytes(quota) : 'No Limit';
                });
            }
        });

        // Close modals when clicking outside
        window.onclick = function(event) {
            const createModal = document.getElementById('createGroupModal');
            const editModal = document.getElementById('editGroupModal');
            if (event.target == createModal) {
                closeCreateGroupModal();
            }
            if (event.target == editModal) {
                closeEditGroupModal();
            }
        }
    </script>
</x-app-layout>
