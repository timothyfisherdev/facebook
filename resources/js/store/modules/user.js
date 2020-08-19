const state = {
	user: null,
	userLoading: true
};

const getters = {
	authUser: state => {
		return state.user;
	},
	authUserLoading: state => {
		return state.userLoading;
	}
};

const actions = {
	getAuthUser ({commit, state}) {
		axios.get('/api/rest/v1/users/me')
			.then(res => {
				commit('setAuthUser', res.data.user);
			}).catch(error => {
				console.log('Unable to get the authenticated user.');
			}).finally(() => {
				commit('setAuthUserLoading', false);
			});
	}
};

const mutations = {
	setAuthUserLoading (state, loading) {
		state.userLoading = loading;
	},
	setAuthUser (state, user) {
		state.user = user;
	}
};

export default {
	state, getters, actions, mutations
}