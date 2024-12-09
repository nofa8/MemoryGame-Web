<script setup>
import { onMounted } from 'vue';
import { ref } from 'vue';

import { useTransactionStore } from '@/stores/transaction';
import PurchaseIcon from '@/assets/purchase.svg';
import InternalIcon from '@/assets/internal.svg';
import BonusIcon from '@/assets/bonus.svg';

const expandedTransactionId = ref(null);

function toggleDetails(id) {
    expandedTransactionId.value = expandedTransactionId.value === id ? null : id;
}
const transactionsStore = useTransactionStore();
const filterType = ref('');

const applyFilter = () => {
    transactionsStore.fetchTransactions(1, filterType.value); // Passa o tipo como parâmetro
};

onMounted(() => {
    transactionsStore.fetchTransactions(1);
});
</script>

<template>



    <div class="flex items-center justify-between mb-4 ml-4 mr-4">
        <!-- Improved Filter Dropdown -->
        <div class="relative">
            <label for="filter" class="block text-sm font-medium text-gray-700 mb-1"></label>
            <div class="relative">
                <select v-model="filterType" @change="applyFilter" class="
                    appearance-none 
                    w-full 
                    py-2 
                    px-3 
                    border 
                    border-gray-300 
                    rounded-md 
                    shadow-sm 
                    focus:outline-none 
                    focus:ring-2 
                    focus:ring-blue-500 
                    focus:border-blue-500 
                    bg-white 
                    text-gray-900 
                    text-sm 
                    transition-all 
                    duration-300 
                    hover:border-blue-400 
                    pr-8
                ">
                    <option value="" class="bg-white text-gray-900">All Transactions</option>
                    <option value="B" class="bg-white text-gray-900">BONUS Transactions</option>
                    <option value="I" class="bg-white text-gray-900">INTERNAL Transactions</option>
                    <option value="P" class="bg-white text-gray-900">PURCHASE Transactions</option>
                </select>
                <!-- Custom dropdown arrow -->
                <div class="pointer-events-none absolute inset-y-0 right-0 flex items-center px-2 text-gray-700">
                    <svg class="fill-current h-4 w-4" xmlns="http://www.w3.org/2000/svg" viewBox="0 0 20 20">
                        <path d="M9.293 12.95l.707.707L15.657 8l-1.414-1.414L10 10.828 5.757 6.586 4.343 8z" />
                    </svg>
                </div>
            </div>
        </div>

        <!-- Purchase Button -->
        <button @click="$router.push('/purchase')" class="
            mt-4 
            px-4 
            py-2 
            bg-blue-600 
            text-white 
            rounded-md 
            hover:bg-blue-700 
            focus:outline-none 
            focus:ring-2 
            focus:ring-blue-500 
            focus:ring-offset-2 
            transition-colors 
            duration-300 
            shadow-md 
            hover:shadow-lg
        ">
            Fazer Compra
        </button>
    </div>





    <div class="container mx-auto p-4">
        <div class="overflow-x-auto rounded-lg shadow-md">

            <div v-for="transaction in transactionsStore.transactions" :key="transaction.id" :class="{
                'bg-blue-100': transaction.type === 'B',  // Bônus: Azul claro
                'bg-yellow-100': transaction.type === 'P', // Compras: Amarelo claro
                'bg-orange-100': transaction.type === 'I', // Interno: Laranja claro
                'bg-green-100': transaction.euros > 0,     // Earnings positivos: Verde claro
                'bg-purple-100': transaction.euros < 0     // Spendings negativos: Roxo claro
            }" class="shadow-md rounded-lg p-4 mb-2 flex justify-between items-center">

                <button @click="toggleDetails(transaction.id)"
                    class="w-full text-left flex items-center justify-between">
                    <div>
                        <!-- Informações principais da transação -->
                        <p :class="{ 'font-bold': transaction.transaction_datetime }">
                            {{ transaction.transaction_datetime ?
                                transactionsStore.formatDate(transaction.transaction_datetime) : '' }}
                        </p>
                        <p v-if="transaction.game_id != null">ID do Jogo: {{ transaction.game_id ? transaction.game_id :
                            'Sem jogo' }}</p>
                        <p :class="{
                            'text-blue-500': transaction.type === 'B',
                            'text-yellow-500': transaction.type === 'P',
                            'text-orange-500': transaction.type === 'I'
                        }">
                            {{
                                transaction.type === 'B' ? 'Bônus' :
                                    transaction.type === 'P' ? 'Compras' :
                                        transaction.type === 'I' ? 'Interno' : ''
                            }}
                        </p>
                        <p v-if="transaction.euros != null"
                            :class="{ 'text-green-500': transaction.euros > 0, 'text-purple-500': transaction.euros < 0 }">
                            Euros: {{ transaction.euros }}
                        </p>
                        <p
                            :class="{ 'text-green-500': transaction.brain_coins > 1, 'text-purple-500': transaction.brain_coins < 1 }">
                            Brain Coins: {{ transaction.brain_coins }}
                        </p>
                    </div>

                    <img :src="transaction.type === 'B' ? BonusIcon : transaction.type === 'P' ? PurchaseIcon : InternalIcon"
                        alt="Transaction Icon" class="w-9 h-9">

                </button>

                <!-- Detalhes adicionais da transação -->
                <div v-if="expandedTransactionId === transaction.id && transaction.type == 'P'"
                    class="p-4 border-t mt-2">
                    <p v-if="transaction.payment_type">Tipo de Pagamento: {{ transaction.payment_type }}</p>
                    <p v-if="transaction.payment_reference">Referência de Pagamento: {{ transaction.payment_reference }}
                    </p>
                    <p v-if="transaction.custom">Dados Personalizados: {{ transaction.custom }}</p>
                </div>
            </div>

        </div>

        <div class="flex justify-between mt-4">
            <button class="px-4 py-2 bg-gray-200 text-gray-700 rounded" :disabled="transactionsStore.currentPage === 1"
                @click="transactionsStore.fetchTransactions(transactionsStore.currentPage - 1)">
                Previous
            </button>

            <span class="self-center">
                Page {{ transactionsStore.currentPage }} of {{ transactionsStore.lastPage }}
            </span>

            <button class="px-4 py-2 bg-gray-200 text-gray-700 rounded"
                :disabled="transactionsStore.currentPage === transactionsStore.lastPage"
                @click="transactionsStore.fetchTransactions(transactionsStore.currentPage + 1)">
                Next
            </button>
        </div>
    </div>
</template>