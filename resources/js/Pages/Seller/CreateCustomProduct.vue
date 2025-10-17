<template>
  <SellerDashboardLayout>
    <div class="container">
      <h2 class="mb-4 text-success"><i class="bi bi-box-seam me-2"></i>Create Custom Product</h2>

      <form @submit.prevent="submit">
        <!-- Basic Info -->
        <input v-model="form.name" class="form-control mb-2" placeholder="Product Name" />
        <input v-model="form.price" type="number" class="form-control mb-2" placeholder="Price" />
        <input v-model="form.stock" type="number" class="form-control mb-2" placeholder="Stock" />
        <textarea v-model="form.description" class="form-control mb-2" placeholder="Description"></textarea>
        <input type="file" @change="e => form.image = e.target.files[0]" class="form-control mb-3" />

        <!-- Customization Options -->
        <h5>Customization Options</h5>

        <div class="form-check" v-for="option in checkboxOptions" :key="option.key">
          <input v-model="form[option.key]" type="checkbox" class="form-check-input" :id="option.key" />
          <label class="form-check-label" :for="option.key">{{ option.label }}</label>
        </div>

        <!-- Nested Material Section -->
        <div v-if="form.allow_material" class="mb-3">
          <h6>Materials</h6>
          <div v-for="(material, mIndex) in form.materials" :key="mIndex" class="mb-3 border p-3 rounded">
            <div class="row mb-2 align-items-center">
              <div class="col-md-4">
                <input v-model="material.name" type="text" class="form-control" placeholder="Material Name (e.g., Bamboo)" />
              </div>
              <div class="col-md-4">
                <input type="file" @change="e => material.image = e.target.files[0]" class="form-control" />
              </div>
              <div class="col-md-2">
                <button type="button" class="btn btn-danger" @click="removeMaterial(mIndex)">Remove</button>
              </div>
            </div>

            <!-- Nested Colors -->
            <div v-if="form.allow_color" class="ms-3 mb-2">
              <h6>Colors</h6>
              <div v-for="(color, cIndex) in material.colors" :key="cIndex" class="row mb-2 align-items-center">
                <div class="col-md-4">
                  <input v-model="color.name" type="text" class="form-control" placeholder="Color Name" />
                </div>
                <div class="col-md-4">
                  <input type="file" @change="e => color.image = e.target.files[0]" class="form-control" />
                </div>
                <div class="col-md-2">
                  <button type="button" class="btn btn-danger" @click="removeColor(mIndex, cIndex)">Remove</button>
                </div>
              </div>
              <button type="button" class="btn btn-primary" @click="addColor(mIndex)">Add Color</button>
            </div>

            <!-- Nested Patterns -->
            <div v-if="form.allow_pattern" class="ms-3 mb-2">
              <h6>Patterns</h6>
              <div v-for="(pattern, pIndex) in material.patterns" :key="pIndex" class="row mb-2 align-items-center">
                <div class="col-md-4">
                  <input v-model="pattern.name" type="text" class="form-control" placeholder="Pattern Name" />
                </div>
                <div class="col-md-4">
                  <input type="file" @change="e => pattern.image = e.target.files[0]" class="form-control" />
                </div>
                <div class="col-md-2">
                  <button type="button" class="btn btn-danger" @click="removePattern(mIndex, pIndex)">Remove</button>
                </div>
              </div>
              <button type="button" class="btn btn-primary" @click="addPattern(mIndex)">Add Pattern</button>
            </div>

            <!-- Nested Sizes -->
            <div v-if="form.allow_size" class="ms-3 mb-2">
              <h6>Sizes</h6>
              <div v-for="(size, sIndex) in material.sizes" :key="sIndex" class="row mb-2 align-items-center">
                <div class="col-md-4">
                  <input v-model="size.name" type="text" class="form-control" placeholder="Size Name" />
                </div>
                <div class="col-md-4">
                  <input type="file" @change="e => size.image = e.target.files[0]" class="form-control" />
                </div>
                <div class="col-md-2">
                  <button type="button" class="btn btn-danger" @click="removeSize(mIndex, sIndex)">Remove</button>
                </div>
              </div>
              <button type="button" class="btn btn-primary" @click="addSize(mIndex)">Add Size</button>
            </div>

          </div>
          <button type="button" class="btn btn-success" @click="addMaterial">Add Material</button>
        </div>

        <button class="btn btn-primary mt-3"><i class="bi bi-plus-circle me-2"></i>Create Custom Product</button>
      </form>
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
  // Convert nested files to FormData automatically
  const fd = new FormData()
  for (const key in form) {
    if (key === 'materials') {
      fd.append(key, JSON.stringify(form[key]))
    } else {
      fd.append(key, form[key])
    }
  }
  // Send via Inertia with forceFormData
  form.post('/seller/custom-products', { forceFormData: true })
}
</script>

<style scoped>
/* input.form-control:focus,
textarea.form-control:focus {
  border-color: #28a745;
  box-shadow: 0 0 0 0.25rem rgba(40, 167, 69, 0.5);
}
.form-check-input:checked {
  background-color: #28a745 !important;
  border-color: #28a745 !important;
} */
input.form-control:focus,
textarea.form-control:focus {
  border-color: #0d6efd; /* Bootstrap primary color */
  box-shadow: 0 0 0 0.25rem rgba(13, 110, 253, 0.5); /* Lighter primary glow */
}

.form-check-input:checked {
  background-color: #0d6efd !important;
  border-color: #0d6efd !important;
}
</style>
