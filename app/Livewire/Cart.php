<?php

namespace App\Livewire;

use Livewire\Component;
use App\Factories\CartFactory;
use Masmerise\Toaster\Toaster;
use Masmerise\Toaster\Toastable;
use Livewire\Attributes\Computed;
use Masmerise\Toaster\PendingToast;
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
        $this->cart->items()->find($itemId)?->increment('quantity');
    }
    
    public function decrement($itemId)
    {
        $item = $this->cart->items()->find($itemId);

        if (!$item) {
            $this->dispatch('notify', ['type' => 'error', 'message' => 'Item not found.']);
            return;
        }

        if ($item->quantity > 1) { 
            $item->decrement('quantity');
        } else {
            $item->delete();
            $this->dispatch('productRemovedFromCart');
        }
    }

    public function delete($itemId)
    {
        $this->cart->items()->where('id', $itemId)->delete();

        $this->dispatch('productRemovedFromCart');
    }

    public function checkout(CreateStripeCheckoutSession $checkoutSession)
    {
        try {
            // validar si el carrito viene vacío
            if ($this->cart->items->isEmpty()) {
                throw new EmptyCartException();
            }
            // validar si el monto es menor a 10 pesos
            if ($this->cart->total->getAmount() < 1000) {
                throw new MinimumPurchaseAmountException();
            }
            // si no hay errores, crear la sesión de pago
            return $checkoutSession->createFromCart($this->cart);
            // manejar los mensajes de error
        } catch (EmptyCartException $e) {
            $this->showError = true;
            $this->emptyCart = $e->getMessage();
            Toaster::info($this->emptyCart, ['showError' => $this->showError, 'emptyCart' => $this->emptyCart]);
        } catch (MinimumPurchaseAmountException $e) {
            $this->showError = true;
            $this->minimumAmount = $e->getMessage();
            Toaster::info($this->minimumAmount, ['showError' => $this->showError, 'minimumAmount' => $this->minimumAmount]);
        } catch (\Exception $e) {
            // otros errores
            $this->showError = true;
            //$this->errorMessage = 'Ocurrió un error al procesar tu solicitud.';
            Toaster::error('Ocurrió un error al procesar tu solicitud.');
        }
    }
    
    public function render()
    {
        return view('livewire.cart');
    }
}
