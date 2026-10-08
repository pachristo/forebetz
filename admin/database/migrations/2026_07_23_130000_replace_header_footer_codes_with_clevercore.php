<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;

/**
 * Replace header/footer/body shell snippets with CleverCore header only (legacy header.json).
 */
return new class extends Migration
{
    public function up(): void
    {
        if (! \Illuminate\Support\Facades\Schema::hasTable('header_footer_codes')) {
            return;
        }

        DB::table('header_footer_codes')->delete();

        $header = <<<'HTML'
<script data-cfasync="false" type="text/javascript" id="clever-core">
/* <![CDATA[ */
    (function (document, window) {
        var a, c = document.createElement("script"), f = window.frameElement;

        c.id = "CleverCoreLoader100214";
        c.src = "https://scripts.cleverwebserver.com/ffe7a9257a4f0177c43bb8f9929f7e2a.js";

        c.async = !0;
        c.type = "text/javascript";
        c.setAttribute("data-target", window.name || (f && f.getAttribute("id")));
        c.setAttribute("data-callback", "put-your-callback-function-here");
        c.setAttribute("data-callback-url-click", "put-your-click-macro-here");
        c.setAttribute("data-callback-url-view", "put-your-view-macro-here");

        try {
            a = parent.document.getElementsByTagName("script")[0] || document.getElementsByTagName("script")[0];
        } catch (e) {
            a = !1;
        }

        a || (a = document.getElementsByTagName("head")[0] || document.getElementsByTagName("body")[0]);
        a.parentNode.insertBefore(c, a);
    })(document, window);
/* ]]> */
</script>
HTML;

        $now = now();
        DB::table('header_footer_codes')->insert([
            'type' => 'header',
            'label' => 'CleverCore',
            'code' => $header,
            'created_at' => $now,
            'updated_at' => $now,
        ]);
    }

    public function down(): void
    {
        // irreversible replace of ad/analytics snippets
    }
};
