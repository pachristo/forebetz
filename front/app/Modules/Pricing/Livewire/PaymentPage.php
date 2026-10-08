<?php

namespace App\Modules\Pricing\Livewire;

use App\Models\PaymentMethod;
use App\Models\Plan;
use App\Models\SiteConfiguration;
use App\Modules\Pricing\Concerns\HasPricingCountry;
use App\Support\PricingCurrency;
use Illuminate\Support\Str;
use Livewire\Attributes\Layout;
use Livewire\Attributes\Locked;
use Livewire\Component;

#[Layout('layouts.app')]
class PaymentPage extends Component
{
    use HasPricingCountry;

    #[Locked]
    public int $planId = 0;

    public function mount(): void
    {
        $this->planId = (int) request()->query('plan', 0);

        abort_unless($this->planId > 0 && $this->plan(), 404);
    }

    protected function plan(): ?Plan
    {
        return once(fn () => Plan::query()->with('category')->find($this->planId));
    }

    private const DEFAULT_PROOF = "After you're done with the payment, kindly send the payment proof to our email {contact_email} "
        .'or WhatsApp phone number {whatsapp_no}. Your account will be activated instantly.';

    /** Payment-proof note: the method's own text, else Site Configuration, else the admin default — placeholders filled in. */
    protected function proofInstruction(?string $text): string
    {
        $config = once(fn () => SiteConfiguration::query()->first());
        $template = trim((string) $text) ?: trim((string) $config?->payment_proof_instruction) ?: self::DEFAULT_PROOF;

        return strtr(strip_tags($template), [
            '{contact_email}' => $config?->contact_email ?: config('site.contact.email'),
            '{whatsapp_no}' => $config?->whatsapp_no ?: config('site.contact.whatsapp'),
            '{contact_phone}' => $config?->contact_phone ?: config('site.contact.whatsapp'),
        ]);
    }

    /** @return list<array{id: int, name: string, image: ?string, details: string, proof: string}> */
    protected function methods(): array
    {
        $methods = PaymentMethod::query()->where('country', $this->country)->orderBy('id')->get();

        if ($methods->isEmpty() && $this->country !== PricingCurrency::OTHERS) {
            $methods = PaymentMethod::query()->where('country', PricingCurrency::OTHERS)->orderBy('id')->get();
        }

        return $methods->map(fn (PaymentMethod $m) => [
            'id' => $m->id,
            'name' => (string) $m->name,
            'image' => $m->image ? (Str::startsWith($m->image, ['http://', 'https://']) ? $m->image : rtrim(config('site.admin_url'), '/').'/storage/'.ltrim($m->image, '/')) : null,
            'details' => trim(strip_tags((string) $m->instructions, '<p><br><strong><b><em><i><u><ul><ol><li><span><a>')),
            'proof' => $this->proofInstruction($m->text),
        ])->all();
    }

    protected function confirmUrl(Plan $plan, ?array $price): string
    {
        $member = auth()->user();
        $message = implode("\n", array_filter([
            'Hello, I have sent payment for a VIP plan.',
            'Plan: '.($plan->category?->name ? $plan->category->name.' — ' : '').($plan->variation ?: $plan->name),
            $price ? 'Amount: '.$price['label'].' ('.$price['code'].')' : null,
            'Country: '.PricingCurrency::options()[$this->country],
            $member ? 'Name: '.$member->name : null,
            $member ? 'Email: '.$member->email : null,
        ]));

        $number = preg_replace('/\D+/', '', (string) config('site.contact.whatsapp'));

        return $number !== '' ? 'https://wa.me/'.$number.'?text='.rawurlencode($message) : (string) config('site.socials.telegram');
    }

    public function render()
    {
        $plan = $this->plan();
        $price = PricingCurrency::price($plan, $this->country);

        return view('pricing::livewire.payment-page', [
            'plan' => $plan,
            'planName' => trim(($plan->category?->title ?: $plan->category?->name ?: 'VIP Plan')),
            'duration' => $plan->variation ?: $plan->name,
            'price' => $price,
            'methods' => $this->methods(),
            'countries' => PricingCurrency::options(),
            'countryLabel' => PricingCurrency::options()[$this->country],
            'flag' => PricingCurrency::flagUrl($this->country),
            'confirmUrl' => $this->confirmUrl($plan, $price),
            'telegramUrl' => (string) config('site.socials.telegram'),
        ])->layoutData([
            'title' => 'Payment — '.config('site.name'),
            'description' => 'Complete payment for your '.config('site.name').' VIP package.',
        ]);
    }
}
