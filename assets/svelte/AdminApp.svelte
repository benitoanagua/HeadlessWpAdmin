<script>
  import { onMount } from "svelte";
  import HeadlessConfig from "./HeadlessConfig.svelte";

  let currentView = "dashboard";
  let loading = true;
  let stats = {
    totalPosts: 0,
    totalPages: 0,
    totalUsers: 0,
  };

  onMount(async () => {
    await loadStats();
    loading = false;

    // Verificar si estamos en la página de configuración headless
    const urlParams = new URLSearchParams(window.location.search);
    if (urlParams.get("page") === "headless-mode") {
      currentView = "headless-config";
    }
  });

  async function loadStats() {
    try {
      // Cargar estadísticas del sitio
      const postsResponse = await fetch("/wp-json/wp/v2/posts?per_page=1");
      const pagesResponse = await fetch("/wp-json/wp/v2/pages?per_page=1");
      const usersResponse = await fetch("/wp-json/wp/v2/users?per_page=1");

      if (postsResponse.ok && pagesResponse.ok && usersResponse.ok) {
        const postsData = await postsResponse.json();
        const pagesData = await pagesResponse.json();
        const usersData = await usersResponse.json();

        stats.totalPosts =
          parseInt(postsResponse.headers.get("X-WP-Total")) || 0;
        stats.totalPages =
          parseInt(pagesResponse.headers.get("X-WP-Total")) || 0;
        stats.totalUsers =
          parseInt(usersResponse.headers.get("X-WP-Total")) || 0;
      }
    } catch (error) {
      console.error("Error loading stats:", error);
    }
  }

  function navigateTo(view) {
    currentView = view;
    if (view === "headless-config") {
      window.history.pushState({}, "", "?page=headless-mode");
    } else {
      window.history.pushState({}, "", "?page=headless-wp-admin");
    }
  }
</script>

<div class="admin-container">
  <header class="admin-header">
    <h1 class="admin-title">Panel de Administración Headless</h1>
    <nav class="admin-nav">
      <button
        class:active={currentView === "dashboard"}
        on:click={() => navigateTo("dashboard")}
      >
        📊 Dashboard
      </button>
      <button
        class:active={currentView === "headless-config"}
        on:click={() => navigateTo("headless-config")}
      >
        ⚙️ Configuración Headless
      </button>
    </nav>
  </header>

  {#if loading}
    <div class="flex items-center justify-center py-8">
      <div
        class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-500"
      ></div>
      <span class="ml-2 text-gray-600">Cargando...</span>
    </div>
  {:else if currentView === "dashboard"}
    <main>
      <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
        <p class="text-blue-800">
          Bienvenido al panel de administración Headless WordPress. Desde aquí
          puedes gestionar toda la configuración del modo headless.
        </p>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6 mb-8">
        <div class="bg-white p-6 rounded-lg shadow-sm border">
          <h3 class="text-lg font-medium text-gray-900 mb-2">Contenido</h3>
          <p class="text-3xl font-bold text-blue-600">{stats.totalPosts}</p>
          <p class="text-sm text-gray-500">Total de entradas</p>
        </div>

        <div class="bg-white p-6 rounded-lg shadow-sm border">
          <h3 class="text-lg font-medium text-gray-900 mb-2">Páginas</h3>
          <p class="text-3xl font-bold text-green-600">{stats.totalPages}</p>
          <p class="text-sm text-gray-500">Total de páginas</p>
        </div>

        <div class="bg-white p-6 rounded-lg shadow-sm border">
          <h3 class="text-lg font-medium text-gray-900 mb-2">Usuarios</h3>
          <p class="text-3xl font-bold text-purple-600">{stats.totalUsers}</p>
          <p class="text-sm text-gray-500">Usuarios registrados</p>
        </div>
      </div>

      <div class="grid grid-cols-1 md:grid-cols-2 gap-6">
        <div class="bg-white p-6 rounded-lg shadow-sm border">
          <h3 class="text-lg font-medium text-gray-900 mb-4">
            Estado Headless
          </h3>
          <div class="space-y-3">
            <div class="flex items-center justify-between">
              <span>Modo Headless</span>
              <span
                class="px-2 py-1 bg-green-100 text-green-800 text-sm rounded-full"
                >Activo</span
              >
            </div>
            <div class="flex items-center justify-between">
              <span>GraphQL API</span>
              <span
                class="px-2 py-1 bg-green-100 text-green-800 text-sm rounded-full"
                >Habilitado</span
              >
            </div>
            <div class="flex items-center justify-between">
              <span>REST API</span>
              <span
                class="px-2 py-1 bg-red-100 text-red-800 text-sm rounded-full"
                >Deshabilitado</span
              >
            </div>
            <div class="flex items-center justify-between">
              <span>Frontend Público</span>
              <span
                class="px-2 py-1 bg-red-100 text-red-800 text-sm rounded-full"
                >Bloqueado</span
              >
            </div>
          </div>
        </div>

        <div class="bg-white p-6 rounded-lg shadow-sm border">
          <h3 class="text-lg font-medium text-gray-900 mb-4">
            Acciones Rápidas
          </h3>
          <div class="space-y-3">
            <button
              on:click={() => navigateTo("headless-config")}
              class="w-full text-left px-4 py-3 bg-blue-50 hover:bg-blue-100 text-blue-700 rounded-lg transition-colors"
            >
              <div class="font-medium">Configurar Headless</div>
              <div class="text-sm">Ajustes completos del modo headless</div>
            </button>
            <button
              class="w-full text-left px-4 py-3 bg-gray-50 hover:bg-gray-100 text-gray-700 rounded-lg transition-colors"
            >
              <div class="font-medium">Ver GraphQL</div>
              <div class="text-sm">Abrir interfaz GraphQL</div>
            </button>
            <button
              class="w-full text-left px-4 py-3 bg-gray-50 hover:bg-gray-100 text-gray-700 rounded-lg transition-colors"
            >
              <div class="font-medium">Ver REST API</div>
              <div class="text-sm">Explorar endpoints REST</div>
            </button>
          </div>
        </div>
      </div>
    </main>
  {:else if currentView === "headless-config"}
    <HeadlessConfig />
  {/if}
</div>

<style>
  .admin-container {
    --apply: p-6 bg-surface rounded-xl shadow-sm;
  }

  .admin-header {
    --apply: mb-6 pb-4 border-b border-outlineVariant;
  }

  .admin-title {
    --apply: text-2xl font-semibold text-onSurface mb-4;
  }

  .admin-nav {
    --apply: flex space-x-2;
  }

  .admin-nav button {
    --apply: px-4 py-2 rounded-full text-sm font-medium transition-colors
      state-layer;
  }

  .admin-nav button.active {
    --apply: bg-primaryContainer text-onPrimaryContainer;
  }

  .admin-nav button:not(.active) {
    --apply: text-onSurfaceVariant hover: text-onSurface hover:
      bg-surfaceContainerLow;
  }
</style>
