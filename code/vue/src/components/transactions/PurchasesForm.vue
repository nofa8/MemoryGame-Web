<script setup>
import { inject, ref } from 'vue'
import axios from 'axios'
import { useAuthStore } from '@/stores/auth'
const authStore = useAuthStore()

const brainCoins = ref(10)
const euros = ref(brainCoins.value / 10)
const paymentType = ref('')
const paymentReference = ref('')
const errorMessage = ref('')
const successMessage = ref('')

const updateEuros = () => {
  euros.value = brainCoins.value / 10
}

const incrementCoins = () => {
  brainCoins.value += 10
  updateEuros()
}

const decrementCoins = () => {
  if (brainCoins.value > 10) {
    brainCoins.value -= 10
    updateEuros()
  }
}

const socketTAES = inject('socket')

const changedTAES = (value) => {
  if (authStore.user != null) {
    socketTAES.emit("transactionTAES",
    authStore.user,
      `Purchase complete: ${value} brain coins added to a total of ${authStore.userbrain_coins_balance} brain coins!`)
  }
}

const submitTransaction = async () => {
  errorMessage.value = ''
  successMessage.value = ''

  const paymentValidators = {
    MBWAY: (value) => /^9\d{8}$/.test(value),
    IBAN: (value) => /^[A-Z]{2}\d{23}$/.test(value),
    MB: (value) => /^\d{5}-\d{9}$/.test(value),
    VISA: (value) => /^4\d{15}$/.test(value),
    PAYPAL: (value) => /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value)
  }

  if (!paymentType.value || !paymentValidators[paymentType.value]?.(paymentReference.value)) {
    errorMessage.value = 'Please enter a valid payment reference for the selected method'
    return
  }

  try {
    const transactionData = {
      type: 'P',
      user_id: authStore.user?.id,
      brain_coins: brainCoins.value,
      euros: euros.value,
      payment_type: paymentType.value,
      payment_reference: paymentReference.value
    }
    const variable = ref(0)
    const response = await axios.post('/transactions', transactionData).then((response) => {
      variable.value =
        response.data.data.user_total_brain_coins - authStore.user.brain_coins_balance
      authStore.user.brain_coins_balance = response.data.data.user_total_brain_coins
      changedTAES(brainCoins.value)
    })
    successMessage.value = 'Purchase made successfully!'
    
  } catch (error) {
    if (error.response && error.response.status === 422) {
      errorMessage.value =
        error.response.data.message || 'An error occurred while making the purchase'
    } else {
      errorMessage.value = 'Unknown error'
    }
  }
}
</script>

<template>
  <div class="p-4">
    <h2 class="text-lg font-bold mb-4">Buy Brain Coins</h2>

    <!-- Seção para selecionar a quantidade de moedas -->
    <div class="flex items-center mb-4">
      <button @click="decrementCoins" class="px-4 py-2 bg-red-500 text-white rounded-md">-</button>
      <input
        type="number"
        v-model="brainCoins"
        @input="updateEuros"
        class="mx-4 p-2 border rounded-md w-20 text-center"
        min="10"
        step="10"
      />
      <button @click="incrementCoins" class="px-4 py-2 bg-green-500 text-white rounded-md">
        +
      </button>
    </div>

    <!-- Exibição do preço calculado -->
    <p class="mb-4">
      Preço total: <strong>{{ euros.toFixed(2) }}€</strong>
    </p>

    <div class="mb-4">
      <label for="paymentType" class="block font-medium">Payment Method:</label>
      <select id="paymentType" v-model="paymentType" class="p-2 border rounded-md w-full">
        <option value="">Select...</option>
        <option value="MBWAY">MBWAY</option>
        <option value="IBAN">IBAN</option>
        <option value="MB">Multibanco</option>
        <option value="VISA">VISA</option>
        <option value="PAYPAL">PAYPAL</option>
      </select>
    </div>

    <!-- Campo para referência de pagamento -->
    <div class="mb-4">
      <label for="paymentReference" class="block font-medium">Payment Reference:</label>
      <input
        type="text"
        id="paymentReference"
        v-model="paymentReference"
        class="p-2 border rounded-md w-full"
        placeholder="Enter payment reference"
      />
    </div>

    <!-- Botão para enviar -->
    <button @click="submitTransaction" class="px-4 py-2 bg-blue-600 text-white rounded-md">
      Confirm Purchase
    </button>

    <!-- Mensagem de erro ou sucesso -->
    <p v-if="errorMessage" class="text-red-500 mt-4">{{ errorMessage }}</p>
    <p v-else-if="successMessage" class="text-green-500 mt-4">{{ successMessage }}</p>
  </div>
</template>
