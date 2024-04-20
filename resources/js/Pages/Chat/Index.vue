<script setup>
import AuthenticatedLayout from '@/Layouts/AuthenticatedLayout.vue';
import { Head, Link, router, useForm, usePage } from '@inertiajs/vue3';
import { onMounted, ref, watch, reactive, onBeforeMount } from 'vue';
import Loading from '@/Components/Loading.vue';
const props = defineProps({
    rooms: Array,
    messages: Array,
    room: Object,
    user: Object,
})
const typing = reactive({
    loading: false,
    loadingId: null
});
const messageData = reactive([]);
const initialUsers = reactive([]);
const sendPermission = ref(false);
const formData = useForm({
    text: null,
    roomId: props.room ? props.room.id : null
})
onBeforeMount(() => {
    if (props.messages) {
        messageData.push(...props.messages)
    }
})
onMounted(() => {

    const target = document.getElementById('text-list');
    if (target) {
        target.scrollTo({
            top: target.scrollHeight,
            // behavior: 'smooth',
        });
    }
});
if (props.room) {
    window.Echo.private(`chat.rooms.${props.room.id}`)
        .listen('ChatAddMessage', (event) => {
            messageData.push(event.message)
        });

    window.Echo.join(`chat.rooms.${props.room.id}`)
        .here((users) => {
            initialUsers.length = 0;
            initialUsers.push(...users)
            // console.log('Initial users:', users);
        })
        .joining((user) => {
            initialUsers.push(user);
            // console.log('User joined:', user);
        })
        .leaving((user) => {
            const index = initialUsers.indexOf(user);
            initialUsers.splice(index, 1)
            // console.log('User left:', user);
        }).error((error) => {
            // console.error(error);
        });

    window.Echo.private(`chat.rooms.${props.room.id}`)
        .listenForWhisper('typing', (event) => {
            const userItem = document.getElementById('user-item-' + event.user.id);
            if (userItem) {
                typing.loading = true;
                typing.loadingId = `loading-badge-${event.user.id}`;
            }

            setTimeout(() => {
                typing.loading = false;
                typing.loadingId = null;

            }, 2000);
        });
}


function sendMessage() {
    if (formData.text) {
        formData.post(route('chat.store'), {
            onSuccess: () => {
                // router.reload();
                formData.text = null;
            }
        });
    }
}
function showRoom(slug) {
    if (props.room) {
        window.Echo.leave(`chat.rooms.${props.room.id}`);
    }
    router.visit(`/chat/${slug}`, {
        method: 'GET',
        preserveState: false,
    });
}
watch(formData, () => {

    if (props.room) {
        Echo.private(`chat.rooms.${props.room.id}`)
            .whisper('typing', {
                user: props.user
            });
    }


    if (formData.text) {
        sendPermission.value = true;
    } else {
        sendPermission.value = false;
    }
})
watch(messageData, () => {
    // const userId = messageData[messageData.length - 1].user_id
    // if (userId !== props.user.id) {
    setTimeout(() => {
        const target = document.getElementById('text-list');
        if (target)
            target.scrollTo({
                // top: 0,
                top: target.scrollHeight + 64,
                // behavior: 'smooth',
            })
    }, 20);
    // }
})



</script>

