<script setup>
import DashboardLayout from '@/Layouts/DashboardLayout.vue';
import { Head, useForm } from '@inertiajs/vue3';
import { ref, computed } from 'vue';

const form = useForm({
    statement: null,
});

const fileInputRef = ref(null);
const dragOver = ref(false);

const fileName = computed(() => {
    if (!form.statement) return 'No file selected';
    return form.statement.name;
});

const fileSize = computed(() => {
    if (!form.statement) return '';
    return formatFileSize(form.statement.size);
});

const isZipFile = computed(() => {
    if (!form.statement) return false;
    return form.statement.name.toLowerCase().endsWith('.zip');
});

function formatFileSize(bytes) {
    if (bytes === 0) return '0 Bytes';
    const k = 1024;
    const sizes = ['Bytes', 'KB', 'MB', 'GB'];
    const i = Math.floor(Math.log(bytes) / Math.log(k));
    return parseFloat((bytes / Math.pow(k, i)).toFixed(2)) + ' ' + sizes[i];
}

function handleFileDrop(e) {
    e.preventDefault();
    dragOver.value = false;
    const file = e.dataTransfer.files[0];
    if (file) {
        validateAndSetFile(file);
    }
}

function handleFileSelect(e) {
    const file = e.target.files[0];
    if (file) {
        validateAndSetFile(file);
    }
}

function validateAndSetFile(file) {
    const allowedTypes = ['application/pdf', 'text/csv', 'application/zip'];
    const maxSize = file.name.endsWith('.zip') ? 51200000 : 10240000; // 50MB for zip, 10MB for others

    if (!allowedTypes.includes(file.type) &&
        !file.name.toLowerCase().endsWith('.pdf') &&
        !file.name.toLowerCase().endsWith('.csv') &&
        !file.name.toLowerCase().endsWith('.zip')) {
        form.errors.statement = 'Invalid file type. Please upload a PDF, CSV, or ZIP file.';
        return;
    }

    if (file.size > maxSize) {
        form.errors.statement = `File size exceeds ${formatFileSize(maxSize)} limit.`;
        return;
    }

    form.statement = file;
    form.errors.statement = null;
}

function triggerFileInput() {
    if (fileInputRef.value) {
        fileInputRef.value.click();
    }
}

function submit() {
    form.post('/statements/upload', {
        preserveScroll: true,
        onSuccess: () => {
            form.reset();
            if (fileInputRef.value) {
                fileInputRef.value.value = '';
            }
        },
    });
}
</script>

<template>
    <Head title="Upload Statements" />

    <DashboardLayout>
        <template #header>
            <h2 class="text-xl font-semibold leading-tight text-gray-800">
                Upload Statements
            </h2>
        </template>

        <div class="py-12">
            <div class="mx-auto max-w-2xl sm:px-6 lg:px-8">
                <div class="overflow-hidden bg-white shadow-sm sm:rounded-lg">
                    <div class="p-6">
                        <form @submit.prevent="submit">
                            <div
                                @dragover.prevent="dragOver = true"
                                @dragleave.prevent="dragOver = false"
                                @drop.prevent="handleFileDrop"
                                :class="[
                                    'border-2 border-dashed rounded-lg p-8 text-center transition-all duration-200',
                                    dragOver ? 'border-blue-500 bg-blue-50' : 'border-gray-300 hover:border-gray-400',
                                    form.errors.statement ? 'border-red-500 bg-red-50' : ''
                                ]"
                            >
                                <input
                                    ref="fileInputRef"
                                    type="file"
                                    name="statement"
                                    accept=".pdf,.csv,.zip"
                                    class="hidden"
                                    @input="handleFileSelect"
                                />

                                <div class="flex flex-col items-center justify-center space-y-4">
                                    <div class="p-3 rounded-full bg-gray-100">
                                        <svg
                                            class="w-8 h-8 text-gray-500"
                                            fill="none"
                                            stroke="currentColor"
                                            viewBox="0 0 24 24"
                                        >
                                            <path
                                                stroke-linecap="round"
                                                stroke-linejoin="round"
                                                stroke-width="2"
                                                d="M7 16a4 4 0 01-.88-7.903A5 5 0 1115.9 6L16 6a5 5 0 011 9.9M15 13l-3-3m0 0l-3 3m3-3v12"
                                            />
                                        </svg>
                                    </div>

                                    <div class="text-center space-y-2">
                                        <button
                                            type="button"
                                            class="text-blue-600 hover:text-blue-700 font-medium"
                                            @click="triggerFileInput"
                                        >
                                            Click to upload
                                        </button>
                                        <span class="text-gray-500"> or drag and drop</span>

                                        <p class="text-sm text-gray-500">
                                            Upload a single PDF/CSV file (max. 10MB)<br>
                                            or a ZIP file containing multiple PDFs (max. 50MB)
                                        </p>
                                    </div>
                                </div>

                                <!-- Selected File Info -->
                                <div
                                    v-if="form.statement"
                                    class="mt-4 text-sm text-gray-600 space-y-1"
                                >
                                    <p>Selected: {{ fileName }}</p>
                                    <p>Size: {{ fileSize }}</p>
                                    <p v-if="isZipFile" class="text-blue-600">
                                        ZIP file detected - all PDF files inside will be processed
                                    </p>
                                </div>

                                <!-- Error Message -->
                                <div
                                    v-if="form.errors.statement"
                                    class="mt-2 text-sm text-red-600"
                                >
                                    {{ form.errors.statement }}
                                </div>
                            </div>

                            <!-- Upload Progress -->
                            <div
                                v-if="form.progress"
                                class="mt-4"
                            >
                                <div class="h-2 bg-gray-200 rounded">
                                    <div
                                        class="h-2 bg-blue-600 rounded transition-all duration-150"
                                        :style="{ width: `${form.progress.percentage}%` }"
                                    />
                                </div>
                                <p class="mt-2 text-sm text-gray-600 text-center">
                                    {{ form.progress.percentage }}% uploaded
                                </p>
                            </div>

                            <!-- Submit Button -->
                            <div class="mt-6">
                                <button
                                    type="submit"
                                    :disabled="!form.statement || form.processing"
                                    class="w-full px-4 py-2 text-sm font-medium text-white bg-blue-600 rounded-md hover:bg-blue-700 focus:outline-none focus:ring-2 focus:ring-offset-2 focus:ring-blue-500 disabled:opacity-50 disabled:cursor-not-allowed"
                                >
                                    <span v-if="form.processing">
                                        {{ isZipFile ? 'Processing ZIP file...' : 'Uploading...' }}
                                    </span>
                                    <span v-else>
                                        {{ isZipFile ? 'Process ZIP File' : 'Upload Statement' }}
                                    </span>
                                </button>
                            </div>
                        </form>
                    </div>
                </div>
            </div>
        </div>
    </DashboardLayout>
</template>
