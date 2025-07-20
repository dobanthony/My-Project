<!-- <template>
  <Line :data="chartData" :options="chartOptions" />
</template>

<script setup>
import { Line } from 'vue-chartjs'
import {
  Chart as ChartJS,
  Title,
  Tooltip,
  Legend,
  LineElement,
  PointElement,
  CategoryScale,
  LinearScale,
  Filler, // ✅ Add this
} from 'chart.js'

ChartJS.register(
  Title,
  Tooltip,
  Legend,
  LineElement,
  PointElement,
  CategoryScale,
  LinearScale,
  Filler // ✅ Register it here
)

const props = defineProps({
  chartData: Object,
  chartOptions: Object
})
</script> -->

<template>
  <div class="chart-container">
    <Line :data="chartData" :options="chartOptions" />
  </div>
</template>

<script setup>
import { Line } from 'vue-chartjs'
import {
  Chart as ChartJS,
  Title,
  Tooltip,
  Legend,
  LineElement,
  PointElement,
  CategoryScale,
  LinearScale,
  Filler,
} from 'chart.js'

ChartJS.register(
  Title,
  Tooltip,
  Legend,
  LineElement,
  PointElement,
  CategoryScale,
  LinearScale,
  Filler
)

const props = defineProps({
  chartData: Object,
  chartOptions: Object
})
</script>

<style scoped>
.chart-container {
  position: relative;
  width: 100%;
  height: 300px; /* ✅ Ensures visibility on all screens */
}

@media (max-width: 768px) {
  .chart-container {
    height: 250px; /* Optional: Adjust for smaller screens */
  }
}
</style>

