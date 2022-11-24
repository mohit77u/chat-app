<template>
<div class="profile pb-4 pt-24 animate__animated animate__fadeIn min-h-[85vh]">
    <div class="lg:w-6/12 w-full mx-auto">
        <div class="p-6 rounded gradient-bg border border-white/10 max-h-[650px] overflow-auto">
            <h2 class="text-white text-2xl font-bold mb-3">Edit Chat Group</h2>
            <h3 class="text-slate-200 text-xl mb-3">Group Information</h3>
            <form @submit.prevent="saveGroupDetails">
                <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                    <div class="form-group">
                        <label class="block mb-2 text-xs text-slate-300">Name</label>
                        <input type="text" :class="[ error.name ? 'border-red-500' : 'border-white/10', 'w-full text-xs bg-transparent focus:outline-none text-slate-300 border px-2 py-3 rounded']" v-model="group.name">
                        <p class="text-red-500 text-xs my-2" v-if="error.name">{{ error.name[0] }}</p>
                    </div>
                    <div class="form-group">
                        <label class="block mb-2 text-xs text-slate-300">Image</label>
                        <input type="file" class="border-white/10 w-full text-xs bg-transparent focus:outline-none text-slate-300 border px-2 py-2.5 rounded" @change="imageUpload">
                        <div class="preview my-2" v-if="previewImage">
                            <img :src="previewImage" alt="preview" class="max-w-[100px] min-w-[100px]">
                        </div>
                    </div>
                    <div class="form-group">
                        <label class="block mb-2 text-xs text-slate-300">Add Participants</label>
                        <div class="user-details py-2" >
                            <ul class="text-sm font-medium text-gray-900 bg-white rounded-lg border border-gray-200 dark:bg-transparent dark:border-gray-600 dark:text-white">
                                <li :class="[index == users.length - 1 ? 'border-b-0' : 'border-b border-gray-200', 'w-full rounded-t-lg dark:border-gray-600']" v-for="(user, index) in users" :key="index">
                                    <div class="flex items-center pl-3">
                                        <input :id="'user' + user.id" type="checkbox" :value="user.id" v-model="members" class="w-4 h-4 text-blue-600 bg-gray-100 rounded border-gray-300 focus:ring-blue-500 dark:focus:ring-blue-600 dark:ring-offset-gray-700 focus:ring-2 dark:bg-gray-600 dark:border-gray-500">
                                        <label class="flex items-center py-3 ml-2 w-full text-sm font-medium text-gray-900 dark:text-gray-300">
                                            <div class="details flex items-start">
                                                <img :src="user.avatar" :alt="user.display_name" class="w-5" v-if="user.avatar">
                                                <img src="/images/user-icon.png" alt="user" class="w-5" v-else>
                                                <label :for="'user' + user.id" class="text-white text-sm ml-2">{{ user.display_name }}</label>
                                            </div>
                                        </label>
                                    </div>
                                </li>
                            </ul>
                        </div>
                        <div class="form-group my-6">
                            <button type="submit" class="w-auto text-sm bg-lime-500 text-center text-white px-5 py-2 rounded flex justify-center items-center" :disabled="loading">
                                <div class="w-5 h-5 rounded-full animate-spin border-4 border-solid border-white border-t-transparent shadow-md mr-2" v-if="loading"></div>
                                <span v-else>Update Group</span>
                            </button>
                        </div>
                    </div>

                    <div class="form-group">
                        <label class="block mb-2 text-xs text-slate-300">Group Members</label>
                        <div class="user-details py-2">
                            <ul class="text-sm font-medium text-gray-900 bg-white rounded-lg border border-gray-200 dark:bg-transparent dark:border-gray-600 dark:text-white">
                                <li :class="[index == groupMembers.length - 1 ? 'border-b-0' : 'border-b border-gray-200', 'w-full rounded-t-lg dark:border-gray-600']" v-for="(user, index) in groupMembers" :key="index">
                                    <label class="flex justify-between items-center py-3 ml-2 w-full text-sm font-medium text-gray-900 dark:text-gray-300">
                                        <div class="details flex items-start">
                                            <img :src="user.avatar" :alt="user.display_name" class="w-5" v-if="user.avatar">
                                            <img src="/images/user-icon.png" alt="user" class="w-5" v-else>
                                            <label class="text-white text-sm ml-2">{{ user.display_name }}</label>
                                        </div>
                                        <div class="remove-btn mr-4" v-if="group.created_by != user.user_two">
                                            <img src="/images/icon-delete.png" alt="delete" class="max-w-[15px] inline cursor-pointer" title="remove" @click="removeUser(user)">
                                        </div>
                                    </label>
                                </li>
                            </ul>
                        </div>
                    </div>
                </div>
            </form>
        </div>
        <div id="toast-success" class="flex fixed right-10 top-16 z-50 items-center p-4 mb-4 w-full max-w-xs text-gray-200 rounded gradient-bg border border-white/10 shadow  animate__animated animate__fadeInRight" role="alert" v-if="toast">
            <div class="inline-flex justify-center items-center w-6 h-6 bg-lime-500 rounded-full text-white">
                <svg aria-hidden="true" class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path>
                </svg>
                <span class="sr-only">Check icon</span>
            </div>
            <div class="ml-3 text-sm font-normal text-gray-200">{{ toast }}</div>
            <button type="button" class="ml-auto -mx-1.5 -my-1.5  text-white" data-dismiss-target="#toast-success" aria-label="Close" @click="toast = !toast">
                <span class="sr-only">Close</span>
                <svg aria-hidden="true" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg">
                    <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path>
                </svg>
            </button>
        </div>
    </div>
