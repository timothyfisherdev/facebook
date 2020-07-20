<template>
	<div>
		<div class="h-64 overflow-hidden">
			<img src="https://mir-s3-cdn-cf.behance.net/project_modules/max_3840/be14d246175477.584a2e3e32918.jpg" alt="" class="object-cover w-full" />
		</div>
	</div>
</template>

<script>
	export default {
		name: 'UserProfile',
		data () {
			return {
				loading: true,
				user: null
			}
		},
		mounted () {
			axios.get('/api/users/' + this.$route.params.userId)
				.then(res => {
					this.user = res.data;
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