import { createRouter, createWebHistory } from 'vue-router'
import { TOKEN_KEY } from '../services/api'
import LoginView from '../views/LoginView.vue'
import OrdersView from '../views/OrdersView.vue'
import OrderFormView from '../views/OrderFormView.vue'
import OrderDetailView from '../views/OrderDetailView.vue'

const router = createRouter({
  history: createWebHistory(import.meta.env.BASE_URL),
  routes: [
    { path: '/', redirect: '/orders' },
    { path: '/login', name: 'login', component: LoginView },
    {
      path: '/orders',
      name: 'orders',
      component: OrdersView,
      meta: { requiresAuth: true },
    },
    {
      path: '/orders/new',
      name: 'order-create',
      component: OrderFormView,
      meta: { requiresAuth: true },
    },
    {
      path: '/orders/:id/edit',
      name: 'order-edit',
      component: OrderFormView,
      meta: { requiresAuth: true },
    },
    {
      path: '/orders/:id',
      name: 'order-detail',
      component: OrderDetailView,
      meta: { requiresAuth: true },
    },
  ],
})

router.beforeEach((to) => {
  const hasToken = Boolean(sessionStorage.getItem(TOKEN_KEY))

  if (to.meta.requiresAuth && !hasToken) return { name: 'login' }
  if (to.name === 'login' && hasToken) return { name: 'orders' }
})

export default router
