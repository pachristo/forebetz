<div>
    <x-account::shell active="dashboard" title="Dashboard">
        <section class="flex flex-col gap-3 rounded-[18px] bg-white p-3 sm:flex-row sm:items-center sm:justify-between sm:p-4">
            <div class="min-w-0">
                <p class="text-[18px] font-bold sm:text-[22px]">Welcome back, {{ $member->firstName() }} 👋</p>
                <div class="mt-1.5 flex flex-wrap items-center gap-2 text-[13px]">
                    <span class="text-[#5a5a5a]">Plan:</span>
                    <span class="font-semibold">{{ $active ? collect($active)->pluck('name')->join(', ') : 'Free' }}</span>
                    <span @class([
                        'rounded-full px-2.5 py-0.5 text-[11px] font-bold uppercase',
                        'bg-[#14ae5c]/15 text-[#0b7a3f]' => $active,
                        'bg-[#f0f0f0] text-[#767676]' => ! $active,
                    ])>{{ $active ? 'Active' : 'Not subscribed' }}</span>
                    @foreach ($active as $plan)
                        @if ($plan['expires'])
                            <span class="text-[12px] text-[#767676]">{{ $plan['name'] }} expires {{ $plan['expires']->format('M j, Y') }} ({{ $plan['expires']->diffForHumans() }})</span>
                        @endif
                    @endforeach
                </div>
            </div>
            <a href="/pricing" class="btn-fx flex h-10 shrink-0 items-center justify-center gap-2 rounded-[12px] bg-gradient-to-b from-[#ff8a3d] to-[#e85d00] px-5 text-[14px] font-bold text-white">
                <x-icon name="crown" class="size-5" />
                {{ $active ? 'Renew / Upgrade' : 'Upgrade' }}
            </a>
        </section>

        @forelse ($active as $plan)
            <section class="rounded-[18px] bg-white p-3 sm:p-4" wire:key="vip-{{ $plan['category_id'] }}">
                <livewire:home::predictions
                    :vip="(string) $plan['category_id']"
                    :heading="$plan['name'].' Tips'"
                    :empty-label="$plan['name'].' tips'"
                    :key="'vip-feed-'.$plan['category_id']"
                />
            </section>
        @empty
            <section class="rounded-[18px] bg-white p-4 text-center sm:p-5">
                <p class="text-[16px] font-semibold sm:text-[18px]">You&rsquo;re not on any VIP plan yet</p>
                <p class="mx-auto mt-1 max-w-[520px] text-[13px] text-[#5a5a5a] sm:text-[14px]">Subscribe to a package to unlock premium daily tips. They will appear right here on your dashboard.</p>
            </section>
            <livewire:home::packages />
        @endforelse

        <section id="history" class="rounded-[18px] bg-white p-3 sm:p-4">
            <h2 class="mb-2.5 text-[16px] font-semibold sm:text-[18px]">Subscription History</h2>
            @if ($history)
                <div class="overflow-x-auto">
                    <table class="w-full min-w-[480px] text-left text-[13px]">
                        <thead class="bg-[#f5f5f5] text-[11px] uppercase text-[#767676]">
                            <tr>
                                <th class="px-3 py-2">Plan</th>
                                <th class="px-3 py-2">Started</th>
                                <th class="px-3 py-2">Expires</th>
                                <th class="px-3 py-2">Status</th>
                            </tr>
                        </thead>
                        <tbody>
                            @foreach ($history as $row)
                                <tr class="border-t border-[#f0f0f0]">
                                    <td class="px-3 py-2 font-medium">{{ $row['name'] }}</td>
                                    <td class="px-3 py-2">{{ $row['start']?->format('M j, Y') ?? '-' }}</td>
                                    <td class="px-3 py-2">{{ $row['end']?->format('M j, Y') ?? '-' }}</td>
                                    <td class="px-3 py-2">
                                        <span @class(['rounded-full px-2 py-0.5 text-[11px] font-bold uppercase', 'bg-[#14ae5c]/15 text-[#0b7a3f]' => $row['current'], 'bg-[#f0f0f0] text-[#767676]' => ! $row['current']])>{{ $row['current'] ? 'Active' : 'Expired' }}</span>
                                    </td>
                                </tr>
                            @endforeach
                        </tbody>
                    </table>
                </div>
            @else
                <p class="text-[13px] text-[#767676]">No subscriptions yet.</p>
            @endif
        </section>

        <p class="text-center text-[13px] text-[#5a5a5a]">Need help? Email <a href="mailto:{{ config('site.contact.email') }}" class="link-fx font-semibold text-[#cc5400]">{{ config('site.contact.email') }}</a></p>
    </x-account::shell>
</div>