</div>
</template>

<script>
export default {
    name: "EditGroup",
    props: ['users', 'user', 'id'],
    data() {
        return {
            group: {},
            name: '',
            image: '',
            members: [],
            groupMembers: '',
            contact: {},
            error: {},
            loading: false,
            toast: false,
            previewImage: '',

        }
    },
    watch: {
        id(newVal, oldVal) {
            if (oldVal != newVal) {
                this.getGroupData()
            }
        }
    },
    methods: {
        imageUpload(e) {
            this.image = e.target.files[0]
            this.previewImage = URL.createObjectURL(this.image)
        },
        saveGroupDetails() {
            this.error = ''
            this.members = [...new Set(this.members)];
            console.log(this.members)
            // this.members.push(this.user.id)
            let data = new FormData()
            data.append('name', this.group.name)
            data.append('image', this.image)
            // data.append('my_id', this.group.my_id)
            data.append('users', this.members)
            this.loading = true
            const token = localStorage.getItem('token');
            const config = {
                headers: {
                    'Authorization': 'Bearer ' + token,
                }
            }
            axios.post('/api/group/' + this.group.id + '/update', data, config)
                .then(res => {
                    this.loading = false
                    this.$emit('getGroupList', true)
                    this.Reset()
                    this.getGroupData()
                    this.toast = res.data.message
                    setTimeout(() => {
                        this.toast = false
                    }, 4000)

                }).catch(err => {
                    this.loading = false
                    if (err.response.status === 422) {
                        this.error = err.response.data.errors
                    }
                    this.toast = 'Error on chat group creation.'
                    setTimeout(() => {
                        this.toast = false
                    }, 4000)
                    if (err.response.status === 400) {
                        this.toast = err.response.data.message
                    }
                    setTimeout(() => {
                        this.toast = false
                    }, 4000)

                })
        },

        getGroupData() {
            const data = {
                user_id: this.user.id
            }
            axios.get('/api/group/' + this.id, data)
                .then((res) => {
                    console.log(res.data)
                    this.group = res.data.group
                    this.previewImage = res.data.group.image
                    this.groupMembers = res.data.users
                }).catch((err) => {
                    console.log(err)
                })
        },

        // Reset
        Reset() {
            Object.assign(this.$data, this.$options.data());
        },

        removeUser(user) {
            const data = {
                user_id: user.user_two,
                group_id: this.group.id,
            }
            axios.post('/api/remove-user/group/', data)
                .then((res) => {
                    this.getGroupData()
                    this.toast = res.data.message
                    setTimeout(() => {
                        this.toast = false
                    }, 4000)
                }).catch((err) => {
                    console.log(err)
                    this.toast = 'Error on remove user.'
                    setTimeout(() => {
                        this.toast = false
                    }, 4000)
                })
        }
    },
    mounted() {
        this.getGroupData()
        // console.log(this.users)
    }

}
</script>

