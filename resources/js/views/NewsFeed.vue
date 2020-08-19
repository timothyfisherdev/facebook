<template>
	<div class="flex flex-col items-center py-4">
		<div class="w-2/3">
			<AddPost />

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
			axios.get('/api/rest/v1/posts?include=user')
				.then(res => {
					this.posts = res.data.posts.map((post) => {
						post.posted_by = post.posted_by.name;
						return post;
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