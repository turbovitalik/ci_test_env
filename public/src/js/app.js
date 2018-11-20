import axios from '../../node_modules/axios/index.js';
import EventBus from './event-bus';

const LIKE_TEXT = 'Like';
const UNLIKE_TEXT = 'Unlike';

var userMixin = {
    methods: {
        getUser: function () {
            return JSON.parse(localStorage.getItem('user'))
        },
        getUserId: function () {
            let user = this.getUser();
            return user ? user.id : false;
        },
        getAuthorizationHeader: function () {
            return { Authorization: 'Bearer ' + this.getUserId() };
        }
    }
}

const Home = Vue.component('home', {
   template: `
       <div class="jumbotron">
           <h1 class="display-3">This is test app</h1>
           <p class="lead">Click on <b>Last 3 news</b> in menu</p>
       </div>
   `
});

Vue.component('top-panel', {
    template: `
        <div class="header clearfix">
            <nav>
                <ul class="nav nav-pills float-right">
                    <li class="nav-item">
                        <router-link class="nav-link" to="/news">Last 3 news</router-link>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Login</a>
                    </li>
                    <li class="nav-item">
                        <a class="nav-link" href="#">Logout</a>
                    </li>
                </ul>
            </nav>
            <h3 class="text-muted">News Test</h3>
        </div>
    `
});

Vue.component('login', {
   mixins: [userMixin],
   template: '<button v-on:click="login">Login</button>',
   methods: {
       login: function (event) {
           axios.get('/auth/login')
               .then(res => {
                   const user = { id: res.data.id, username: res.data.username }
                   localStorage.setItem('user', JSON.stringify(user));
               })
       },
   }
});

Vue.component('like-button', {

    mixins: [userMixin],

    template: `<button 
        v-on:click="performAction" 
        class="btn btn-success">{{ actionName }} ({{ count }})</button>`,

    data() {
        return {
            liked: null
            // actionName: LIKE_TEXT,
        }
    },

    methods: {
        performAction: function () {
            if (this.actionName === LIKE_TEXT) {
                this.like();
            } else if (this.actionName === UNLIKE_TEXT) {
                this.unlike();
            }
        },

        composePostData: function () {
            let postData = {};

            postData['id'] = this.id;
            postData['type'] = this.postDataMap.type;
            postData['user_id'] = this.getUserId();

            return postData;
        },

        like: function (event) {

            axios.defaults.headers = { Authorization: "Bearer" + '12345' };

            axios.post(this.likeUrl, this.composePostData(), { headers: this.getAuthorizationHeader()})
                .then(res => {

                    // console.log(this.liked);
                    this.liked = 1;
                    this.count = this.count + 1;
                    // console.log(this.liked);
                })
        },

        unlike: function (event) {

            axios.post(this.unlikeUrl, this.composePostData())
                .then(res => {

                    this.liked = 0;
                    this.count = this.count - 1;
                });
        }
    },

    computed: {
        count: {
            get: function () {
                return this.likesCount;
            },
            set: function (value) {
                console.log(this.liked)
                EventBus.$emit('update-likes', { likes: value, item_id: this.id, type: this.postDataMap.type, isLike: this.liked });
            }
        },
        actionName: {
            get: function () {

                // console.log(this.isLiked)
                // console.log('Getter start')
                // console.log(this.isLiked)
                return this.isLiked ? UNLIKE_TEXT : LIKE_TEXT;
            }
        }
    },

    props: {
        likeUrl: String,
        unlikeUrl: String,
        postDataMap: Object,
        id: String,
        likesCount: Number,
        isLiked: Number
    }
});

const News = Vue.component('latest-news', {

    data() {
        return {
            items: null
        }
    },

    template: `
        <div>
            <div v-for="item in items" class="row">
                <div class="col-md-2">
                    <img width="200" class="img-thumbnail" v-bind:src="item.img" />
                </div>
                <div class="col-md-10">
                    <h4>{{ item.header }}</h4> 
                    <p>{{ item.short_description }}</p>
                    <h6>Created: {{ item.time_created }}</h6>
                    <p><router-link :to="{ name: 'newsSingle', params: { id: item.id }}" tag="button" class="btn btn-primary">Show more</router-link></p>
                    <br/><br/><br/>
                </div>
            </div>
        </div>
    `,

    created() {
        axios.get('/news/news')
            .then(response => {
                this.items = response.data;
            })
    },

    methods: {
        showMoreLink: function (id) {
            return '/news/' + id;
        }
    }

});

