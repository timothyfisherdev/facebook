<template>
	<div class="flex flex-col items-center py-4">
		<AddPost />

		<Post v-for="post in posts.data" :key="post.data.id" :post="post" />
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
				posts: null
			}
		},
		mounted () {
			axios.get('/api/posts').then((res) => {
				this.posts = res.data;
			}, (error) => {
				console.log('Unable to fetch posts.');
			});
		}
	}
</script>

<style scoped>

</style>