<template>
    <Head title="Chat" />

    <AuthenticatedLayout>


        <div class="flex justify-between h-full">
            <!-- Sidebar -->
            <div id="docs-sidebar"
                class="hs-overlay h-[91vh] [--auto-close:lg] hs-overlay-open:translate-x-0 -translate-x-full transition-all duration-300 transform hidden static top-0 start-0 bottom-0 z-[60] w-64 bg-white border-e border-gray-200 pt-7 pb-10 overflow-y-auto lg:block lg:translate-x-0 lg:end-auto lg:bottom-0 [&::-webkit-scrollbar]:w-2 [&::-webkit-scrollbar-thumb]:rounded-full [&::-webkit-scrollbar-track]:bg-gray-100 [&::-webkit-scrollbar-thumb]:bg-gray-300 dark:[&::-webkit-scrollbar-track]:bg-slate-700 dark:[&::-webkit-scrollbar-thumb]:bg-slate-500 dark:bg-gray-800 dark:border-gray-700">
                <div class="px-6">
                    <a class="flex-none text-xl font-semibold dark:text-white" href="#" aria-label="Brand">Chats</a>
                </div>
                <nav class="hs-accordion-group p-6 w-full flex flex-col flex-wrap" data-hs-accordion-always-open>
                    <ul class="space-y-1.5">
                        <li v-for="(room, index) in props.rooms" :key="index">
                            <a class="flex items-center gap-x-3.5 py-2 px-2.5 bg-gray-100 text-sm text-slate-700 rounded-lg hover:bg-gray-100 dark:bg-gray-900 dark:text-white dark:hover:text-black"
                                href="#">
                                <svg class="size-4" xmlns="http://www.w3.org/2000/svg" width="24" height="24"
                                    viewBox="0 0 24 24" fill="none" stroke="currentColor" stroke-width="2"
                                    stroke-linecap="round" stroke-linejoin="round">
                                    <path d="m3 9 9-7 9 7v11a2 2 0 0 1-2 2H5a2 2 0 0 1-2-2z" />
                                    <polyline points="9 22 9 12 15 12 15 22" />
                                </svg>
                                <button @click="showRoom(room.slug)">
                                    {{ room.title }}
                                </button>
                            </a>
                        </li>
                    </ul>
                </nav>
            </div>
            <!-- End Sidebar -->

            <div class="max-w-7xl my-8 w-full flex gap-4 mx-auto sm:px-6 lg:px-8">
                <div
                    class="bg-white flex flex-col justify-between pb-6 dark:bg-gray-800 w-full overflow-hidden shadow-sm sm:rounded-lg">
                    <div>
                        <!-- title -->
                        <div class="bg-[#4F46E5] text-white px-5 py-2">
                            Messages
                        </div>
                        <!-- messages -->
                        <div class="p-6 text-gray-900 dark:text-gray-100">
                        </div>
                        <ul v-if="messages.length" id="text-list" class="mx-6 h-[25rem] overflow-y-auto">
                            <li class="border-b dark:text-slate-200 py-2 last:border-b-0"
                                v-for="(message, index) in messageData" :key="index">
                                <div class="flex gap-1">
                                    <spam class="font-black">
                                        {{ message.user.name }} :
                                    </spam>
                                    <spam>
                                        {{ message.created_at }}

                                    </spam>
                                </div>

                                {{ message.text }}
                            </li>
                        </ul>
                    </div>

                    <!-- new message -->
                    <div class="relative mx-6">
                        <textarea v-model="formData.text" id="hs-textarea-ex-1"
                            class="p-4 pb-12 block w-full border-gray-200 rounded-lg text-sm focus:border-blue-500 focus:ring-blue-500 disabled:opacity-50 disabled:pointer-events-none dark:bg-slate-900 dark:border-gray-700 dark:text-gray-400 dark:focus:ring-gray-600"
                            placeholder="say hello"></textarea>

                        <!-- Toolbar -->
                        <div class="absolute bottom-px inset-x-px p-2 rounded-b-md bg-white dark:bg-slate-900">
                            <div class="flex justify-between items-center">
                                <!-- Button Group -->
                                <div class="flex items-center">
                                    <!-- Mic Button -->
                                    <button type="button"
                                        class="inline-flex flex-shrink-0 justify-center items-center size-8 rounded-lg text-gray-500 hover:text-blue-600 focus:z-10 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:hover:text-blue-500">
                                        <svg class="flex-shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24"
                                            height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <rect width="18" height="18" x="3" y="3" rx="2"></rect>
                                            <line x1="9" x2="15" y1="15" y2="9"></line>
                                        </svg>
                                    </button>
                                    <!-- End Mic Button -->

                                    <!-- Attach Button -->
                                    <button type="button"
                                        class="inline-flex flex-shrink-0 justify-center items-center size-8 rounded-lg text-gray-500 hover:text-blue-600 focus:z-10 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:hover:text-blue-500">
                                        <svg class="flex-shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24"
                                            height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path
                                                d="m21.44 11.05-9.19 9.19a6 6 0 0 1-8.49-8.49l8.57-8.57A4 4 0 1 1 18 8.84l-8.59 8.57a2 2 0 0 1-2.83-2.83l8.49-8.48">
                                            </path>
                                        </svg>
                                    </button>
                                    <!-- End Attach Button -->
                                </div>
                                <!-- End Button Group -->

                                <!-- Button Group -->
                                <div class="flex items-center gap-x-1">
                                    <!-- Mic Button -->
                                    <button type="button"
                                        class="inline-flex flex-shrink-0 justify-center items-center size-8 rounded-lg text-gray-500 hover:text-blue-600 focus:z-10 focus:outline-none focus:ring-2 focus:ring-blue-500 dark:hover:text-blue-500">
                                        <svg class="flex-shrink-0 size-4" xmlns="http://www.w3.org/2000/svg" width="24"
                                            height="24" viewBox="0 0 24 24" fill="none" stroke="currentColor"
                                            stroke-width="2" stroke-linecap="round" stroke-linejoin="round">
                                            <path d="M12 2a3 3 0 0 0-3 3v7a3 3 0 0 0 6 0V5a3 3 0 0 0-3-3Z"></path>
                                            <path d="M19 10v2a7 7 0 0 1-14 0v-2"></path>
                                            <line x1="12" x2="12" y1="19" y2="22"></line>
                                        </svg>
                                    </button>
                                    <!-- End Mic Button -->

                                    <!-- Send Button -->
                                    <button :disabled="!sendPermission" @click="sendMessage" type="button"
                                        class="inline-flex flex-shrink-0 justify-center items-center size-8 rounded-lg text-white bg-blue-600 hover:bg-blue-500 focus:z-10 focus:outline-none focus:ring-2 focus:ring-blue-500 disabled:bg-slate-400 disabled:pointer-events-none">
                                        <svg class="flex-shrink-0 size-3.5" xmlns="http://www.w3.org/2000/svg" width="16"
                                            height="16" fill="currentColor" viewBox="0 0 16 16">
                                            <path
                                                d="M15.964.686a.5.5 0 0 0-.65-.65L.767 5.855H.766l-.452.18a.5.5 0 0 0-.082.887l.41.26.001.002 4.995 3.178 3.178 4.995.002.002.26.41a.5.5 0 0 0 .886-.083l6-15Zm-1.833 1.89L6.637 10.07l-.215-.338a.5.5 0 0 0-.154-.154l-.338-.215 7.494-7.494 1.178-.471-.47 1.178Z">
                                            </path>
                                        </svg>
                                    </button>
                                    <!-- End Send Button -->
                                </div>
                                <!-- End Button Group -->
                            </div>
                        </div>
                        <!-- End Toolbar -->
                    </div>
                </div>
                <div class="bg-white h-fit w-[30%] dark:bg-gray-800 overflow-hidden shadow-sm sm:rounded-lg">
                    <div class="bg-[#4F46E5] text-white px-5 py-2">
                        Users
                    </div>
                    <div class="px-6 py-2 text-gray-900 dark:text-gray-100">
                        <ul>

                            <li :id="'user-item-' + user.user.id" class="dark:text-slate-200 flex mb-2 gap-2"
                                v-for="(user, index) in initialUsers" :key="index">
                                <span>
                                    {{ user.user.name }}
                                </span>
                                <span :id="'loading-badge-' + user.user.id"
                                    v-if="typing.loading === true && typing.loadingId === 'loading-badge-' + user.user.id"
                                    class="bg-[#4F46E5] w-fit flex gap-2 text-white px-2 ml-2 py-1 rounded-xl text-xs">
                                    <span>
                                        typing
                                    </span>
                                    <span class="rotate-180">
                                        <Loading fill="#fff" width="1rem" height="1rem" />
                                    </span>
                                </span>
                            </li>
                        </ul>
                    </div>
                </div>
            </div>
        </div>
    </AuthenticatedLayout>
</template>
