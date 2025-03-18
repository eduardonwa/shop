<!-- Notificaciones -->
<div
    x-data="{ showError: @entangle('showError') }"
    x-show="showError"
    x-transition:enter-start="translate-y-12 opacity-0"
    x-transition:enter-end="translate-y-0 opacity-100"
    x-transition:leave-end="translate-y-12 opacity-0"
    class="fixed bottom-0 right-0 m-4 p-4 bg-red-500 text-white rounded shadow-lg max-w-xs"
>
    <div class="flex justify-between items-center">
        <!-- Mensaje de la notificación -->
        <p>
            @if ($emptyCart)
                {{ $emptyCart }}
            @elseif ($minimumAmount)
                {{ $minimumAmount }}
            @endif
        </p>

        <!-- Botón de cerrar -->
        <button @click="showError = false;" class="ml-4">
            <svg xmlns="http://www.w3.org/2000/svg" class="h-5 w-5" viewBox="0 0 20 20" fill="currentColor">
                <path fill-rule="evenodd" d="M4.293 4.293a1 1 0 011.414 0L10 8.586l4.293-4.293a1 1 0 111.414 1.414L11.414 10l4.293 4.293a1 1 0 01-1.414 1.414L10 11.414l-4.293 4.293a1 1 0 01-1.414-1.414L8.586 10 4.293 5.707a1 1 0 010-1.414z" clip-rule="evenodd"/>
            </svg>
        </button>
    </div>

    <!-- Enlaces adicionales -->
    <div class="mt-2">
        @if ($emptyCart)
            <a href="/catalogo" class="text-blue-200 hover:text-blue-100 underline">Ver catálogo</a>
        @elseif ($minimumAmount)
            <a href="/ofertas" class="text-green-200 hover:text-green-100 underline">Ver ofertas</a>
        @endif
    </div>
</div>