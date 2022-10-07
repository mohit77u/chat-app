<template>
    <div class="home-page min-h-screen max-h-screen overflow-auto">
        <section class="min-h-screen backdrop-blur-[50px]">
            <div class="flex flex-wrap gap-y-4">
                <div :class="[menu ? 'fixed top-o left-0 z-50 max-h-screen h-full overflow-y-auto' : 'hidden md:block fixed top-o md:left-0 left-[-100%] z-50 max-h-screen h-full overflow-y-auto', 'border-r border-white/10 w-64 transition-all duration-500']">
                    <div class="top-bar sticky top-0 z-20 py-2 gradient-bg px-4">
                        <div class="flex justify-between items-center">
                            <div class="user-icon">
                                <img src="/images/logo.svg" alt="logo" class="max-w-[40px]">
                            </div>
                            <div class="contact-book-icon md:hidden block">
                                <img src="/images/icon-close.png" alt="close" class="max-w-[40px] cursor-pointer" @click="menu = !menu">
                            </div>
                            <!-- <div class="sub-menu-icon md:block hidden">
                                <img src="/images/three-dots.png" alt="dots" class="max-w-[26px] cursor-pointer" @click="subMenu = !subMenu">
                            </div> -->
                        </div>
                    </div>
                    <label class="block text-slate-200 txt-md px-4 mb-4 py-2 border-b border-white/20 ">Contacts</label>
                    <div class="message-box pb-5">
                        <div class="cursor-pointer my-2" v-for="(user,index) in users.slice(1,-1)" :key="index">
                            <div class="main-message flex rounded px-4" @click="getUserMessage(user)">
                                <div class="left mr-2 w-2/12">
                                    <img src="/images/user-icon.png" alt="user" class="max-w-[30px] max-h-[65px] rounded-full">
                                    <!-- <img src="/images/icon-boy.png" alt="user" class="max-w-[30px] max-h-[65px] rounded-full"> -->
                                </div>
                                <div class="right mt-1 w-10/12">
                                    <div class="message flex justify-between">
                                        <div class="name-message w-full">
                                            <h5 class="text-white/60 hover:text-white transition-all duration-150 text-sm text-ellipsis sm:max-w-[170px] max-w-[110px] whitespace-nowrap overflow-hidden">{{ user.name }}</h5>
                                            <!-- <p class="text-gray-400 text-sm text-ellipsis sm:max-w-[170px] max-w-[110px] whitespace-nowrap overflow-hidden">A chat app with a little bit of everything</p> -->
                                        </div>
                                        <!-- <div class="w-3/12">
                                            <p class="text-sm text-right text-gray-400 whitespace-nowrap">01:25 PM</p>
                                        </div> -->
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="md:ml-64 w-full p-4 md:px-12 px-4 relative min-h-screen main-screen overflow-x-hidden">
                    <div :class="[currentUser ? 'justify-between' : 'justify-end', 'top-main flex items-center sticky top-0 z-20 min-h-[50px]']">
                        <div class="flex items-start" v-if="currentUser">
                            <div class="right flex items-start">
                                <img src="/images/icon-boy.png" alt="boy" class="max-w-[50px]">
                                <div class="name-time ml-2 mt-1">
                                    <h6 class="text-md text-white">{{ currentUser.name }}</h6>
                                    <p class="text-xs text-gray-300">Last seen 1 min ago</p>
                                    <!-- <p class="text-xs text-gray-300">Last seen {{ moment(currentUser.created_at).fromNow() }}</p> -->
                                </div>
                            </div>
                        </div>
                        <div class="flex items-center justify-end">
                            <div class="sub-menu-icon">
                                <img src="/images/three-dots.png" alt="dots" class="max-w-[26px] cursor-pointer" @click="subMenu = !subMenu">
                            </div>
                            <div class="left ml-4 block md:hidden">
                                <img src="/images/icon-menu.svg" alt="menu" class="cursor-pointer" @click="menu = !menu">
                            </div>
                            <div v-if="subMenu" class="gradient-bg absolute top-[50px] z-50 right-0 min-w-[150px] bg-white/5 rounded">
                                <button class="py-2 text-slate-300 text-sm block w-full text-left hover:bg-[#fff3] border-b border-white/10 px-3" @click="stepChange('profile')">Profile</button>
                                <button class="py-2 text-slate-300 text-sm block w-full text-left hover:bg-[#fff3] border-b border-white/10 px-3">Add New Contact</button>
                                <button class="py-2 text-slate-300 text-sm block w-full text-left hover:bg-[#fff3] px-3" @click="LogoutUser">Logout</button>
                            </div>
                        </div>
                    </div>

                    <transition name="fade">
                        <div v-if="step === 'message'" class="messages-data">
                            <div class="main-message-box py-4 pb-10 max-h-[85vh] overflow-y-auto relative">
                                <div class="content">
                                    <div class="messages my-5" v-for="(createdDate, index) in userMessages" :key="index">
                                        <p class="message-date text-center my-10 text-white text-xs">{{index}}</p>
                                        <div v-for="(user,index ) in createdDate" :key="index">
                                            <div class="text-right my-4" v-if="user.from_user_id === from_user_id">
                                                <div class="user-message py-2 px-3 rounded-lg w-auto inline-block bg-lime-500">
                                                    <p class="text-white text-md">{{ user.message }}</p>
                                                    <p class="text-sm text-gray-300">{{ moment(user.created_at).format('hh:mm A') }}</p>
                                                </div>
                                            </div>
                                            <div class="text-left my-4" v-else>
                                                <div class="my-message py-2 px-3 rounded-lg w-auto inline-block bg-white/10">
                                                    <p class="text-white text-md">{{ user.message }}</p>
                                                    <p class="text-sm text-gray-300"> {{ moment(user.created_at).format('hh:mm A') }}</p>
                                                </div>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div>

                            <div class="bottom md:absolute fixed p-2 w-full left-0 z-40 gradient-bg bottom-0 right-0">
                            <div class="flex items-center">
                                    <textarea name="message" id="message" class="w-full bg-transparent py-2 px-6 text-gray-300 text-xs resize-none focus:outline-none" v-model="message"></textarea>
                                    <button class="w-10 h-10 leading-10 bg-lime-500 rounded-full" @click="sendMessage">
                                        <img src="/images/icon-send.png" alt="send" class="max-w-[20px] mx-auto">
                                    </button>
                            </div>
                            </div>
                        </div>
                    </transition>

                    <transition name="fade">
                        <div v-if="step === 'profile'">
                            <Profile />
                        </div>
                    </transition>
                    
                </div>
            </div>
        </section>
        
    </div>
