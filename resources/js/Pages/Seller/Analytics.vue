<template>
  <Head title="Dashboard" />
  <SellerDashboardLayout>
    <div class="container">
      <!-- IF SELLER HAS A SHOP -->
      <div v-if="shop && shop.id">
        <!-- Header -->
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-3 gap-2">
          <!-- Left: Analytics Header -->
          <h2 class="fw-bold mb-2 mb-md-0 text-dark">
            <i class="bi bi-bar-chart-line me-2"></i>Analytics
          </h2>

          <!-- Download Excel Button -->
          <div class="text-start text-md-end">
            <button class="btn btn-outline-success" @click="downloadExcel">
              <i class="bi bi-printer"></i> Print
            </button>
          </div>
        </div>
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-3 gap-2">
          <select class="form-select w-100 w-md-auto" v-model="selectedRange" @change="applyFilter">
            <option value="week">This Week</option>
            <option value="month">This Month</option>
            <option value="year">This Year</option>
          </select>
        </div>

        <!-- Stat Cards -->
        <div class="row mb-4 g-3">
          <div class="col-12 col-sm-6 col-md-3" v-for="stat in stats" :key="stat.key">
            <div class="card shadow h-100 border-0">
              <div class="card-body d-flex align-items-center">
                <div class="me-3">
                  <i :class="stat.icon" style="font-size: 2rem;"></i>
                </div>
                <div class="text-start">
                  <h6 class="text-muted mb-1">{{ stat.label }}</h6>
                  <h4 class="mb-0 text-dark">
                    {{ stat.isCurrency ? '₱ ' : '' }}{{ stat.value }}
                  </h4>
                </div>
              </div>
            </div>
          </div>
        </div>

        <!-- Sales Chart -->
        <div class="card mb-4 shadow">
          <div class="card-header bg-primary text-white">
            <i class="bi bi-graph-up me-2"></i>Sales Chart
          </div>
          <div class="card-body">
            <div style="position: relative; width: 100%; height: 400px;">
              <LineChart :chart-data="salesChart" :chart-options="chartOptions" />
            </div>
          </div>
        </div>

        <!-- Top-Selling Bar Chart -->
        <div class="card mb-4 shadow">
          <div class="card-header bg-success text-white">
            <i class="bi bi-fire me-2"></i>Top-Selling Products Chart
          </div>
          <div class="card-body">
            <div style="position: relative; width: 100%; height: 400px;">
              <BarChart :chart-data="topSellingChart" :chart-options="barChartOptions" />
            </div>
          </div>
        </div>

        <!-- Top-Selling & Low Stock Lists -->
        <div class="row mb-4">
          <div class="col-12 col-md-6 mb-3">
            <div class="card shadow h-100">
              <div class="card-header bg-success text-white">
                <i class="bi bi-fire me-2"></i>Top-Selling Products (List)
              </div>
              <div class="card-body">
                <ul class="list-group">
                  <li v-for="product in topSelling" :key="product.id" class="list-group-item d-flex justify-content-between">
                    {{ product.name }}
                    <span class="badge bg-success">{{ product.total_sold ?? 0 }}</span>
                  </li>
                </ul>
              </div>
            </div>
          </div>

          <div class="col-12 col-md-6 mb-3">
            <div class="card shadow h-100">
              <div class="card-header bg-danger text-white">
                <i class="bi bi-exclamation-triangle me-2"></i>Low Stock Products
              </div>
              <div class="card-body">
                <ul class="list-group">
                  <li v-for="product in lowStock" :key="product.id" class="list-group-item d-flex justify-content-between">
                    {{ product.name }}
                    <span class="badge bg-danger">{{ product.stock }}</span>
                  </li>
                </ul>
              </div>
            </div>
          </div>
        </div>

        <!-- Customer, Rating, Orders -->
        <div class="row mb-4">
          <div class="col-12 col-md-4 mb-3">
            <div class="card shadow border-info text-center h-100">
              <div class="card-body">
                <h6 class="text-muted">
                  <i class="bi bi-award me-1"></i>Top Customer
                </h6>
                <p class="mb-1 fw-semibold">{{ customerStats.first_name }} {{ customerStats.last_name }}</p>
                <p class="text-success">₱ {{ customerStats.total_spent }}</p>
              </div>
            </div>
          </div>

          <div class="col-12 col-md-4 mb-3">
            <div class="card shadow border-warning text-center h-100">
              <div class="card-body">
                <h6 class="text-muted">
                  <i class="bi bi-star-fill me-1"></i>Ratings
                </h6>
                <p class="mb-1 text-warning fs-4">{{ ratings.average }} <i class="bi bi-star-fill"></i></p>
                <small class="text-muted">Based on {{ ratings.count }} reviews</small>
              </div>
            </div>
          </div>

          <div class="col-12 col-md-4 mb-3">
            <div class="card shadow border-secondary text-center h-100">
              <div class="card-body">
                <h6 class="text-muted">
                  <i class="bi bi-box-seam me-1"></i>Orders This Week
                </h6>
                <p class="fs-4 text-secondary fw-bold">{{ weeklyOrders }}</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Recent Orders Table -->
        <div class="card mb-4 shadow">
          <div class="card-header bg-dark text-white">
            <i class="bi bi-journal-text me-2"></i>Recent Orders
          </div>
          <div class="card-body table-responsive">
            <table class="table table-striped text-center align-middle">
              <thead>
                <tr>
                  <th>Order #</th>
                  <th>Customer</th>
                  <th>Total (₱)</th>
                  <th>Status</th>
                  <th>Date</th>
                </tr>
              </thead>
              <tbody>
                <tr v-for="order in recentOrders" :key="order.id">
                  <td>#{{ order.id }}</td>
                  <td>{{ order.first_name }}, {{ order.last_name }}</td>
                  <td>₱{{ order.total }}</td>
                  <td><span class="badge bg-info">{{ order.status }}</span></td>
                  <td>{{ order.date }}</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>
      </div>
    </div>
  </SellerDashboardLayout>
</template>

<script setup>
import SellerDashboardLayout from '@/Layouts/SellerDashboardLayout.vue'
import { ref } from 'vue'
import { router, usePage, Head } from '@inertiajs/vue3'
import LineChart from '@/Components/Charts/LineChart.vue'
import BarChart from '@/Components/Charts/BarChart.vue'

const page = usePage()
const selectedRange = ref(page.props.range || 'week')

function applyFilter() {
  router.get(route('seller.analytics'), { range: selectedRange.value }, {
    preserveScroll: true,
    preserveState: true,
  })
}

function downloadExcel() {
  const url = route('seller.analytics.export', { range: selectedRange.value });
  window.open(url, '_blank'); // triggers browser download
}

const props = defineProps({
  shop: Object,
  stats: Array,
  topSelling: Array,
  lowStock: Array,
  customerStats: Object,
  ratings: Object,
  weeklyOrders: Number,
  recentOrders: Array,
  reports: Object,
  salesChart: Object,
  topSellingChart: Object,
})

const chartOptions = {
  responsive: true,
  maintainAspectRatio: false,
  plugins: { legend: { display: true, position: 'top' }, title: { display: false } },
  scales: { y: { beginAtZero: true } },
}

const barChartOptions = {
  responsive: true,
  maintainAspectRatio: false,
  plugins: { legend: { display: false }, title: { display: false } },
  scales: { y: { beginAtZero: true } },
}
</script>
