<script setup>
import { ref } from 'vue'
import axios from 'axios'
import { useAuthStore } from '@/stores/auth';
const authStore = useAuthStore();

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

const submitTransaction = async () => {
  errorMessage.value = ''
  successMessage.value = ''

  const paymentValidators = {
  MBWAY: value => /^9\d{8}$/.test(value), 
  IBAN: value => /^[A-Z]{2}\d{23}$/.test(value),
  MB: value => /^\d{5}-\d{9}$/.test(value), 
  VISA: value => /^4\d{15}$/.test(value), 
  PAYPAL: value => /^[^\s@]+@[^\s@]+\.[^\s@]+$/.test(value), 
};


  if (!paymentType.value || !paymentValidators[paymentType.value]?.(paymentReference.value)) {
    errorMessage.value = 'Por favor, insira uma referência de pagamento válida para o método selecionado.'
    return
  }

  try {
    const transactionData = {
      type: 'P',
      user_id: authStore.user?.id,
      brain_coins: brainCoins.value,
      euros: euros.value,
      payment_type: paymentType.value,
      payment_reference: paymentReference.value,
    }

    const response = await axios.post('/transactions', transactionData)
    successMessage.value = 'Compra realizada com sucesso!'
  } catch (error) {
    errorMessage.value = error.response?.data?.message || 'Ocorreu um erro ao realizar a compra.'
  }
}
</script>

<template>
  <div class="p-4">
    <h2 class="text-lg font-bold mb-4">Comprar Brain Coins</h2>

    <!-- Seção para selecionar a quantidade de moedas -->
    <div class="flex items-center mb-4">
      <button 
        @click="decrementCoins" 
        class="px-4 py-2 bg-red-500 text-white rounded-md"
      >-</button>
      <input 
        type="number" 
        v-model="brainCoins" 
        @input="updateEuros" 
        class="mx-4 p-2 border rounded-md w-20 text-center" 
        min="10" step="10"
      />
      <button 
        @click="incrementCoins" 
        class="px-4 py-2 bg-green-500 text-white rounded-md"
      >+</button>
    </div>

    <!-- Exibição do preço calculado -->
    <p class="mb-4">Preço total: <strong>{{ euros.toFixed(2) }}€</strong></p>

    <!-- Dropdown para selecionar o tipo de pagamento -->
    <div class="mb-4">
      <label for="paymentType" class="block font-medium">Método de Pagamento:</label>
      <select 
        id="paymentType" 
        v-model="paymentType" 
        class="p-2 border rounded-md w-full"
      >
        <option value="">Selecione...</option>
        <option value="MBWAY">MBWAY</option>
        <option value="IBAN">IBAN</option>
        <option value="MB">Multibanco</option>
        <option value="VISA">VISA</option>
        <option value="PAYPAL">PAYPAL</option>
      </select>
    </div>

    <!-- Campo para referência de pagamento -->
    <div class="mb-4">
      <label for="paymentReference" class="block font-medium">Referência de Pagamento:</label>
      <input 
        type="text" 
        id="paymentReference" 
        v-model="paymentReference" 
        class="p-2 border rounded-md w-full"
        placeholder="Insira a referência de pagamento"
      />
    </div>

    <!-- Botão para enviar -->
    <button 
      @click="submitTransaction" 
      class="px-4 py-2 bg-blue-600 text-white rounded-md"
    >Confirmar Compra</button>

    <!-- Mensagem de erro ou sucesso -->
    <p v-if="errorMessage" class="text-red-500 mt-4">{{ errorMessage }}</p>
    <p v-if="successMessage" class="text-green-500 mt-4">{{ successMessage }}</p>
  </div>
</template>
