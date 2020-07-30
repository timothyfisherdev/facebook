const state = {
	user: null,
	userLoading: true
};

const getters = {
	authUser: state => {
		return state.user;
	}
};

const actions = {
	getAuthUser ({commit, state}) {
		axios.get('/api/me')
			.then(res => {
				commit('setAuthUser', res.data);
			}).catch(error => {
				console.log('Unable to get the authenticated user.');
			});
	}
};

const mutations = {
	setAuthUser (state, user) {
		user.data.attributes.id = user.data.id;
		state.user = user.data.attributes;
		state.userLoading = false;
	}
};

export default {
	state, getters, actions, mutations
}