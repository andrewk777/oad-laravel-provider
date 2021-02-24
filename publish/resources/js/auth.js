import { CONF } from './config';
import store from "./store";
import router from "./routes";
import { utils } from './utils';

//if use is registered forward user to the default app page
function frontAuth (to, from, next) {
    // store.commit('setAccessToken', null);
    if (store.state.accessToken) {
        next(CONF.APP_DEFAULT_URL);
    } else {
        next();
    }
}

//if user is not registered forward user to the app login page
function backAuth (to, from, next) {
    if (!store.state.accessToken) {
        next(CONF.APP_LOGIN_URL);
    } else {
        next();
    }
}

function setAxiosToken(token) {
    store.commit('setAccessToken', token );
    axios.defaults.headers.common['Authorization'] = 'Bearer ' + token;
}

function login(self) {
    self.loading = true;
    axios.post(CONF.APP_URL + '/login', self.form )
    .then(res => {

        utils.c_notify(res.data.res, res.data.status);

        if (res.data.status == 'success') {
            setAxiosToken(res.data.token)
            store.commit('setUser', res.data.user );
            router.push(CONF.APP_DEFAULT_URL)
        }
        
        self.loading = false;
    })
}

function confirmLogout() {
    this.$utils.swalConfigm.fire({
        title: 'Logout?',
        text: '',
        showCancelButton: true,
        confirmButtonText: 'Confirm Logout'
    }).then((result) => {
        if (result.value) {
         logout()
        }
    })
}

function logout() {
    
    axios.get( CONF.APP_URL + '/logout')
    .then(res => {
        if (res.data.status == 'success') {

            store.commit('resetStorage',''); 
            router.push({ name: 'login' })

        }
    })

}

function sendPasswordResetEmail(self) {
    self.loading = true;
    axios.post(CONF.APP_URL + '/resetPasswordSendEmail', self.form )
    .then(res => {

        utils.c_notify(res.data.res, res.data.status);

        if (res.data.status == 'success') {
            self.form.email = ''
        }
        
        self.loading = false;
    })
}

function resetPassword(self) {
    self.loading = true;
    axios.post(CONF.APP_URL + '/resetPasswordComplete', self.form )
    .then(res => {

        utils.c_notify(res.data.res, res.data.status);

        if (res.data.status == 'success') {
            self.form.password = '';
            self.form.password_confirm = '';
            setTimeout(() => self.$router.push(CONF.APP_LOGIN_URL), 1000)
        }
        
        self.loading = false;
    })
}

export { frontAuth, backAuth, login, logout, confirmLogout, sendPasswordResetEmail, resetPassword };