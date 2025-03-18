<?php

namespace App\Livewire;

use Log;
use Livewire\Component;
use App\Factories\CartFactory;
use Masmerise\Toaster\Toaster;
use Masmerise\Toaster\Toastable;
use Livewire\Attributes\Computed;
use App\Exceptions\EmptyCartException;
use App\Exceptions\MinimumPurchaseAmountException;
use App\Actions\Webshop\CreateStripeCheckoutSession;

class Cart extends Component
{
    use Toastable;
    
    public $showError = false;
    public $emptyCart = '';
    public $minimumAmount = '';
    public $errorMessage = '';

    public $listeners = [
        'CartUpdated' => '$refresh',
    ];
    
    #[Computed]
    public function cart()
    {
        return CartFactory::make()->loadMissing(['items', 'items.product', 'items.variant']);
    }

    #[Computed]
    public function items()
    {
        return$this->cart->items;
    }

    public function increment($itemId)
    {
        $item = $this->cart->items()->find($itemId);
        if ($item) {
            $item->increment('quantity');
            $this->dispatch('productAddedToCart');
            $this->dispatch('CartUpdated');
        }
    }
    
    public function decrement($itemId)
    {
        $item = $this->cart->items()->find($itemId);

        if (!$item) {
            return;
        }

        if ($item->quantity > 1) {
            $item->decrement('quantity');
        } else {
            $item->delete();
        }

        $this->dispatch('productRemovedFromCart');
        $this->dispatch('CartUpdated');
    }

    public function delete($itemId)
    {
        $this->cart->items()->where('id', $itemId)->delete();

        $this->dispatch('productRemovedFromCart');
        $this->dispatch('CartUpdated');
    }

    public function checkout(CreateStripeCheckoutSession $checkoutSession)
    {
        try {
            if ($this->cart->total->getAmount() < 1000) { // 10 pesos
                throw new MinimumPurchaseAmountException();
            }
    
            // Si no hay errores, crear la sesión de pago
            return $checkoutSession->createFromCart($this->cart);
        } catch (MinimumPurchaseAmountException $e) {
            // Concatenar el mensaje de la excepción con el enlace HTML
            $message = $e->getMessage() . ' <a href="/ofertas" class="underline">¡Revisa nuestras ofertas!</a>';
    
            // Pasar el mensaje completo al toaster
            Toaster::info($message, [
                'showError' => true,
                'minimumAmount' => $message, // Pasamos el mensaje completo
            ]);
        } catch (\Exception $e) {
            // Otros errores
            Toaster::error('Ocurrió un error al procesar tu solicitud.');
        }
    }
    
    public function render()
    {
        // si el carrito viene vacío mostrar mensaje
        $this->showError = $this->cart->items->isEmpty();
        $this->emptyCart = $this->showError ? 'Tu carrito está vacío.' : '';

        return view('livewire.cart');
    }
}
