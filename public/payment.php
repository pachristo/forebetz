<?php
require_once __DIR__ . '/includes/config.php';

$planKey = strtolower((string) ($_GET['plan'] ?? 'weekly'));
$plans = [
    'weekly' => ['label' => 'WEEKLY', 'duration' => 'Two Weeks', 'price' => '70'],
    '2weeks' => ['label' => '2 WEEKS', 'duration' => 'Two Weeks', 'price' => '70'],
    'month' => ['label' => 'ONE MONTH', 'duration' => 'One Month', 'price' => '70'],
];
$plan = $plans[$planKey] ?? $plans['weekly'];

$pageTitle = 'Premium Plan Payment — Forebetz';
$pageDescription = 'Choose your preferred payment method to activate your Forebetz VIP Plan.';

$paymentMethods = [
    [
        'id' => 'bitcoin',
        'title' => 'Payment with Bitcoin',
        'icon' => 'bitcoin.svg',
        'amount' => '₦50,000',
        'field_label' => 'Wallet Address:',
        'field_value' => 'btc324242238234824242',
    ],
    [
        'id' => 'usdt',
        'title' => 'Payment with uSDT (TRC20)',
        'icon' => 'usdt.svg',
        'amount' => '₦50,000',
        'field_label' => 'Wallet Address:',
        'field_value' => 'btc324242238234824242',
    ],
    [
        'id' => 'paypal',
        'title' => 'payment with Paypal',
        'icon' => 'skrill.png',
        'amount' => '₦50,000',
        'field_label' => 'Email/Address:',
        'field_value' => 'joelessien962@gmail.com',
    ],
    [
        'id' => 'skrill',
        'title' => 'payment with Skrill',
        'icon' => 'skrill.png',
        'amount' => '₦50,000',
        'field_label' => 'Email/Address:',
        'field_value' => 'forebetz@gmail.com',
    ],
];

include __DIR__ . '/includes/head.php';
?>

