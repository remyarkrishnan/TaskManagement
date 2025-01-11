<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { ref } from 'vue';
import { usePage } from '@inertiajs/vue3';
import { router } from '@inertiajs/vue3';

const props = defineProps({
    users: Array ,
    tasks :Array,
    canCreateTask: Boolean,
});
const showModal = ref(false);
const form = useForm({
    title: '',
    description: '',
    selectedUsers: [],
    task_id : '', 
});

const errors = ref({
    title: null,
    description: null,
    selectedUsers: null,
});

const validateForm = () => {
    errors.value = {
        title: form.title ? null : 'Title is required',
        description: form.description ? null : 'Description is required',
    };
};
const openModal = () => {
    showModal.value = true;
};

const closeModal = () => {
    showModal.value = false;
};

const saveTask = () => {
    validateForm();

    if (!errors.value.title && !errors.value.description && !errors.value.selectedUsers) {
        form.post('/create-task', {
            onSuccess: () => {
                closeModal();
                form.reset();
            }
        });
    }
};

const editTask = (taskId) => {
    const task = props.tasks.find(t => t.id === taskId);  
    if (task) {
        form.title = task.title;
        form.description = task.description;
        form.selectedUsers = task.users.map(user => user.id); 
        form.task_id = taskId;
        showModal.value = true;  
    }
};

const deleteTask = (taskId) => {
    if (confirm("Are you sure you want to delete this task?")) {
        form.delete('/tasks/'+taskId, {
            onSuccess: () => {
                closeModal();
                form.reset();
            }
        });
    }
};

const unassignFromMe = (taskId) => {
    router.post(`/tasks/${taskId}/unassign`);
};
const isAssigned = (task) => {
    return task.users.some(user => user.id === usePage().props.auth.user.id);
};

const assignToMe = (taskId) => {
    router.post(`/tasks/${taskId}/assign`);
};

const canChangeStatus = (task) => {
    return task.users.some(user => user.id === usePage().props.auth.user.id);
};

const updateStatus = (task) => {
   
    router.post(`/update-task-status/${task.id}`,{
        status: task.status,
    });
    };
</script>

<template>
    <Head title="Tasks" />

    <AuthenticatedLayout>
        <template #header>
            <h2 class="font-semibold text-xl text-gray-800 leading-tight">Tasks</h2>
        </template>
        
        <div class="flex justify-end items-start p-4" v-if="canCreateTask">
            <button @click="openModal" 
                class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                Create Task +
            </button>
        </div>

        <div v-if="showModal" class="fixed inset-0 flex items-center justify-center bg-black bg-opacity-50 z-50">
            <div class="bg-white w-1/2 rounded-lg shadow-lg p-6">
                <h3 class="text-lg font-semibold mb-4">Create New Task</h3>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700">Title</label>
                    <input v-model="form.title" type="text" required 
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                        placeholder="Enter task title">
                    <p v-if="errors.title" class="text-red-500 text-sm">{{ errors.title }}</p>
                </div>

                <div class="mb-4">
                    <label class="block text-sm font-medium text-gray-700">Description</label>
                    <textarea v-model="form.description" required 
                        class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm"
                        rows="3" placeholder="Enter task description"></textarea>
                    <p v-if="errors.description" class="text-red-500 text-sm">{{ errors.description }}</p>
                </div>

                <div class="mb-4" v-if="users && users.length > 0">
                    <label class="block text-sm font-medium text-gray-700">Users :</label>
                    <select name="users" multiple v-model="form.selectedUsers" required class="mt-1 block w-full border-gray-300 rounded-md shadow-sm focus:ring-indigo-500 focus:border-indigo-500 sm:text-sm">
                        <option v-for="user in users" :key="user.id" :value="user.id">{{ user.name }}</option>
                    </select>
                    
                    <!-- <div class="mt-4">
                        <p>Assigned Users:</p>
                        <ul>
                            <li v-for="user in form.selectedUsers" :key="user">{{ user }}</li>
                        </ul>
                    </div> -->
                </div>

                <div class="flex justify-end space-x-2">
                    <button @click="closeModal" 
                        class="px-4 py-2 bg-gray-500 text-white rounded hover:bg-gray-600">
                        Cancel
                    </button>
                    <button @click="saveTask" 
                        class="inline-flex items-center px-4 py-2 bg-gray-800 border border-transparent rounded-md font-semibold text-xs text-white uppercase tracking-widest hover:bg-gray-700 focus:bg-gray-700 active:bg-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2 transition ease-in-out duration-150">
                        Save
                    </button>
                </div>
            </div>
        </div>

        <!-- Task List -->
        <div class="m-5 p-5" v-if="tasks && tasks.length > 0">
            <table class="min-w-full bg-white border border-gray-300 rounded-lg shadow-md p-4">
                <thead>
                    <tr class="bg-slate-200">
                        <th class="py-2 px-4 text-left">Title</th>
                        <th class="py-2 px-4 text-left">Description</th>
                        <th class="py-2 px-4 text-left">Assigned Users</th>
                        <th class="py-2 px-4 text-left">Actions</th>
                    </tr>
                </thead>
                <tbody>
                    <tr v-for="task in tasks" :key="task.id" class="border border-1 border-slate-100">
                        <td class="py-2 px-4">{{ task.title }}</td>
                        <td class="py-2 px-4">{{ task.description }}</td>
                        <td class="py-2 px-4">
                            <ul>
                                <li v-for="user in task.users" :key="user.id">{{ user.name }}</li>
                            </ul>
                        </td>
                        <td class="py-2 px-4">
                            <button @click="editTask(task.id)" class="text-blue-600 hover:text-blue-800" v-if="canCreateTask">
                                 Edit
                            </button>
                            <button @click="deleteTask(task.id)" class="ml-4 text-red-600 hover:text-red-800" v-if="canCreateTask">
                                <i class="fas fa-trash"></i> Delete
                            </button>
                            <button v-if="isAssigned(task)" @click="unassignFromMe(task.id)"
                                class="ml-4 px-3 py-1 bg-red-500 text-white rounded hover:bg-red-600">
                                Unassign Me
                            </button>
                            <button v-else @click="assignToMe(task.id)"
                                class="ml-4 px-3 py-1 bg-green-500 text-white rounded hover:bg-green-600">
                                Assign to Me
                            </button>

                            <select v-if="$page.props.auth.user && canChangeStatus(task)" 
                                    v-model="task.status" 
                                    @change="updateStatus(task)" 
                                    class="border rounded-md p-1 ml-2 w-28">
                                <option value="1">In Progress</option>
                                <option value="2">Completed</option>
                            </select>
                        </td>
                    </tr>
                </tbody>
            </table>
        </div>
        <!-- No Tasks Found -->
        <div class="py-12" v-else>
            <div class="max-w-7xl mx-auto sm:px-6 lg:px-8">
                <div class="bg-white overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="p-6 text-gray-900">No Task Found!</div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>

