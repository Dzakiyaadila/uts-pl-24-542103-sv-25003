<script setup>
import { computed, onMounted, reactive, ref } from 'vue'
import { useRoute, useRouter } from 'vue-router'
import api from '../services/api'

const route = useRoute()
const router = useRouter()
const editing = computed(() => Boolean(route.params.id))

const form = reactive({
  customer_name: '',
  product_name: '',
  quantity: 1,
  unit_price: 0,
  status: 'pending',
  due_date: '',
  notes: '',
})

const loading = ref(false)
const saving = ref(false)
const message = ref('')
const errors = ref({})

async function loadOrder() {
  if (!editing.value) return

  loading.value = true

  try {
    const { data } = await api.get(`/orders/${route.params.id}`)

    Object.assign(form, {
      customer_name: data.customer_name,
      product_name: data.product_name,
      quantity: data.quantity,
      unit_price: data.unit_price,
      status: data.status,
      due_date: data.due_date.slice(0, 10),
      notes: data.notes ?? '',
    })
  } catch {
    message.value = 'Pesanan tidak ditemukan atau tidak dapat dimuat.'
  } finally {
    loading.value = false
  }
}

async function saveOrder() {
  saving.value = true
  message.value = ''
  errors.value = {}

  const payload = {
    customer_name: form.customer_name,
    product_name: form.product_name,
    quantity: Number(form.quantity),
    unit_price: Number(form.unit_price),
    status: form.status,
    due_date: form.due_date,
    notes: form.notes || null,
  }

  try {
    if (editing.value) {
      await api.put(`/orders/${route.params.id}`, payload)
    } else {
      await api.post('/orders', payload)
    }

    await router.push({ name: 'orders' })
  } catch (error) {
    if (error.response?.status === 422) {
      errors.value = error.response.data.errors ?? {}
    } else {
      message.value = 'Pesanan gagal disimpan. Coba lagi.'
    }
  } finally {
    saving.value = false
  }
}

onMounted(loadOrder)
</script>

<template>
  <main class="page form-page">
    <RouterLink class="back-link" :to="{ name: 'orders' }">← Kembali ke pesanan</RouterLink>

    <div class="page-header">
      <div>
        <p class="section-label">PENGELOLAAN PESANAN</p>
        <h1>{{ editing ? 'Edit pesanan' : 'Tambah pesanan' }}</h1>
        <p class="muted">Isi informasi yang diperlukan untuk mencatat pekerjaan.</p>
      </div>
    </div>

    <section class="panel">
      <p v-if="loading" role="status">Memuat pesanan…</p>

      <form v-else class="order-form" @submit.prevent="saveOrder">
        <h2>Informasi pesanan</h2>

        <div class="form-grid">
          <div class="field">
            <label for="customer">Nama pelanggan</label>
            <input id="customer" v-model.trim="form.customer_name" required maxlength="255" />
            <p v-if="errors.customer_name" class="field-error">
              {{ errors.customer_name[0] }}
            </p>
          </div>

          <div class="field">
            <label for="product">Produk</label>
            <input id="product" v-model.trim="form.product_name" required maxlength="255" />
            <p v-if="errors.product_name" class="field-error">
              {{ errors.product_name[0] }}
            </p>
          </div>
        </div>

        <h2>Harga & tenggat</h2>

        <div class="form-grid">
          <div class="field">
            <label for="quantity">Jumlah</label>
            <input
              id="quantity"
              v-model.number="form.quantity"
              type="number"
              min="1"
              max="100000"
              step="1"
              required
            />
            <p v-if="errors.quantity" class="field-error">{{ errors.quantity[0] }}</p>
          </div>

          <div class="field">
            <label for="price">Harga satuan (Rp)</label>
            <input
              id="price"
              v-model.number="form.unit_price"
              type="number"
              min="0"
              max="1000000000"
              step="1"
              required
            />
            <p v-if="errors.unit_price" class="field-error">{{ errors.unit_price[0] }}</p>
          </div>

          <div class="field">
            <label for="due-date">Tenggat</label>
            <input id="due-date" v-model="form.due_date" type="date" required />
            <p v-if="errors.due_date" class="field-error">{{ errors.due_date[0] }}</p>
          </div>

          <div class="field">
            <label for="status">Status</label>
            <select id="status" v-model="form.status">
              <option value="pending">Menunggu</option>
              <option value="processing">Diproses</option>
              <option value="completed">Selesai</option>
              <option value="cancelled">Dibatalkan</option>
            </select>
            <p v-if="errors.status" class="field-error">{{ errors.status[0] }}</p>
          </div>
        </div>

        <div class="field">
          <label for="notes">Catatan <span class="muted">(opsional)</span></label>
          <textarea id="notes" v-model="form.notes" maxlength="5000" />
          <p v-if="errors.notes" class="field-error">{{ errors.notes[0] }}</p>
        </div>

        <p v-if="message" class="field-error" role="alert">{{ message }}</p>

        <div class="form-actions">
          <RouterLink class="button" :to="{ name: 'orders' }">Batal</RouterLink>
          <button class="button button--primary" type="submit" :disabled="saving">
            {{ saving ? 'Menyimpan…' : 'Simpan pesanan' }}
          </button>
        </div>
      </form>
    </section>
  </main>
</template>

<style scoped>
.form-page {
  max-width: 820px;
}

.back-link {
  display: inline-block;
  margin-bottom: 32px;
  color: var(--muted);
  font-size: 0.9rem;
  text-decoration: none;
}

.back-link:hover {
  color: var(--accent);
}

.section-label {
  margin-bottom: 12px;
  color: var(--accent);
  font-size: 0.75rem;
  font-weight: 800;
  letter-spacing: 0.13em;
}

.order-form {
  display: grid;
  gap: 24px;
}

.order-form h2 {
  margin: 8px 0 0;
  padding-bottom: 12px;
  border-bottom: 1px solid var(--line);
}

.form-grid {
  display: grid;
  grid-template-columns: 1fr 1fr;
  gap: 20px;
}

.form-actions {
  display: flex;
  justify-content: flex-end;
  gap: 12px;
  padding-top: 20px;
  border-top: 1px solid var(--line);
}

@media (max-width: 640px) {
  .form-grid {
    grid-template-columns: 1fr;
  }

  .form-actions {
    flex-direction: column-reverse;
  }
}
</style>
