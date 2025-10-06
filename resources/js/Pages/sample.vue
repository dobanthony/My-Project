<template>
  <AdminLayout>
    <div class="container py-4">
      <h3 class="fw-bold mb-4">Customization Image Uploads</h3>

      <div v-for="material in materials" :key="material.name" class="mb-5 border rounded p-3">

        <h4 class="fw-bold text-success mb-3">Bamboo </h4>
        <div class="mb-3">
          <img
            v-if="getImage('Material', material.name, material.name)"
            :src="`/storage/${getImage('Material', material.name, material.name)}`"
            class="img-thumbnail mb-2" style="max-height:150px;"
          />
          <form @submit.prevent="upload('Material', material.name, material.name)">
            <input type="file" class="form-control mb-2" @change="onFileChange($event, material.name + '_material')">
            <button class="btn btn-success btn-sm">Upload Material Image</button>
          </form>
        </div>

        <!-- Colors -->
        <h5 class="fw-semibold mt-3">Colors</h5>
        <div class="row">
          <div v-for="color in colors" :key="color" class="col-md-3 mb-3">
            <strong>{{ color }}</strong>
            <div class="my-2">
              <img
                v-if="getImage('Color', material.name, color)"
                :src="`/storage/${getImage('Color', material.name, color)}`"
                class="img-fluid rounded"
              />
            </div>
            <form @submit.prevent="upload('Color', material.name, color)">
              <input type="file" class="form-control mb-2" @change="onFileChange($event, material.name + '_color_' + color)">
              <button class="btn btn-primary btn-sm w-100">Upload</button>
            </form>
          </div>
        </div>

        <!-- Patterns -->
        <h5 class="fw-semibold mt-3">Patterns</h5>
        <div class="row">
          <div v-for="pattern in patterns" :key="pattern" class="col-md-3 mb-3">
            <strong>{{ pattern }}</strong>
            <div class="my-2">
              <img
                v-if="getImage('Pattern', material.name, pattern)"
                :src="`/storage/${getImage('Pattern', material.name, pattern)}`"
                class="img-fluid rounded"
              />
            </div>
            <form @submit.prevent="upload('Pattern', material.name, pattern)">
              <input type="file" class="form-control mb-2" @change="onFileChange($event, material.name + '_pattern_' + pattern)">
              <button class="btn btn-primary btn-sm w-100">Upload</button>
            </form>
          </div>
        </div>

        <!-- Sizes -->
        <h5 class="fw-semibold mt-3">Sizes</h5>
        <div class="row">
          <div v-for="size in sizes" :key="size" class="col-md-3 mb-3">
            <strong>{{ size }}</strong>
            <div class="my-2">
              <img
                v-if="getImage('Size', material.name, size)"
                :src="`/storage/${getImage('Size', material.name, size)}`"
                class="img-fluid rounded"
              />
            </div>
            <form @submit.prevent="upload('Size', material.name, size)">
              <input type="file" class="form-control mb-2" @change="onFileChange($event, material.name + '_size_' + size)">
              <button class="btn btn-primary btn-sm w-100">Upload</button>
            </form>
          </div>

          <h4 class="fw-bold text-success mb-3">Bamboo </h4>
        <div class="mb-3">
          <img
            v-if="getImage('Material', material.name, material.name)"
            :src="`/storage/${getImage('Material', material.name, material.name)}`"
            class="img-thumbnail mb-2" style="max-height:150px;"
          />
          <form @submit.prevent="upload('Material', material.name, material.name)">
            <input type="file" class="form-control mb-2" @change="onFileChange($event, material.name + '_material')">
            <button class="btn btn-success btn-sm">Upload Material Image</button>
          </form>
        </div>

        <!-- Colors -->
        <h5 class="fw-semibold mt-3">Colors</h5>
        <div class="row">
          <div v-for="color in colors" :key="color" class="col-md-3 mb-3">
            <strong>{{ color }}</strong>
            <div class="my-2">
              <img
                v-if="getImage('Color', material.name, color)"
                :src="`/storage/${getImage('Color', material.name, color)}`"
                class="img-fluid rounded"
              />
            </div>
            <form @submit.prevent="upload('Color', material.name, color)">
              <input type="file" class="form-control mb-2" @change="onFileChange($event, material.name + '_color_' + color)">
              <button class="btn btn-primary btn-sm w-100">Upload</button>
            </form>
          </div>
        </div>

        <!-- Patterns -->
        <h5 class="fw-semibold mt-3">Patterns</h5>
        <div class="row">
          <div v-for="pattern in patterns" :key="pattern" class="col-md-3 mb-3">
            <strong>{{ pattern }}</strong>
            <div class="my-2">
              <img
                v-if="getImage('Pattern', material.name, pattern)"
                :src="`/storage/${getImage('Pattern', material.name, pattern)}`"
                class="img-fluid rounded"
              />
            </div>
            <form @submit.prevent="upload('Pattern', material.name, pattern)">
              <input type="file" class="form-control mb-2" @change="onFileChange($event, material.name + '_pattern_' + pattern)">
              <button class="btn btn-primary btn-sm w-100">Upload</button>
            </form>
          </div>
        </div>

        <!-- Sizes -->
        <h5 class="fw-semibold mt-3">Sizes</h5>
        <div class="row">
          <div v-for="size in sizes" :key="size" class="col-md-3 mb-3">
            <strong>{{ size }}</strong>
            <div class="my-2">
              <img
                v-if="getImage('Size', material.name, size)"
                :src="`/storage/${getImage('Size', material.name, size)}`"
                class="img-fluid rounded"
              />
            </div>
            <form @submit.prevent="upload('Size', material.name, size)">
              <input type="file" class="form-control mb-2" @change="onFileChange($event, material.name + '_size_' + size)">
              <button class="btn btn-primary btn-sm w-100">Upload</button>
            </form>
          </div>

          <h4 class="fw-bold text-success mb-3">Bamboo </h4>
        <div class="mb-3">
          <img
            v-if="getImage('Material', material.name, material.name)"
            :src="`/storage/${getImage('Material', material.name, material.name)}`"
            class="img-thumbnail mb-2" style="max-height:150px;"
          />
          <form @submit.prevent="upload('Material', material.name, material.name)">
            <input type="file" class="form-control mb-2" @change="onFileChange($event, material.name + '_material')">
            <button class="btn btn-success btn-sm">Upload Material Image</button>
          </form>
        </div>

        <!-- Colors -->
        <h5 class="fw-semibold mt-3">Colors</h5>
        <div class="row">
          <div v-for="color in colors" :key="color" class="col-md-3 mb-3">
            <strong>{{ color }}</strong>
            <div class="my-2">
              <img
                v-if="getImage('Color', material.name, color)"
                :src="`/storage/${getImage('Color', material.name, color)}`"
                class="img-fluid rounded"
              />
            </div>
            <form @submit.prevent="upload('Color', material.name, color)">
              <input type="file" class="form-control mb-2" @change="onFileChange($event, material.name + '_color_' + color)">
              <button class="btn btn-primary btn-sm w-100">Upload</button>
            </form>
          </div>
        </div>

        <!-- Patterns -->
        <h5 class="fw-semibold mt-3">Patterns</h5>
        <div class="row">
          <div v-for="pattern in patterns" :key="pattern" class="col-md-3 mb-3">
            <strong>{{ pattern }}</strong>
            <div class="my-2">
              <img
                v-if="getImage('Pattern', material.name, pattern)"
                :src="`/storage/${getImage('Pattern', material.name, pattern)}`"
                class="img-fluid rounded"
              />
            </div>
            <form @submit.prevent="upload('Pattern', material.name, pattern)">
              <input type="file" class="form-control mb-2" @change="onFileChange($event, material.name + '_pattern_' + pattern)">
              <button class="btn btn-primary btn-sm w-100">Upload</button>
            </form>
          </div>
        </div>

        <!-- Sizes -->
        <h5 class="fw-semibold mt-3">Sizes</h5>
        <div class="row">
          <div v-for="size in sizes" :key="size" class="col-md-3 mb-3">
            <strong>{{ size }}</strong>
            <div class="my-2">
              <img
                v-if="getImage('Size', material.name, size)"
                :src="`/storage/${getImage('Size', material.name, size)}`"
                class="img-fluid rounded"
              />
            </div>
            <form @submit.prevent="upload('Size', material.name, size)">
              <input type="file" class="form-control mb-2" @change="onFileChange($event, material.name + '_size_' + size)">
              <button class="btn btn-primary btn-sm w-100">Upload</button>
            </form>
          </div>

          <h4 class="fw-bold text-success mb-3">Bamboo </h4>
        <div class="mb-3">
          <img
            v-if="getImage('Material', material.name, material.name)"
            :src="`/storage/${getImage('Material', material.name, material.name)}`"
            class="img-thumbnail mb-2" style="max-height:150px;"
          />
          <form @submit.prevent="upload('Material', material.name, material.name)">
            <input type="file" class="form-control mb-2" @change="onFileChange($event, material.name + '_material')">
            <button class="btn btn-success btn-sm">Upload Material Image</button>
          </form>
        </div>

        <!-- Colors -->
        <h5 class="fw-semibold mt-3">Colors</h5>
        <div class="row">
          <div v-for="color in colors" :key="color" class="col-md-3 mb-3">
            <strong>{{ color }}</strong>
            <div class="my-2">
              <img
                v-if="getImage('Color', material.name, color)"
                :src="`/storage/${getImage('Color', material.name, color)}`"
                class="img-fluid rounded"
              />
            </div>
            <form @submit.prevent="upload('Color', material.name, color)">
              <input type="file" class="form-control mb-2" @change="onFileChange($event, material.name + '_color_' + color)">
              <button class="btn btn-primary btn-sm w-100">Upload</button>
            </form>
          </div>
        </div>

        <!-- Patterns -->
        <h5 class="fw-semibold mt-3">Patterns</h5>
        <div class="row">
          <div v-for="pattern in patterns" :key="pattern" class="col-md-3 mb-3">
            <strong>{{ pattern }}</strong>
            <div class="my-2">
              <img
                v-if="getImage('Pattern', material.name, pattern)"
                :src="`/storage/${getImage('Pattern', material.name, pattern)}`"
                class="img-fluid rounded"
              />
            </div>
            <form @submit.prevent="upload('Pattern', material.name, pattern)">
              <input type="file" class="form-control mb-2" @change="onFileChange($event, material.name + '_pattern_' + pattern)">
              <button class="btn btn-primary btn-sm w-100">Upload</button>
            </form>
          </div>
        </div>

        <!-- Sizes -->
        <h5 class="fw-semibold mt-3">Sizes</h5>
        <div class="row">
          <div v-for="size in sizes" :key="size" class="col-md-3 mb-3">
            <strong>{{ size }}</strong>
            <div class="my-2">
              <img
                v-if="getImage('Size', material.name, size)"
                :src="`/storage/${getImage('Size', material.name, size)}`"
                class="img-fluid rounded"
              />
            </div>
            <form @submit.prevent="upload('Size', material.name, size)">
              <input type="file" class="form-control mb-2" @change="onFileChange($event, material.name + '_size_' + size)">
              <button class="btn btn-primary btn-sm w-100">Upload</button>
            </form>
          </div>
        </div>
      </div>
    </div>
  </AdminLayout>
</template>

