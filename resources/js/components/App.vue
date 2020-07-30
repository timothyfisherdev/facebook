<template>
	<div class="flex flex-col flex-1 h-screen overflow-y-hidden">
		<Nav />

		<div class="flex flex-1 overflow-y-hidden">
			<Sidebar />

			<div class="overflow-x-hidden w-2/3">
				<router-view></router-view>
			</div>
		</div>
	</div>
</template>

<script>
	import Nav from './Nav';
	import Sidebar from './Sidebar';

	export default {
		name: 'App',
		components: {
			Nav,
			Sidebar
		},
		mounted () {
			this.$store.dispatch('getAuthUser');
		}
	}
</script>

<style scoped>

</style>