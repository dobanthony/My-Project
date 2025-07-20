<template>
  <SellerDashboardLayout>
    <div class="container">
      <!-- IF SELLER HAS A SHOP -->
      <div v-if="shop && shop.id">
        <!-- Header -->
         <h2 class="fw-bold mb-2 text-dark"><i class="bi bi-bar-chart-line me-2"></i>Analytics</h2>
        <div class="d-flex flex-column flex-md-row justify-content-between align-items-start align-items-md-center mb-3 gap-2">
          <select class="form-select w-100 w-md-auto" v-model="selectedRange" @change="applyFilter">
            <option value="week">This Week</option>
            <option value="month">This Month</option>
            <option value="year">This Year</option>
          </select>
        </div>

        <!-- Stat Cards -->
        <div class="row mb-4">
          <div class="col-12 col-sm-6 col-md-3 mb-3" v-for="(value, key) in stats" :key="key">
            <div class="card shadow border-success h-100">
              <div class="card-body text-center">
                <h6 class="text-muted text-capitalize">{{ key.replace(/([A-Z])/g, ' $1') }}</h6>
                <h4 class="text-success">₱ {{ value }}</h4>
              </div>
            </div>
          </div>
        </div>

        <!-- Sales Chart -->
        <div class="card mb-4 shadow">
          <div class="card-header bg-primary text-white">📈 Sales Chart</div>
          <div class="card-body">
            <div style="position: relative; width: 100%; height: 400px;">
              <LineChart :chart-data="salesChart" :chart-options="chartOptions" />
            </div>
          </div>
        </div>

        <!-- Top-Selling Bar Chart -->
        <div class="card mb-4 shadow">
          <div class="card-header bg-success text-white">🔥 Top-Selling Products Chart</div>
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
              <div class="card-header bg-success text-white">🔥 Top-Selling Products (List)</div>
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
              <div class="card-header bg-danger text-white">⚠️ Low Stock Products</div>
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
                <h6 class="text-muted">🏆 Top Customer</h6>
                <p class="mb-1 fw-semibold">{{ customerStats.name }}</p>
                <p class="text-success">₱ {{ customerStats.total_spent }}</p>
              </div>
            </div>
          </div>

          <div class="col-12 col-md-4 mb-3">
            <div class="card shadow border-warning text-center h-100">
              <div class="card-body">
                <h6 class="text-muted">⭐ Ratings</h6>
                <p class="mb-1 text-warning fs-4">{{ ratings.average }} ★</p>
                <small class="text-muted">Based on {{ ratings.count }} reviews</small>
              </div>
            </div>
          </div>

          <div class="col-12 col-md-4 mb-3">
            <div class="card shadow border-secondary text-center h-100">
              <div class="card-body">
                <h6 class="text-muted">📦 Orders This Week</h6>
                <p class="fs-4 text-secondary fw-bold">{{ weeklyOrders }}</p>
              </div>
            </div>
          </div>
        </div>

        <!-- Recent Orders Table -->
        <div class="card mb-4 shadow">
          <div class="card-header bg-dark text-white">🧾 Recent Orders</div>
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
                  <td>{{ order.customer_name }}</td>
                  <td>₱{{ order.total }}</td>
                  <td><span class="badge bg-info">{{ order.status }}</span></td>
                  <td>{{ order.date }}</td>
                </tr>
              </tbody>
            </table>
          </div>
        </div>

        <!-- Reports Summary -->
        <div class="card shadow">
          <div class="card-header bg-secondary text-white">📋 Receipt / Reports Summary</div>
          <div class="card-body">
            <ul class="list-group">
              <li class="list-group-item d-flex justify-content-between">
                Receipts Acknowledged <span class="badge bg-secondary">{{ reports.receipts }}</span>
              </li>
              <li class="list-group-item d-flex justify-content-between">
                Reports Filed <span class="badge bg-warning">{{ reports.reportsFiled }}</span>
              </li>
              <li class="list-group-item d-flex justify-content-between">
                Approved Reports <span class="badge bg-success">{{ reports.approved }}</span>
              </li>
            </ul>
          </div>
        </div>
      </div>

      <!-- IF NO SHOP -->
      <div v-else class="mt-5 text-center">
        <h3 class="text-muted mb-4">You don’t have a shop yet</h3>
        <div class="row justify-content-center">
          <div class="col-12 col-md-4 mb-3">
            <div class="card p-3 h-100 shadow">
              <h5>🛍️ Your Shop</h5>
              <p>No shop yet</p>
              <Link href="/seller/shop/edit" class="btn btn-sm btn-primary">Create/Edit Shop</Link>
            </div>
          </div>
          <div class="col-12 col-md-4 mb-3">
            <div class="card p-3 h-100 shadow">
              <h5>📦 Products</h5>
              <p>Manage your items for sale</p>
              <Link href="/seller/products" class="btn btn-sm btn-secondary">Go to Products</Link>
            </div>
          </div>
          <div class="col-12 col-md-4 mb-3">
            <div class="card p-3 h-100 shadow">
              <h5>🧾 Orders</h5>
              <p>Track and manage customer orders</p>
              <Link href="/seller/orders" class="btn btn-sm btn-success">View Orders</Link>
            </div>
          </div>
        </div>
      </div>
    </div>
  </SellerDashboardLayout>
</template>

<script setup>
import SellerDashboardLayout from '@/Layouts/SellerDashboardLayout.vue'
import { ref } from 'vue'
import { router, usePage, Link } from '@inertiajs/vue3'
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

const props = defineProps({
  shop: Object,
  stats: Object,
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
  plugins: {
    legend: {
      display: true,
      position: 'top',
    },
    title: {
      display: false,
    },
  },
  scales: {
    y: {
      beginAtZero: true
    }
  }
}

const barChartOptions = {
  responsive: true,
  maintainAspectRatio: false,
  plugins: {
    legend: {
      display: false
    },
    title: {
      display: false
    },
  },
  scales: {
    y: {
      beginAtZero: true
    }
  }
}
</script>
