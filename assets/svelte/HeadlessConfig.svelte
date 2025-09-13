<script>
  import { onMount } from "svelte";

  let settings = {};
  let loading = true;
  let activeTab = "general";
  let message = "";
  let messageType = "";

  // Usar la variable global de WordPress para la URL del sitio
  const homeUrl = window.headlessWpAdmin?.home_url || "/";

  onMount(async () => {
    await loadSettings();
    loading = false;
  });

  async function loadSettings() {
    try {
      const response = await fetch("/wp-json/headless/v1/config");
      if (response.ok) {
        const data = await response.json();
        settings = data.data;
      }
    } catch (error) {
      console.error("Error loading settings:", error);
    }
  }

  async function saveSettings() {
    try {
      const response = await fetch("/wp-json/headless/v1/config", {
        method: "POST",
        headers: {
          "Content-Type": "application/json",
          "X-WP-Nonce": window.wpApiSettings.nonce,
        },
        body: JSON.stringify(settings),
      });

      if (response.ok) {
        showMessage("Configuración guardada correctamente", "success");
      } else {
        showMessage("Error al guardar la configuración", "error");
      }
    } catch (error) {
      console.error("Error saving settings:", error);
      showMessage("Error de conexión", "error");
    }
  }

  function showMessage(text, type) {
    message = text;
    messageType = type;
    setTimeout(() => {
      message = "";
      messageType = "";
    }, 3000);
  }

  function setTab(tab) {
    activeTab = tab;
    // Actualizar URL
    const url = new URL(window.location);
    url.searchParams.set("tab", tab);
    window.history.replaceState({}, "", url);
  }
</script>

<div class="headless-admin-container">
  {#if loading}
    <div class="flex items-center justify-center py-8">
      <div
        class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-500"
      ></div>
      <span class="ml-2 text-gray-600">Cargando configuración...</span>
    </div>
  {:else}
    <div class="headless-header">
      <h1 class="text-3xl font-bold text-gray-900">🚀 Headless WordPress</h1>
      <p class="text-gray-600">Configuración completa del modo headless</p>
    </div>

    {#if message}
      <div
        class={`notice notice-${messageType} ${messageType === "success" ? "bg-green-100 border-green-400 text-green-700" : "bg-red-100 border-red-400 text-red-700"} px-4 py-3 rounded mb-6`}
      >
        {message}
      </div>
    {/if}

    <div class="headless-tabs">
      <button
        class:active={activeTab === "general"}
        on:click={() => setTab("general")}
      >
        📋 General
      </button>
      <button
        class:active={activeTab === "apis"}
        on:click={() => setTab("apis")}
      >
        🔌 APIs
      </button>
      <button
        class:active={activeTab === "blocked-page"}
        on:click={() => setTab("blocked-page")}
      >
        🎨 Página Bloqueada
      </button>
      <button
        class:active={activeTab === "security"}
        on:click={() => setTab("security")}
      >
        🔒 Seguridad
      </button>
      <button
        class:active={activeTab === "advanced"}
        on:click={() => setTab("advanced")}
      >
        ⚙️ Avanzado
      </button>
    </div>

    <div class="headless-content">
      {#if activeTab === "general"}
        <!-- Contenido de la pestaña General -->
        <div class="headless-section">
          <h3>Configuración General</h3>
          <div class="headless-form-row">
            <label>
              <input
                type="checkbox"
                bind:checked={settings.blocked_page_enabled}
              />
              Mostrar página personalizada cuando se bloquee el acceso
            </label>
          </div>
          <!-- Más campos de configuración general -->
        </div>
      {:else if activeTab === "apis"}
        <!-- Contenido de la pestaña APIs -->
        <div class="headless-section">
          <h3>GraphQL API</h3>
          <div class="headless-form-row">
            <label>
              <input type="checkbox" bind:checked={settings.graphql_enabled} />
              Habilitar GraphQL API
            </label>
          </div>
          <!-- Más campos de GraphQL -->
        </div>
      {:else if activeTab === "blocked-page"}
        <!-- Contenido de la pestaña Página Bloqueada -->
        <div class="headless-section">
          <h3>Apariencia de la Página</h3>
          <div class="headless-form-row">
            <label for="blocked_page_title">Título Principal</label>
            <input
              type="text"
              id="blocked_page_title"
              bind:value={settings.blocked_page_title}
            />
          </div>
          <!-- Más campos de apariencia -->
        </div>
      {:else if activeTab === "security"}
        <!-- Contenido de la pestaña Seguridad -->
        <div class="headless-section">
          <h3>Configuración de Seguridad</h3>
          <div class="headless-form-row">
            <label>
              <input
                type="checkbox"
                bind:checked={settings.security_headers_enabled}
              />
              Habilitar headers de seguridad
            </label>
          </div>
          <!-- Más campos de seguridad -->
        </div>
      {:else if activeTab === "advanced"}
        <!-- Contenido de la pestaña Avanzado -->
        <div class="headless-section">
          <h3>Configuración Avanzada</h3>
          <div class="headless-form-row">
            <label for="custom_redirect_rules"
              >Reglas de Redirección Personalizadas</label
            >
            <textarea
              id="custom_redirect_rules"
              bind:value={settings.custom_redirect_rules}
              rows="6"
            ></textarea>
          </div>
          <!-- Más campos avanzados -->
        </div>
      {/if}
    </div>

    <div class="headless-footer">
      <button on:click={saveSettings} class="btn-primary">
        💾 Guardar Configuración
      </button>
      <button
        on:click={() => window.open(homeUrl, "_blank")}
        class="btn-secondary"
      >
        👁️ Vista Previa
      </button>
    </div>
  {/if}
</div>

<style>
  .headless-admin-container {
    --apply: p-6 bg-surface rounded-xl shadow-sm;
  }

  .headless-header {
    --apply: mb-6 pb-4 border-b border-outlineVariant;
  }

  .headless-tabs {
    --apply: flex border-b border-outlineVariant mb-6;
  }

  .headless-tabs button {
    --apply: px-4 py-3 font-medium text-onSurfaceVariant border-b-2
      border-transparent transition-colors duration-200 state-layer;
  }

  .headless-tabs button.active {
    --apply: text-primary border-primary;
  }

  .headless-section {
    --apply: mb-6;
  }

  .headless-section h3 {
    --apply: text-xl font-semibold mb-4 text-onSurface;
  }

  .headless-form-row {
    --apply: mb-4;
  }

  .headless-form-row label {
    --apply: block font-medium text-onSurface mb-2;
  }

  .headless-form-row input[type="text"],
  .headless-form-row textarea {
    --apply: w-full max-w-md px-4 py-3 border border-outline rounded-lg
      shadow-sm focus: outline-none focus: ring-2 focus: ring-primary focus:
      border-primary;
  }

  .headless-form-row textarea {
    --apply: h-24;
  }

  .headless-footer {
    --apply: mt-8 pt-6 border-t border-outlineVariant flex gap-4;
  }

  .btn-primary {
    --apply: px-5 py-3 bg-primary text-onPrimary rounded-full hover:
      bg-primary/90 focus: outline-none focus: ring-2 focus: ring-primary focus:
      ring-offset-2 transition-all duration-200 state-layer;
  }

  .btn-secondary {
    --apply: px-5 py-3 bg-surfaceContainer text-onSurfaceContainer rounded-full
      hover: bg-surfaceContainerHigh focus: outline-none focus: ring-2 focus:
      ring-outline focus: ring-offset-2 transition-all duration-200 state-layer;
  }
</style>
