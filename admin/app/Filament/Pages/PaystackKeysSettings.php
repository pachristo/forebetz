<?php

namespace App\Filament\Pages;

use App\Services\PaymentDetailsFileRepository;
use Filament\Forms;
use Filament\Forms\Concerns\InteractsWithForms;
use Filament\Forms\Contracts\HasForms;
use Filament\Forms\Form;
use Filament\Notifications\Notification;
use Filament\Pages\Page;

class PaystackKeysSettings extends Page implements HasForms
{
    use InteractsWithForms;

    protected static ?string $navigationIcon = 'heroicon-o-credit-card';

    protected static string $view = 'filament.pages.paystack-keys-settings';

    protected static ?string $navigationGroup = 'Payments';

    protected static ?string $navigationLabel = 'Paystack keys';

    protected static ?int $navigationSort = 5;

    protected static ?string $title = 'Paystack API keys';

    /** @var array<string, mixed> */
    public ?array $data = [];

    public function mount(PaymentDetailsFileRepository $repository): void
    {
        $payload = $repository->read();
        $this->form->fill([
            'paystack_public_key' => (string) ($payload['paystack_public_key'] ?? ''),
            'paystack_secret_key' => (string) ($payload['paystack_secret_key'] ?? ''),
        ]);
    }

    public function form(Form $form): Form
    {
        return $form
            ->schema([
                Forms\Components\Section::make('Paystack')
                    ->description('Updates `paystack_public_key` and `paystack_secret_key` in payment_details.json used by the public site checkout. Other fields in that file are left unchanged.')
                    ->schema([
                        Forms\Components\Placeholder::make('file_path')
                            ->label('File')
                            ->content(fn (): string => app(PaymentDetailsFileRepository::class)->path()),
                        Forms\Components\TextInput::make('paystack_public_key')
                            ->label('Public key')
                            ->maxLength(512)
                            ->helperText('Usually starts with pk_live_ or pk_test_.'),
                        Forms\Components\TextInput::make('paystack_secret_key')
                            ->label('Secret key')
                            ->password()
                            ->revealable()
                            ->maxLength(512)
                            ->dehydrated(fn (?string $state): bool => filled($state))
                            ->helperText('Leave blank to keep the current secret. When set, usually starts with sk_live_ or sk_test_.'),
                    ]),
            ])
            ->statePath('data');
    }

    public function save(PaymentDetailsFileRepository $repository): void
    {
        $state = $this->form->getState();
        $public = trim((string) ($state['paystack_public_key'] ?? ''));
        $secret = array_key_exists('paystack_secret_key', $state)
            ? trim((string) $state['paystack_secret_key'])
            : null;

        try {
            $repository->updatePaystackKeys($public, $secret);
            Notification::make()
                ->title('Paystack keys saved')
                ->body('payment_details.json was updated. The storefront will use these keys on the next request.')
                ->success()
                ->send();
        } catch (\Throwable $e) {
            Notification::make()
                ->title('Save failed')
                ->body($e->getMessage())
                ->danger()
                ->send();
        }
    }
}
