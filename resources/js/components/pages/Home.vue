<template>
<div class="home-page min-h-screen max-h-screen overflow-auto">
    <section class="min-h-screen backdrop-blur-[50px]">
        <div class="flex flex-wrap gap-y-4">
            <div :class="[menu ? 'sidebar mobile-menu fixed top-0 left-0 z-50 max-h-screen h-full overflow-y-auto' : 'sidebar fixed top-0 md:left-0 left-[-100%] z-50 max-h-screen h-full overflow-y-auto', 'border-r border-white/10 w-64 transition-all duration-500 bg-[#0f1940] md:bg-transparent']">
                <div class="top-bar sticky top-0 z-20 py-2 bg-transparent px-4 ">
                    <div class="flex justify-between items-center">
                        <div class="logo-icon flex items-center">
                            <img src="/images/logo.svg" alt="logo" class="max-w-[40px]">
                            <p class="text-[12px] md:text-[15px] text-slate-300 ml-2">Chat app, <span> the real thing</span></p>
                        </div>
                        <div class="contact-book-icon md:hidden block">
                            <img src="/images/icon-close.png" alt="close" class="max-w-[30px] cursor-pointer" @click="menu = !menu">
                        </div>
                        <!-- <div class="sub-menu-icon md:block hidden">
                                <img src="/images/three-dots.png" alt="dots" class="max-w-[26px] cursor-pointer" @click="subMenu = !subMenu">
                            </div> -->
                    </div>
                </div>
                <!-- contact list -->
                <label class="block text-slate-200 txt-md px-4 mb-4 py-2 border-b border-t border-white/20 ">Contact List</label>
                <div class="message-box pb-5">
                    <div class="py-1" v-for="(user,index) in users" :key="index">
                        <div class="main-message flex justify-between rounded px-4 relative">
                            <div class="flex cursor-pointer "  @click="getUserMessage(user)">
                                <div class="left mr-2">
                                    <img src="/images/user-icon.png" alt="user" class="max-w-[25px] max-h-[25px] rounded-full">
                                    <!-- <img src="/images/icon-boy.png" alt="user" class="max-w-[30px] max-h-[65px] rounded-full"> -->
                                </div>
                                <div class="right">
                                    <div class="message flex justify-between">
                                        <div class="name-message w-full">
                                            <h5 class="text-white/60 hover:text-white transition-all duration-150 text-sm text-ellipsis sm:max-w-[170px] max-w-[110px] whitespace-nowrap overflow-hidden capitalize">{{ user.display_name }}</h5>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="edit cursor-pointer" @click="editContact(user.contactList_id, 'edit-contact')">
                                <img src="https://img.icons8.com/external-becris-lineal-becris/48/ffffff/external-edit-mintab-for-ios-becris-lineal-becris.png" alt="dots" class="max-w-[15px] opacity-50" style="filter: grayscale(1)">
                            </div>
                        </div>
                    </div>
                </div>
                <!-- groups -->
                <label class="block text-slate-200 txt-md px-4 mb-4 py-2 border-b border-t border-white/20 ">Groups</label>
                <div class="message-box pb-5">
                    <div class="py-1" v-for="(group,index) in groups" :key="index">
                        <div class="main-message flex justify-between rounded px-4">
                            <div class="flex cursor-pointer ">
                                <div class="left mr-2">
                                    <img src="/images/user-icon.png" alt="user" class="max-w-[25px] max-h-[25px] rounded-full">
                                </div>
                                <div class="right">
                                    <div class="message flex justify-between">
                                        <div class="name-message w-full">
                                            <h5 class="text-white/60 hover:text-white transition-all duration-150 text-sm text-ellipsis sm:max-w-[170px] max-w-[110px] whitespace-nowrap overflow-hidden capitalize">{{ group.name }}</h5>
                                        </div>
                                    </div>
                                </div>
                            </div>
                            <div class="edit cursor-pointer">
                                <img src="https://img.icons8.com/external-becris-lineal-becris/48/ffffff/external-edit-mintab-for-ios-becris-lineal-becris.png" alt="dots" class="max-w-[15px] opacity-50" style="filter: grayscale(1)">
                            </div>
                            
                        </div>
                    </div>
                </div>
            </div>
            <div class="md:ml-64 w-full p-4 md:px-12 px-4 relative min-h-screen main-screen overflow-x-hidden">
                <div :class="[currentUser ? 'justify-between' : 'justify-end', 'top-main flex items-center sticky top-0 z-10 min-h-[50px]']">
                    <div class="flex items-start" v-if="currentUser">
                        <div class="right flex items-start">
                            <img src="/images/icon-boy.png" alt="boy" class="max-w-[50px]">
                            <div class="name-time ml-2 mt-1">
                                <h6 class="text-md text-white">{{ currentUser.display_name }}</h6>
                                <p class="text-xs text-gray-300">Last seen 1 min ago</p>
                                <!-- <p class="text-xs text-gray-300">Last seen {{ moment(currentUser.created_at).fromNow() }}</p> -->
                            </div>
                        </div>
                    </div>
                    <div class="flex items-center justify-end">
                        <div class="sub-menu-icon">
                            <img src="/images/icon-settings.png" alt="dots" class="max-w-[30px] cursor-pointer" @click="subMenu = !subMenu">
                        </div>
                        <div class="left ml-4 block md:hidden">
                            <img src="/images/icon-menu.svg" alt="menu" class="cursor-pointer" @click="menu = !menu">
                        </div>
                        <div v-if="subMenu" class="gradient-bg absolute top-[50px] z-50 right-0 min-w-[150px] max-w-[200px] bg-white/5 rounded">
                            <button class="py-2 text-slate-300 text-sm block w-full text-left hover:bg-[#fff3] border-b border-white/10 px-3" @click="stepChange('profile')">Profile</button>
                            <button class="py-2 text-slate-300 text-sm block w-full text-left hover:bg-[#fff3] border-b border-white/10 px-3" @click="stepChange('add-contact')">Add New Contact</button>
                            <button class="py-2 text-slate-300 text-sm block w-full text-left hover:bg-[#fff3] border-b border-white/10 px-3" @click="stepChange('create-group')">Create New Group</button>
                            <button class="py-2 text-slate-300 text-sm block w-full text-left hover:bg-[#fff3] px-3" @click="LogoutUser">Logout</button>
                        </div>
                    </div>
                </div>

                
                <div v-if="step === 'message'" class="messages-data">
                    <div class="main-message-box py-4 pb-10 min-h-[85vh] max-h-[85vh] overflow-y-auto relative" ref="message_container">
                        <div class="content">
                            <div class="messages my-5" v-for="(createdDate, index) in userMessages" :key="index">
                                    <p class="message-date text-center my-10 text-white text-xs">{{index}}</p>
                                    <div v-for="(user,index ) in createdDate" :key="index">
                                        <div class="text-left my-4" v-if="user.from_user_id === from_user_id">
                                            <div class="user-message py-2 px-3 rounded-lg w-auto inline-block bg-white/10 relative">
                                                <p class="text-white text-md" v-if="user.type === 'text'">{{ user.message }}</p>
                                                <div class="download-attachment">
                                                    <a :href="user.message" v-if="user.type === 'image'" download>
                                                        <img :src="user.message" alt="message" class="max-w-[300px] max-h-[300px]" />
                                                    </a>
                                                    <div class="download absolute left-0 top-0 z-[900] flex items-center justify-center w-full h-full bg-black/50 transition-opacity duration-500 opacity-0 hover:opacity-100">
                                                        <a :href="user.message" v-if="user.type === 'image'" download class="p-4 bg-slate-900 rounded-full shadow-lg border border-white/10 hover:bg-gray-600 transition-all duration-200">
                                                            <img src="/images/icon-download.png" alt="download">
                                                        </a>
                                                    </div>
                                                </div>
                                                
                                                <p class="text-sm text-gray-300 mt-2">{{ moment(user.created_at).format('hh:mm A') }}</p>
                                            </div>
                                        </div>
                                        <div class="text-right my-4"  v-if="user.to_user_id === from_user_id">
                                            <div class="my-message py-2 px-3 rounded-lg w-auto inline-block bg-lime-500 relative">
                                                <p class="text-white text-md" v-if="user.type === 'text'">{{ user.message }}</p>
                                                <div class="download-attachment">
                                                    <a :href="user.message" v-if="user.type === 'image'" download>
                                                        <img :src="user.message" alt="message" class="max-w-[300px] max-h-[300px]" />
                                                    </a>
                                                    <div class="download absolute left-0 top-0 z-[900] flex items-center justify-center w-full h-full bg-black/50 transition-opacity duration-500 opacity-0 hover:opacity-100">
                                                        <a :href="user.message" v-if="user.type === 'image'" download class="p-4 bg-slate-900 rounded-full shadow-lg border border-white/10 hover:bg-gray-600 transition-all duration-200">
                                                            <img src="/images/icon-download.png" alt="download" style="filter: grayscale(1);" >
                                                        </a>
                                                    </div>
                                                </div>

                                                <p class="text-sm text-gray-300 mt-2"> {{ moment(user.created_at).format('hh:mm A') }}</p>
                                            </div>
                                        </div>
                                    </div>
                            </div>
                        </div>
                        

                    </div>
                    
                    <div class="bottom md:absolute fixed p-2 w-full left-0 z-40 bottom-0 right-0" v-if="currentUser">
                        <div class="preview-image flex items-center justify-center w-[250px] h-[250px] object-cover mx-auto p-5 border border-white/10 rounded-lg bg-[#0f1940]" v-if="previewAttachement">
                            <img :src="previewAttachement" alt="previewAttachement" class="max-w-[180px] max-h-[180px] mx-auto">
                        </div>
                        <div class="flex mt-3 items-center gap-3">
                            <label class="cursor-pointer">
                                <!-- <span class="inline-block w-8 h-8 leading[25px] text-xl text-center bg-transparent border border-white/20 rounded-full text-white/40">+</span> -->
                                <img src="https://img.icons8.com/fluency/24/000000/attach.png" />
                                <input type='file' class="hidden" @change="attachmentUpload" />
                            </label>
                            <input type="text" class="w-full bg-transparent py-3 px-6 border border-white/10 rounded-full text-gray-300 text-xs resize-none focus:outline-none" v-model="message">
                            <!-- <textarea name="message" id="message" ></textarea> -->
                            <button type="submit" @click="sendMessage">
                                <div class="w-8 h-8 leading[25px] bg-lime-500 rounded-full flex justify-center items-center">
                                    <img src="/images/icon-send.png" alt="send" class="max-w-[20px] mx-auto">
                                </div>
                            </button>
                        </div>
                    </div>
                </div>
               

                <!-- profile -->
                <div v-if="step === 'profile'">
                    <Profile :user="loggedInUser" />
                </div>

                <!-- add contact -->
                <div v-if="step === 'add-contact'">
                    <AddContact :user="loggedInUser" @getContactList="getContactList"/>
                </div>

                <!-- edit contact -->
                <div v-if="step === 'edit-contact'">
                    <EditContact :user="loggedInUser" :id="edit" />
                </div>

                <!-- create group -->
                <div v-if="step === 'create-group'">
                    <CreateGroup :users="users" @getGroupList="getGroupList" />
                </div>

                <!-- loader -->
                <div class="main-loader" v-if="loading">
                    <div class="loader">
                        <div id="cssLoader17" class="main-wrap main-wrap--white">
                            <div class="cssLoader17"></div>
                        </div>
                    </div>
                </div>

            </div>
        </div>
    </section>

