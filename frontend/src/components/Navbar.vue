<template >
        <div class="flex h-screen">
      <!-- Sidebar -->
      <aside class="w-64 bg-gray-800 text-white flex flex-col p-4">
        <div class="text-xl font-bold mb-6">TDC Admin</div>
        <nav class="space-y-2">
            <p class="text-gray-500">Menu</p>
            <router-link to="/dashboard" class="block px-3 py-2 rounded hover:bg-gray-700">🏠 Dashboard</router-link>
            <p class="text-gray-500">App</p>
          <router-link to="/dashboard/users" class="block px-3 py-2 rounded hover:bg-gray-700">🙍‍♂️ Users</router-link>
          <router-link to="/dashboard/listings" class="block px-3 py-2 rounded hover:bg-gray-700">📖 Listings</router-link>
          <router-link to="/dashboard/pending" class="block px-3 py-2 rounded hover:bg-gray-700">🕔🗓 Pending</router-link>
          <router-link to="/dashboard/pending" class="block px-3 py-2 rounded hover:bg-gray-700">🕔🗓 Dashboard</router-link>
        </nav>
      </aside>


      <!-- Main area -->
      <div class="flex-1 flex flex-col bg-gray-100">
        <!-- Top Header -->
        <header class="flex items-center justify-between p-4 bg-white shadow">
          <div class="text-lg font-semibold">Search Analytics Dashboard</div>
          <div class="flex items-center gap-4">
            <input type="text" placeholder="Search..." class="border rounded px-3 py-1" />
            <button class="p-2">⚙️</button>
          </div>
        </header>


        <!-- Content -->
        <main class="p-6 overflow-y-auto">
          <router-view />
        </main>
      </div>
    </div>
</template>

<script>

</script>