<template>
    <div class="profile pb-4 pt-24 animate__animated animate__fadeInRight min-h-[85vh]">
        <div class="lg:w-6/12 w-full mx-auto">
            <div class="p-6 rounded gradient-bg border border-white/10 max-h-[650px] overflow-auto">
                <h2 class="text-white text-2xl font-bold mb-3">Create Chat Group</h2>
                <h3 class="text-slate-200 text-xl mb-3">Group Information</h3>
                <form @submit.prevent="saveGroupDetails" enctype="multipart/form-data">
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
                            <div class="user-details py-2" v-for="(user, index) in users" :key="index">
                                <label class="flex items-start">
                                    <input :id="'user' + user.id" type="checkbox" class="w-5 h-5 rounded border border-white/10 bg-transparent mr-4 block" :name="group['user_id'+ user.id]" :value="'user_id' + user.id" v-model="group['user_id'+ user.id]">
                                    <div class="details flex items-start">
                                        <img :src="user.avatar" :alt="user.display_name" class="max-w-[25px]" v-if="user.avatar">
                                        <img src="/images/user-icon.png" alt="user" class="max-w-[25px]" v-else>
                                        <label :for="'user' + user.id" class="text-white text-sm ml-2">{{ user.display_name }}</label>
                                    </div>
                                </label>
                            </div>
                        </div>
                    </div>
                    <div class="form-group my-6">
                        <button type="submit" class="w-auto text-sm bg-lime-500 text-center text-white px-5 py-2 rounded flex justify-center items-center" :disabled="loading">
                            <div class="w-5 h-5 rounded-full animate-spin border-4 border-solid border-white border-t-transparent shadow-md mr-2" v-if="loading"></div>
                            <span v-else>Create Group</span> 
                        </button>
                    </div>
                </form>
            </div>
            <div id="toast-success" class="flex fixed top-0 right-5 z-50 items-center p-4 mb-4 w-full max-w-xs text-gray-200 rounded gradient-bg border border-white/10 shadow animate__animated animate__fadeInRight" role="alert" v-if="toast">
                <div class="inline-flex justify-center items-center w-6 h-6 bg-lime-500 rounded-full text-white">
                    <svg aria-hidden="true" class="w-4 h-4" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M16.707 5.293a1 1 0 010 1.414l-8 8a1 1 0 01-1.414 0l-4-4a1 1 0 011.414-1.414L8 12.586l7.293-7.293a1 1 0 011.414 0z" clip-rule="evenodd"></path></svg>
                    <span class="sr-only">Check icon</span>
                </div>
                <div class="ml-3 text-sm font-normal text-gray-200">{{ toast }}</div>
                <button type="button" class="ml-auto -mx-1.5 -my-1.5  text-white" data-dismiss-target="#toast-success" aria-label="Close" @click="toast = !toast">
                    <span class="sr-only">Close</span>
                    <svg aria-hidden="true" class="w-5 h-5" fill="currentColor" viewBox="0 0 20 20" xmlns="http://www.w3.org/2000/svg"><path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"></path></svg>
                </button>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: "CreateGroup",
    props:['users'],
    data(){
        return{
            user: {},
            group: {},
            groupUsers: {},
            contact: {},
            error: {},
            loading: false,
            toast: false,
            previewImage: '',

        }
    },
    methods:{
        imageUpload(e){
            this.group.image = e.target.files[0]
            this.previewImage = URL.createObjectURL(this.group.image)
        },
        saveGroupDetails(){
            this.group.my_id = this.user.id
            console.log(this.group)
            let data = new FormData()
            data.append('name', this.group.name)
            data.append('image', this.group.image)
            data.append('my_id', this.group.my_id)
            data.append('users', [this.group])
            this.loading = true
            axios.post('/api/create-group', data, this.group)
            .then(res=>{ 
                this.loading = false
                this.$emit('getGroupList', true)
                this.Reset()
                this.getUserDetails()
                this.toast = res.data.message
                setTimeout(()=>{
                    this.toast = false
                }, 4000)
                
            }).catch(err=>{
                this.loading = false
                if(err.response.status === 422)
                {
                    this.error = err.response.data.errors
                }
                this.toast = 'Error on chat group creation.'
                setTimeout(()=>{
                    this.toast = false
                }, 4000)
                if(err.response.status === 400)
                {
                    this.toast = err.response.data.message
                }
                setTimeout(()=>{
                    this.toast = false
                }, 4000)
                
            })
        }, 
        getUserDetails(){
            const token = localStorage.getItem('token');
            const config = {
                headers:{
                    'Authorization': 'Bearer ' + token,
                }
            }
            axios.get('/api/user', config)
            .then(res=>{
                const user = res.data.user
                this.user = user
            }).catch(err=>[
                console.log(err)
            ])
        },

        // Reset
        Reset(){
            Object.assign(this.$data, this.$options.data());
        },
    },
    mounted(){
        this.getUserDetails()
        // console.log(this.users)
    }

}
</script>