<div class="relative min-h-screen overflow-x-hidden">
    <div class="relative z-10 flex flex-col">
        <?php include __DIR__ . '/includes/header.php'; ?>

        <?php /* Mobile 477:95617 — stacked cards; tablet 2-col; desktop 3-col */ ?>
        <main class="w-full px-2.5 pb-8 pt-2 sm:px-8 sm:pb-10 sm:pt-2.5 lg:px-[100px]">
            <div class="mx-auto flex w-full max-w-site flex-col gap-[15px]">

                <div class="flex flex-col gap-[3px] py-3 sm:gap-[5px] sm:py-5">
                    <h1 class="text-[30px] font-medium leading-tight text-[#fcbd02] sm:text-[40px] lg:text-[48px]">
                        Premium Plan
                    </h1>
                    <p class="text-[15px] font-normal leading-[17.5px] text-white sm:text-[16px] sm:leading-7 lg:text-[18px] lg:leading-7">
                        Choose your preferred payment method below to activate your VIP Plan
                    </p>
                </div>

                <div class="flex w-full flex-col items-center rounded-[20px] bg-[#f0f0f0] px-[15px] py-5 text-[#1e1e1e] sm:gap-[30px] sm:rounded-[24px] sm:px-8 sm:py-8 md:rounded-[30px] md:px-10 lg:px-[150px] lg:py-10">
                    <div class="flex w-full max-w-[1140px] flex-col gap-[26px] py-2.5 sm:gap-[31px] sm:py-0">

                        <div class="flex flex-col gap-[30px]">
                            <div class="flex flex-col items-center gap-2.5 text-center text-[17px] sm:text-[20px]">
                                <p class="font-semibold leading-normal text-[#1e1e1e]">
                                    Methods available in:&nbsp;Nigeria
                                </p>
                                <p class="font-normal text-[#1e1e1e]">
                                    Not your country?&nbsp;<a href="/pricing.php" class="text-[#ef1410] hover:underline">Change Country</a>
                                </p>
                            </div>

                            <div class="w-full rounded-[20px] border-[5px] border-[#ef1410] bg-gradient-to-b from-[rgba(239,20,16,0.05)] to-[rgba(183,18,15,0.05)] p-5 backdrop-blur-[3.65px]">
                                <div class="flex flex-col items-center justify-center gap-2.5 text-center">
                                    <p class="text-[20px] font-medium text-[#a77900] sm:text-[21px]">
                                        ⏵ PREMIUM → <?= htmlspecialchars($plan['label']) ?>
                                    </p>
                                    <p class="font-medium text-[#04292d]">
                                        <span class="text-[37px] sm:text-[38px]">$<?= htmlspecialchars($plan['price']) ?></span><span class="text-[13px] sm:text-[14px]">/ <?= htmlspecialchars($plan['duration']) ?></span>
                                    </p>
                                </div>
                            </div>

                            <?php /* Mobile: 1 col. Tablet: 2 col. Desktop lg+: 3 col */ ?>
                            <div class="grid grid-cols-1 gap-2.5 md:grid-cols-2 lg:grid-cols-3">
                                <?php foreach ($paymentMethods as $method): ?>
                                    <article class="flex flex-col gap-2.5 rounded-[15px] bg-[#dcdee2] p-2.5">
                                        <div class="flex items-center justify-center gap-2 py-1">
                                            <span class="size-[37px] shrink-0 overflow-hidden rounded-[13px]">
                                                <img
                                                    src="<?= $asset ?>/icons/payment/<?= htmlspecialchars($method['icon']) ?>"
                                                    alt=""
                                                    class="h-full w-full object-contain"
                                                >
                                            </span>
                                            <h2 class="text-[16px] font-bold capitalize leading-[18px] tracking-[0.17px] text-[#1e1e1e] sm:text-[17px]">
                                                <?= htmlspecialchars($method['title']) ?>
                                            </h2>
                                        </div>

                                        <div class="flex flex-1 flex-col gap-[5px] rounded-[10px] bg-white p-2.5">
                                            <div class="rounded-[10px] border border-[#bf6a02] p-2.5">
                                                <div class="flex flex-col gap-5">
                                                    <div class="flex flex-col gap-[5px] text-[15px] sm:text-[16px]">
                                                        <p class="font-normal tracking-[0.2px] text-[#5a5a5a]">Amount:</p>
                                                        <p class="font-bold text-[#04292d]"><?= htmlspecialchars($method['amount']) ?></p>
                                                    </div>
                                                    <div class="flex flex-col gap-[5px]">
                                                        <p class="text-[15px] font-normal tracking-[0.2px] text-[#5a5a5a] sm:text-[16px]">
                                                            <?= htmlspecialchars($method['field_label']) ?>
                                                        </p>
                                                        <div class="flex min-w-0 items-center gap-2.5">
                                                            <p class="min-w-0 break-all text-[15px] font-bold leading-normal text-[#04292d] sm:text-[16px]">
                                                                <?= htmlspecialchars($method['field_value']) ?>
                                                            </p>
                                                            <button
                                                                type="button"
                                                                class="js-copy size-6 shrink-0 overflow-hidden"
                                                                data-copy="<?= htmlspecialchars($method['field_value']) ?>"
                                                                aria-label="Copy <?= htmlspecialchars($method['field_label']) ?>"
                                                            >
                                                                <img src="<?= $asset ?>/icons/payment/copy.svg" alt="" class="h-full w-full object-contain">
                                                            </button>
                                                        </div>
                                                    </div>
                                                </div>
                                            </div>

                                            <div class="rounded-[10px] bg-[#fffbeb] p-2.5">
                                                <div class="flex flex-col gap-[5px] tracking-[0.2px]">
                                                    <p class="text-[16px] font-medium text-[#bf6a02] sm:text-[17px]">
                                                        Important Instructions:
                                                    </p>
                                                    <div class="text-[13px] font-normal leading-normal text-[#303030] sm:text-[14px]">
                                                        <p class="mb-2">
                                                            After a successful transaction, Kindly Forward us an email containing “Your Forebetz
                                                            <strong>Account Email</strong>,
                                                            <strong>Amount Paid</strong>, and
                                                            <strong>Teller Number</strong>
                                                            to infoForebetz@gmail.com or through WhatsApp to +447345042524
                                                        </p>
                                                        <p class="font-medium italic">*Your account will be activated once payment is confirmed.</p>
                                                    </div>
                                                </div>
                                            </div>
                                        </div>

                                    <a
                                        href="/dashboard.php"
                                        class="flex w-full items-center justify-center rounded-[15px] bg-[#fcbd02] px-[30px] py-[15px] text-center text-[18px] font-medium tracking-[0.2px] text-[#1e1e1e] backdrop-blur-[7.45px] hover:brightness-95 sm:text-[19px]"
                                    >
                                        I Have Sent the Money
                                    </a>
                                    </article>
                                <?php endforeach; ?>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </main>
    </div>
</div>

<script>
(() => {
  document.querySelectorAll('.js-copy').forEach((btn) => {
    btn.addEventListener('click', async () => {
      const value = btn.getAttribute('data-copy') || '';
      try {
        await navigator.clipboard.writeText(value);
        btn.classList.add('opacity-60');
        setTimeout(() => btn.classList.remove('opacity-60'), 600);
      } catch (_) {}
    });
  });
})();
</script>

<?php include __DIR__ . '/includes/footer.php'; ?>
