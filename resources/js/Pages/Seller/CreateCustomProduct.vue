<template>
  <SellerDashboardLayout>
    <div class="container py-4">

      <!-- 🧩 Page Header -->
      <div class="text-center text-md-start mb-4 pb-3 border-bottom">
        <h2 class="fw-bold text-primary mb-2">
          <i class="bi bi-box-seam me-2"></i> Create Custom Product
        </h2>
        <p class="text-muted mb-0">
          Customize your products with different materials, colors, patterns, and sizes.
        </p>
      </div>

      <!-- 🛠️ Form Card -->
      <div class="card shadow-sm border-0">
        <div class="card-body">
          <form @submit.prevent="submit">

            <!-- 🧾 Basic Information -->
            <div class="mb-4">
              <h5 class="fw-bold text-secondary mb-3">
                <i class="bi bi-info-circle-fill me-2 text-primary"></i>Basic Information
              </h5>
              <div class="row g-3">
                <div class="col-md-4">
                  <input v-model="form.name" class="form-control" placeholder="Product Name" />
                </div>
                <div class="col-md-4">
                  <input v-model="form.price" type="number" class="form-control" placeholder="Price" />
                </div>
                <div class="col-md-4">
                  <input v-model="form.stock" type="number" class="form-control" placeholder="Stock" />
                </div>
              </div>
              <textarea v-model="form.description" class="form-control mt-3" rows="3" placeholder="Product Description"></textarea>
              <div class="mt-3">
                <input type="file" @change="e => form.image = e.target.files[0]" class="form-control" />
              </div>
            </div>

            <!-- ⚙️ Customization Options -->
            <div class="mb-4">
              <h5 class="fw-bold text-secondary mb-3">
                <i class="bi bi-sliders me-2 text-success"></i>Customization Options
              </h5>

              <div class="row g-2">
                <div v-for="option in checkboxOptions" :key="option.key" class="col-md-3">
                  <div class="form-check">
                    <input v-model="form[option.key]" type="checkbox" class="form-check-input" :id="option.key" />
                    <label class="form-check-label" :for="option.key">
                      <i class="bi bi-check2-square text-primary me-1"></i>{{ option.label }}
                    </label>
                  </div>
                </div>
              </div>
            </div>

            <!-- 🧱 Materials Section -->
            <div v-if="form.allow_material" class="mb-4">
              <div class="d-flex justify-content-between align-items-center mb-3">
                <h5 class="fw-bold text-secondary">
                  <i class="bi bi-layers-fill text-warning me-2"></i>Materials
                </h5>
                <button type="button" class="btn btn-success btn-sm shadow-sm" @click="addMaterial">
                  <i class="bi bi-plus-circle me-1"></i>Add Material
                </button>
              </div>

              <div v-for="(material, mIndex) in form.materials" :key="mIndex" class="card mb-3 border-0 shadow-sm">
                <div class="card-body">
                  <div class="row g-3 mb-2 align-items-center">
                    <div class="col-md-4">
                      <input v-model="material.name" type="text" class="form-control" placeholder="Material Name (e.g., Bamboo)" />
                    </div>
                    <div class="col-md-4">
                      <input type="file" @change="e => material.image = e.target.files[0]" class="form-control" />
                    </div>
                    <div class="col-md-3 text-end">
                      <button type="button" class="btn btn-outline-danger btn-sm" @click="removeMaterial(mIndex)">
                        <i class="bi bi-trash me-1"></i>Remove
                      </button>
                    </div>
                  </div>

                  <!-- 🎨 Colors -->
                  <div v-if="form.allow_color" class="ms-3 mb-3">
                    <h6 class="fw-bold text-secondary"><i class="bi bi-palette2 me-1 text-info"></i>Colors</h6>
                    <div v-for="(color, cIndex) in material.colors" :key="cIndex" class="row mb-2 align-items-center">
                      <div class="col-md-4">
                        <input v-model="color.name" type="text" class="form-control" placeholder="Color Name" />
                      </div>
                      <div class="col-md-4">
                        <input type="file" @change="e => color.image = e.target.files[0]" class="form-control" />
                      </div>
                      <div class="col-md-3 text-end">
                        <button type="button" class="btn btn-outline-danger btn-sm" @click="removeColor(mIndex, cIndex)">
                          <i class="bi bi-x-circle"></i>
                        </button>
                      </div>
                    </div>
                    <button type="button" class="btn btn-outline-primary btn-sm" @click="addColor(mIndex)">
                      <i class="bi bi-plus-circle me-1"></i>Add Color
                    </button>
                  </div>

                  <!-- 🌀 Patterns -->
                  <div v-if="form.allow_pattern" class="ms-3 mb-3">
                    <h6 class="fw-bold text-secondary"><i class="bi bi-grid-1x2-fill me-1 text-warning"></i>Patterns</h6>
                    <div v-for="(pattern, pIndex) in material.patterns" :key="pIndex" class="row mb-2 align-items-center">
                      <div class="col-md-4">
                        <input v-model="pattern.name" type="text" class="form-control" placeholder="Pattern Name" />
                      </div>
                      <div class="col-md-4">
                        <input type="file" @change="e => pattern.image = e.target.files[0]" class="form-control" />
                      </div>
                      <div class="col-md-3 text-end">
                        <button type="button" class="btn btn-outline-danger btn-sm" @click="removePattern(mIndex, pIndex)">
                          <i class="bi bi-x-circle"></i>
                        </button>
                      </div>
                    </div>
                    <button type="button" class="btn btn-outline-primary btn-sm" @click="addPattern(mIndex)">
                      <i class="bi bi-plus-circle me-1"></i>Add Pattern
                    </button>
                  </div>

                  <!-- 📏 Sizes -->
                  <div v-if="form.allow_size" class="ms-3 mb-2">
                    <h6 class="fw-bold text-secondary"><i class="bi bi-aspect-ratio-fill me-1 text-success"></i>Sizes</h6>
                    <div v-for="(size, sIndex) in material.sizes" :key="sIndex" class="row mb-2 align-items-center">
                      <div class="col-md-4">
                        <input v-model="size.name" type="text" class="form-control" placeholder="Size Name" />
                      </div>
                      <div class="col-md-4">
                        <input type="file" @change="e => size.image = e.target.files[0]" class="form-control" />
                      </div>
                      <div class="col-md-3 text-end">
                        <button type="button" class="btn btn-outline-danger btn-sm" @click="removeSize(mIndex, sIndex)">
                          <i class="bi bi-x-circle"></i>
                        </button>
                      </div>
                    </div>
                    <button type="button" class="btn btn-outline-primary btn-sm" @click="addSize(mIndex)">
                      <i class="bi bi-plus-circle me-1"></i>Add Size
                    </button>
                  </div>
                </div>
              </div>
            </div>

            <!-- 🧾 Submit -->
            <div class="text-end">
              <button class="btn btn-primary px-4 py-2 shadow-sm">
                <i class="bi bi-check-circle me-2"></i>Create Custom Product
              </button>
            </div>

          </form>
        </div>
      </div>
    </div>
  </SellerDashboardLayout>
