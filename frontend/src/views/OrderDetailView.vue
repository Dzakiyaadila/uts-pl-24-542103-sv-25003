<script setup>
import { onMounted, ref } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import api from '../services/api'

const route = useRoute()
const router = useRouter()
const order = ref(null)
const loading = ref(true)
const deleting = ref(false)
const message = ref('')

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
    month: 'long',
    year: 'numeric',
  }).format(new Date(year, month - 1, day))
}

async function loadOrder() {
  try {
    const { data } = await api.get(`/orders/${route.params.id}`)
    order.value = data
  } catch {
    message.value = 'Pesanan tidak ditemukan atau tidak dapat dimuat.'
  } finally {
    loading.value = false
  }
}

async function deleteOrder() {
  if (
    !window.confirm(
      `Hapus pesanan ${order.value.customer_name}? Tindakan ini tidak dapat dibatalkan.`,
    )
  ) {
    return
  }

  deleting.value = true
  message.value = ''

  try {
    await api.delete(`/orders/${order.value.id}`)
    await router.replace({ name: 'orders' })
  } catch {
    message.value = 'Pesanan gagal dihapus. Coba lagi.'
    deleting.value = false
  }
}

onMounted(loadOrder)
</script>

<template>
  <main class="page detail-page">
    <RouterLink class="back-link" :to="{ name: 'orders' }">← Kembali ke pesanan</RouterLink>

    <p v-if="loading" role="status">Memuat pesanan…</p>
    <p v-else-if="!order" class="field-error" role="alert">{{ message }}</p>

    <template v-else>
      <div class="page-header">
        <div>
          <p class="section-label">DETAIL PESANAN</p>
          <h1>{{ order.customer_name }}</h1>
          <p class="muted">{{ order.product_name }}</p>
        </div>
        <span class="status" :class="`status--${order.status}`">
          {{ statusLabels[order.status] ?? order.status }}
        </span>
      </div>

      <section class="panel">
        <h2>Informasi pesanan</h2>

        <dl class="detail-grid">
          <div>
            <dt>Jumlah</dt>
            <dd>{{ order.quantity }}</dd>
          </div>
          <div>
            <dt>Harga satuan</dt>
            <dd>{{ rupiah.format(order.unit_price) }}</dd>
          </div>
          <div>
            <dt>Total harga</dt>
            <dd class="total">{{ rupiah.format(order.total_price) }}</dd>
          </div>
          <div>
            <dt>Tenggat</dt>
            <dd>{{ dueDate(order.due_date) }}</dd>
          </div>
          <div class="notes">
            <dt>Catatan</dt>
            <dd>{{ order.notes || 'Tidak ada catatan.' }}</dd>
          </div>
        </dl>

        <p v-if="message" class="field-error" role="alert">{{ message }}</p>

        <div class="actions">
          <button
            class="button button--danger"
            type="button"
            :disabled="deleting"
            @click="deleteOrder"
          >
            {{ deleting ? 'Menghapus…' : 'Hapus pesanan' }}
          </button>
          <RouterLink
            class="button button--primary"
            :to="{ name: 'order-edit', params: { id: order.id } }"
          >
            Edit pesanan
          </RouterLink>
        </div>
      </section>
    </template>
  </main>
</template>

<style scoped>
.detail-page {
  max-width: 820px;
}

.back-link {
  display: inline-block;
  margin-bottom: 32px;
  color: var(--muted);
  font-size: 0.9rem;
  text-decoration: none;
}

.section-label {
  margin-bottom: 12px;
  color: var(--accent);
  font-size: 0.75rem;
  font-weight: 800;
  letter-spacing: 0.13em;
}

.detail-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 28px;
  margin: 28px 0;
}

.detail-grid > div {
  padding-bottom: 18px;
  border-bottom: 1px solid var(--line);
}

dt {
  margin-bottom: 8px;
  color: var(--muted);
  font-size: 0.85rem;
}

dd {
  margin: 0;
  font-weight: 600;
}

.total {
  color: var(--accent);
  font-size: 1.25rem;
}

.notes {
  grid-column: 1 / -1;
}

.notes dd {
  white-space: pre-wrap;
}

.actions {
  display: flex;
  justify-content: flex-end;
  gap: 12px;
}

@media (max-width: 640px) {
  .detail-grid {
    grid-template-columns: 1fr;
  }

  .actions {
    flex-direction: column-reverse;
  }
}
</style>
