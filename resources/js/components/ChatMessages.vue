<template>
<div id="container">
    <aside>
        <div>
            <li class="cursor-pointer bg-hover">
                <div class="img-name" :style="{ backgroundColor: getColorForLetter(getFirstLetter(loginuser.name)) }">
                    {{ getFirstLetter(loginuser.name) }}
                </div>
                <div>
                    <h2 class="loginusername"><b>{{ loginuser.name }}</b></h2>
                </div>
                <div class="dropdown">
                    <span href="#" role="button" id="logoutDropdown" data-bs-toggle="dropdown" aria-haspopup="true" aria-expanded="false">
                        <b>...</b>
                    </span>

                    <div class="dropdown-menu" aria-labelledby="logoutDropdown">
                        <a class="dropdown-item" @click="logout">Logout</a>
                    </div>
                </div>
            </li>
        </div>
        <header>
            <input type="text" placeholder="search" v-model="search">
        </header>
        <ul>
            <li v-for="user in filteredUserList" :key="user.id" @click="selectchat(user.id,user.name)" class="cursor-pointer">
                <div class="img-name" :style="{ backgroundColor: getColorForLetter(getFirstLetter(user.name)) }">
                    {{ getFirstLetter(user.name) }}
                </div>
                <div>
                    <h2><b>{{ user.name }}</b></h2>
                    <span v-if="typingUser.id == user.id" class="typing-indicator typingshow">Typing...</span>
                    <h3>
                        <span :class="[ 'status',{'green': isUserOnline(user.id), 'orange': !isUserOnline(user.id)} ]"></span>
                        {{ isUserOnline(user.id) ? 'Online' : 'Offline' }}
                    </h3>
                </div>
            </li>
        </ul>
    </aside>
    <main>
        <div v-if="selectuser">
            <header class="userheader">
                <div class="img-name" :style="['margin-left: 0px;', { backgroundColor: getColorForLetter(getFirstLetter(selectuser)) }]">
                    {{ getFirstLetter(selectuser) }}
                </div>
                <div>
                    <h2><b>Chat with {{ selectuser }}</b></h2>
                    <h3>already {{messages.length}} messages</h3>
                    <span v-if="typingUser.name && typingUser.id == selectuserid" class="typing-indicator typingname">{{ typingUser.name }} is Typing...</span>
                </div>
                <h3 class="online-status">
                    <span :class="['status',{'green': isUserOnline(selectuserid), 'orange': !isUserOnline(selectuserid)} ]"></span>
                    {{ isUserOnline(selectuserid) ? 'Online' : 'Offline' }}
                </h3>
            </header>
            <ul id="chat" ref="messageBox">
                <li v-for="message in messages" :key="message.id" :class="['list-group-item', { 'me': message.sender_user_id == loginuser.id, 'you': message.sender_user_id != loginuser.id }]">
                    <div class="entete" v-if="message.sender_user_id == loginuser.id">
                        <h3>{{ formatCreatedAt(message.created_at) }}</h3>
                        <h2 class="entete-left">{{ message.user.name }}</h2>
                        <span class="status blue"></span>
                    </div>
                    <div class="entete" v-else>
                        <span class="status green"></span>
                        <h2 class="entete-right">{{ message.user.name }}</h2>
                        <h3>{{ formatCreatedAt(message.created_at) }}</h3>
                    </div>
                    <div class="triangle"></div>
                    <div class="message">
                        {{ message.message }}
                    </div>
                </li>
            </ul>
            <footer>
                <div class="input-group">
                    <input id="btn-input" type="text" name="message" class="form-control input-sm" placeholder="Type your message here..." v-model="newMessage" @keyup.enter="sendMessage" @keydown="onTyping" />
                    <button class="btn-send" id="btn-chat" @click="sendMessage">
                        Send
                    </button>
                </div>
            </footer>
        </div>
        <div v-else class="dashboard">
            <div class="card-body text-center">
                <h2>Welcome, <b>{{ loginuser.name }}!</b></h2>
                <p>Start a conversation by selecting a user from the list on the left.</p>
            </div>
        </div>
    </main>
</div>
</template>

<script>
import {
    format,
    isToday,
    isYesterday
} from 'date-fns';

