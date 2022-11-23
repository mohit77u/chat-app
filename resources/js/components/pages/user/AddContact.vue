<template>
    <div class="profile pb-4 pt-24 animate__animated animate__fadeIn min-h-[85vh]">
        <div class="lg:w-6/12 w-full mx-auto">
            <div class="p-6 rounded gradient-bg border border-white/10 max-h-[650px] overflow-auto">
                <h2 class="text-white text-2xl font-bold mb-3">Add New Contact</h2>
                <h3 class="text-slate-200 text-xl mb-3">Contact Information</h3>
                <form @submit.prevent="saveUserDetails" enctype="multipart/form-data">
                    <div class="grid grid-cols-1 sm:grid-cols-2 gap-6">
                        <div class="form-group">
                            <label class="block mb-2 text-xs text-slate-300">Display Name</label>
                            <input type="text" :class="[ error.display_name ? 'border-red-500' : 'border-white/10', 'w-full text-xs bg-transparent focus:outline-none text-slate-300 border px-2 py-2 rounded']" v-model="contact.display_name">
                            <p class="text-red-500 text-xs my-2" v-if="error.display_name">{{ error.display_name[0] }}</p>
                        </div>
                        <div class="form-group">
                            <label class="block mb-2 text-xs text-slate-300">Mobile Number</label>
                            <input type="text" :class="[ error.mobile_number ? 'border-red-500' : 'border-white/10', 'w-full text-xs bg-transparent focus:outline-none text-slate-300 border px-2 py-2 rounded']" v-model="contact.mobile_number" maxlength="10">
                            <p class="text-red-500 text-xs my-2" v-if="error.mobile_number">{{ error.mobile_number[0] }}</p>
                        </div>
                    </div>
                    <div class="form-group my-6">
                        <button type="submit" class="w-auto text-sm bg-lime-500 text-center text-white px-5 py-2 rounded flex justify-center items-center" :disabled="loading">
                            <div class="w-5 h-5 rounded-full animate-spin border-4 border-solid border-white border-t-transparent shadow-md mr-2" v-if="loading"></div>
                            <span v-else>Add to contacts</span> 
                        </button>
                    </div>
                </form>
            </div>
            <div id="toast-success" class="flex fixed right-10 top-16 z-50 items-center p-4 mb-4 w-full max-w-xs text-gray-200 rounded gradient-bg border border-white/10 shadow  animate__animated animate__fadeInRight" role="alert" v-if="toast">
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
    name: "AddContactPage",
    props:['user'],
    data(){
        return{
            // user: {},
            contact: {},
            error: {},
            loading: false,
            toast: false,

        }
    },
    methods:{
        saveUserDetails(){
            console.log(this.contact)
            this.contact.user_one = this.user.id
            this.loading = true
            axios.post('/api/add-contact', this.contact)
            .then((res) => {
                this.loading = false
                this.$emit('getContactList', true)
                this.Reset()
                this.getUserDetails()
                this.toast = res.data.message
                setTimeout(()=>{
                    this.toast = false
                }, 4000)
                
            }).catch((err) => {
                this.loading = false
                if(err.response.data.status === 422)
                {
                    this.error = err.response.data.errors
                }
                this.toast = 'Error on contact add.'
                setTimeout(()=>{
                    this.toast = false
                }, 4000)
                
            })

        },

        // Reset
        Reset(){
            Object.assign(this.$data, this.$options.data());
        },
    },
    mounted(){
        // this.getUserDetails()
    }

}
</script>