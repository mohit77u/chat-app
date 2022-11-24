<template>
<div>
    <div class="main-message-box py-4 pb-10 min-h-[85vh] max-h-[85vh] overflow-y-auto relative" ref="message_container">
        <div class="content">
            <div class="messages my-5" v-for="(createdDate, index) in groupMessages" :key="index">
                <p class="message-date text-center my-10 text-white text-xs">{{index}}</p>
                <div v-for="(user,index ) in createdDate" :key="index">
                    <div class="text-left my-4" v-if="user.user_id !== loggedInUser.id">
                        <div class="user-message py-2 px-3 rounded-lg w-auto inline-block bg-white/10 relative">
                            <p class="text-white text-md" v-if="user.type == 'text'">{{ user.message }}</p>
                            <div class="download-attachment" v-if="user.type != 'text'">
                                <a :href="user.message" v-if="user.type === 'image'" download>
                                    <img :src="user.message" alt="message" class="max-w-[300px] max-h-[300px]" />
                                </a>
                                <div class="download absolute left-0 top-0 z-[900] flex items-center justify-center w-full h-full bg-black/50 transition-opacity duration-500 opacity-0 hover:opacity-100">
                                    <a :href="user.message" v-if="user.type === 'image'" download class="p-4 bg-slate-900 rounded-full shadow-lg border border-white/10 hover:bg-gray-600 transition-all duration-200" title="download">
                                        <img src="/images/icon-download.png" alt="download">
                                    </a>
                                </div>
                            </div>

                            <p class="text-sm text-gray-300 mt-2">{{ moment(user.created_at).format('hh:mm A') }}</p>
                        </div>
                    </div>
                    <div class="text-right my-4" v-else>
                        <div class="my-message py-2 px-3 rounded-lg w-auto inline-block bg-lime-500 relative">
                            <p class="text-white text-md" v-if="user.type == 'text'">{{ user.message }}</p>
                            <div class="download-attachment" v-if="user.type != 'text'">
                                <a :href="user.message" v-if="user.type === 'image'" download>
                                    <img :src="user.message" alt="message" class="max-w-[300px] max-h-[300px]" />
                                </a>
                                <div class="download absolute left-0 top-0 z-[900] flex items-center justify-center w-full h-full bg-black/50 transition-opacity duration-500 opacity-0 hover:opacity-100">
                                    <a :href="user.message" v-if="user.type === 'image'" download class="p-4 bg-slate-900 rounded-full shadow-lg border border-white/10 hover:bg-gray-600 transition-all duration-200" title="download">
                                        <img src="/images/icon-download.png" alt="download" style="filter: grayscale(1);">
                                    </a>
                                </div>
                            </div>

                            <p class="text-sm text-gray-100 mt-2"> {{ moment(user.created_at).format('hh:mm A') }}</p>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="bottom md:absolute fixed p-2 w-full left-0 z-40 bottom-0 right-0" v-if="currentGroup">
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
</template>

<script>
import moment from 'moment'
export default {
    name: 'GroupMessage',
    props: ['currentGroup', 'loggedInUser'],
    data() {
        return {
            groupMessages: '',
            attachments: [],
            previewAttachement: '',
            message: '',
        }
    },
    watch:{
        currentGroup(newVal, oldVal){
            if (oldVal != newVal){
                this.getGroupMessages()
            }
        }
    },
    methods: {
        moment(date) {
            return moment(date);
        },
        getGroupMessages() {
            const data = {
                groupId: this.currentGroup.id,
            }
            axios.post('/api/group-messages', data)
            .then(res => {
                    this.groupMessages = res.data.messages
                    // this.menu = false
                    setTimeout(() => {
                        this.scrollToElement()
                    }, 500)

                }).catch(err => {
                    console.log(err)
                    // this.loading = false
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
            // console.log(this.currentGroup)
            data.append('group_id', this.currentGroup.id)
            data.append('message', this.message)
            data.append('user_id', this.loggedInUser.id)
            data.append('attachments', this.attachments)

            console.log(this.message)
            axios.post('/api/group-message', data)
                .then(res => {
                    console.log(res)
                    this.getGroupMessages()
                    this.message = ''
                    this.attachments = ''
                    this.previewAttachement = ''
                }).catch(err => {
                    console.log(err)
                })
        },
        scrollToElement() {
            const element = this.$refs.message_container;
            var offsetHeight = element.offsetHeight;
            console.log(offsetHeight)
            element.scrollTop = element.scrollHeight;
        },
    },
    mounted(){
        this.getGroupMessages();
    }
}
</script>
