<div class="bg-white rounded shadow mt-12 p-5 max-w-xl mx-auto">
    @if ($this->order)
        Gracias por tu orden (#{{ $this->order->id }})
        <p>
            <a href="{{ route('my-orders') }}" class="underline">Tu recibo de compra</a>
        </p>
    @else
        <p wire:poll>
            Esperando la confirmación del pago. Por favor, espera...
        </p>
    @endif
</div>
