<template>
	<div class="flex flex-col items-center">
		<div v-if="user" class="relative mb-8">
			<div class="w-100 h-64 overflow-hidden">
				<img src="https://via.placeholder.com/1000" alt="" class="object-cover w-full" width="1000" />
			</div>

			<div class="absolute bottom-0 -mb-8 ml-12 left-0 flex items-center">
				<div class="w-32">
					<img src="https://image.cnbcfm.com/api/v1/image/106330923-1578676182018gettyimages-1178141599.jpeg?v=1584633147&w=1400&h=950" alt="" class="w-32 h-32 object-cover border-4 border-gray-200 rounded-full shadow-lg" />
				</div>

				<p class="text-2xl text-gray-100 ml-4">{{ user.name }}</p>
			</div>
		</div>

		<div class="w-2/3">
			<p v-if="loading">Loading posts...</p>

			<div v-else>
				<div v-if="posts">
					<Post v-for="post in posts" :key="post.id" :post="post" />
				</div>
				
				<p v-else>No posts to show.</p>
			</div>
		</div>
	</div>
</template>

<script>
	import _ from 'lodash';
	import Post from '../components/Post';

	export default {
		name: 'UserProfile',
		components: {
			Post
		},
		data () {
			return {
				loading: true,
				user: null,
				posts: null
			}
		},
		mounted () {
			axios.get('/api/rest/v1/users/' + this.$route.params.userId + '?include=posts')
				.then(res => {
					this.user = _.omit(res.data.user, 'posts');
					this.posts = res.data.user.posts.map((post) => {
						post.posted_by = this.user.name;
						return post;
					});
				}).catch(err => {
					console.log('Unable to fetch user data.');
				}).finally(() => {
					this.loading = false
				});
		}
	}
</script>

<style scoped>

</style>