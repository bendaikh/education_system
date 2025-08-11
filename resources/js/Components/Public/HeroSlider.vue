<script setup>
import { ref, onMounted, onBeforeUnmount } from 'vue';

const props = defineProps({
  slides: {
    type: Array,
    required: true,
  },
  interval: {
    type: Number,
    default: 5000,
  },
});

const current = ref(0);
let timer;

const next = () => {
  current.value = (current.value + 1) % props.slides.length;
};

onMounted(() => {
  timer = setInterval(next, props.interval);
});

onBeforeUnmount(() => clearInterval(timer));
</script>

<template>
  <div class="relative w-full overflow-hidden">
    <div
      v-for="(slide, idx) in props.slides"
      :key="idx"
      class="absolute inset-0 transition-opacity duration-700"
      :class="{ 'opacity-100 z-10': current === idx, 'opacity-0': current !== idx }"
    >
      <div
        class="flex min-h-[480px] items-center justify-center bg-cover bg-center"
        :style="`background-image: linear-gradient(rgba(0,0,0,0.3),rgba(0,0,0,0.5)), url('${slide.image}')`"
      >
        <div class="px-4 text-center text-white max-w-2xl space-y-4">
          <h1 class="text-3xl md:text-5xl font-black leading-tight">{{ slide.title }}</h1>
          <p class="text-base md:text-lg">{{ slide.subtitle }}</p>
          <a
            v-if="slide.cta && slide.link"
            :href="slide.link"
            class="inline-flex h-12 items-center justify-center rounded bg-primary-600 px-6 text-base font-bold text-white"
          >
            {{ slide.cta }}
          </a>
        </div>
      </div>
    </div>

    <!-- Dots -->
    <div class="absolute bottom-4 left-1/2 -translate-x-1/2 flex gap-2">
      <button
        v-for="(slide, idx) in props.slides"
        :key="'dot'+idx"
        @click="current = idx"
        class="h-3 w-3 rounded-full"
        :class="current === idx ? 'bg-white' : 'bg-white/40'"
      />
    </div>
  </div>
</template>

<style scoped>
</style>
