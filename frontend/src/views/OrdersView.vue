<script setup>
import { onMounted, ref } from 'vue'
import { useRouter } from 'vue-router'
import api, { TOKEN_KEY } from '../services/api'

const router = useRouter()
const orders = ref([])
const loading = ref(true)
const leaving = ref(false)
const errorMessage = ref('')

const statusLabels = {
  pending: 'Menunggu',
  processing: 'Diproses',
  completed: 'Selesai',
  cancelled: 'Dibatalkan',
}

const rupiah = new Intl.NumberFormat('id-ID', {
  style: 'currency',
  currency: 'IDR',
  maximumFractionDigits: 0,
})

function dueDate(value) {
  if (!value) return '—'

  const [year, month, day] = value.slice(0, 10).split('-').map(Number)
  return new Intl.DateTimeFormat('id-ID', {
    day: 'numeric',
    month: 'short',
    year: 'numeric',
  }).format(new Date(year, month - 1, day))
}

async function loadOrders() {
  loading.value = true
  errorMessage.value = ''

  try {
    const { data } = await api.get('/orders')
    orders.value = data
  } catch {
    errorMessage.value = 'Pesanan belum dapat dimuat. Coba lagi.'
  } finally {
    loading.value = false
  }
}

async function logout() {
  leaving.value = true
  errorMessage.value = ''

  try {
    await api.post('/logout')
    sessionStorage.removeItem(TOKEN_KEY)
    await router.replace({ name: 'login' })
  } catch {
    errorMessage.value = 'Gagal keluar. Periksa koneksi lalu coba lagi.'
  } finally {
    leaving.value = false
  }
}

onMounted(loadOrders)
</script>

<template>
  <div>
    <header class="topbar">
      <span class="wordmark">ORDERLY<span class="wordmark-mark">.</span></span>
      <button class="button" type="button" :disabled="leaving" @click="logout">
        {{ leaving ? 'Keluar…' : 'Keluar' }}
      </button>
    </header>

    <main class="page">
      <div class="page-header">
        <div>
          <p class="section-label">PENGELOLAAN PESANAN</p>
          <h1>Pesanan</h1>
          <p class="muted subtitle">Pantau pekerjaan dan tenggat pelanggan.</p>
        </div>

        <RouterLink class="button button--primary" :to="{ name: 'order-create' }">
          Tambah pesanan
        </RouterLink>
      </div>

      <section class="panel" aria-labelledby="orders-heading">
        <div class="list-heading">
          <h2 id="orders-heading">Daftar pesanan</h2>
          <span v-if="!loading" class="muted">{{ orders.length }} pesanan</span>
        </div>

        <p v-if="loading" class="state-message" role="status">Memuat pesanan…</p>

        <div v-else-if="errorMessage" class="state-message" role="alert">
          <p>{{ errorMessage }}</p>
          <button class="button" type="button" @click="loadOrders">Coba lagi</button>
        </div>

        <p v-else-if="orders.length === 0" class="state-message">
          Belum ada pesanan untuk akun ini.
        </p>

        <div v-else>
          <div class="order-head" aria-hidden="true">
            <span>Pelanggan / produk</span>
            <span>Jumlah</span>
            <span>Total</span>
            <span>Tenggat</span>
            <span>Status</span>
          </div>

          <ul class="order-list">
            <li v-for="order in orders" :key="order.id" class="order-row">
              <div class="order-name">
                <RouterLink :to="{ name: 'order-detail', params: { id: order.id } }">
                  {{ order.customer_name }}
                </RouterLink>
                <span class="muted">{{ order.product_name }}</span>
                <RouterLink :to="{ name: 'order-edit', params: { id: order.id } }">
                  Edit pesanan
                </RouterLink>
              </div>

              <div>
                <span class="mobile-label">Jumlah</span>
                {{ order.quantity }}
              </div>

              <div class="amount">
                <span class="mobile-label">Total</span>
                {{ rupiah.format(order.total_price) }}
              </div>

              <div>
                <span class="mobile-label">Tenggat</span>
                {{ dueDate(order.due_date) }}
              </div>

              <div>
                <span class="mobile-label">Status</span>
                <span class="status" :class="`status--${order.status}`">
                  {{ statusLabels[order.status] ?? order.status }}
                </span>
              </div>
            </li>
          </ul>
        </div>
      </section>
    </main>
  </div>
</template>

<style scoped>
.topbar {
  display: flex;
  align-items: center;
  justify-content: space-between;
  padding: 16px max(24px, calc((100vw - 1120px) / 2));
  border-bottom: 1px solid var(--line);
  background: var(--surface);
}

.wordmark {
  font-size: 0.95rem;
  font-weight: 800;
  letter-spacing: 0.1em;
}

.wordmark-mark {
  color: var(--accent);
}

.section-label {
  margin-bottom: 12px;
  color: var(--accent);
  font-size: 0.75rem;
  font-weight: 800;
  letter-spacing: 0.13em;
}

.subtitle {
  margin: 10px 0 0;
}

.list-heading {
  display: flex;
  align-items: baseline;
  justify-content: space-between;
  gap: 16px;
  padding-bottom: 20px;
}

.list-heading h2 {
  margin-bottom: 0;
}

.order-head,
.order-row {
  display: grid;
  grid-template-columns: minmax(180px, 2fr) 70px 135px 125px 115px;
  align-items: center;
  gap: 16px;
}

.order-head {
  padding: 14px 0;
  border-bottom: 1px solid var(--line);
  color: var(--muted);
  font-size: 0.8rem;
  font-weight: 700;
}

.order-list {
  margin: 0;
  padding: 0;
  list-style: none;
}

.order-row {
  min-height: 78px;
  padding: 14px 0;
  border-bottom: 1px solid var(--line);
  font-size: 0.9rem;
}

.order-row:last-child {
  border-bottom: 0;
}

.order-name {
  display: grid;
  gap: 4px;
}

.amount {
  font-variant-numeric: tabular-nums;
  font-weight: 650;
}

.mobile-label {
  display: none;
}

.state-message {
  margin: 0;
  padding: 40px 0;
  color: var(--muted);
}

.state-message p {
  margin-bottom: 12px;
}

@media (max-width: 760px) {
  .order-head {
    display: none;
  }

  .order-row {
    grid-template-columns: 1fr 1fr;
    gap: 18px;
    padding: 22px 0;
  }

  .order-name {
    grid-column: 1 / -1;
  }

  .mobile-label {
    display: block;
    margin-bottom: 5px;
    color: var(--muted);
    font-size: 0.75rem;
  }
}
</style>
