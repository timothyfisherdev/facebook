<template>
	<div class="flex flex-col items-center">
		<div class="relative mb-8">
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

		<p v-if="loading">Loading posts...</p>

		<div v-else>
			<p v-if="! posts.length">No posts to show.</p>

			<div v-else>
				<Post v-for="post in posts" :key="post.id" :post="post" />
			</div>
		</div>
	</div>
</template>

<script>
	import * as r from 'ramda';
	import * as ra from 'ramda-adjunct';
	import merge from 'json-api-merge';
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
			axios.get('/api/users/' + this.$route.params.userId + '?include=posts')
				.then(res => {
					let user = merge(res.data.included, res.data.data);

					this.user = user.attributes;
					this.posts = user.relationships.posts.data.map((post) => {
						post.attributes.user = this.user;
						return post.attributes;	
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