</template>

<script setup>
import { useForm } from '@inertiajs/vue3'
import SellerDashboardLayout from '@/Layouts/SellerDashboardLayout.vue'

const checkboxOptions = [
  { key: 'allow_material', label: 'Allow Material' },
  { key: 'allow_color', label: 'Allow Color' },
  { key: 'allow_pattern', label: 'Allow Pattern' },
  { key: 'allow_size', label: 'Allow Size' },
]

const form = useForm({
  name: '',
  price: '',
  stock: '',
  description: '',
  image: null,
  allow_material: false,
  allow_color: false,
  allow_pattern: false,
  allow_size: false,
  materials: [],
})

// Material Functions
const addMaterial = () => form.materials.push({ name: '', image: null, colors: [], patterns: [], sizes: [] })
const removeMaterial = (index) => form.materials.splice(index, 1)

// Color Functions
const addColor = (mIndex) => form.materials[mIndex].colors.push({ name: '', image: null })
const removeColor = (mIndex, cIndex) => form.materials[mIndex].colors.splice(cIndex, 1)

// Pattern Functions
const addPattern = (mIndex) => form.materials[mIndex].patterns.push({ name: '', image: null })
const removePattern = (mIndex, pIndex) => form.materials[mIndex].patterns.splice(pIndex, 1)

// Size Functions
const addSize = (mIndex) => form.materials[mIndex].sizes.push({ name: '', image: null })
const removeSize = (mIndex, sIndex) => form.materials[mIndex].sizes.splice(sIndex, 1)

// Submit
const submit = () => {
  const fd = new FormData()
  for (const key in form) {
    if (key === 'materials') {
      fd.append(key, JSON.stringify(form[key]))
    } else {
      fd.append(key, form[key])
    }
  }
  form.post('/seller/custom-products', { forceFormData: true })
}
</script>

<style scoped>
input.form-control:focus,
textarea.form-control:focus {
  border-color: #0d6efd;
  box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.25);
}
.card {
  border-radius: 0.75rem;
}
</style>
