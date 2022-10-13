<template>
    <div class="profile pb-4 pt-24 animate__animated animate__fadeInRight min-h-[85vh]">
        <div class="lg:w-6/12 w-full mx-auto">
            <div class="p-6 rounded gradient-bg border border-white/10 max-h-[650px] overflow-auto">
                <h2 class="text-white text-2xl font-bold mb-3">Profile</h2>
                <h3 class="text-slate-200 text-xl mb-3">Account Information</h3>
                <form @submit.prevent="saveUserDetails" enctype="multipart/form-data">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div class="form-group">
                            <label class="block mb-2 text-xs text-slate-300">Name</label>
                            <input type="text" :class="[ error.name ? 'border-red-500' : 'border-white/10', 'w-full text-xs bg-transparent focus:outline-none text-slate-300 border px-2 py-2 rounded']" v-model="user.name">
                            <p class="text-red-500 text-xs my-2" v-if="error.name">{{ error.name[0] }}</p>
                        </div>
                        <div class="form-group">
                            <label class="block mb-2 text-xs text-slate-300">Email</label>
                            <input type="email" :class="[ error.email ? 'border-red-500' : 'border-white/10', 'w-full text-xs bg-transparent focus:outline-none text-slate-300 border px-2 py-2 rounded']" v-model="user.email">
                            <p class="text-red-500 text-xs my-2" v-if="error.email">{{ error.email[0] }}</p>
                        </div>
                        <div class="form-group">
                            <label class="block mb-2 text-xs text-slate-300">Password</label>
                            <input type="password" :class="[ error.password ? 'border-red-500' : 'border-white/10', 'w-full text-xs bg-transparent focus:outline-none text-slate-300 border px-2 py-2 rounded']" v-model="user.password">
                            <p class="text-red-500 text-xs my-2" v-if="error.password">{{ error.password[0] }}</p>
                        </div>
                        <div class="form-group">
                            <label class="block mb-2 text-xs text-slate-300">Password Confirmation</label>
                            <input type="password" :class="[ error.password_confirmation ? 'border-red-500' : 'border-white/10', 'w-full text-xs bg-transparent focus:outline-none text-slate-300 border px-2 py-2 rounded']" v-model="user.password_confirmation">
                            <p class="text-red-500 text-xs my-2" v-if="error.password_confirmation">{{ error.password_confirmation[0] }}</p>
                        </div>
                        <div class="form-group">
                            <label class="block mb-2 text-xs text-slate-300">Avatar</label>
                            <input type="file" class='border-white/10 w-full text-xs bg-transparent focus:outline-none text-slate-300 border px-2 py-2 rounded' @change="UploadAvatar">
                            <p class="text-slate-200 text-xs my-2">Note: Please upload image of dimensions 70x70</p>
                            <p class="text-red-500 text-xs my-2" v-if="error.avatar">{{ error.avatar[0] }}</p>
                        </div>
                    </div>
                    <div class="my-3">
                        <img :src="previewImage ? previewImage : user.avatar" alt="avatar" v-if="previewImage || user.avatar" class="max-w-[70px]" />
                    </div>
                    <div class="form-group my-6">
                        <button type="submit" class="w-auto text-sm bg-lime-500 text-center text-white px-5 py-2 rounded flex justify-center items-center" :disabled="loading">
                            <div class="w-5 h-5 rounded-full animate-spin border-4 border-solid border-white border-t-transparent shadow-md mr-2" v-if="loading"></div>
                            <span v-else>Save Details</span> 
                        </button>
                    </div>
                </form>
            </div>
            <div id="toast-success" class="flex fixed top-0 right-5 z-50 items-center p-4 mb-4 w-full max-w-xs text-gray-200 rounded gradient-bg border border-white/10 shadow dark:text-gray-400 dark:bg-gray-800" role="alert" v-if="toast">
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
    name: "ProfilePage",
    props:['user'],
    data(){
        return{
            // user: {},
            previewImage: '',
            error: {},
            loading: false,
            toast: false,

        }
    },
    methods:{
        UploadAvatar(e){
            this.user.avatar = e.target.files[0]
            this.previewImage = URL.createObjectURL(this.user.avatar)

        },
        saveUserDetails(){
            console.log(this.user)
            this.loading = true
            let data = new FormData();
            data.append('id', this.user.id);
            data.append('name', this.user.name);
            data.append('email', this.user.email);
            data.append('avatar', this.user.avatar);
            data.append('password', this.user.password);
            axios.post('/api/update-user', data)
            .then(res=>{
                this.loading = false
                this.toast = 'Profile updated successfully.'
                setTimeout(()=>{
                    this.toast = false
                }, 4000)
                this.getUserDetails()
                
            }).catch(err=>{
                this.loading = false
                if(err.response.status === 422)
                {
                    this.error = err.response.data.errors
                }
                this.toast = 'Error on profile update.'
                setTimeout(()=>{
                    this.toast = false
                }, 4000)
            })
        }, 
        // getUserDetails(){
        //     const token = localStorage.getItem('token');
        //     const config = {
        //         headers:{
        //             'Authorization': 'Bearer ' + token,
        //         }
        //     }
        //     axios.get('/api/user', config)
        //     .then(res=>{
        //         const user = res.data.user
        //         this.user = user
        //     }).catch(err=>[
        //         console.log(err)
        //     ])
        // },
    },
    mounted(){
        // this.getUserDetails()
    }

}
</script>