const SingleNews = Vue.component('single-news', {
    template: `
        <div class="row">
            <div class="news-container">
                <img width="400" v-bind:src="img" class="img-thumbnail" />
                <h1 style="margin-top: 25px">{{ header }}</h1>
                <div class="news-text">
                    {{ text }}
                </div>
                <br />
                <p>
                    <like-button
                         v-if="isLiked !== null"
                         likeUrl="/likes/save"
                         unlikeUrl="/likes/remove"
                         @update-likes-count="onUpdateLikesCount"
                         v-bind:isLiked="isLiked"
                         v-bind:likesCount="likesCount"
                         v-bind:id="id"
                         v-bind:postDataMap="{idKey: 'news_id', type: 'news'}"
                    ></like-button>
                </p>
            </div>
            <div class="comments" style="margin-top: 50px">
                <h2>Comments:</h2>
                <comments :comments="comments" :newsId="id" @submit-comment="submitComment"></comments>
            </div>
        </div>
    `,
    data() {
        return {
            id: null,
            header: null,
            img: null,
            text: null,
            likesCount: null,
            isLiked: null,
            comments: []
        }
    },
    created() {
        axios.get('/news/news_single/' + this.$route.params.id)
            .then(response => {
                this.id = response.data.news_item.id;
                this.header = response.data.news_item.header;
                this.img = response.data.news_item.img;
                this.text = response.data.news_item.text;
                this.comments = response.data.comments;
                this.isLiked = response.data.news_item.isLiked;
                this.likesCount = parseInt(response.data.likes);
            })
    },

    mounted() {
        let me = this;
        EventBus.$on('update-likes', function (data) {

            if (data.type === 'news') {
                me.likesCount = data.likes;
                me.isLiked = data.isLike;
            }

            if (data.type === 'comment') {
                console.log(data);
                me.comments.find(function (item) {
                    if (item.comment_id == data.item_id) {
                        item.likes_count = data.likes;
                        item.isLiked = data.isLike;
                    }
                });
            }

        });
    },

    methods: {
        submitComment: function(reply) {
            this.comments.push({
                comment_id: 111, //todo: change this
                likes_count: 0,
                user_id: 1,
                comment: reply
            });
        },
        onUpdateLikesCount: function (value) {
            this.likesCount = value;
        }
    }
});

Vue.component('comments', {
    mixins: [userMixin],
    template: `
        <div class="comments">
            <div>
                <single-comment 
                    v-for="comment in comments"
                    :comment="comment"
                    :likesCount="parseInt(comment.likes_count)"
                    :key="comment.id"
                ></single-comment>
            </div>
            <hr>
            <div class="reply">
                <form class="form-inline">
                    <div class="form-group" style="margin-right: 20px">
                        <input
                            type="text" 
                            v-model.trim="reply" 
                            class="form-control reply--text" 
                            placeholder="Leave a comment..."
                            maxlength="250"
                            required
                            @keyup.enter="submitComment"
                        />
                    </div>
                    <div class="form-group">
                    <button 
                        class="btn btn-primary reply--button" 
                        @click.prevent="submitComment">
                        <i class="fa fa-paper-plane"></i> Send
                    </button>
                    </div>
                </form>
            </div>
        </div>
    `,
    data: function() {
        return {
            reply: ''
        }
    },
    methods: {
        //Tell the parent component(main app) that we have a new comment
        submitComment: function() {
            axios.post('/news/comment', { news_id: this.newsId, comment: this.reply, user_id: this.getUserId() }) //todo: hardcoded data
                .then(res => {
                    if(this.reply !== '') {
                        this.$emit('submit-comment', this.reply);
                        this.reply = '';
                    }
                });
        }
    },
    //What the component expects as parameters
    props: ['comments', 'newsId']
});

Vue.component('single-comment', {
    template: `
        <div class="comment" style="margin-bottom: 40px;">
            <div class="text">
                <h6>by user # {{ comment.user_id }}</h6> 
                <span>{{ comment.comment }}</span>
            </div>
            <div style="margin-top: 10px">
                <like-button
                         v-if="comment.isLiked !== null"
                         likeUrl="/likes/save"
                         unlikeUrl="/likes/remove"
                         @update-likes-count="onUpdateLikesCount"
                         v-bind:isLiked="comment.isLiked"
                         v-bind:likesCount="parseInt(comment.likes_count)"
                         v-bind:id="comment.comment_id"
                         v-bind:postDataMap="{idKey: 'comment_id', type: 'comment'}"
                 ></like-button>
            </div>
        </div>
    `,

    data() {
        return {
            commentLikesCount: this.likesCount
        }
    },

    methods: {
        onUpdateLikesCount: function (value) {
            console.log(value);
            this.commentLikesCount = value;
        }
    },

    props: ['comment', 'likesCount']
});

// 2. Define some routes
// Each route should map to a component. The "component" can
// either be an actual component constructor created via
// `Vue.extend()`, or just a component options object.
// We'll talk about nested routes later.
const routes = [
    { name: 'home', path: '/', component: Home },
    { name: 'newsList', path: '/news', component: News },
    { name: 'newsSingle', path: '/single/:id', component: SingleNews}
];

// 3. Create the router instance and pass the `routes` option
// You can pass in additional options here, but let's
// keep it simple for now.
const router = new VueRouter({
    hashBang: false,
    mode: 'history',
    routes: routes
});

//Vue.use(require('vue-chartist'));

// 4. Create and mount the root instance.
// Make sure to inject the router with the router option to make the
// whole app router-aware.
const app = new Vue({
    template:`
       <div class="container">
           <top-panel></top-panel>
           <div class="row marketing">
               <div class="col-md-12">
                   <router-view></router-view>
               </div>
           </div>
       </div>
    `,
    router: router
}).$mount('#app');

