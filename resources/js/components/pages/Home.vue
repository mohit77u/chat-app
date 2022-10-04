<template>
    <div class="home-page min-h-screen max-h-screen overflow-auto">
        <section class="min-h-screen backdrop-blur-[50px]">
            <div class="flex flex-wrap gap-y-4">
                <div :class="[menu ? 'sm:w-96 w-[300px] fixed top-o left-0 z-50 max-h-screen h-full overflow-y-auto p-4 bg-slate-900' : 'hidden md:block fixed top-o left-0 z-50 max-h-screen h-full overflow-y-auto p-4 bg-slate-900']">
                    <div class="top-bar">
                        <div class="flex justify-between items-center">
                            <div class="user-icon">
                                <h2 class="text-white text-2xl font-medium">Chat App</h2>
                            </div>
                            <div class="contact-book-icon">
                                <img src="/images/icon-close.png" alt="close" class="md:hidden block max-w-[40px] max-h-[40px] cursor-pointer" @click="menu = !menu">
                            </div>
                        </div>
                    </div>
                    <div class="message-box py-5">
                        <div class="single-message cursor-pointer bg-white/5 my-3" v-for="(user,index) in users.slice(1,-1)" :key="index">
                            <div class="main-message flex backdrop-blur-[20px] rounded px-4 py-2" @click="getUserMessage(user)">
                                <div class="left mr-2 w-2/12">
                                    <img src="/images/icon-boy.png" alt="user" class="max-w-[65px] max-h-[65px] rounded-full">
                                </div>
                                <div class="right mt-1 w-10/12 pl-3">
                                    <div class="message flex justify-between">
                                        <div class="name-message w-9/12">
                                            <h5 class="text-white text-md text-ellipsis sm:max-w-[170px] max-w-[110px] whitespace-nowrap overflow-hidden">{{ user.name }}</h5>
                                            <p class="text-gray-400 text-sm text-ellipsis sm:max-w-[170px] max-w-[110px] whitespace-nowrap overflow-hidden">A chat app with a little bit of everything</p>
                                        </div>
                                        <div class="w-3/12">
                                            <p class="text-sm text-right text-gray-400 whitespace-nowrap">01:25 PM</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="md:ml-96 w-full p-4 px-12 relative min-h-screen main-screen overflow-x-hidden">
                    <div class="top-main flex items-start sticky top-0 z-20">
                        <div class="left mr-4 block md:hidden">
                            <img src="/images/icon-menu.png" alt="menu" class="cursor-pointer" @click="menu = !menu">
                        </div>
                        <div class="right flex items-start">
                            <img src="/images/icon-boy.png" alt="boy" class="max-w-[50px]">
                            <div class="name-time ml-2 mt-1">
                                <h6 class="text-md text-white">{{ currentUser.name }}</h6>
                                <p class="text-xs text-gray-300">Last seen 1 min ago</p>
                                <!-- <p class="text-xs text-gray-300">Last seen {{ moment(currentUser.created_at).fromNow() }}</p> -->
                            </div>
                        </div>
                    </div>

                    <div class="main-message-box py-4 pb-10 max-h-[85vh] overflow-y-auto relative">
                        <div class="content">
                            <div class="messages my-5" v-for="(createdDate, index) in userMessages" :key="index">
                                <p class="message-date text-center my-10 text-white text-xs">{{index}}</p>
                                <div v-for="(user,index ) in createdDate" :key="index">
                                    <div class="text-right my-4" v-if="user.from_user_id === from_user_id">
                                        <div class="user-message py-2 px-3 rounded-lg w-auto inline-block bg-purple-600">
                                            <p class="text-white text-md">{{ user.message }}</p>
                                            <p class="text-sm text-gray-300">{{ moment(user.created_at).format('h:m A') }}</p>
                                        </div>
                                    </div>
                                    <div class="text-left my-4" v-else>
                                        <div class="my-message py-2 px-3 rounded-lg w-auto inline-block bg-white/10">
                                            <p class="text-white text-md">{{ user.message }}</p>
                                            <p class="text-sm text-gray-300"> {{ moment(user.created_at).format('h:m A') }}</p>
                                        </div>
                                    </div>
                                </div>
                            </div>
                        </div>

                    </div>

                    <div class="bottom md:absolute fixed p-2 w-full left-0 z-40 bg-slate-900 bottom-0 right-0">
                       <div class="flex items-center">
                            <textarea name="message" id="message" class="w-full bg-transparent py-2 px-6 text-gray-300 text-xs resize-none focus:outline-none" v-model="message"></textarea>
                            <button class="w-10 h-10 leading-10 bg-purple-600 rounded-full" @click="sendMessage">
                                <img src="/images/icon-send.png" alt="send" class="max-w-[20px] mx-auto">
                            </button>
                       </div>
                    </div>
                </div>
            </div>
        </section>
        
    </div>
</template>

<script>
import moment from 'moment'
export default {
    name: 'Home',
    data(){
        return{
            menu: false,
            users: [],
            message: '',
            from_user_id: 1,
            to_user_id: 2,
            userMessages: [],
            currentUser: '',
        }
    },
    methods:{
        moment: function (date) {
            return moment(date);
        },
        getUsers(){
            axios.get('/api/users')
            .then(res=>{
                console.log(res.data.users)
                this.users = res.data.users
                this.currentUser = res.data.users[1]
                this.to_user_id = res.data.users[1].id
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
                console.log(res.data)
                this.message = ''
                this.getUserMessage()
            })
        },
        getUserMessage(user){
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
                console.log(res.data.user_messages)
                // console.log(res.data.user_messages[res.data.user_messages.length - 1])
                this.userMessages = res.data.user_messages
            }).catch(err=>{
                console.log(err)
            })  
        }
    },
    mounted(){
        this.getUsers()
        this.getUserMessage()
    }
}
</script>