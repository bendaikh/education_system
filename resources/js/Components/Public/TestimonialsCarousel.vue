<script setup>
import { ref, onMounted, onBeforeUnmount } from 'vue';

const props = defineProps({
  testimonials: Array, // [{name, role, date, rating, text, image}]
  interval: { type: Number, default: 7000 },
});

const current = ref(0);
let timer;
const next = () => (current.value = (current.value + 1) % props.testimonials.length);

onMounted(() => (timer = setInterval(next, props.interval)));
onBeforeUnmount(() => clearInterval(timer));
</script>

<template>
  <section class="bg-white py-16">
    <div class="mx-auto max-w-4xl px-4">
      <h2 class="mb-8 text-center text-2xl font-bold text-[#111218]">What People Say</h2>
      <div class="relative">
        <div v-for="(t, idx) in testimonials" :key="t.name" :class="[idx===current?'block':'hidden']">
          <div class="flex flex-col items-center gap-4">
            <img :src="t.image" class="h-20 w-20 rounded-full object-cover" alt="avatar" />
            <p class="text-lg font-medium text-[#111218]">{{ t.name }}</p>
            <p class="text-sm text-[#5f668c]">{{ t.role }} &bull; {{ t.date }}</p>
            <p class="max-w-xl text-center text-base text-[#111218]">{{ t.text }}</p>
          </div>
        </div>
        <!-- dots -->
        <div class="mt-6 flex justify-center gap-2">
          <button
            v-for="(t, idx) in testimonials"
            :key="'tdot'+idx"
            class="h-3 w-3 rounded-full"
            :class="idx===current?'bg-primary-600':'bg-[#d1d5db]'"
            @click="current = idx"
          />
        </div>
      </div>
    </div>
  </section>
</template>

<style scoped></style>