</div>
</template>

<script>
import moment from 'moment'
import Profile from './user/Profile.vue'
import AddContact from './user/AddContact.vue'
import EditContact from './user/EditContact.vue'
import CreateGroup from './user/CreateGroup.vue'
export default {
    name: 'Home',
    components: {
        Profile,
        AddContact,
        CreateGroup,
        EditContact
    },
    data() {
        return {
            loading: false,
            menu: false,
            subMenu: false,
            users: [],
            message: '',
            attachments: [],
            previewAttachement: '',
            from_user_id: '',
            to_user_id: '',
            userMessages: [],
            currentUser: '',
            step: 'message',
            groups: {},
            loggedInUser: {},
            edit: '',
        }
    },
    methods: {
        moment(date) {
            return moment(date);
        },
        getUsers() {
            const token = localStorage.getItem('token');
            const config = {
                headers: {
                    'Authorization': 'Bearer ' + token,
                }
            }
            axios.get('/api/contact-list', config)
                .then(res => {
                    // console.log(res)
                    this.users = res.data.users
                })
        },
        attachmentUpload(e) {
            this.attachments = e.target.files[0]
            this.previewAttachement = URL.createObjectURL(this.attachments)

        },
        sendMessage() {
            let data = new FormData();
            if(this.attachments !== null) {
                if(this.message === '' || this.message === null){
                    this.message = 'null'
                }
            }
            data.append('message', this.message)
            data.append('from_user_id', this.from_user_id)
            data.append('to_user_id', this.to_user_id)
            data.append('attachments', this.attachments)

            console.log(this.message)
            const config = {
                headers: {
                    'content-type': 'multipart/form-data'
                }
            }

            axios.post('/api/message', data, config)
                .then(res => {
                    console.log(res)
                    this.getUserMessage()
                    this.message = ''
                    this.attachments = ''
                    this.previewAttachement = ''
                }).catch(err => {
                    console.log(err)
                })
        },
        getUserMessage(user) {
            this.step = 'message'
            this.subMenu = false
            this.loading = true
            if (user) {
                this.currentUser = user
                this.to_user_id = user.id
            }
            const data = {
                from_user_id: this.from_user_id,
                to_user_id: this.to_user_id
            }
            axios.post('/api/user-messages', data)
                .then(res => {
                    this.userMessages = res.data.user_messages
                    this.menu = false
                    setTimeout(() => {
                        this.scrollToElement()
                        this.loading = false
                    }, 500)

                }).catch(err => {
                    console.log(err)
                    this.loading = false
                })
        },
        getLoggedInUser() {
            const token = localStorage.getItem('token');
            const config = {
                headers: {
                    'Authorization': 'Bearer ' + token,
                }
            }
            axios.get('/api/user', config)
                .then(res => {
                    const user = res.data.user
                    this.from_user_id = user.id
                    this.loggedInUser = user
                }).catch(err => [
                    console.log(err)
                ])
        },
        LogoutUser() {
            localStorage.removeItem('token')
            this.$router.push('/login')
        },
        stepChange(profile) {
            this.step = profile
            this.subMenu = false
        },
        scrollToElement() {
            const element = this.$refs.message_container;
            var offsetHeight = element.offsetHeight;
            console.log(offsetHeight)
            element.scrollTop = element.scrollHeight;
            // console.log(element)
            // element.scrollTop = offsetHeight;
        },
        getContactList(refresh){
            console.log(refresh)
            if(refresh){
                console.log(refresh)
                this.getUsers()
            }
        },
        getGroupList(refresh){
            console.log(refresh)
            if(refresh){
                console.log(refresh)
                this.getGroups()
            }
        },
        getGroups(){
            const token = localStorage.getItem('token');
            const config = {
                headers: {
                    'Authorization': 'Bearer ' + token,
                }
            }
            axios.get('/api/groups', config)
            .then(res=>{
                this.groups = res.data.groups
            }).catch(err=>{
                console.log(err)
            })
        },
        editContact(id, step){
            this.edit = id
            this.step = step
        }
    },
    mounted() {
        this.getUsers()
        this.getLoggedInUser()
        this.scrollToElement()
        this.getGroups()
    }
}
</script>
