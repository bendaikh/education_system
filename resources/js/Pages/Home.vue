<template>
  <div class="min-h-screen bg-gray-900 text-white">
    <Head title="EduConnect - School Management System" />
    
    <!-- Navigation -->
    <nav class="bg-gray-900 border-b border-gray-800 px-6 py-4">
      <div class="max-w-7xl mx-auto flex items-center justify-between">
        <div class="flex items-center gap-2">
          <div class="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center">
            <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24">
              <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
            </svg>
          </div>
          <span class="text-xl font-bold text-white">EduConnect</span>
        </div>
        
        <div class="hidden md:flex items-center gap-8">
          <a href="#" class="text-gray-300 hover:text-white transition-colors">Home</a>
          <a href="#features" class="text-gray-300 hover:text-white transition-colors">Features</a>
          <a href="#pricing" class="text-gray-300 hover:text-white transition-colors">Pricing</a>
          <a href="#contact" class="text-gray-300 hover:text-white transition-colors">Contact</a>
        </div>
        
        <a href="/login" class="bg-blue-600 hover:bg-blue-700 text-white px-6 py-2 rounded-lg transition-colors font-medium">
          Get Started
        </a>
      </div>
    </nav>

    <!-- Hero Section -->
    <section class="relative py-20 px-6 overflow-hidden">
      <div class="max-w-7xl mx-auto relative z-10">
        <div class="grid lg:grid-cols-2 gap-12 items-center">
          <div>
            <h1 class="text-5xl lg:text-6xl font-bold mb-6 leading-tight">
              Empowering Education with 
              <span class="text-blue-400">Seamless Management</span>
            </h1>
            <p class="text-xl text-gray-300 mb-8 leading-relaxed">
              EduConnect is a comprehensive school management system designed to streamline administrative tasks, 
              enhance communication, and foster a collaborative learning environment.
            </p>
            <a href="/login" class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-4 rounded-lg text-lg font-semibold transition-colors shadow-lg inline-block">
              Get Started
            </a>
          </div>
          
          <!-- Hero Image -->
          <div class="relative">
            <div class="bg-gradient-to-br from-blue-500 to-purple-600 rounded-2xl p-8 transform rotate-3 shadow-2xl">
              <div class="bg-white rounded-lg p-6 transform -rotate-3">
                <div class="flex items-center gap-3 mb-4">
                  <div class="w-8 h-8 bg-green-500 rounded-full"></div>
                  <div class="h-4 bg-gray-200 rounded w-32"></div>
                </div>
                <div class="space-y-2 mb-6">
                  <div class="h-3 bg-gray-200 rounded w-full"></div>
                  <div class="h-3 bg-gray-200 rounded w-3/4"></div>
                  <div class="h-3 bg-gray-200 rounded w-1/2"></div>
                </div>
                <div class="grid grid-cols-2 gap-4">
                  <div class="h-16 bg-blue-100 rounded"></div>
                  <div class="h-16 bg-purple-100 rounded"></div>
                </div>
              </div>
            </div>
            <!-- Floating Elements -->
            <div class="absolute -top-4 -right-4 w-20 h-20 bg-yellow-400 rounded-full opacity-80"></div>
            <div class="absolute -bottom-4 -left-4 w-16 h-16 bg-pink-400 rounded-full opacity-60"></div>
          </div>
        </div>
      </div>
      
      <!-- Background Pattern -->
      <div class="absolute inset-0 opacity-10">
        <svg class="w-full h-full" viewBox="0 0 100 100" xmlns="http://www.w3.org/2000/svg">
          <defs>
            <pattern id="grid" width="10" height="10" patternUnits="userSpaceOnUse">
              <path d="M 10 0 L 0 0 0 10" fill="none" stroke="currentColor" stroke-width="0.5"/>
            </pattern>
          </defs>
          <rect width="100" height="100" fill="url(#grid)" />
        </svg>
      </div>
    </section>

    <!-- Explore Section -->
    <section class="py-20 px-6 bg-gray-800">
      <div class="max-w-7xl mx-auto">
        <h2 class="text-3xl font-bold text-center mb-16">Explore EduConnect</h2>
        
        <div class="grid md:grid-cols-3 gap-8">
          <!-- Streamline Administrative Tasks -->
          <div class="bg-gray-900 rounded-xl p-8 hover:bg-gray-700 transition-colors">
            <div class="w-16 h-16 bg-blue-600 rounded-lg mb-6 flex items-center justify-center">
              <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 5H7a2 2 0 00-2 2v10a2 2 0 002 2h8a2 2 0 002-2V7a2 2 0 00-2-2h-2M9 5a2 2 0 002 2h2a2 2 0 002-2M9 5a2 2 0 012-2h2a2 2 0 012 2"/>
              </svg>
            </div>
            <h3 class="text-xl font-semibold mb-3">Streamline Administrative Tasks</h3>
            <p class="text-gray-300">
              Manage student records, scheduling, and administrative operations efficiently.
            </p>
          </div>

          <!-- Enhance Classroom Collaboration -->
          <div class="bg-gray-900 rounded-xl p-8 hover:bg-gray-700 transition-colors">
            <div class="w-16 h-16 bg-green-600 rounded-lg mb-6 flex items-center justify-center">
              <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
              </svg>
            </div>
            <h3 class="text-xl font-semibold mb-3">Enhance Classroom Collaboration</h3>
            <p class="text-gray-300">
              Facilitate interactive learning and communication between students and teachers.
            </p>
          </div>

          <!-- Improve Student Engagement -->
          <div class="bg-gray-900 rounded-xl p-8 hover:bg-gray-700 transition-colors">
            <div class="w-16 h-16 bg-purple-600 rounded-lg mb-6 flex items-center justify-center">
              <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
              </svg>
            </div>
            <h3 class="text-xl font-semibold mb-3">Improve Student Engagement</h3>
            <p class="text-gray-300">
              Track student progress and provide personalized learning experiences.
            </p>
          </div>
        </div>
      </div>
    </section>

    <!-- Video Section -->
    <section class="py-20 px-6">
      <div class="max-w-4xl mx-auto">
        <div class="bg-gradient-to-br from-teal-500 to-cyan-600 rounded-2xl aspect-video flex items-center justify-center relative overflow-hidden">
          <!-- Play Button -->
          <button class="w-20 h-20 bg-white bg-opacity-20 rounded-full flex items-center justify-center hover:bg-opacity-30 transition-all duration-300 backdrop-blur-sm">
            <svg class="w-8 h-8 text-white ml-1" fill="currentColor" viewBox="0 0 24 24">
              <path d="M8 5v14l11-7z"/>
            </svg>
          </button>
          
          <!-- Background Pattern -->
          <div class="absolute inset-0 opacity-10">
            <div class="absolute top-4 left-4 w-32 h-32 border-2 border-white rounded-full"></div>
            <div class="absolute bottom-4 right-4 w-24 h-24 border-2 border-white rounded-full"></div>
            <div class="absolute top-1/2 left-1/2 transform -translate-x-1/2 -translate-y-1/2 w-40 h-40 border-2 border-white rounded-full"></div>
          </div>
        </div>
      </div>
    </section>

    <!-- Key Features -->
    <section id="features" class="py-20 px-6 bg-gray-800">
      <div class="max-w-7xl mx-auto">
        <div class="text-center mb-16">
          <h2 class="text-4xl font-bold mb-4">Key Features</h2>
          <p class="text-xl text-gray-300 max-w-3xl mx-auto">
            EduConnect offers a range of powerful features to simplify school management and improve 
            the educational experience for students, teachers, and administrators.
          </p>
        </div>

        <div class="grid md:grid-cols-2 lg:grid-cols-4 gap-8">
          <!-- User Management -->
          <div class="text-center">
            <div class="w-16 h-16 bg-blue-600 rounded-lg mx-auto mb-4 flex items-center justify-center">
              <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M12 4.354a4 4 0 110 5.292M15 21H3v-1a6 6 0 0112 0v1zm0 0h6v-1a6 6 0 00-9-5.197m13.5-9a2.5 2.5 0 11-5 0 2.5 2.5 0 015 0z"/>
              </svg>
            </div>
            <h3 class="text-lg font-semibold mb-2">User Management</h3>
            <p class="text-gray-400 text-sm">
              Manage students, teachers, and administrative staff with ease. Streamline user information with comprehensive profiles.
            </p>
          </div>

          <!-- Scheduling & Attendance -->
          <div class="text-center">
            <div class="w-16 h-16 bg-green-600 rounded-lg mx-auto mb-4 flex items-center justify-center">
              <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 7V3m8 4V3m-9 8h10M5 21h14a2 2 0 002-2V7a2 2 0 00-2-2H5a2 2 0 00-2 2v12a2 2 0 002 2z"/>
              </svg>
            </div>
            <h3 class="text-lg font-semibold mb-2">Scheduling & Attendance</h3>
            <p class="text-gray-400 text-sm">
              Create and manage class schedules, track attendance, and manage events with an intuitive calendar interface.
            </p>
          </div>

          <!-- Performance Tracking -->
          <div class="text-center">
            <div class="w-16 h-16 bg-purple-600 rounded-lg mx-auto mb-4 flex items-center justify-center">
              <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 19v-6a2 2 0 00-2-2H5a2 2 0 00-2 2v6a2 2 0 002 2h2a2 2 0 002-2zm0 0V9a2 2 0 012-2h2a2 2 0 012 2v10m-6 0a2 2 0 002 2h2a2 2 0 002-2m0 0V5a2 2 0 012-2h2a2 2 0 012 2v14a2 2 0 01-2 2h-2a2 2 0 01-2-2z"/>
              </svg>
            </div>
            <h3 class="text-lg font-semibold mb-2">Performance Tracking</h3>
            <p class="text-gray-400 text-sm">
              Monitor student progress, generate reports, and track academic improvement with comprehensive performance tracking.
            </p>
          </div>

          <!-- Communication Tools -->
          <div class="text-center">
            <div class="w-16 h-16 bg-orange-600 rounded-lg mx-auto mb-4 flex items-center justify-center">
              <svg class="w-8 h-8 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M8 12h.01M12 12h.01M16 12h.01M21 12c0 4.418-4.03 8-9 8a9.863 9.863 0 01-4.255-.949L3 20l1.395-3.72C3.512 15.042 3 13.574 3 12c0-4.418 4.03-8 9-8s9 3.582 9 8z"/>
              </svg>
            </div>
            <h3 class="text-lg font-semibold mb-2">Communication Tools</h3>
            <p class="text-gray-400 text-sm">
              Facilitate seamless communication between teachers, students, and parents through integrated messaging systems.
            </p>
          </div>
        </div>
      </div>
    </section>

    <!-- Benefits -->
    <section class="py-20 px-6">
      <div class="max-w-7xl mx-auto">
        <div class="text-center mb-16">
          <h2 class="text-4xl font-bold mb-4">Benefits of EduConnect</h2>
          <p class="text-xl text-gray-300 max-w-3xl mx-auto">
            Experience the transformative impact of EduConnect on your school's operations and educational outcomes.
          </p>
        </div>

        <div class="grid md:grid-cols-3 gap-8">
          <!-- Enhanced Collaboration -->
          <div class="bg-gray-800 rounded-xl overflow-hidden">
            <div class="h-48 bg-gradient-to-br from-blue-500 to-purple-600 flex items-center justify-center">
              <svg class="w-20 h-20 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M17 20h5v-2a3 3 0 00-5.356-1.857M17 20H7m10 0v-2c0-.656-.126-1.283-.356-1.857M7 20H2v-2a3 3 0 015.356-1.857M7 20v-2c0-.656.126-1.283.356-1.857m0 0a5.002 5.002 0 019.288 0M15 7a3 3 0 11-6 0 3 3 0 016 0zm6 3a2 2 0 11-4 0 2 2 0 014 0zM7 10a2 2 0 11-4 0 2 2 0 014 0z"/>
              </svg>
            </div>
            <div class="p-6">
              <h3 class="text-xl font-semibold mb-3">Enhanced Collaboration</h3>
              <p class="text-gray-300">
                Foster a collaborative environment where teachers, students, and parents can easily connect and share information.
              </p>
            </div>
          </div>

          <!-- Improved Academic Tasks -->
          <div class="bg-gray-800 rounded-xl overflow-hidden">
            <div class="h-48 bg-gradient-to-br from-green-500 to-teal-600 flex items-center justify-center">
              <svg class="w-20 h-20 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M9 12l2 2 4-4m6 2a9 9 0 11-18 0 9 9 0 0118 0z"/>
              </svg>
            </div>
            <div class="p-6">
              <h3 class="text-xl font-semibold mb-3">Improved Academic Tasks</h3>
              <p class="text-gray-300">
                Streamline academic tasks, reduce paperwork, and free up valuable time for teachers to focus on teaching.
              </p>
            </div>
          </div>

          <!-- Better Student Outcomes -->
          <div class="bg-gray-800 rounded-xl overflow-hidden">
            <div class="h-48 bg-gradient-to-br from-orange-500 to-red-600 flex items-center justify-center">
              <svg class="w-20 h-20 text-white" fill="none" stroke="currentColor" viewBox="0 0 24 24">
                <path stroke-linecap="round" stroke-linejoin="round" stroke-width="2" d="M13 10V3L4 14h7v7l9-11h-7z"/>
              </svg>
            </div>
            <div class="p-6">
              <h3 class="text-xl font-semibold mb-3">Better Student Outcomes</h3>
              <p class="text-gray-300">
                Empower students to achieve their full potential with personalized learning experiences and better feedback.
              </p>
            </div>
          </div>
        </div>
      </div>
    </section>

    <!-- Testimonials -->
    <section class="py-20 px-6 bg-gray-800">
      <div class="max-w-7xl mx-auto">
        <h2 class="text-4xl font-bold text-center mb-16">Testimonials</h2>
        
        <div class="grid md:grid-cols-3 gap-8">
          <!-- Testimonial 1 -->
          <div class="bg-gray-700 rounded-xl p-6">
            <div class="flex items-center mb-4">
              <div class="w-12 h-12 bg-blue-500 rounded-full flex items-center justify-center text-white font-semibold">
                S
              </div>
              <div class="ml-4">
                <h4 class="font-semibold">Sarah Johnson</h4>
                <p class="text-gray-400 text-sm">Principal</p>
              </div>
            </div>
            <p class="text-gray-300">
              "EduConnect has revolutionized our school's operations. It's incredibly user-friendly and has significantly improved communication between staff and students."
            </p>
          </div>

          <!-- Testimonial 2 -->
          <div class="bg-gray-700 rounded-xl p-6">
            <div class="flex items-center mb-4">
              <div class="w-12 h-12 bg-green-500 rounded-full flex items-center justify-center text-white font-semibold">
                M
              </div>
              <div class="ml-4">
                <h4 class="font-semibold">Mark Thompson</h4>
                <p class="text-gray-400 text-sm">Student</p>
              </div>
            </div>
            <p class="text-gray-300">
              "As a student, I find EduConnect incredibly helpful for managing my assignments and tracking my progress. It's made learning much more efficient."
            </p>
          </div>

          <!-- Testimonial 3 -->
          <div class="bg-gray-700 rounded-xl p-6">
            <div class="flex items-center mb-4">
              <div class="w-12 h-12 bg-purple-500 rounded-full flex items-center justify-center text-white font-semibold">
                E
              </div>
              <div class="ml-4">
                <h4 class="font-semibold">Emily Davis</h4>
                <p class="text-gray-400 text-sm">School Administrator</p>
              </div>
            </div>
            <p class="text-gray-300">
              "EduConnect has been a game-changer for our school. It has streamlined our operations and improved student engagement significantly."
            </p>
          </div>
        </div>
      </div>
    </section>

    <!-- CTA Section -->
    <section class="py-20 px-6">
      <div class="max-w-4xl mx-auto text-center">
        <h2 class="text-4xl font-bold mb-6">Ready to Transform Your School's Management?</h2>
        <p class="text-xl text-gray-300 mb-8">
          Join the growing community of schools that are leveraging EduConnect to enhance their educational ecosystem.
        </p>
        <a href="/login" class="bg-blue-600 hover:bg-blue-700 text-white px-8 py-4 rounded-lg text-lg font-semibold transition-colors shadow-lg inline-block">
          Get Started
        </a>
      </div>
    </section>

    <!-- Footer -->
    <footer class="bg-gray-800 border-t border-gray-700 py-12 px-6">
      <div class="max-w-7xl mx-auto">
        <div class="grid md:grid-cols-4 gap-8">
          <div>
            <div class="flex items-center gap-2 mb-4">
              <div class="w-8 h-8 bg-blue-600 rounded-lg flex items-center justify-center">
                <svg class="w-5 h-5 text-white" fill="currentColor" viewBox="0 0 24 24">
                  <path d="M12 2l3.09 6.26L22 9.27l-5 4.87 1.18 6.88L12 17.77l-6.18 3.25L7 14.14 2 9.27l6.91-1.01L12 2z"/>
                </svg>
              </div>
              <span class="text-xl font-bold text-white">EduConnect</span>
            </div>
            <p class="text-gray-400">
              Empowering education through seamless management solutions.
            </p>
          </div>
          
          <div>
            <h3 class="font-semibold mb-4">Features</h3>
            <ul class="space-y-2 text-gray-400">
              <li><a href="#" class="hover:text-white transition-colors">User Management</a></li>
              <li><a href="#" class="hover:text-white transition-colors">Scheduling</a></li>
              <li><a href="#" class="hover:text-white transition-colors">Performance Tracking</a></li>
              <li><a href="#" class="hover:text-white transition-colors">Communication</a></li>
            </ul>
          </div>
          
          <div>
            <h3 class="font-semibold mb-4">Pricing</h3>
            <ul class="space-y-2 text-gray-400">
              <li><a href="#" class="hover:text-white transition-colors">Basic Plan</a></li>
              <li><a href="#" class="hover:text-white transition-colors">Pro Plan</a></li>
              <li><a href="#" class="hover:text-white transition-colors">Enterprise</a></li>
            </ul>
          </div>
          
          <div>
            <h3 class="font-semibold mb-4">Contact</h3>
            <ul class="space-y-2 text-gray-400">
              <li><a href="#" class="hover:text-white transition-colors">Support</a></li>
              <li><a href="#" class="hover:text-white transition-colors">Documentation</a></li>
              <li><a href="#" class="hover:text-white transition-colors">Contact Us</a></li>
            </ul>
          </div>
        </div>
        
        <div class="border-t border-gray-700 mt-8 pt-8 text-center text-gray-400">
          <p>&copy; 2023 EduConnect. All rights reserved.</p>
        </div>
      </div>
    </footer>
  </div>
</template>

<script setup>
import { Head } from '@inertiajs/vue3';
</script>

<style scoped>
/* Additional styles if needed */
</style>
