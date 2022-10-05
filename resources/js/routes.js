import { createRouter, createWebHistory } from 'vue-router'
import HomeView from './components/pages/Home.vue'
const Register = () => import('./components/pages/auth/Register.vue')
const Login = () => import('./components/pages/auth/Login.vue')

const routes = [
  {
    path: '/',
    name: 'home',
    component: HomeView,
    meta: {
      authRequired: true,
      guest: false,
    }
  },
  {
    path: '/register',
    name: 'register',
    component: Register,
    meta: {
      guest: true,
    }
  },
  {
    path: '/login',
    name: 'login',
    component: Login,
    meta: {
      guest: true,
    }
  }
]

const router = createRouter({
  history: createWebHistory('/'),
  routes
})

router.beforeEach((to, from, next) => {
  var token = localStorage.getItem('token');
  // if meta required but not logged in
  if (to.meta.authRequired && !to.meta.guest && token === null) 
  {
    next({path: '/login'})
  }
  // if meta not required but logged in
  else if(!to.meta.authRequired && to.meta.guest && token)
  {
    next({path: '/'})
  }
  else
  {
    next(true)
  }

  // if (!to.meta.authRequired && token) 
  // {
  //   next(true)
  // }
  // else 
  // {
  //   next({path: '/login'})
  // }
})

export default router