</template>

<script>
import moment from 'moment'
import Profile from './user/Profile.vue'
export default {
    name: 'Home',
    components: {
        Profile
    },
    data(){
        return{
            menu: false,
            subMenu: false,
            users: [],
            message: '',
            from_user_id: 1,
            to_user_id: 2,
            userMessages: [],
            currentUser: '',
            step: 'message',
        }
    },
    methods:{
        moment: function (date) {
            return moment(date);
        },
        getUsers(){
            axios.get('/api/users')
            .then(res=>{
                this.users = res.data.users
            })
        },
        sendMessage(){
            const data = {
                message: this.message,
                from_user_id: 1,
                to_user_id: 3,
            }
            axios.post('/api/message', data)
            .then(res=>{
                this.message = ''
                this.getUserMessage()
            })
        },
        getUserMessage(user){
            this.step = 'message'
            this.subMenu = false
            if(user){
                this.currentUser = user
                this.to_user_id = user.id
            }
            const data = { 
                from_user_id: this.from_user_id,
                to_user_id : this.to_user_id
            }
            axios.post('/api/user-messages', data)
            .then(res=>{
                this.userMessages = res.data.user_messages
            }).catch(err=>{
                console.log(err)
            })  
        },
        getLoggedInUser(){
            const token = localStorage.getItem('token');
            const config = {
                headers:{
                    'Authorization': 'Bearer ' + token,
                }
            }
            axios.get('/api/user', config)
            .then(res=>{
                const user = res.data.user
                this.from_user_id = user.id
            }).catch(err=>[
                console.log(err)
            ])
        },
        LogoutUser(){
            localStorage.removeItem('token')
            this.$router.push('/login')
        },
        stepChange(profile){
            this.step = profile
            this.subMenu = false
        }
    },
    mounted(){
        this.getUsers()
        this.getLoggedInUser()
        // this.getUserMessage()
    }
}
</script>