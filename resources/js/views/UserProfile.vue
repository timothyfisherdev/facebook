<template>
	<div>
		<div class="h-64 overflow-hidden">
			<img src="https://via.placeholder.com/1000" alt="" class="object-cover w-full" width="1000" />
		</div>
	</div>
</template>

<script>
	import * as r from 'ramda';
	import * as ra from 'ramda-adjunct';
	import merge from 'json-api-merge';

	export default {
		name: 'UserProfile',
		data () {
			return {
				loading: true,
				user: null
			}
		},
		mounted () {
			axios.get('/api/users/' + this.$route.params.userId + '?include=posts')
				.then(res => {
					this.user = merge(res.data.included, res.data.data);
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