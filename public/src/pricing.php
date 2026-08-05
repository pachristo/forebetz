<?php
require_once __DIR__ . '/../includes/config.php';

$pageTitle = 'VIP Packages — Forebetz Pricing';
$pageDescription = 'Choose a Forebetz VIP package. Weekly, 2 Weeks, and One Month premium football prediction plans.';

$pricingPlans = [
    [
        'badge' => 'Weekly',
        'price' => '70',
        'features' => [
            'Sure 2 Odds daily',
            '24/7 Support',
            'Access to Risk Management Guide.',
        ],
    ],
    [
        'badge' => '2 Weeks',
        'price' => '70',
        'features' => [
            'Sure 3.5+ Odds daily',
            '24/7 Support',
            'Access to Risk Management Guide.',
        ],
    ],
    [
        'badge' => 'One Month',
        'price' => '70',
        'features' => [
            'Sure 1.50 - 1.90 Odds daily',
            '24/7 Support',
            'Access to Risk Management Guide.',
        ],
    ],
];

$pricingCountries = [
    ['name' => 'United Kingdom', 'flag' => 'uk.svg'],
    ['name' => 'Albania', 'flag' => 'albania.svg'],
    ['name' => 'Algeria', 'flag' => 'algeria.svg'],
    ['name' => 'Andorra', 'flag' => 'andorra.svg'],
    ['name' => 'Angola', 'flag' => 'angola.svg'],
];

include __DIR__ . '/../includes/head.php';
?>

