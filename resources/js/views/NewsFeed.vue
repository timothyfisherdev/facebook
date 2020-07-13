<template>
	<div class="flex flex-col items-center py-4">
		<AddPost />
	</div>
</template>

<script>
	import AddPost from '../components/AddPost';

	export default {
		name: 'NewsFeed',
		components: {
			AddPost
		}
	}
</script>

<style scoped>

</style>