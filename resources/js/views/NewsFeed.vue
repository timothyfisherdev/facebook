<template>
	<div class="flex flex-col items-center py-4">
		<AddPost />

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
	import AddPost from '../components/AddPost';
	import Post from '../components/Post';

	export default {
		name: 'NewsFeed',
		components: {
			AddPost,
			Post
		},
		data () {
			return {
				loading: true,
				posts: null
			}
		},
		mounted () {
			axios.get('/api/posts?include=user')
				.then(res => {
					let posts = merge(res.data.included, res.data.data);
					
					this.posts = posts.map((post) => {
						post.attributes.user = post.relationships.user.data.attributes;
						return post.attributes;
					});
				}).catch(err => {
					console.log('Unable to fetch posts.');
				}).finally(() => {
					this.loading = false;
				});
		}
	}
</script>

<style scoped>

</style>