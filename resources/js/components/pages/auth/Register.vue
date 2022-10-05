<template>
    <div class="auth min-h-screen flex items-center justify-center backdrop-blur-[20px]">
        <div class="min-w-[330px]">
            <div class="text-center mb-4">
                <img src="/images/logo.svg" alt="logo" class="mx-auto max-w-[60px]">
                <p class="text-[10px] text-slate-300">Chat app, the real thing</p>
            </div>
            <div class="p-6 rounded gradient-bg border border-white/10">
                <form @submit.prevent="SubmitForm">
                    <div class="form-group mb-3">
                        <label class="block mb-2 text-xs text-slate-300">Name</label>
                        <input type="text" :class="[ error.name ? 'border-red-500' : 'border-white/10', 'w-full text-xs bg-transparent focus:outline-none text-slate-300 border px-2 py-2 rounded']" v-model="register.name">
                        <p class="text-red-500 text-xs my-2" v-if="error.name">{{ error.name[0] }}</p>
                    </div>
                    <div class="form-group mb-3">
                        <label class="block mb-2 text-xs text-slate-300">Email</label>
                        <input type="email" :class="[ error.email ? 'border-red-500' : 'border-white/10', 'w-full text-xs bg-transparent focus:outline-none text-slate-300 border px-2 py-2 rounded']" v-model="register.email">
                        <p class="text-red-500 text-xs my-2" v-if="error.email">{{ error.email[0] }}</p>
                    </div>
                    <div class="form-group mb-3">
                        <label class="block mb-2 text-xs text-slate-300">Password</label>
                        <input type="password" :class="[ error.password ? 'border-red-500' : 'border-white/10', 'w-full text-xs bg-transparent focus:outline-none text-slate-300 border px-2 py-2 rounded']" v-model="register.password">
                        <p class="text-red-500 text-xs my-2" v-if="error.password">{{ error.password[0] }}</p>
                    </div>
                    <div class="form-group mb-3">
                        <label class="block mb-2 text-xs text-slate-300">Password Confirmation</label>
                        <input type="password" :class="[ error.password_confirmation ? 'border-red-500' : 'border-white/10', 'w-full text-xs bg-transparent focus:outline-none text-slate-300 border px-2 py-2 rounded']" v-model="register.password_confirmation">
                        <p class="text-red-500 text-xs my-2" v-if="error.password_confirmation">{{ error.password_confirmation[0] }}</p>
                    </div>
                    <div class="form-group mb-3">
                         <button type="submit" class="w-full text-sm bg-lime-500 text-center text-white px-2 py-2 rounded flex justify-center items-center" :disabled="loading">
                            <div class="w-5 h-5 rounded-full animate-spin border-4 border-solid border-white border-t-transparent shadow-md mr-2" v-if="loading"></div>
                            <span v-else>Sign Up</span> 
                        </button>
                    </div>
                </form>
            </div>
            <div class="p-6 rounded gradient-bg border border-white/10 mt-3 text-center">
                <p class="text-slate-300 text-sm">Already have an account? <router-link to='/login' class="text-blue-500"> Sign in →</router-link></p>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: "RegisterPage",
    data(){
        return{
            register: {},
            error: {},
            loading: false
        }
    },
    methods:{
        SubmitForm(){
            this.loading = true
            axios.post('/api/register', this.register)
            .then(res=>{
                this.loading = false
                this.$router.push('/login')
            }).catch(err=>{
                this.loading = false
                if(err.response.status === 422)
                {
                    this.error = err.response.data.errors
                }
            })
        }
    }
}
</script>