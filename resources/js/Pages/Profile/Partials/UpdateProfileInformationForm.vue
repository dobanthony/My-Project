<script setup>
import InputError from '@/Components/InputError.vue';
import InputLabel from '@/Components/InputLabel.vue';
import PrimaryButton from '@/Components/PrimaryButton.vue';
import TextInput from '@/Components/TextInput.vue';
import { Link, useForm, usePage } from '@inertiajs/vue3';
import { ref, watch } from 'vue';

defineProps({
    mustVerifyEmail: {
        type: Boolean,
    },
    status: {
        type: String,
    },
});

const user = usePage().props.auth.user;

const form = useForm({
    first_name: user.first_name || '',
    middle_name: user.middle_name || '',
    last_name: user.last_name || '',
    email: user.email || '',
    phone: user.phone || '',
    address: user.address || '',
    dob: user.dob || '',
    social_links: {
        facebook: user.social_links?.facebook || '',
        twitter: user.social_links?.twitter || '',
    },
});

// ✅ Toast state
const showToast = ref(false);
watch(() => form.recentlySuccessful, (val) => {
    if (val) {
        showToast.value = true;
        setTimeout(() => showToast.value = false, 3000);
    }
});
</script>

<template>
    <section>
        <header>
            <h2 class="text-lg font-medium text-gray-900">Profile Information</h2>
            <p class="mt-1 text-sm text-gray-600">
                Update your account's profile information and email address.
            </p>
        </header>

        <form @submit.prevent="form.patch(route('profile.update'))" class="mt-6 space-y-6">
            <!-- First Name -->
            <div>
                <InputLabel for="first_name" value="First Name" />
                <TextInput id="first_name" v-model="form.first_name" type="text" class="mt-1 block w-full" required />
                <InputError class="mt-2" :message="form.errors.first_name" />
            </div>

            <!-- Middle Name -->
            <div>
                <InputLabel for="middle_name" value="Middle Name (Optional)" />
                <TextInput id="middle_name" v-model="form.middle_name" type="text" class="mt-1 block w-full" />
                <InputError class="mt-2" :message="form.errors.middle_name" />
            </div>

            <!-- Last Name -->
            <div>
                <InputLabel for="last_name" value="Last Name" />
                <TextInput id="last_name" v-model="form.last_name" type="text" class="mt-1 block w-full" required />
                <InputError class="mt-2" :message="form.errors.last_name" />
            </div>

            <!-- Email -->
            <div>
                <InputLabel for="email" value="Email" />
                <TextInput id="email" type="email" class="mt-1 block w-full" v-model="form.email" required />
                <InputError class="mt-2" :message="form.errors.email" />
            </div>

            <!-- Phone Number -->
            <div>
                <InputLabel for="phone" value="Phone Number" />
                <TextInput id="phone" type="text" class="mt-1 block w-full" v-model="form.phone" />
                <InputError class="mt-2" :message="form.errors.phone" />
            </div>

            <!-- Address -->
            <div>
                <InputLabel for="address" value="Address" />
                <TextInput id="address" type="text" class="mt-1 block w-full" v-model="form.address" />
                <InputError class="mt-2" :message="form.errors.address" />
            </div>

            <!-- Date of Birth -->
            <div>
                <InputLabel for="dob" value="Date of Birth" />
                <TextInput id="dob" type="date" class="mt-1 block w-full" v-model="form.dob" />
                <InputError class="mt-2" :message="form.errors.dob" />
            </div>

            <!-- Social Links -->
            <div>
                <InputLabel for="facebook" value="Facebook (Optional)" />
                <TextInput id="facebook" type="url" class="mt-1 block w-full" v-model="form.social_links.facebook" placeholder="https://facebook.com/yourprofile" />
                <InputError class="mt-2" :message="form.errors['social_links.facebook']" />
            </div>

            <div>
                <InputLabel for="twitter" value="Twitter (Optional)" />
                <TextInput id="twitter" type="url" class="mt-1 block w-full" v-model="form.social_links.twitter" placeholder="https://twitter.com/yourprofile" />
                <InputError class="mt-2" :message="form.errors['social_links.twitter']" />
            </div>

            <!-- Email verification notice -->
            <div v-if="mustVerifyEmail && user.email_verified_at === null">
                <p class="mt-2 text-sm text-gray-800">
                    Your email address is unverified.
                    <Link
                        :href="route('verification.send')"
                        method="post"
                        as="button"
                        class="rounded-md text-sm text-gray-600 underline hover:text-gray-900 focus:outline-none focus:ring-2 focus:ring-indigo-500 focus:ring-offset-2"
                    >
                        Click here to re-send the verification email.
                    </Link>
                </p>
                <div v-show="status === 'verification-link-sent'" class="mt-2 text-sm font-medium text-green-600">
                    A new verification link has been sent to your email address.
                </div>
            </div>

            <!-- Save Button -->
            <div class="flex items-center gap-4">
                <PrimaryButton :disabled="form.processing">Save</PrimaryButton>

                <Transition
                    enter-active-class="transition ease-in-out"
                    enter-from-class="opacity-0"
                    leave-active-class="transition ease-in-out"
                    leave-to-class="opacity-0"
                >
                    <p v-if="form.recentlySuccessful" class="text-sm text-gray-600">Saved.</p>
                </Transition>
            </div>
        </form>

        <!-- ✅ Toast Modal -->
        <transition
            enter-active-class="transition ease-out duration-300"
            enter-from-class="opacity-0 -translate-y-4"
            leave-active-class="transition ease-in duration-300"
            leave-to-class="opacity-0 -translate-y-4"
        >
            <div
                v-if="showToast"
                class="fixed top-6 left-1/2 transform -translate-x-1/2 z-50 px-4 py-3 bg-green-600 text-white rounded shadow-lg"
            >
                ✅ Profile updated successfully!
            </div>
        </transition>
    </section>
</template>
