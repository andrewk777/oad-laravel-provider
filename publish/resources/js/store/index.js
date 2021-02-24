import Vue from 'vue'
import Vuex from 'vuex'
import createPersistedState from 'vuex-persistedstate'

Vue.use(Vuex)

export default new Vuex.Store({
	state: {
		accessToken: 	null,
		user: 			null,
		showSesExp:		true,
		tables: 		{},
		formParams:		{}
	},
	actions: {
	},
	modules: {
	},
	mutations: {
		setAccessToken(state,payload) {
			state.accessToken = payload
		},
		setUser(state,payload) {
			state.user = payload 
		},
		setNewTable(state,ref) {
			state.tables[ref] = {
				refresher: new Date().getTime(),
				search: '',
				filters: {},
				colsToggled: [],
				page: 1
			};
		},
		setTableSearchTerm(state, updateObj) {
			state.tables[updateObj.ref].search = updateObj.val
		},
		setTablePage(state, tableRef, page) {
			state.tables[tableRef].page = page
		},
		setTableFilters(state, updateObj){
			state.tables[updateObj.ref].filters = updateObj.val
		},
		setShowSesExp(state, val) {
			state.showSesExp = val
		},
		setTableColToggled(state, updateObj) { // { tableRef: tableRef, col: { name: colName, visible: col.visible  } }
			var newColData = updateObj.col;
			var colsToggled = state.tables[updateObj.tableRef].colsToggled;
			var indx = colsToggled.findIndex( o => o.name == newColData.name )

			if (indx == -1) {
				state.tables[updateObj.tableRef].colsToggled.push(newColData)
			} else {
				state.tables[updateObj.tableRef].colsToggled[indx] = newColData;
			}
		},
		setFormParams(state, payload){
			state.formParams[payload.id] = payload.data
		},
		resetStorage(state, payload) {
			state.accessToken	= null;
			state.user			= null;
			state.showSesExp	= true;
			state.tables		= {};
			state.formParams	= {};
		}
	},
	getters: {
		getUserInfo: (state) 	=> state.user,
		getTableInfo: (state) 	=> state.tables,
		getAccessToken: (state) => state.accessToken,
		getTables: (state) 		=> state.tables,
		getFormParams: (state) 	=> state.formParams
	},
	plugins: [createPersistedState()]
})
