<template>
	<div class="flex flex-col items-center py-4">
		<AddPost />

		<Post />
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
		}
	}
</script>

<style scoped>

</style>