<div class="relative min-h-screen overflow-x-hidden">
    <div class="relative z-10 flex flex-col">
        <?php include __DIR__ . '/../includes/header.php'; ?>

        <?php /* Mobile 477:90488 / 477:90490 — stacked; tablet keeps 1 col until lg */ ?>
        <main class="w-full px-2.5 pb-8 pt-2 sm:px-8 sm:pb-10 sm:pt-2.5 lg:px-[100px]">
            <div class="mx-auto flex w-full max-w-site flex-col gap-[15px] sm:gap-5 lg:gap-6">

                <div class="flex flex-col gap-1 py-1 sm:gap-1.5 sm:py-2">
                    <h1 class="text-[32px] font-medium leading-tight text-[#fcbd02] sm:text-[40px] lg:text-[48px]">
                        VIP Packages
                    </h1>
                    <p class="text-[14px] font-normal text-white sm:text-[16px] sm:leading-7 lg:text-[18px]">
                        Pricing
                    </p>
                </div>

                <div class="flex w-full flex-col gap-[30px] rounded-[20px] bg-[#f0f0f0] px-[15px] py-5 sm:rounded-[24px] sm:px-8 sm:py-8 md:rounded-[30px] md:px-10 lg:px-[100px] lg:py-10">

                    <?php /* Country — mobile: #f5f5f5, 20px radius, compact dropdown */ ?>
                    <div class="w-full rounded-[20px] bg-[#f5f5f5] px-2.5 py-[15px] shadow-[0_0_2px_rgba(0,0,0,0.15)] sm:px-5 sm:py-5 md:px-6 md:py-6 lg:px-8 lg:py-8">
                        <p class="mb-1 text-[16px] font-semibold leading-6 text-[#1e1e1e] sm:mb-3 sm:text-[18px]">
                            Please kindly choose your country:
                        </p>
                        <label class="relative mb-2.5 flex w-full items-center sm:mb-3">
                            <span class="sr-only">Country</span>
                            <span class="pointer-events-none absolute left-2.5 top-1/2 size-6 -translate-y-1/2 overflow-hidden sm:left-4 sm:size-7">
                                <img src="<?= $asset ?>/flags/uk.svg" alt="" class="h-full w-full object-cover" id="pricing-flag">
                            </span>
                            <select
                                id="pricing-country"
                                class="w-full appearance-none rounded-[7px] border border-[#d9d9d9] bg-[#e6e6e6] py-2.5 pl-11 pr-10 text-[13px] font-medium text-[#333] outline-none focus:ring-2 focus:ring-[#fcbd02] sm:rounded-[10px] sm:border-0 sm:bg-[#f0f0f0] sm:py-3.5 sm:pl-14 sm:pr-12 sm:text-[15px] sm:text-[#1e1e1e] md:py-4 md:text-[16px]"
                            >
                                <?php foreach ($pricingCountries as $c): ?>
                                    <option value="<?= htmlspecialchars($c['flag']) ?>" <?= $c['name'] === 'United Kingdom' ? 'selected' : '' ?>>
                                        <?= htmlspecialchars($c['name']) ?>
                                    </option>
                                <?php endforeach; ?>
                            </select>
                            <span class="pointer-events-none absolute right-2.5 top-1/2 size-[18px] -translate-y-1/2 overflow-hidden opacity-70 sm:right-4 sm:size-5">
                                <img src="<?= $asset ?>/icons/dropdown-down.svg" alt="" class="h-full w-full object-contain">
                            </span>
                        </label>
                        <p class="text-[14px] leading-snug text-[#1e1e1e] sm:text-[15px] sm:leading-6 sm:text-[#5a5a5a]">
                            Select from our available plans below ensuring a perfect match for your needs.
                        </p>
                    </div>

                    <section class="relative overflow-hidden rounded-[24px] sm:rounded-[30px]">
                        <div class="pointer-events-none absolute inset-0" aria-hidden="true">
                            <div class="absolute inset-0 rounded-[24px] bg-[#162640] sm:rounded-[30px]"></div>
                            <img
                                src="<?= $asset ?>/images/packages-bg.png"
                                alt=""
                                class="absolute inset-0 size-full rounded-[24px] object-cover opacity-20 backdrop-blur-[5px] sm:rounded-[30px]"
                            >
                        </div>

                        <div class="relative z-10 overflow-hidden rounded-[24px] p-3 sm:rounded-[30px] sm:p-5 md:p-6 lg:p-8">
                            <div class="pointer-events-none absolute inset-0" aria-hidden="true">
                                <div class="absolute inset-0 rounded-[20px] bg-[#0a111d] sm:rounded-[24px]"></div>
                                <img
                                    src="<?= $asset ?>/images/invest-bg.png"
                                    alt=""
                                    class="absolute inset-0 size-full rounded-[20px] object-cover opacity-20 sm:rounded-[24px]"
                                >
                            </div>

                            <div class="relative z-10 flex flex-col gap-4 sm:gap-5 md:gap-6">
                                <h2 class="text-center text-[24px] font-bold text-white sm:text-[28px] lg:text-[32px]">
                                    Go Premium
                                </h2>

                                <?php /* 477:90488 — 1 col mobile + tablet; 3 col desktop */ ?>
                            <div class="grid grid-cols-1 gap-2 sm:gap-3 md:mx-auto md:max-w-[520px] md:gap-4 lg:mx-0 lg:max-w-none lg:grid-cols-3 lg:gap-5">
                                <?php
                                $planSlugs = ['Weekly' => 'weekly', '2 Weeks' => '2weeks', 'One Month' => 'month'];
                                foreach ($pricingPlans as $plan):
                                    $slug = $planSlugs[$plan['badge']] ?? 'weekly';
                                ?>
                                    <article class="flex flex-col rounded-[20px] border-[1.5px] border-[#fcbd02] bg-gradient-to-b from-[#162640] to-[#163540] p-1 backdrop-blur-[6px] sm:rounded-[25px] sm:border-2 sm:p-[5px]">
                                        <div class="flex min-h-0 flex-1 flex-col gap-3.5 p-4 sm:gap-5 sm:p-5">
                                            <div class="flex flex-col gap-2 pb-2 sm:gap-3 sm:pb-0">
                                                <span class="inline-flex w-fit rounded-[4px] bg-[#fcbd02] px-2 py-1 text-[14px] font-bold text-black sm:rounded-[5px] sm:px-2.5 sm:text-[15px]">
                                                    <?= htmlspecialchars($plan['badge']) ?>
                                                </span>
                                                <p class="font-bold leading-none text-[#f0f0f0]">
                                                    <span class="text-[23px] sm:text-[28px] lg:text-[32px]">$</span><span class="text-[32px] sm:text-[42px] lg:text-[48px]"><?= htmlspecialchars($plan['price']) ?></span>
                                                </p>
                                            </div>
                                            <ul class="flex flex-col gap-3 border-t border-[#b7bcc4] py-2 sm:gap-3 sm:pt-5 sm:pb-0">
                                                <?php foreach ($plan['features'] as $feature): ?>
                                                    <li class="flex items-center gap-2 text-[13px] text-[#e9e9e9] sm:gap-2.5 sm:text-[14px]">
                                                        <span class="size-[17px] shrink-0 overflow-hidden sm:size-[22px]">
                                                            <img src="<?= $asset ?>/icons/check-fill.svg" alt="" class="h-full w-full object-contain">
                                                        </span>
                                                        <?= htmlspecialchars($feature) ?>
                                                    </li>
                                                <?php endforeach; ?>
                                            </ul>
                                        </div>
                                        <a
                                            href="/payment.php?plan=<?= urlencode($slug) ?>"
                                            class="mb-1 mx-1 flex h-[42px] items-center justify-center gap-1.5 rounded-[16px] px-6 text-[15px] font-bold uppercase text-black sm:mb-[5px] sm:mx-[5px] sm:h-[54px] sm:gap-2 sm:rounded-[20px] sm:px-8 sm:text-[16px]"
                                            style="background-image: linear-gradient(109deg, #ffc108 13%, #c39202 101%);"
                                        >
                                            subscribe
                                            <span class="size-[19px] shrink-0 overflow-hidden sm:size-6">
                                                <img src="<?= $asset ?>/icons/arrow-right.svg" alt="" class="h-full w-full object-contain">
                                            </span>
                                        </a>
                                    </article>
                                <?php endforeach; ?>
                            </div>
                            </div>
                        </div>
                    </section>
                </div>
            </div>
        </main>
    </div>
</div>

<script>
(() => {
  const select = document.getElementById('pricing-country');
  const flag = document.getElementById('pricing-flag');
  if (!select || !flag) return;
  select.addEventListener('change', () => {
    flag.src = '/assets/flags/' + select.value;
  });
})();
</script>

<?php include __DIR__ . '/../includes/footer.php'; ?>
