import Vue from 'vue';
import VueRouter from 'vue-router';
import NewsFeed from './views/NewsFeed';
import UserProfile from './views/UserProfile';

Vue.use(VueRouter);

export default new VueRouter({
	mode: 'history',
	routes: [
		{
			path: '/',
			name: 'home',
			component: NewsFeed,
			meta: {
				title: 'News Feed'
			}
		},
		{
			path: '/user-profiles/:userId',
			name: 'user.profile',
			component: UserProfile,
			meta: {
				title: 'User Profile'
			}
		}
	]
});