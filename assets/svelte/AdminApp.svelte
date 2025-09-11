<script>
    import { onMount } from 'svelte';
    
    let message = 'Hola desde el Admin!';
    let loading = true;
    
    onMount(() => {
        // Simular carga de datos
        setTimeout(() => {
            loading = false;
        }, 1000);
    });
</script>

<div class="admin-container">
    <header class="admin-header">
        <h1 class="admin-title">Panel de Administración</h1>
    </header>
    
    {#if loading}
        <div class="flex items-center justify-center py-8">
            <div class="animate-spin rounded-full h-8 w-8 border-b-2 border-blue-500"></div>
            <span class="ml-2 text-gray-600">Cargando...</span>
        </div>
    {:else}
        <main>
            <div class="bg-blue-50 border border-blue-200 rounded-lg p-4 mb-6">
                <p class="text-blue-800">{message}</p>
            </div>
            
            <div class="grid grid-cols-1 md:grid-cols-2 lg:grid-cols-3 gap-6">
                <div class="bg-white p-6 rounded-lg shadow-sm border">
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Estadísticas</h3>
                    <p class="text-3xl font-bold text-blue-600">42</p>
                    <p class="text-sm text-gray-500">Total de elementos</p>
                </div>
                
                <div class="bg-white p-6 rounded-lg shadow-sm border">
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Configuración</h3>
                    <button class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-md text-sm font-medium transition-colors">
                        Configurar
                    </button>
                </div>
                
                <div class="bg-white p-6 rounded-lg shadow-sm border">
                    <h3 class="text-lg font-medium text-gray-900 mb-2">Herramientas</h3>
                    <div class="space-y-2">
                        <button class="w-full text-left px-3 py-2 text-sm text-gray-700 hover:bg-gray-50 rounded">
                            Exportar datos
                        </button>
                        <button class="w-full text-left px-3 py-2 text-sm text-gray-700 hover:bg-gray-50 rounded">
                            Importar datos
                        </button>
                    </div>
                </div>
            </div>
        </main>
    {/if}
</div>
