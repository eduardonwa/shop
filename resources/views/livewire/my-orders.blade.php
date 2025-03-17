<x-order-panel title="Mis Pedidos" class="max-w-lg mx-auto mt-12">
    <table class="w-full">
        <thead>
            <tr>
                <th class="text-left">Orden</th>
                <th class="text-left">Ordenado</th>
                <th class="text-right">Total</th>
            </tr>
        </thead>
        <tbody>
            @foreach ($this->orders as $order)
                <tr>
                    <td>
                        <a href="{{ route('view-order', $order->id) }}" class="underline font-medium">#{{ $order->id }}</a>
                    </td>
                    <td>{{ $order->created_at->diffForHumans() }}</td>
                    <td class="text-right">{{ $order->amount_total }}</td>
                </tr>
            @endforeach
        </tbody>
    </table>
</x-order-panel>