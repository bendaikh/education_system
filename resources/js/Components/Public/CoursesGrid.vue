<script setup>
const props = defineProps({
  courses: Array, // [{title, stream, description, image, link}]
});
</script>

<template>
  <section class="bg-white py-16">
    <div class="mx-auto max-w-6xl px-4">
      <h2 class="mb-8 text-center text-2xl font-bold text-[#111218]">Our Main Courses</h2>
      <div class="grid gap-6 md:grid-cols-3">
        <div v-for="course in courses" :key="course.title" class="rounded-lg border shadow-sm overflow-hidden flex flex-col">
          <img :src="course.image" alt="course" class="aspect-video w-full object-cover" />
          <div class="p-4 flex-1 flex flex-col">
            <span class="mb-1 text-xs uppercase text-primary-600">{{ course.stream }}</span>
            <h3 class="mb-2 text-lg font-semibold text-[#111218]">{{ course.title }}</h3>
            <p class="mb-4 flex-1 text-sm text-[#5f668c]">{{ course.description }}</p>
            <a :href="course.link" class="inline-flex h-9 items-center justify-center rounded bg-primary-600 px-4 text-sm font-bold text-white self-start">Apply Now</a>
          </div>
        </div>
      </div>
    </div>
  </section>
</template>

<style scoped></style>
