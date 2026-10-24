<script setup>
import { ref } from 'vue'
import { useRouter } from 'vue-router'
import api, { TOKEN_KEY } from '../services/api'

const router = useRouter()
const email = ref('')
const password = ref('')
const pending = ref(false)
const message = ref('')
const errors = ref({})

async function submit() {
  pending.value = true
  message.value = ''
  errors.value = {}

  try {
    const { data } = await api.post('/login', {
      email: email.value,
      password: password.value,
    })

    sessionStorage.setItem(TOKEN_KEY, data.token)
    await router.replace({ name: 'orders' })
  } catch (error) {
    if (error.response?.status === 422) {
      errors.value = error.response.data.errors ?? {}
    } else if (error.response?.status === 401) {
      message.value = 'Email atau password salah.'
    } else {
      message.value = 'Login gagal. Periksa koneksi ke API lalu coba lagi.'
    }
  } finally {
    pending.value = false
  }
}
</script>

<template>
  <main class="login-page">
    <section class="panel login-card" aria-labelledby="login-title">
      <p class="brand">ORDERLY <span> / PESANAN</span></p>
      <h1 id="login-title">Masuk ke akunmu</h1>
      <p class="muted intro">Kelola pesanan souvenir dalam satu tempat.</p>

      <form class="login-form" @submit.prevent="submit">
        <div class="field">
          <label for="email">Email</label>
          <input id="email" v-model.trim="email" type="email" autocomplete="username" required />
          <p v-if="errors.email" class="field-error">{{ errors.email[0] }}</p>
        </div>

        <div class="field">
          <label for="password">Password</label>
          <input
            id="password"
            v-model="password"
            type="password"
            autocomplete="current-password"
            required
          />
          <p v-if="errors.password" class="field-error">{{ errors.password[0] }}</p>
        </div>

        <p v-if="message" class="field-error" role="alert">{{ message }}</p>

        <button class="button button--primary submit" type="submit" :disabled="pending">
          {{ pending ? 'Memeriksa akun…' : 'Masuk' }}
        </button>
      </form>
    </section>
  </main>
</template>

<style scoped>
.login-page {
  display: grid;
  min-height: 100dvh;
  place-items: center;
  padding: 24px;
}

.login-card {
  width: min(100%, 440px);
  padding: clamp(28px, 5vw, 44px);
}

.brand {
  margin-bottom: 36px;
  color: var(--accent);
  font-size: 0.78rem;
  font-weight: 800;
  letter-spacing: 0.13em;
}

.brand span {
  color: var(--muted);
  font-weight: 500;
}

.intro {
  margin: 12px 0 32px;
  line-height: 1.5;
}

.login-form {
  display: grid;
  gap: 20px;
}

.submit {
  width: 100%;
  margin-top: 4px;
}
</style>
