<script>
    export let type = 'default';
    
    let count = 0;
    
    function increment() {
        count += 1;
    }
</script>

<div class="frontend-container">
    <div class="frontend-content">
        <h2 class="text-xl font-semibold mb-4">Plugin Frontend ({type})</h2>
        
        <div class="bg-white p-4 rounded-lg shadow-sm border">
            <p class="mb-4">Contador: <span class="font-bold text-2xl text-blue-600">{count}</span></p>
            <button 
                on:click={increment}
                class="bg-blue-500 hover:bg-blue-600 text-white px-4 py-2 rounded-md font-medium transition-colors"
            >
                Incrementar
            </button>
        </div>
    </div>
</div>
