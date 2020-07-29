import FrontLayout from './Front/components/Layout';
import Login from './Front/pages/Login';
import BackendLayout from './Backend/components/Layout';
import { CONF } from '@src/config';


export default [
    {
        path: '/init',
        component: BackendLayout,
        redirect: CONF.APP_DEFAULT_URL,
        meta: { auth: true },
        children: [
        @foreach ($backendRoutes as $route)
            {
                name: '{!! $route->name!!}',
                path: '{!! $route->path !!}',
                @if ($route->componentPath)
component: require('@src/Backend{!! $route->componentPath !!}.vue').default @endif
            },

        @endforeach
        ]
    },
    {
        path: '/',
        component: FrontLayout,
        meta: { auth: false },
        children: [
                {
                    path: '/',
                    name: 'Login',
                    component: Login
                },
            ],
    }
]
