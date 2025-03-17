<div class="grid grid-cols-2 gap-4">
    <x-order-panel class="mt-12 col-span-2" title="Orden #{{$this->order->id}}">
        {{ $this->order->id }}
        <table class="w-full">
            <thead>
                <tr>
                    <th class="text-left">Producto</th>
                    <th class="text-left">Cantidad</th>
                    <th class="text-right">Total</th>
                </tr>
            </thead>
            <tbody>
                @foreach ($this->order->items as $item)
                    <tr>
                        <td>
                            {{ $item->name }} <br>
                            {{ $item->description }}
                        </td>

                        <td>{{ $item->quantity }}</td>
                        
                        <td class="text-right">
                            {{ $item->amount_total }}
                        </td>
                    </tr>
                @endforeach
            </tbody>

            <tfoot>
                @if ($this->order->amount_shipping->isPositive())
                    <tr>
                        <td colspan="2" class="text-right font-medium">Envío</td>
                        <td class="font-medium text-right">{{ $this->order->amount_shipping }}</td>
                    </tr>
                @endif

                @if ($this->order->amount_discount->isPositive())
                    <tr>
                        <td colspan="2" class="text-right font-medium">Descuento</td>
                        <td class="font-medium text-right">{{ $this->order->amount_discount }}</td>
                    </tr>
                @endif

                @if ($this->order->amount_tax->isPositive())
                    <tr>
                        <td colspan="2" class="text-right font-medium">Impuesto</td>
                        <td class="font-medium text-right">{{ $this->order->amount_tax }}</td>
                    </tr>
                @endif

                @if ($this->order->amount_subtotal->isPositive())
                    <tr>
                        <td colspan="2" class="text-right font-medium">Subtotal</td>
                        <td class="font-medium text-right">{{ $this->order->amount_subtotal }}</td>
                    </tr>
                @endif

                @if ($this->order->amount_total->isPositive())
                    <tr>
                        <td colspan="2" class="text-right font-medium">Total</td>
                        <td class="font-medium text-right">{{ $this->order->amount_total }}</td>
                    </tr>
               @endif
            </tfoot>
        </table>
    </x-order-panel>

    <x-order-panel class="col-span-1" title="Información de facturación">
        @foreach ($this->order->billing_address->filter() as $value)
            {{ $value }} <br>
        @endforeach
    </x-order-panel>

    <x-order-panel class="col-span-1" title="Información de envío">
        @foreach ($this->order->shipping_address->filter() as $value)
            {{ $value }} <br>
        @endforeach
    </x-order-panel>
</div>
