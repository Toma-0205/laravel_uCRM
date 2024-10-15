<script setup>
import { Chart, registerables } from 'chart.js';
import { BarChart } from 'vue-chart-3';
import { reactive, computed } from 'vue';

const props = defineProps({
  "data" : Object
})

const labels = computed(() => props.data.labels)
const totals = computed(() => props.data.totals)

Chart.register(...registerables);

const barData = reactive({
    // labels: ["Jan", "Feb", "Mar", "Apr", "May", "Jun", "Jul"],
    labels: labels,
    datasets: [
        {
            label: '売上',
            // data:[65, 59, 80, 81, 56, 55, 40],
            data: totals,
            backgroundColor: "rgb(75, 192, 192)",
            tension: 0.1,
        }
    ]
})

</script>

<template>
  <div v-show="props.data">
    <BarChart :chartData="barData" />
  </div>
</template>



<style>

</style>