export default {
    props: ["loginuser"],
    data() {
        return {
            selectuser: '',
            selectuserid: '',
            newMessage: "",
            search: '',
            isTyping: false,
            typingUser: {},
            typingClock: null,
            messages: [],
            onlineusers: [],
            users: [],
            userid: null,
        };
    },

    created() {
        this.fetchMessages(this.userid);
        window.Echo.join('chat.' + this.loginuser.id)
            .listen('MessageSent', (e) => {
                if (e.message.receiver_user_id === this.userid || e.message.sender_user_id === this.userid) {
                    this.messages.push({
                        message: e.message.message,
                        sender_user_id: e.message.sender_user_id,
                        receiver_user_id: e.message.receiver_user_id,
                        created_at: e.message.created_at,
                        user: e.user,
                    });
                }
            })
            .listenForWhisper('typing', (e) => {
                this.typingUser = e.user;
                if (this.typingClock) clearTimeout();

                this.typingClock = setTimeout(() => {
                    this.typingUser = {};
                }, 5000);
            });

        window.Echo.join('online-users')
            .here((users) => {
                // Update your onlineUsers array with global online users
                this.onlineusers = users; // Uncomment this line if you want to update the onlineusers array with global online users
                this.updateUsersList();
            })
            .joining((user) => {
                // 'joining' event is fired when a new user joins the presence channel.
                this.onlineusers.push(user);
                this.updateUsersList();
            })
            .leaving((user) => {
                // 'leaving' event is fired when a user leaves the presence channel.
                this.onlineusers = this.onlineusers.filter(u => u.id !== user.id);
                this.updateUsersList();
            });
    },

    computed: {
        UserList() {
            // Filter online users excluding the logged-in user
            return this.users.filter(user => user.id !== this.loginuser.id);
        },

        filteredUserList() {
            const searchTerm = this.search.toLowerCase();
            return this.UserList.filter(user => user.name.toLowerCase().includes(searchTerm));
        },
    },

    methods: {
        updateUsersList() {
            // Loop through online users
            this.onlineusers.forEach(onlineUser => {
                // Check if the online user is not in the users list
                if (!this.users.some(user => user.id === onlineUser.id)) {
                    // Add the online user to the users list
                    this.users.push(onlineUser);
                }
            });

        },

        fetchMessages(userid) {
            this.userid = userid;

            axios.get(`/api/chatlist`).then(response => {
                this.users = response.data;
            });

            axios.get(`/api/messages/${userid}`).then(response => {
                this.messages = response.data;
            });
        },
        addMessage(message) {
            message.receiver_user_id = this.userid;
            message.sender_user_id = window.receiveruserid;
            this.messages.push(message);

            axios.post('/api/messages', message).then(response => {
                console.log(response.data);
            });
        },

        logout() {
            axios.post('/logout')
                .then(response => {
                    // Handle successful logout, e.g., redirect to the login page
                    window.location.href = '/login';
                })
                .catch(error => {
                    // Handle logout error
                    console.error('Logout failed:', error);
                });
        },
        formatCreatedAt(created_at) {
            const date = new Date(created_at);

            if (isToday(date)) {
                return format(date, "h:mm a', Today'");
            } else if (isYesterday(date)) {
                return format(date, "h:mm a', Yesterday'");
            } else {
                return format(date, 'h:mm a MMM d, yyyy');
            }
        },

        selectchat(userid, username) {
            this.fetchMessages(userid)
            this.newMessage = "";
            this.selectuser = username;
            this.selectuserid = userid;
            this.$nextTick(this.scrollToBottom);
        },

        isUserOnline(userId) {
            return this.onlineusers.some(user => user.id === userId);
        },

        sendMessage() {
            const messageData = {
                user: this.loginuser,
                message: this.newMessage,
                created_at: new Date(),
            };

            // Call the addMessage function with the messageData
            this.addMessage(messageData);
            this.newMessage = "";
            this.$nextTick(() => {
                this.scrollToBottom();
            });
        },

        scrollToBottom() {
            const messageBox = this.$refs.messageBox;
            if (messageBox) {
                messageBox.scrollTop = messageBox.scrollHeight;
            }
        },

        getFirstLetter(name) {
            return name ? name.charAt(0) : '';
        },

        // Add a method to generate random background color
        getColorForLetter(letter) {
            const colorMap = {
                A: '#FF5733',
                B: '#33FF57',
                C: '#5733FF',
                D: '#FF33C7',
                E: '#33C7FF',
                F: '#FF3333',
                G: '#33FFC7',
                H: '#C733FF',
                I: '#FFC733',
                J: '#C733FF',
                K: '#FF5733',
                L: '#33FF57',
                M: '#5733FF',
                N: '#FF33C7',
                O: '#33C7FF',
                P: '#FF3333',
                Q: '#33FFC7',
                R: '#C733FF',
                S: '#FFC733',
                T: '#FFC733',
                U: '#FF5733',
                V: '#33FF57',
                W: '#5733FF',
                X: '#FF33C7',
                Y: '#33C7FF',
                Z: '#FF3333',
                // Add more letters and corresponding colors as needed
            };

            return colorMap[letter.toUpperCase()] || '#333'; // Default to a neutral color if not found
        },

        onTyping() {
            window.Echo.join('chat.' + this.selectuserid).whisper('typing', {
                user: this.loginuser
            });
        },
    },

    watch: {
        messages: {
            handler() {
                this.$nextTick(() => {
                    this.scrollToBottom();
                });
            },
            deep: true,
        },
    },
};
</script>
