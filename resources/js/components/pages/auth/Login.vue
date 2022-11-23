<template>
    <div class="auth min-h-screen flex items-center justify-center backdrop-blur-[20px]">
        <div class="min-w-[330px]">
            <div class="text-center mb-4">
                <img src="/images/logo.svg" alt="logo" class="mx-auto max-w-[60px]">
                <p class="text-[10px] text-slate-300">Chat App, The Real Thing</p>
            </div>
            <div class="p-6 rounded gradient-bg border border-white/10">
                <form @submit.prevent="SubmitForm">
                    <div class="form-group mb-3">
                        <label class="block mb-2 text-xs text-slate-300">Email</label>
                        <input type="email" :class="[ error.email ? 'border-red-500' : 'border-white/10', 'w-full text-xs bg-transparent focus:outline-none text-slate-300 border px-2 py-2 rounded']" v-model="login.email">
                        <p class="text-red-500 text-xs my-2" v-if="error.email">{{ error.email[0] }}</p>
                    </div>
                    <div class="form-group mb-3">
                        <label class="block mb-2 text-xs text-slate-300">Password</label>
                        <input type="password" :class="[ error.password ? 'border-red-500' : 'border-white/10', 'w-full text-xs bg-transparent focus:outline-none text-slate-300 border px-2 py-2 rounded']" v-model="login.password">
                        <p class="text-red-500 text-xs my-2" v-if="error.password">{{ error.password[0] }}</p>
                    </div>
                    <div class="form-group mb-3">
                        <button type="submit" class="w-full text-sm bg-lime-500 text-center text-white px-2 py-2 rounded flex justify-center items-center" :disabled="loading">
                            <div class="w-5 h-5 rounded-full animate-spin border-4 border-solid border-white border-t-transparent shadow-md mr-2" v-if="loading"></div>
                            <span v-else>Sign In</span> 
                        </button>
                    </div>
                </form>
            </div>
            <div class="p-6 rounded gradient-bg border border-white/10 mt-3 text-center">
                <p class="text-slate-300 text-sm">Don't have an account? <router-link to='/register' class="text-blue-500"> Sign up →</router-link></p>
            </div>
        </div>
    </div>
</template>

<script>
export default {
    name: "LoginPage",
    data(){
        return{
            login: {},
            error: {},
            loading: false
        }
    },
    methods:{
        SubmitForm(){
            this.loading = true
            axios.post('/api/login', this.login)
            .then(res=>{
                this.loading = false
                localStorage.setItem('token', res.data.token)
                this.$router.push('/')
            }).catch(err=>{
                this.loading = false
                this.error = err.response.data.errors
            })
        }
    }
}
</script>