<div>
    <x-page.title :title="$page->head1 ?: 'Contact Us'" :subtitle="$page->head2" />

    <x-page.panel>
        <div class="grid w-full grid-cols-1 gap-4 lg:grid-cols-2">
            <div class="flex flex-col gap-3">
                @if (trim((string) $page->content) !== '')
                    <div class="home-content rounded-[18px] bg-white px-4 py-4 sm:px-6">{!! $page->content !!}</div>
                @endif

                <a href="mailto:{{ $email }}" class="card-fx flex items-center gap-4 rounded-[16px] bg-white px-4 py-3">
                    <img src="{{ $asset }}/images/contact/email.svg" alt="" class="size-8">
                    <span class="flex flex-col">
                        <span class="text-[13px] text-[#5a5a5a]">Email Us</span>
                        <span class="text-[16px] font-semibold sm:text-[18px]">{{ $email }}</span>
                    </span>
                </a>
                <a href="https://wa.me/{{ preg_replace('/\D+/', '', $phone) }}" target="_blank" rel="noopener" class="card-fx flex items-center gap-4 rounded-[16px] bg-white px-4 py-3">
                    <img src="{{ $asset }}/images/contact/phone.svg" alt="" class="size-8">
                    <span class="flex flex-col">
                        <span class="text-[13px] text-[#5a5a5a]">WhatsApp / Call</span>
                        <span class="text-[16px] font-semibold sm:text-[18px]">{{ $phone }}</span>
                    </span>
                </a>
            </div>

            <section class="rounded-[18px] bg-white p-4 sm:p-5">
                <h2 class="mb-3 text-[20px] font-bold">Send us a message</h2>

                @if ($sent)
                    <div class="mb-3 rounded-[10px] border border-[#14ae5c]/40 bg-[#ecfdf3] px-3 py-2 text-[13px] font-medium text-[#0b7a3f]">Thanks! Your message has been sent. We usually reply within 24 hours.</div>
                @endif

                <form wire:submit="send" class="flex flex-col gap-3">
                    <x-form.field tone="light" label="Name" name="name" placeholder="Enter name" />
                    <x-form.field tone="light" label="Email" name="email" type="email" placeholder="e.g. myemail@gmail.com" />
                    <label class="flex flex-col gap-1">
                        <span class="text-[12px] font-semibold text-[#1e1e1e] sm:text-[13px]">Message</span>
                        <textarea wire:model="message" rows="5" placeholder="How can we help?" class="w-full rounded-[10px] border border-[#d9d9d9] bg-white px-3 py-2 text-[14px] outline-none transition focus:border-[#ff6900] focus:ring-2 focus:ring-[#ff6900]/30"></textarea>
                        @error('message')<span class="text-[12px] font-medium text-[#ec221f]">{{ $message }}</span>@enderror
                    </label>
                    <input type="text" wire:model="website" tabindex="-1" autocomplete="off" class="hidden" aria-hidden="true">
                    <x-form.submit target="send">Send Message</x-form.submit>
                </form>
            </section>
        </div>
    </x-page.panel>
</div>
