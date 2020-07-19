<template>
	<div class="flex flex-col items-center py-4">
		<AddPost />

		<p v-if="loading">Loading posts...</p>
		<Post v-else v-for="post in posts.data" :key="post.data.id" :post="post" />
	</div>
</template>

<script>
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
				.then((res) => {
					this.posts = res.data;
				}).catch((err) => {
					console.log('Unable to fetch posts.');
				}).finally(() => {
					this.loading = false;
				});
		}
	}
</script>

<style scoped>

</style>